<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VoicePrompt extends Model
{
    protected $fillable = [
        'user_id', 'question_id', 'audio_path',
        'duration_seconds', 'show_on_profile', 'plays_count',
    ];

    protected function casts(): array
    {
        return ['show_on_profile' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(VoicePromptQuestion::class, 'question_id');
    }

    public function getAudioUrlAttribute(): string
    {
        return \Illuminate\Support\Facades\Storage::url($this->audio_path);
    }
}
