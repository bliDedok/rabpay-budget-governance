<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use App\Models\RabItem;
use App\Models\Transaction;
use App\Models\RiskScore;
use App\Models\AuditLog;
use App\Models\FieldUnitCard;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required|string',
            'vendor_code' => 'required|string',
            'item_name' => 'required|string',
            'amount' => 'required|numeric|min:1',
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Cek kartu RFID
            $card = FieldUnitCard::with('fieldUnit.virtualAccount')
                ->where('rfid_uid', strtoupper($request->rfid_uid))
                ->where('status', 'active')
                ->first();

            if (!$card) {
                return response()->json([
                    'status' => 'rejected',
                    'message' => 'Kartu tidak terdaftar atau tidak aktif.',
                ], 404);
            }

            $fieldUnit = $card->fieldUnit;

            // 2. Cek vendor
            $vendor = Vendor::where('code', $request->vendor_code)
                ->where('status', 'active')
                ->first();

            if (!$vendor) {
                return response()->json([
                    'status' => 'rejected',
                    'message' => 'Vendor tidak valid.',
                ], 404);
            }

            // 3. Cek virtual account
            $virtualAccount = $fieldUnit->virtualAccount;

            if (!$virtualAccount) {
                return response()->json([
                    'status' => 'rejected',
                    'message' => 'Saldo virtual bidang tidak ditemukan.',
                ], 404);
            }

            // 4. Cari item RAB approved milik bidang tersebut
            $rabItem = RabItem::whereHas('rabProposal', function ($query) use ($fieldUnit) {
                    $query->where('field_unit_id', $fieldUnit->id)
                          ->where('status', 'approved');
                })
                ->where('item_name', $request->item_name)
                ->first();

            // 5. Cek kategori vendor vs kategori item
            $vendorCategoryMismatch = false;

            if ($rabItem) {
                $vendorCategory = strtolower(trim($vendor->category ?? ''));
                $itemCategory = strtolower(trim($rabItem->category ?? ''));

                if (
                    $vendorCategory !== '' &&
                    $itemCategory !== '' &&
                    $vendorCategory !== $itemCategory
                ) {
                    $vendorCategoryMismatch = true;
                }
            }

            // 6. Tentukan status transaksi + risk score
            $remainingItemBudget = $rabItem
                ? ($rabItem->total_price - $rabItem->realized_amount)
                : 0;

            $riskScore = 0;
            $indicators = [];
            $transactionStatus = 'success';
            $message = 'Transaksi berhasil.';

            if (!$rabItem) {
                $transactionStatus = 'pending_approval';
                $message = 'Item tidak ada di RAB. Transaksi masuk pending approval.';
                $riskScore += 40;
                $indicators[] = 'Item tidak ada di RAB';
            } elseif ($vendorCategoryMismatch) {
                $transactionStatus = 'pending_approval';
                $message = 'Kategori vendor tidak sesuai dengan kategori item RAB. Transaksi membutuhkan persetujuan.';
                $riskScore += 40;
                $indicators[] = 'Kategori vendor tidak sesuai dengan item RAB';
            } elseif ($request->amount > $remainingItemBudget) {
                $transactionStatus = 'rejected';
                $message = 'Nominal melebihi sisa anggaran item RAB.';
                $riskScore += 60;
                $indicators[] = 'Nominal melebihi sisa anggaran item RAB';
            } elseif ($request->amount > $virtualAccount->current_balance) {
                $transactionStatus = 'rejected';
                $message = 'Saldo virtual tidak mencukupi.';
                $riskScore += 60;
                $indicators[] = 'Saldo virtual tidak mencukupi';
            }

            if ($request->amount >= 1000000) {
                $riskScore += 10;
                $indicators[] = 'Nominal transaksi besar';
            }

            $riskLevel = $riskScore >= 60 ? 'high' : ($riskScore >= 30 ? 'medium' : 'low');

            // 7. Simpan transaksi
            $transaction = Transaction::create([
                'transaction_code' => 'TRX-' . strtoupper(Str::random(8)),
                'field_unit_id' => $fieldUnit->id,
                'vendor_id' => $vendor->id,
                'rab_item_id' => $rabItem?->id,
                'item_name' => $request->item_name,
                'category' => $rabItem?->category ?? $vendor->category,
                'amount' => $request->amount,
                'status' => $transactionStatus,
                'note' => $message,
            ]);

            // 8. Simpan risk score
            RiskScore::create([
                'transaction_id' => $transaction->id,
                'score' => $riskScore,
                'risk_level' => $riskLevel,
                'risk_indicators' => $indicators,
                'recommendation' => $this->getRiskRecommendation($transactionStatus, $riskLevel),
            ]);

            // 9. Kurangi saldo hanya jika transaksi success
            if ($transactionStatus === 'success') {
                $virtualAccount->decrement('current_balance', $request->amount);

                if ($rabItem) {
                    $rabItem->increment('realized_amount', $request->amount);
                }
            }

            // 10. Audit trail
            AuditLog::create([
                'action' => 'create_transaction',
                'module' => 'transactions',
                'description' => $message,
                'new_data' => [
                    'transaction_code' => $transaction->transaction_code,
                    'card_code' => $card->card_code,
                    'rfid_uid' => $card->rfid_uid,
                    'field_unit' => $fieldUnit->name,
                    'vendor' => $vendor->name,
                    'vendor_category' => $vendor->category,
                    'item_name' => $request->item_name,
                    'item_category' => $rabItem?->category,
                    'amount' => $request->amount,
                    'status' => $transactionStatus,
                    'risk_score' => $riskScore,
                    'risk_level' => $riskLevel,
                    'indicators' => $indicators,
                ],
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'status' => $transactionStatus,
                'message' => $message,
                'data' => [
                    'transaction_code' => $transaction->transaction_code,
                    'field_unit' => $fieldUnit->name,
                    'vendor' => $vendor->name,
                    'vendor_category' => $vendor->category,
                    'item' => $transaction->item_name,
                    'item_category' => $rabItem?->category,
                    'amount' => $transaction->amount,
                    'risk_score' => $riskScore,
                    'risk_level' => $riskLevel,
                    'risk_indicators' => $indicators,
                    'remaining_balance' => $virtualAccount->fresh()->current_balance,
                    'status' => $transaction->status,
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                ],
            ]);
        });
    }

    public function latest()
    {
        $transactions = Transaction::with(['fieldUnit', 'vendor', 'riskScore'])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($transaction) {
                return [
                    'transaction_code' => $transaction->transaction_code,
                    'field_unit' => $transaction->fieldUnit->name ?? '-',
                    'vendor' => $transaction->vendor->name ?? '-',
                    'item' => $transaction->item_name,
                    'amount' => $transaction->amount,
                    'status' => $transaction->status,
                    'risk_score' => optional($transaction->riskScore)->score ?? 0,
                    'risk_level' => optional($transaction->riskScore)->risk_level ?? 'low',
                    'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                ];
            });

        return response()->json([
            'status' => 'success',
            'data' => $transactions,
        ]);
    }

    private function getRiskRecommendation(string $transactionStatus, string $riskLevel): string
    {
        if ($transactionStatus === 'rejected') {
            return 'Transaksi ditolak. Perlu pemeriksaan anggaran, saldo, dan kesesuaian RAB.';
        }

        if ($transactionStatus === 'pending_approval') {
            return 'Transaksi membutuhkan persetujuan bidang keuangan atau pimpinan sebelum diproses.';
        }

        if ($riskLevel === 'high') {
            return 'Perlu audit tambahan karena transaksi memiliki tingkat risiko tinggi.';
        }

        if ($riskLevel === 'medium') {
            return 'Perlu verifikasi tambahan untuk memastikan transaksi sesuai kebijakan RAB.';
        }

        return 'Transaksi dapat diproses sesuai prosedur.';
    }
}
