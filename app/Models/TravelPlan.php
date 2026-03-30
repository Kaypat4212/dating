<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $user_id
 * @property string $destination
 * @property string $destination_country
 * @property string|null $origin_country
 * @property string|null $from_city
 * @property float|null $destination_lat
 * @property float|null $destination_lng
 * @property \Illuminate\Support\Carbon|null $travel_from
 * @property \Illuminate\Support\Carbon|null $travel_to
 * @property string|null $travel_type
 * @property string|null $description
 * @property array|null $interests
 * @property string|null $accommodation
 * @property bool $is_active
 * @property bool $is_visible
 */
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
