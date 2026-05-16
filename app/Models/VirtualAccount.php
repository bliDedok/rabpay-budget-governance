<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VirtualAccount extends Model
{
    protected $fillable = [
        'field_unit_id',
        'initial_balance',
        'current_balance',
        'status',
    ];

    public function fieldUnit()
    {
        return $this->belongsTo(FieldUnit::class);
    }
}
