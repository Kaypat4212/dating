<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
