<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    protected $fillable = [
        'user_id', 'min_age', 'max_age', 'max_distance_km', 'preferred_state',
        'seeking_gender', 'body_types', 'show_online_only',
    ];

    protected function casts(): array
    {
        return [
            'body_types'      => 'array',
            'show_online_only' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
