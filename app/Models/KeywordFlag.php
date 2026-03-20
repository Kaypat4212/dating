<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $message_id
 * @property int|null $keyword_id
 * @property int|null $sender_id
 * @property int|null $conversation_id
 * @property string $matched_word
 * @property bool $is_reviewed
 * @property \Illuminate\Support\Carbon|null $reviewed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 */
class KeywordFlag extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'message_id', 'keyword_id', 'sender_id', 'conversation_id',
        'matched_word', 'is_reviewed', 'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'is_reviewed' => 'boolean',
            'reviewed_at' => 'datetime',
            'created_at'  => 'datetime',
        ];
    }

    public function message(): BelongsTo
    {
        return $this->belongsTo(Message::class);
    }

    public function keyword(): BelongsTo
    {
        return $this->belongsTo(FlaggedKeyword::class, 'keyword_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }
}
