<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class FeedPost extends Model
{
    protected $fillable = [
        'user_id', 'body', 'media_path', 'media_type',
        'original_post_id', 'likes_count', 'comments_count', 'reposts_count', 'is_active',
        'post_type', 'poll_question', 'poll_options',
    ];

    protected $appends = ['media_url'];

    protected function casts(): array
    {
        return [
            'is_active'    => 'boolean',
            'poll_options' => 'array',
        ];
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function originalPost(): BelongsTo
    {
        return $this->belongsTo(FeedPost::class, 'original_post_id');
    }

    public function reposts(): HasMany
    {
        return $this->hasMany(FeedPost::class, 'original_post_id');
    }

    public function likes(): HasMany
    {
        return $this->hasMany(FeedPostLike::class, 'post_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(FeedPostComment::class, 'post_id')->whereNull('parent_id')->latest();
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(FeedPostComment::class, 'post_id');
    }

    // ── Accessors ─────────────────────────────────────────────────────────────

    public function getMediaUrlAttribute(): ?string
    {
        return $this->media_path ? Storage::disk('public')->url($this->media_path) : null;
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function isLikedBy(int $userId): bool
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    public function isRepostedBy(int $userId): bool
    {
        return static::where('user_id', $userId)->where('original_post_id', $this->id)->exists();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
