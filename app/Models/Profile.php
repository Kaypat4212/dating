<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Profile extends Model
{
    protected $fillable = [
        'user_id', 'headline', 'bio', 'mood_status', 'height_cm', 'body_type',
        'ethnicity', 'religion', 'education', 'occupation',
        'relationship_goal', 'smoking', 'drinking',
        'has_children', 'wants_children',
        'latitude', 'longitude', 'city', 'state', 'country',
        'views_count', 'is_paused', 'private_photos',
        'location_updates_count',
    ];

    protected function casts(): array
    {
        return [
            'has_children'   => 'boolean',
            'latitude'       => 'float',
            'longitude'      => 'float',
            'is_paused'               => 'boolean',
            'private_photos'          => 'boolean',
            'location_updates_count'  => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function interests(): BelongsToMany
    {
        return $this->belongsToMany(Interest::class, 'profile_interest');
    }

    /**
     * Haversine distance scope in km.
     * Usage: Profile::withinKm($lat, $lng, $km)->get()
     */
    public function scopeWithinKm($query, float $lat, float $lng, int $km)
    {
        return $query->selectRaw(
            '*, ( 6371 * acos( cos( radians(?) ) * cos( radians(latitude) ) *
             cos( radians(longitude) - radians(?) ) + sin( radians(?) ) *
             sin( radians(latitude) ) ) ) AS distance_km',
            [$lat, $lng, $lat]
        )->having('distance_km', '<=', $km)
         ->orderBy('distance_km');
    }

    public function getCompletionPercentAttribute(): int
    {
        $fields = ['headline', 'bio', 'height_cm', 'body_type', 'relationship_goal'];
        $filled = collect($fields)->filter(fn($f) => !empty($this->$f))->count();
        return (int) (($filled / count($fields)) * 100);
    }
}
