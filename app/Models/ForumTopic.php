<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $category_id
 * @property int $user_id
 * @property string $title
 * @property string $slug
 * @property string $content
 * @property array|null $tags
 * @property bool $is_pinned
 * @property bool $is_locked
 * @property bool $is_answered
 * @property int $views_count
 * @property int $replies_count
 * @property int $likes_count
 */
class ForumTopic extends Model
{
    protected $fillable = [
        'category_id', 'user_id', 'title', 'slug', 'content', 'tags',
        'is_pinned', 'is_locked', 'is_answered',
        'views_count', 'replies_count', 'likes_count',
        'last_reply_user_id', 'last_reply_at',
    ];

    protected function casts(): array
    {
        return [
            'tags'          => 'array',
            'is_pinned'     => 'boolean',
            'is_locked'     => 'boolean',
            'is_answered'   => 'boolean',
            'last_reply_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ForumCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function lastReplyUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_reply_user_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ForumReply::class, 'topic_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
