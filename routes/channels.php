<?php

use App\Models\Conversation;
use Illuminate\Support\Facades\Broadcast;

// User-level private channel (notifications, match events)
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Notification channel used by Laravel's HasBroadcastNotifications
Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Conversation channel — only participants can listen
Broadcast::channel('conversation.{conversationId}', function ($user, int $conversationId) {
    $conversation = Conversation::find($conversationId);
    if (! $conversation) {
        return false;
    }
    return $conversation->user1_id === $user->id
        || $conversation->user2_id === $user->id;
});
