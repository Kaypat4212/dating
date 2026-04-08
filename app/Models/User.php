<?php

namespace App\Models;

use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string|null $username
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $gender
 * @property string|null $seeking
 * @property \Illuminate\Support\Carbon|null $date_of_birth
 * @property bool $is_premium
 * @property \Illuminate\Support\Carbon|null $premium_expires_at
 * @property bool $is_banned
 * @property string|null $banned_reason
 * @property bool $likes_restricted
 * @property bool $swipes_restricted
 * @property string|null $email_otp
 * @property \Illuminate\Support\Carbon|null $email_otp_expires_at
 * @property \Illuminate\Support\Carbon|null $last_active_at
 * @property bool $profile_complete
 * @property int $onboarding_step
 * @property int $reminder_count
 * @property \Illuminate\Support\Carbon|null $last_reminder_at
 * @property int $credit_balance
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class User extends Authenticatable implements MustVerifyEmail, FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name', 'username', 'email', 'password',
        'gender', 'seeking', 'date_of_birth',
        'is_premium', 'premium_plan', 'premium_expires_at',
        'is_banned', 'banned_reason',
        'likes_restricted', 'swipes_restricted',
        'passport_mode', 'passport_country',
        'email_otp', 'email_otp_expires_at',
        'is_verified',
        'hide_last_seen',
        'last_active_at', 'profile_complete', 'onboarding_step',
        'reminder_count', 'last_reminder_at',
        'secret_word',
        'totp_secret', 'totp_recovery_codes',
        'google_id',
        'credit_balance',
        'location_filter_uses',
        'registration_ip', 'last_login_ip', 'last_login_at',
        'referral_code', 'referred_by',
        'elo_score',
        'login_streak', 'last_checkin_date', 'streak_freeze_count',
        'read_receipts_enabled',
    ];

    protected $hidden = [
        'password', 'remember_token', 'secret_word',
    ];

    /** Only admins (role=admin) can access the Filament panel. */
    public function canAccessPanel(Panel $panel): bool
    {
        // ID=1 is always the superuser; also allow anyone with the 'admin' role
        return $this->id === 1 || $this->hasRole('admin');
    }

    // ---- Filament 2FA (TOTP) ----

    public function getAppAuthenticationSecret(): ?string
    {
        return $this->totp_secret;
    }

    public function saveAppAuthenticationSecret(?string $secret): void
    {
        $this->forceFill(['totp_secret' => $secret])->save();
    }

    public function getAppAuthenticationHolderName(): string
    {
        return $this->email;
    }

    public function getAppAuthenticationRecoveryCodes(): ?array
    {
        return $this->totp_recovery_codes;
    }

    public function saveAppAuthenticationRecoveryCodes(?array $codes): void
    {
        $this->forceFill(['totp_recovery_codes' => $codes])->save();
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'date_of_birth'     => 'date',
            'is_premium'        => 'boolean',
            'premium_expires_at' => 'datetime',
            'is_banned'         => 'boolean',
            'is_verified'       => 'boolean',
            'hide_last_seen'         => 'boolean',
            'read_receipts_enabled'  => 'boolean',
            'last_active_at'        => 'datetime',
            'email_otp_expires_at'  => 'datetime',
            'profile_complete'      => 'boolean',
            'reminder_count'        => 'integer',
            'last_reminder_at'      => 'datetime',
            'passport_mode'         => 'boolean',
            'totp_recovery_codes'   => 'array',
            'location_filter_uses'  => 'integer',
            'login_streak'          => 'integer',
            'last_checkin_date'     => 'date',
            'streak_freeze_count'   => 'integer',
        ];
    }

    // ---- Referral relationships ----

    public function referrals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Referral::class, 'referrer_id');
    }

    public function referredByUser(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    public function referralRecord(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Referral::class, 'referred_id');
    }

    // ---- Relationships ----

    public function profile(): HasOne
    {
        return $this->hasOne(Profile::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(Photo::class);
    }

    public function primaryPhoto(): HasOne
    {
        return $this->hasOne(Photo::class)->where('is_primary', true)->where('is_approved', true);
    }

    public function sentLikes(): HasMany
    {
        return $this->hasMany(Like::class, 'sender_id');
    }

    public function receivedLikes(): HasMany
    {
        return $this->hasMany(Like::class, 'receiver_id');
    }

    public function matchesAsUser1(): HasMany
    {
        return $this->hasMany(UserMatch::class, 'user1_id');
    }

    public function matchesAsUser2(): HasMany
    {
        return $this->hasMany(UserMatch::class, 'user2_id');
    }

    public function badges(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
                    ->withPivot('earned_at', 'is_pinned')
                    ->orderByPivot('earned_at', 'desc');
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class, 'blocker_id');
    }

    public function blockedBy(): HasMany
    {
        return $this->hasMany(Block::class, 'blocked_id');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }

    public function preferences(): HasOne
    {
        return $this->hasOne(UserPreference::class);
    }

    public function savedFilters(): HasMany
    {
        return $this->hasMany(SavedFilter::class);
    }

    public function premiumPayments(): HasMany
    {
        return $this->hasMany(PremiumPayment::class);
    }

    public function sentWaves(): HasMany
    {
        return $this->hasMany(Wave::class, 'sender_id');
    }

    public function receivedWaves(): HasMany
    {
        return $this->hasMany(Wave::class, 'receiver_id');
    }

    public function stories(): HasMany
    {
        return $this->hasMany(Story::class);
    }

    public function boosts(): HasMany
    {
        return $this->hasMany(Boost::class);
    }

    public function voicePrompts(): HasMany
    {
        return $this->hasMany(VoicePrompt::class);
    }

    public function travelPlans(): HasMany
    {
        return $this->hasMany(TravelPlan::class);
    }

    public function travelInterests(): HasMany
    {
        return $this->hasMany(TravelInterest::class);
    }

    public function verification(): HasOne
    {
        return $this->hasOne(UserVerification::class);
    }

    public function activeBoost()
    {
        return $this->boosts()->where('active', true)->where('ends_at', '>', now())->latest()->first();
    }

    // ---- Helpers ----

    public function getAgeAttribute(): int
    {
        return $this->date_of_birth ? $this->date_of_birth->age : 0;
    }

    public function getDisplayPhotoAttribute(): ?Photo
    {
        return $this->primaryPhoto ?? $this->photos()->where('is_approved', true)->first();
    }

    public function isPremiumActive(): bool
    {
        return $this->is_premium && (
            $this->premium_expires_at === null || $this->premium_expires_at->isFuture()
        );
    }

    public function isVerified(): bool
    {
        return (bool) $this->is_verified;
    }

    public function setPremium(string $plan): void
    {
        $days = match ($plan) {
            '30day'  => 30,
            '90day'  => 90,
            '365day' => 365,
            default  => 30,
        };
        $this->update([
            'is_premium'         => true,
            'premium_plan'       => $plan,
            'premium_expires_at' => now()->addDays($days),
        ]);
    }

    public function revokePremium(): void
    {
        $this->update(['is_premium' => false, 'premium_plan' => null, 'premium_expires_at' => null, 'hide_last_seen' => false]);
    }

    /** True only when the user has the yearly (highest) active plan. */
    public function isHighestPremium(): bool
    {
        return $this->isPremiumActive() && $this->premium_plan === '365day';
    }

    /** True when the user is on the 90-day or 365-day plan (passport mode eligible). */
    public function isPassportEligible(): bool
    {
        return $this->isPremiumActive() && in_array($this->premium_plan, ['90day', '365day']);
    }

    /** True when passport mode is active AND the user is still eligible. */
    public function isPassportActive(): bool
    {
        return $this->passport_mode && $this->isPassportEligible();
    }

    /**
     * Return the last-seen Carbon or null for display, honouring the user's
     * privacy setting (hide_last_seen is available for any premium tier).
     */
    public function visibleLastSeenTo(?self $viewer): ?\Illuminate\Support\Carbon
    {
        if ($this->hide_last_seen && $this->isPremiumActive()) {
            // Still visible to themselves
            if ($viewer && $viewer->id === $this->id) {
                return $this->last_active_at;
            }
            return null;
        }
        return $this->last_active_at;
    }

    public function hasBlockedOrIsBlocked(int $userId): bool
    {
        return Block::where(function ($q) use ($userId) {
            $q->where('blocker_id', $this->id)->where('blocked_id', $userId);
        })->orWhere(function ($q) use ($userId) {
            $q->where('blocker_id', $userId)->where('blocked_id', $this->id);
        })->exists();
    }

    public function hasLiked(int $userId): bool
    {
        return $this->sentLikes()->where('receiver_id', $userId)->exists();
    }

    public function isMatchedWith(int $userId): bool
    {
        return UserMatch::where(function ($q) use ($userId) {
            $q->where('user1_id', $this->id)->where('user2_id', $userId);
        })->orWhere(function ($q) use ($userId) {
            $q->where('user1_id', $userId)->where('user2_id', $this->id);
        })->where('is_active', true)->exists();
    }

    /**
     * Granular online status based on last_active_at.
     *
     * Returns one of: 'online' | 'recent' | 'today' | 'away'
     *   online  — active within the last 10 minutes
     *   recent  — active between 10 min and 1 hour ago
     *   today   — active today (but more than 1 hour ago)
     *   away    — last active more than 24 hours ago (or never)
     */
    public function onlineStatus(): string
    {
        if (! $this->last_active_at) return 'away';

        $minutes = $this->last_active_at->diffInMinutes(now());

        if ($minutes < 10)                             return 'online';
        if ($minutes < 60)                             return 'recent';
        if ($this->last_active_at->isToday())          return 'today';
        return 'away';
    }

    /**
     * Human-readable online status label.
     */
    public function onlineStatusLabel(): string
    {
        return match ($this->onlineStatus()) {
            'online' => 'Online now',
            'recent' => 'Active ' . $this->last_active_at->diffForHumans(),
            'today'  => 'Active today',
            default  => $this->last_active_at ? 'Active ' . $this->last_active_at->diffForHumans() : 'Offline',
        };
    }

    /**
     * Tailwind/Bootstrap colour for the online status dot.
     */
    public function onlineStatusColor(): string
    {
        return match ($this->onlineStatus()) {
            'online' => '#22c55e', // green
            'recent' => '#eab308', // yellow
            'today'  => '#f97316', // orange
            default  => '#9ca3af', // gray
        };
    }
}


