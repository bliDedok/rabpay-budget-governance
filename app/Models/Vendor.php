<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'code',
        'name',
        'category',
        'qr_code',
        'status',
    ];

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
