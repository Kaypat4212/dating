<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfilePass extends Model
{
    protected $table = 'profile_passes';

    protected $fillable = ['passer_id', 'passed_id', 'resurfaced', 'passed_at'];

    protected function casts(): array
    {
        return ['resurfaced' => 'boolean', 'passed_at' => 'datetime'];
    }

    public function passer(): BelongsTo { return $this->belongsTo(User::class, 'passer_id'); }
    public function passed(): BelongsTo { return $this->belongsTo(User::class, 'passed_id'); }
}
