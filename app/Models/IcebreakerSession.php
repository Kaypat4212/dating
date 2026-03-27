<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IcebreakerSession extends Model
{
    protected $fillable = [
        'user_a_id', 'user_b_id', 'match_id',
        'current_question_id', 'completed',
    ];

    protected function casts(): array
    {
        return ['completed' => 'boolean'];
    }

    public function userA(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_a_id');
    }

    public function userB(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_b_id');
    }

    public function match(): BelongsTo
    {
        return $this->belongsTo(UserMatch::class, 'match_id');
    }

    public function currentQuestion(): BelongsTo
    {
        return $this->belongsTo(IcebreakerQuestion::class, 'current_question_id');
    }
}
