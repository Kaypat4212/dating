<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IcebreakerAnswer extends Model
{
    protected $fillable = [
        'user_id', 'question_id', 'answer', 'choice', 'show_on_profile',
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
        return $this->belongsTo(IcebreakerQuestion::class, 'question_id');
    }
}
