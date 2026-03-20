<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Boost extends Model
{
    protected $fillable = ['user_id', 'started_at', 'ends_at', 'active'];

    protected $casts = ['started_at' => 'datetime', 'ends_at' => 'datetime', 'active' => 'boolean'];

    public function user() { return $this->belongsTo(User::class); }

    public function isActive(): bool
    {
        return $this->active && $this->ends_at && $this->ends_at->isFuture();
    }
}
