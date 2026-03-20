<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $conversation_id
 * @property int $sender_id
 * @property string $body
 * @property string|null $type
 * @property \Illuminate\Support\Carbon|null $read_at
 * @property \Illuminate\Support\Carbon $created_at
 */
class Message extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'conversation_id', 'sender_id', 'body', 'type',
        'attachment_path', 'attachment_name', 'attachment_mime', 'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at'    => 'datetime',
            'created_at' => 'datetime',
        ];
    }

    /** Full public URL for an attachment. */
    public function getAttachmentUrlAttribute(): ?string
    {
        return $this->attachment_path
            ? asset('storage/' . $this->attachment_path)
            : null;
    }

    public function isImage(): bool
    {
        return $this->type === 'image';
    }

    public function isAudio(): bool
    {
        return $this->type === 'audio';
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function reactions()
    {
        return $this->hasMany(MessageReaction::class);
    }
}
