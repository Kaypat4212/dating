<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecretMessage extends Model
{
    protected $fillable = ['sender_id', 'receiver_id', 'body', 'is_revealed', 'revealed_at'];

    protected function casts(): array
    {
        return [
            'is_revealed' => 'boolean',
            'revealed_at' => 'datetime',
        ];
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
