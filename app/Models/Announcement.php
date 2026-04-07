<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    protected $fillable = [
        'title', 'body', 'type', 'version', 'badge_label', 'badge_color',
        'is_published', 'show_popup', 'target_user_id', 'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'show_popup'   => 'boolean',
        'published_at' => 'datetime',
    ];

    /** Bootstrap color → hex for icon tinting */
    public const TYPE_ICONS = [
        'feature'     => '✨',
        'update'      => '🔄',
        'maintenance' => '🔧',
        'message'     => '💬',
        'promo'       => '🎁',
    ];

    public const TYPE_COLORS = [
        'feature'     => 'success',
        'update'      => 'primary',
        'maintenance' => 'warning',
        'message'     => 'info',
        'promo'       => 'danger',
    ];

    // ── Relationships ──────────────────────────────────────────────────────

    public function reads(): HasMany
    {
        return $this->hasMany(AnnouncementRead::class);
    }

    public function targetUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    // ── Scopes ─────────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                     ->where(fn($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where(fn($q) => $q->whereNull('target_user_id')->orWhere('target_user_id', $userId));
    }

    // ── Helpers ────────────────────────────────────────────────────────────

    public function typeIcon(): string
    {
        return self::TYPE_ICONS[$this->type] ?? '📢';
    }

    public function typeColor(): string
    {
        return self::TYPE_COLORS[$this->type] ?? 'secondary';
    }

    public function isReadBy(int $userId): bool
    {
        return $this->reads()->where('user_id', $userId)->exists();
    }
}
