<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RfidTap extends Model
{
    protected $fillable = [
        'field_unit_card_id',
        'rfid_uid',
        'status',
        'message',
        'tapped_at',
    ];

    protected $casts = [
        'tapped_at' => 'datetime',
    ];

    public function card()
    {
        return $this->belongsTo(FieldUnitCard::class, 'field_unit_card_id');
    }
}
