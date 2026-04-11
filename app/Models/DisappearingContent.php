<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Snapchat-style disappearing content (view once)
 * Content is deleted after first view
 */
class DisappearingContent extends Model
{
    protected $table = 'disappearing_content';

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'media_path',
        'media_type',
        'viewed',
        'viewed_at',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'viewed' => 'boolean',
            'viewed_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Get full public URL for the media
     */
    public function getMediaUrlAttribute(): ?string
    {
        return $this->media_path ? asset('storage/' . $this->media_path) : null;
    }

    /**
     * Mark as viewed and schedule for deletion
     */
    public function markAsViewed(): void
    {
        $this->update([
            'viewed' => true,
            'viewed_at' => now(),
            'expires_at' => now()->addSeconds(10), // Delete 10 seconds after viewing
        ]);
    }

    /**
     * Delete expired content
     */
    public static function deleteExpired(): void
    {
        $expired = self::where('expires_at', '<=', now())->get();

        foreach ($expired as $content) {
            // Delete the file from storage
            if ($content->media_path && Storage::disk('public')->exists($content->media_path)) {
                Storage::disk('public')->delete($content->media_path);
            }
            $content->delete();
        }
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }
}
