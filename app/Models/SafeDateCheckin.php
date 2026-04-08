<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SafeDateCheckin extends Model
{
    protected $fillable = [
        'user_id', 'date_location',
        'emergency_contact_name', 'emergency_contact_phone', 'emergency_contact_email',
        'date_at', 'checkin_minutes', 'checked_in_at', 'alert_sent_at', 'status',
    ];

    protected function casts(): array
    {
        return [
            'date_at'        => 'datetime',
            'checked_in_at'  => 'datetime',
            'alert_sent_at'  => 'datetime',
        ];
    }

    public function user(): BelongsTo { return $this->belongsTo(User::class); }

    public function isExpired(): bool
    {
        return $this->status === 'active'
            && now()->gt($this->date_at->addMinutes($this->checkin_minutes));
    }
}
