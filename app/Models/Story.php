<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property string $media_path
 * @property string $media_type
 * @property string|null $caption
 * @property \Illuminate\Support\Carbon|null $expires_at
 * @property int $views_count
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class Story extends Model
{
    protected $fillable = ['user_id', 'media_path', 'media_type', 'caption', 'expires_at', 'views_count'];

    protected $casts = ['expires_at' => 'datetime'];

    public function user() { return $this->belongsTo(User::class); }

    public function isExpired(): bool { return $this->expires_at && $this->expires_at->isPast(); }

    public function scopeActive($query) { return $query->where('expires_at', '>', now()); }
}
