<?php

namespace App\Notifications\Concerns;

use Illuminate\Notifications\Messages\BroadcastMessage;

/**
 * Trait for adding real-time broadcast support to notifications
 * 
 * Usage in your notification class:
 * 1. use BroadcastsNotification;
 * 2. Add 'broadcast' to via() method array
 * 3. Optionally override getBroadcastPayload() to customize
 */
trait BroadcastsNotification
{
    /**
     * Get the broadcastable representation of the notification
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->getBroadcastPayload($notifiable));
    }

    /**
     * Get the data to broadcast (defaults to toArray data)
     * Override this in your notification to customize
     */
    protected function getBroadcastPayload(object $notifiable): array
    {
        $data = $this->toArray($notifiable);
        
        // Add common fields for frontend display
        return array_merge($data, [
            'notification_id' => $this->id ?? null,
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Get the type of the notification for the broadcast channel
     */
    public function broadcastType(): string
    {
        return 'notification.new';
    }
}
