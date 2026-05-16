<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RabProposal extends Model
{
    protected $fillable = [
        'field_unit_id',
        'title',
        'description',
        'total_budget',
        'status',
        'created_by',
    ];

    public function fieldUnit()
    {
        return $this->belongsTo(FieldUnit::class);
    }

    public function items()
    {
        return $this->hasMany(RabItem::class);
    }
}
