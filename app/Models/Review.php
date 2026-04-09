<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    protected $fillable = [
        'user_id', 'guest_name', 'guest_email',
        'rating', 'title', 'body',
        'status', 'admin_note', 'helpful_count',
    ];

    protected function casts(): array
    {
        return [
            'rating'        => 'integer',
            'helpful_count' => 'integer',
        ];
    }

    // ── Relations ────────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ReviewComment::class);
    }

    public function helpfulVotes(): HasMany
    {
        return $this->hasMany(ReviewHelpfulVote::class);
    }

    // ── Scopes ───────────────────────────────────────────────────────────────

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // ── Accessors ────────────────────────────────────────────────────────────

    /** Display name: registered user name or guest name. */
    public function getAuthorNameAttribute(): string
    {
        return $this->user?->name ?? $this->guest_name ?? 'Anonymous';
    }

    /** Whether the given user has already marked this review as helpful. */
    public function hasBeenMarkedHelpfulBy(?int $userId): bool
    {
        if (! $userId) return false;
        return $this->helpfulVotes()->where('user_id', $userId)->exists();
    }
}
