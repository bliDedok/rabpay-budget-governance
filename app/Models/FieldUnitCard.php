<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldUnitCard extends Model
{
    protected $fillable = [
        'field_unit_id',
        'card_code',
        'rfid_uid',
        'label',
        'description',
        'status',
    ];

    public function fieldUnit()
    {
        return $this->belongsTo(FieldUnit::class);
    }


}
