<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $icon
 * @property string|null $color
 * @property int $order
 * @property bool $is_active
 * @property bool $requires_verified
 * @property string|null $country_code
 */
class ForumCategory extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'icon', 'color',
        'order', 'is_active', 'requires_verified', 'country_code',
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
