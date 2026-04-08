<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedPostComment extends Model
{
    protected $fillable = ['post_id', 'user_id', 'parent_id', 'body', 'likes_count', 'is_flagged'];

    protected function casts(): array
    {
        return ['is_flagged' => 'boolean'];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(FeedPost::class, 'post_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(FeedPostComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(FeedPostComment::class, 'parent_id')->oldest();
    }

    public function commentLikes(): HasMany
    {
        return $this->hasMany(FeedCommentLike::class, 'comment_id');
    }

    public function isLikedBy(int $userId): bool
    {
        return $this->commentLikes()->where('user_id', $userId)->exists();
    }
}
