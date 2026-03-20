<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletWithdrawalRequest extends Model
{
    protected $fillable = [
        'user_id', 'amount', 'destination', 'currency', 'network', 'status', 'admin_note'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
