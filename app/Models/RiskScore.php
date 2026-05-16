<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiskScore extends Model
{
    protected $fillable = [
        'transaction_id',
        'score',
        'risk_level',
        'risk_indicators',
        'recommendation',
    ];

    protected $casts = [
        'risk_indicators' => 'array',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
