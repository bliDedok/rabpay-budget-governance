<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FieldUnitCard;
use App\Models\RfidTap;
use Illuminate\Http\Request;

class RfidController extends Controller
{
    public function checkCard(Request $request)
    {
        $request->validate([
            'uid' => 'required|string',
        ]);

        $uid = strtoupper($request->uid);

        $card = FieldUnitCard::with('fieldUnit.virtualAccount')
            ->where('rfid_uid', $uid)
            ->where('status', 'active')
            ->first();

        if (!$card) {
            RfidTap::create([
                'field_unit_card_id' => null,
                'rfid_uid' => $uid,
                'status' => 'invalid',
                'message' => 'Kartu tidak terdaftar atau tidak aktif',
                'tapped_at' => now(),
            ]);

            return response()->json([
                'status' => 'invalid',
                'message' => 'Kartu tidak terdaftar atau tidak aktif',
            ], 404);
        }

        RfidTap::create([
            'field_unit_card_id' => $card->id,
            'rfid_uid' => $uid,
            'status' => 'valid',
            'message' => 'Kartu valid',
            'tapped_at' => now(),
        ]);

        $fieldUnit = $card->fieldUnit;

        return response()->json([
            'status' => 'valid',
            'message' => 'Kartu valid',
            'data' => [
                'card_id' => $card->id,
                'card_code' => $card->card_code,
                'card_label' => $card->label,
                'rfid_uid' => $card->rfid_uid,
                'field_unit_id' => $fieldUnit->id,
                'field_unit_code' => $fieldUnit->code,
                'field_unit_name' => $fieldUnit->name,
                'description' => $card->description,
                'balance' => optional($fieldUnit->virtualAccount)->current_balance ?? 0,
            ],
        ]);
    }

    public function latestTap()
    {
        $tap = RfidTap::with('card.fieldUnit.virtualAccount')
            ->latest('tapped_at')
            ->first();

        if (!$tap) {
            return response()->json([
                'status' => 'empty',
                'message' => 'Belum ada kartu yang ditap',
            ]);
        }

        if ($tap->status !== 'valid' || !$tap->card) {
            return response()->json([
                'status' => 'invalid',
                'message' => $tap->message,
                'data' => [
                    'rfid_uid' => $tap->rfid_uid,
                    'tapped_at' => optional($tap->tapped_at)->format('Y-m-d H:i:s'),
                ],
            ]);
        }

        $card = $tap->card;
        $fieldUnit = $card->fieldUnit;

        return response()->json([
            'status' => 'valid',
            'message' => 'Kartu terakhir ditemukan',
            'data' => [
                'card_id' => $card->id,
                'card_code' => $card->card_code,
                'card_label' => $card->label,
                'rfid_uid' => $card->rfid_uid,
                'field_unit_id' => $fieldUnit->id,
                'field_unit_code' => $fieldUnit->code,
                'field_unit_name' => $fieldUnit->name,
                'description' => $card->description,
                'balance' => optional($fieldUnit->virtualAccount)->current_balance ?? 0,
                'tapped_at' => optional($tap->tapped_at)->format('Y-m-d H:i:s'),
            ],
        ]);
    }
}
