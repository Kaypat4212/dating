<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CompatibilityQuestion extends Model
{
    protected $fillable = ['question_text', 'category', 'weight', 'options', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['options' => 'array', 'is_active' => 'boolean'];
    }

    public function answers(): HasMany
    {
        return $this->hasMany(UserAnswer::class, 'question_id');
    }
}
