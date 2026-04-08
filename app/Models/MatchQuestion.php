<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MatchQuestion extends Model
{
    protected $fillable = ['question', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function answers(): HasMany
    {
        return $this->hasMany(MatchQuestionAnswer::class, 'question_id');
    }
}
