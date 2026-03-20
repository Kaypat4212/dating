<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletFundingRequest extends Model
{
    protected $fillable = [
        'user_id', 'amount', 'txid', 'proof_path', 'status', 'admin_note'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
