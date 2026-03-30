<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string $type
 * @property string|null $breed
 * @property int|null $age_years
 * @property int|null $age_months
 * @property string|null $size
 * @property string|null $about
 * @property string|null $photo_path
 * @property bool $show_on_profile
 */
class Pet extends Model
{
    protected $fillable = [
        'user_id', 'name', 'type', 'breed',
        'age_years', 'age_months', 'size',
        'about', 'photo_path', 'show_on_profile',
    ];

    protected function casts(): array
    {
        return ['show_on_profile' => 'boolean'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getPhotoUrlAttribute(): ?string
    {
        return $this->photo_path
            ? \Illuminate\Support\Facades\Storage::url($this->photo_path)
            : null;
    }
}
