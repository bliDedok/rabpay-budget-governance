<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RabItem extends Model
{
    protected $fillable = [
        'rab_proposal_id',
        'item_name',
        'category',
        'quantity',
        'unit_price',
        'total_price',
        'realized_amount',
    ];

    public function rabProposal()
    {
        return $this->belongsTo(RabProposal::class);
    }
}
