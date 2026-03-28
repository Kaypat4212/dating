<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TravelPlan extends Model
{
    protected $fillable = [
        'user_id', 'destination', 'destination_country',
        'origin_country', 'from_city',
        'destination_lat', 'destination_lng',
        'travel_from', 'travel_to', 'travel_type',
        'description', 'interests', 'accommodation',
        'is_active', 'is_visible',
    ];

    protected function casts(): array
    {
        return [
            'interests'       => 'array',
            'travel_from'     => 'date',
            'travel_to'       => 'date',
            'is_active'       => 'boolean',
            'is_visible'      => 'boolean',
            'destination_lat' => 'float',
            'destination_lng' => 'float',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function travelInterests(): HasMany
    {
        return $this->hasMany(TravelInterest::class, 'plan_id');
    }
}
