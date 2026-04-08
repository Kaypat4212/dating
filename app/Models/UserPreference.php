<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id', 'min_age', 'max_age', 'max_distance_km', 'preferred_state',
        'seeking_gender', 'body_types', 'show_online_only',
        // Email notification preferences
        'email_new_message', 'email_new_match', 'email_profile_liked',
        'email_wave_received', 'email_travel_interest', 'email_login_alert',
        'email_weekly_digest',
    ];

    protected function casts(): array
    {
        return [
            'body_types'            => 'array',
            'show_online_only'      => 'boolean',
            'email_new_message'     => 'boolean',
            'email_new_match'       => 'boolean',
            'email_profile_liked'   => 'boolean',
            'email_wave_received'   => 'boolean',
            'email_travel_interest' => 'boolean',
            'email_login_alert'     => 'boolean',
            'email_weekly_digest'   => 'boolean',
        ];
    }

    /**
     * Check whether a specific email notification type is enabled by the user.
     * Defaults to true if the preference record doesn't exist yet.
     */
    public function wantsEmail(string $key): bool
    {
        return (bool) ($this->$key ?? true);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
