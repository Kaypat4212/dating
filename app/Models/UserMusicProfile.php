<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMusicProfile extends Model
{
    protected $fillable = [
        'user_id', 'service', 'service_user_id', 'display_name',
        'access_token', 'refresh_token', 'token_expires_at',
        'top_artists', 'top_tracks', 'top_genres',
        'anthem_track_id', 'anthem_track_name', 'anthem_artist_name', 'anthem_preview_url',
        'show_on_profile', 'last_synced_at',
    ];

    protected $hidden = ['access_token', 'refresh_token'];

    protected function casts(): array
    {
        return [
            'top_artists'     => 'array',
            'top_tracks'      => 'array',
            'top_genres'      => 'array',
            'show_on_profile' => 'boolean',
            'token_expires_at' => 'datetime',
            'last_synced_at'  => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isTokenExpired(): bool
    {
        return $this->token_expires_at && $this->token_expires_at->isPast();
    }
}
