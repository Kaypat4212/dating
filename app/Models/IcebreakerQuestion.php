<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IcebreakerQuestion extends Model
{
    protected $fillable = [
        'question', 'type', 'option_a', 'option_b', 'is_active', 'order',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function answers(): HasMany
    {
        return $this->hasMany(IcebreakerAnswer::class, 'question_id');
    }
}
