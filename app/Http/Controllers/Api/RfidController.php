<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FieldUnit;
use Illuminate\Http\Request;

class RfidController extends Controller
{
    public function checkCard(Request $request)
    {
        $request->validate([
            'uid' => 'required|string',
        ]);

        $fieldUnit = FieldUnit::where('rfid_uid', $request->uid)->first();

        if (!$fieldUnit) {
            return response()->json([
                'status' => 'invalid',
                'message' => 'Kartu tidak terdaftar',
            ], 404);
        }

        return response()->json([
            'status' => 'valid',
            'message' => 'Kartu valid',
            'data' => [
                'field_unit_id' => $fieldUnit->id,
                'field_unit_name' => $fieldUnit->name,
                'balance' => optional($fieldUnit->virtualAccount)->current_balance ?? 0,
            ],
        ]);
    }
}
