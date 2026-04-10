<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseCloudMessagingService
{
    protected string $apiKey;
    protected string $projectId;

    public function __construct()
    {
        $this->apiKey = config('services.firebase.api_key');
        $this->projectId = config('services.firebase.project_id');
    }

    /**
     * Send push notification to a specific device token
     *
     * @param string $deviceToken
     * @param string $title
     * @param string $body
     * @param array $data Additional data payload
     * @return bool
     */
    public function sendToDevice(string $deviceToken, string $title, string $body, array $data = []): bool
    {
        if (empty($this->apiKey)) {
            Log::warning('FCM: API key not configured');
            return false;
        }

        try {
            $response = Http::post("https://fcm.googleapis.com/fcm/send", [
                'to' => $deviceToken,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'icon' => '/favicon.ico',
                    'click_action' => url('/'),
                ],
                'data' => $data,
            ], [
                'Authorization' => 'key=' . $this->apiKey,
                'Content-Type' => 'application/json',
            ]);

            if ($response->successful()) {
                Log::info('FCM notification sent successfully', ['token' => substr($deviceToken, 0, 10) . '...']);
                return true;
            }

            Log::error('FCM notification failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('FCM notification exception', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Send notification to multiple devices
     *
     * @param array $deviceTokens
     * @param string $title
     * @param string $body
     * @param array $data
     * @return int Number of successful sends
     */
    public function sendToMultipleDevices(array $deviceTokens, string $title, string $body, array $data = []): int
    {
        $successCount = 0;

        foreach ($deviceTokens as $token) {
            if ($this->sendToDevice($token, $title, $body, $data)) {
                $successCount++;
            }
        }

        return $successCount;
    }

    /**
     * Send notification to a topic (for broadcast notifications)
     *
     * @param string $topic
     * @param string $title
     * @param string $body
     * @param array $data
     * @return bool
     */
    public function sendToTopic(string $topic, string $title, string $body, array $data = []): bool
    {
        if (empty($this->apiKey)) {
            Log::warning('FCM: API key not configured');
            return false;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post("https://fcm.googleapis.com/fcm/send", [
                'to' => '/topics/' . $topic,
                'notification' => [
                    'title' => $title,
                    'body' => $body,
                    'icon' => '/favicon.ico',
                ],
                'data' => $data,
            ]);

            return $response->successful();

        } catch (\Exception $e) {
            Log::error('FCM topic notification exception', [
                'topic' => $topic,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
