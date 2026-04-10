<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $id
 * @property int $creator_id
 * @property string $name
 * @property string $slug
 * @property string|null $description
 * @property string|null $avatar
 * @property string $type
 * @property int $max_members
 * @property int $members_count
 * @property int $messages_count
 * @property bool $is_active
 * @property bool $requires_approval
 * @property array|null $interests
 * @property string|null $location
 */
class ChatRoom extends Model
{
    protected $fillable = [
        'creator_id', 'name', 'slug', 'description', 'avatar',
        'type', 'max_members', 'members_count', 'messages_count',
        'is_active', 'requires_approval', 'interests', 'location',
        'is_private', 'invite_token',
    ];

    protected function casts(): array
    {
        return [
            'interests'         => 'array',
            'is_active'         => 'boolean',
            'requires_approval' => 'boolean',
            'is_private'        => 'boolean',
        ];
    }

    /** Generate a unique invite token for this room. */
    public function generateInviteToken(): string
    {
        $token = bin2hex(random_bytes(16));
        $this->update(['invite_token' => $token]);
        return $token;
    }

    /** Whether a given user can see/join this room. */
    public function isAccessibleBy(int $userId): bool
    {
        if (! $this->is_private) return true;
        return $this->members()->where('user_id', $userId)->exists();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ChatRoomMember::class, 'room_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(ChatRoomMessage::class, 'room_id');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
