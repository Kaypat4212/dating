<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VpnDetectionLog extends Model
{
    protected $fillable = [
        'ip_address',
        'user_id',
        'is_vpn',
        'confidence',
        'provider',
        'detection_details',
        'action_taken',
        'user_agent',
    ];

    protected $casts = [
        'is_vpn' => 'boolean',
        'confidence' => 'integer',
        'detection_details' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get color class based on confidence level
     */
    public function getConfidenceColorAttribute(): string
    {
        return match (true) {
            $this->confidence >= 80 => 'danger',
            $this->confidence >= 50 => 'warning',
            default => 'info',
        };
    }
}
