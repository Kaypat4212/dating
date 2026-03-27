<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ForumCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'color',
        'order', 'is_active', 'requires_verified',
    ];

    protected function casts(): array
    {
        return [
            'is_active'         => 'boolean',
            'requires_verified' => 'boolean',
        ];
    }

    public function topics(): HasMany
    {
        return $this->hasMany(ForumTopic::class, 'category_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
