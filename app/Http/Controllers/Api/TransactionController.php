<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FieldUnit;
use App\Models\Vendor;
use App\Models\RabItem;
use App\Models\Transaction;
use App\Models\RiskScore;
use App\Models\AuditLog;
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
            $card = \App\Models\FieldUnitCard::with('fieldUnit.virtualAccount')
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

            $vendor = Vendor::where('code', $request->vendor_code)
                ->where('status', 'active')
                ->first();

            if (!$vendor) {
                return response()->json([
                    'status' => 'rejected',
                    'message' => 'Vendor tidak valid.',
                ], 404);
            }

            $virtualAccount = $fieldUnit->virtualAccount;

            if (!$virtualAccount || $virtualAccount->current_balance < $request->amount) {
                return response()->json([
                    'status' => 'rejected',
                    'message' => 'Saldo virtual tidak mencukupi.',
                ], 422);
            }

            $rabItem = RabItem::whereHas('rabProposal', function ($query) use ($fieldUnit) {
                    $query->where('field_unit_id', $fieldUnit->id)
                          ->where('status', 'approved');
                })
                ->where('item_name', $request->item_name)
                ->first();

            if (!$rabItem) {
                $transactionStatus = 'pending_approval';
                $message = 'Item tidak ada di RAB. Transaksi masuk pending approval.';
            } else {
                $remainingItemBudget = $rabItem->total_price - $rabItem->realized_amount;

                if ($request->amount > $remainingItemBudget) {
                    return response()->json([
                        'status' => 'rejected',
                        'message' => 'Nominal melebihi sisa anggaran item RAB.',
                    ], 422);
                }

                $transactionStatus = 'success';
                $message = 'Transaksi berhasil.';
            }

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

            $riskScore = 0;
            $indicators = [];

            if (!$rabItem) {
                $riskScore += 25;
                $indicators[] = 'Item tidak ada di RAB';
            }

            if ($request->amount >= 1000000) {
                $riskScore += 10;
                $indicators[] = 'Nominal transaksi besar';
            }

            $riskLevel = $riskScore >= 60 ? 'high' : ($riskScore >= 30 ? 'medium' : 'low');

            RiskScore::create([
                'transaction_id' => $transaction->id,
                'score' => $riskScore,
                'risk_level' => $riskLevel,
                'risk_indicators' => $indicators,
                'recommendation' => $riskLevel === 'high'
                    ? 'Perlu audit tambahan'
                    : 'Transaksi dapat diproses sesuai prosedur',
            ]);

            if ($transactionStatus === 'success') {
                $virtualAccount->decrement('current_balance', $request->amount);

                if ($rabItem) {
                    $rabItem->increment('realized_amount', $request->amount);
                }
            }

            AuditLog::create([
                'action' => 'create_transaction',
                'module' => 'transactions',
                'description' => $message,
                'new_data' => $transaction->toArray(),
                'ip_address' => $request->ip(),
            ]);

            return response()->json([
                'status' => $transactionStatus,
                'message' => $message,
                'data' => [
                    'transaction_code' => $transaction->transaction_code,
                    'field_unit' => $fieldUnit->name,
                    'vendor' => $vendor->name,
                    'item' => $transaction->item_name,
                    'amount' => $transaction->amount,
                    'risk_score' => $riskScore,
                    'risk_level' => $riskLevel,
                    'remaining_balance' => $virtualAccount->fresh()->current_balance,
                ],
            ]);
        });
    }
}
