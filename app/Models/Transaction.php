<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'transaction_code',
        'field_unit_id',
        'vendor_id',
        'rab_item_id',
        'item_name',
        'category',
        'amount',
        'status',
        'note',
        'evidence_file',
    ];

    public function fieldUnit()
    {
        return $this->belongsTo(FieldUnit::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function rabItem()
    {
        return $this->belongsTo(RabItem::class);
    }

    public function riskScore()
    {
        return $this->hasOne(RiskScore::class);
    }
}
