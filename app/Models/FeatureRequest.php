<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int|null $user_id
 * @property string $name
 * @property string $email
 * @property string $type
 * @property string $title
 * @property string $body
 * @property string $status
 * @property string|null $admin_response
 * @property \Illuminate\Support\Carbon|null $responded_at
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 */
class FeatureRequest extends Model
{
    protected $fillable = [
        'user_id', 'name', 'email', 'type', 'title', 'body',
        'status', 'admin_response', 'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'open'        => 'Open',
            'in_progress' => 'In Progress',
            'resolved'    => 'Resolved',
            'declined'    => 'Declined',
            default       => ucfirst($this->status),
        };
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'open'        => 'primary',
            'in_progress' => 'warning',
            'resolved'    => 'success',
            'declined'    => 'secondary',
            default       => 'secondary',
        };
    }
}
