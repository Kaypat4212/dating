<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int $amount
 * @property string $destination
 * @property string|null $currency
 * @property string|null $network
 * @property string $status
 * @property string|null $admin_note
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
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
