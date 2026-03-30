<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $author_id
 * @property int $category_id
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string $content
 * @property string|null $featured_image
 * @property array|null $tags
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $published_at
 * @property int $views_count
 * @property int $likes_count
 * @property int $comments_count
 * @property bool $allow_comments
 * @property bool $is_featured
 */
class BlogPost extends Model
{
    protected $fillable = [
        'author_id', 'category_id', 'title', 'slug', 'excerpt', 'content',
        'featured_image', 'tags', 'status', 'published_at',
        'views_count', 'likes_count', 'comments_count',
        'allow_comments', 'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'tags'           => 'array',
            'published_at'   => 'datetime',
            'allow_comments' => 'boolean',
            'is_featured'    => 'boolean',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BlogComment::class, 'post_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                     ->where('published_at', '<=', now());
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
