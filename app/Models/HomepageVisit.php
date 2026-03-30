<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HomepageVisit extends Model
{
    protected $fillable = [
        'ip_address',
        'user_id',
        'user_agent',
        'country',
        'country_code',
        'city',
        'region',
        'isp',
        'org',
        'is_proxy',
        'browser',
        'visited_at',
    ];

    protected $casts = [
        'is_proxy'   => 'boolean',
        'visited_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
