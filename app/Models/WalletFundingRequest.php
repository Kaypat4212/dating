<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $amount
 * @property string|null $txid
 * @property string|null $proof_path
 * @property string $status
 * @property string|null $admin_note
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
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
