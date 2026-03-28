<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TravelInterest extends Model
{
    protected $fillable = ['user_id', 'plan_id', 'expressed_at', 'status'];

    protected $with = ['plan'];

    protected function casts(): array
    {
        return ['expressed_at' => 'datetime'];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(TravelPlan::class, 'plan_id');
    }
}
