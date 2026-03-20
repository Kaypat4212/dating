<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlaggedKeyword extends Model
{
    protected $fillable = ['word', 'severity', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function flags(): HasMany
    {
        return $this->hasMany(KeywordFlag::class, 'keyword_id');
    }

    /** Return all active words as a plain array (cached per request). */
    public static function activeWords(): array
    {
        return static::where('is_active', true)->pluck('word')->map(fn ($w) => mb_strtolower($w))->all();
    }
}
