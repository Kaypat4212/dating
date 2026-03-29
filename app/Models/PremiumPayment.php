<?php

namespace App\Models;

use App\Services\AdminFundingAlertService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $plan
 * @property float|null $amount
 * @property string|null $crypto_currency
 * @property string|null $wallet_address
 * @property string|null $tx_hash
 * @property string|null $proof_image
 * @property string $status
 * @property string|null $notes
 * @property int|null $approved_by
 * @property \Illuminate\Support\Carbon|null $approved_at
 * @property bool $is_upgrade
 * @property string|null $upgrade_from_plan
 * @property float|null $upgrade_credit
 * @property string|null $invoice_number
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property-read string $plan_label
 */
class PremiumPayment extends Model
{
    protected $fillable = [
        'user_id', 'plan', 'amount', 'crypto_currency',
        'wallet_address', 'tx_hash', 'proof_image', 'status', 'notes',
        'approved_by', 'approved_at',
        'is_upgrade', 'upgrade_from_plan', 'upgrade_credit', 'invoice_number',
    ];

    protected function casts(): array
    {
        return [
            'approved_at'    => 'datetime',
            'amount'         => 'decimal:8',
            'upgrade_credit' => 'decimal:2',
            'is_upgrade'     => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::created(function (PremiumPayment $payment): void {
            $number = 'INV-' . now()->format('Y') . '-' . str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT);
            $payment->updateQuietly(['invoice_number' => $number]);

            if ($payment->status === 'pending') {
                $payment->refresh();
                app(AdminFundingAlertService::class)->notifyNewPendingFunding($payment);
            }
        });
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function approver(): BelongsTo { return $this->belongsTo(User::class, 'approved_by'); }

    public function planLabel(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => match ($this->plan) {
                '30day'  => '1 Month',
                '90day'  => '3 Months',
                '365day' => '1 Year',
                default  => ucfirst($this->plan),
            }
        );
    }

    public function upgradePlanLabel(): \Illuminate\Database\Eloquent\Casts\Attribute
    {
        return \Illuminate\Database\Eloquent\Casts\Attribute::make(
            get: fn () => match ($this->upgrade_from_plan) {
                '30day'  => '1 Month',
                '90day'  => '3 Months',
                '365day' => '1 Year',
                default  => $this->upgrade_from_plan ? ucfirst($this->upgrade_from_plan) : null,
            }
        );
    }
}
