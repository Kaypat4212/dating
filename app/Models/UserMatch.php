<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * @property int $id
 * @property int $user1_id
 * @property int $user2_id
 * @property \Illuminate\Support\Carbon|null $matched_at
 * @property bool $is_active
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class UserMatch extends Model
{
    protected $table = 'matches';

    protected $fillable = ['user1_id', 'user2_id', 'matched_at', 'is_active'];

    protected function casts(): array
    {
        return [
            'matched_at' => 'datetime',
            'is_active'  => 'boolean',
        ];
    }

    public function user1(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user1_id');
    }

    public function user2(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user2_id');
    }

    public function conversation(): HasOne
    {
        return $this->hasOne(Conversation::class, 'match_id');
    }

    /**
     * Get the "other" user in this match relative to the given user ID.
     */
    public function getOtherUser(int $userId): User
    {
        return $this->user1_id === $userId ? $this->user2 : $this->user1;
    }
}
