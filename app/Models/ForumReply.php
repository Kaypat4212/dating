<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumReply extends Model
{
    protected $fillable = [
        'topic_id', 'user_id', 'parent_id', 'content',
        'is_best_answer', 'likes_count', 'is_flagged',
    ];

    protected function casts(): array
    {
        return [
            'is_best_answer' => 'boolean',
            'is_flagged'     => 'boolean',
        ];
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(ForumTopic::class, 'topic_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(ForumReply::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(ForumReply::class, 'parent_id');
    }
}
