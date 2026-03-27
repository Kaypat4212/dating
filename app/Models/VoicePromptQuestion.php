<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VoicePromptQuestion extends Model
{
    protected $fillable = ['prompt_text', 'is_active', 'order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function voicePrompts(): HasMany
    {
        return $this->hasMany(VoicePrompt::class, 'question_id');
    }
}
