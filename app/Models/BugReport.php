<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BugReport extends Model
{
    protected $fillable = [
        'user_id', 'title', 'description', 'category',
        'page_url', 'browser', 'status', 'admin_notes', 'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    public const CATEGORIES = [
        'snap'     => '📸 Snap / Stories',
        'call'     => '📞 Voice / Video Call',
        'chat'     => '💬 Chat / Messages',
        'profile'  => '👤 Profile',
        'match'    => '❤️ Matching',
        'payment'  => '💳 Payment / Subscription',
        'login'    => '🔐 Login / Account',
        'general'  => '🐛 General Bug',
        'other'    => '🔧 Other',
    ];

    public const STATUSES = [
        'open'        => 'Open',
        'in_progress' => 'In Progress',
        'resolved'    => 'Resolved',
        'closed'      => 'Closed',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function isResolved(): bool
    {
        return in_array($this->status, ['resolved', 'closed']);
    }
}
