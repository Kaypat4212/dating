<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpeedDatingMessage extends Model
{
    public $timestamps = false;

    protected $table = 'speed_dating_messages';

    protected $fillable = ['room_id', 'sender_id', 'body'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(SpeedDatingRoom::class, 'room_id');
    }
}
