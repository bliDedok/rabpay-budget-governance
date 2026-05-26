<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldUnit extends Model
{
    protected $fillable = [
        'code',
        'name',
        'rfid_uid',
        'pic_name',
        'status',
    ];

    public function rabProposals()
    {
        return $this->hasMany(RabProposal::class);
    }

    public function virtualAccount()
    {
        return $this->hasOne(VirtualAccount::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function cards()
    {
        return $this->hasMany(FieldUnitCard::class);
    }
}
