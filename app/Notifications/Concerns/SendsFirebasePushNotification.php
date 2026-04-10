<?php

namespace App\Notifications\Concerns;

use App\Services\FirebaseCloudMessagingService;
use Illuminate\Support\Facades\Log;

/**
 * Trait for adding Firebase Cloud Messaging support to notifications
 * 
 * Usage:
 * 1. Add `use SendsFirebasePushNotification;` to your notification class
 * 2. Call `$this->sendFCM($notifiable, $title, $body, $data)` in your notification
 */
trait SendsFirebasePushNotification
{
    /**
     * Send Firebase Cloud Messaging push notification
     *
     * @param object $notifiable The user to notify
     * @param string $title Notification title
     * @param string $body Notification body text
     * @param array $data Additional data payload (optional)
     * @return bool Success status
     */
    protected function sendFCM(object $notifiable, string $title, string $body, array $data = []): bool
    {
        // Check if user has FCM token
        if (empty($notifiable->fcm_token)) {
            Log::debug('FCM: User has no device token', ['user_id' => $notifiable->id]);
            return false;
        }

        // Check if user wants push notifications
        if (isset($notifiable->preferences) && method_exists($notifiable->preferences, 'wantsPushNotifications')) {
            if (!$notifiable->preferences->wantsPushNotifications()) {
                Log::debug('FCM: User disabled push notifications', ['user_id' => $notifiable->id]);
                return false;
            }
        }

        try {
            $fcm = app(FirebaseCloudMessagingService::class);
            
            return $fcm->sendToDevice(
                $notifiable->fcm_token,
                $title,
                $body,
                $data
            );
        } catch (\Exception $e) {
            Log::error('FCM: Failed to send notification', [
                'user_id' => $notifiable->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send FCM to multiple users
     *
     * @param array $users Array of user objects with fcm_token
     * @param string $title
     * @param string $body
     * @param array $data
     * @return int Number of successful sends
     */
    protected function sendFCMToMultiple(array $users, string $title, string $body, array $data = []): int
    {
        $tokens = collect($users)
            ->filter(fn($user) => !empty($user->fcm_token))
            ->pluck('fcm_token')
            ->toArray();

        if (empty($tokens)) {
            return 0;
        }

        try {
            $fcm = app(FirebaseCloudMessagingService::class);
            return $fcm->sendToMultipleDevices($tokens, $title, $body, $data);
        } catch (\Exception $e) {
            Log::error('FCM: Failed to send multiple notifications', [
                'count' => count($tokens),
                'error' => $e->getMessage(),
            ]);
            return 0;
        }
    }
}
