<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int    $id
 * @property int    $user_id
 * @property string $type  tip_sent|tip_received|deposit|withdrawal|admin_credit|admin_debit
 * @property int    $amount
 * @property int    $balance_after
 * @property int|null    $reference_id
 * @property string|null $reference_type
 * @property string|null $description
 */
class WalletTransaction extends Model
{
    protected $fillable = [
        'user_id', 'type', 'amount', 'balance_after',
        'reference_id', 'reference_type', 'description',
    ];

    protected function casts(): array
    {
        return [
            'amount'       => 'integer',
            'balance_after' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isCredit(): bool
    {
        return in_array($this->type, ['tip_received', 'deposit', 'admin_credit']);
    }

    public function signedAmount(): string
    {
        return ($this->isCredit() ? '+' : '-') . $this->amount;
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'tip_sent'     => '💸 Gift Sent',
            'tip_received' => '🎁 Gift Received',
            'deposit'      => '💳 Deposit',
            'withdrawal'   => '🏧 Withdrawal',
            'admin_credit' => '⬆ Admin Credit',
            'admin_debit'  => '⬇ Admin Debit',
            default        => ucfirst(str_replace('_', ' ', $this->type)),
        };
    }
}
