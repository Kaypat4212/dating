<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Daily.co REST API wrapper — free tier: 10,000 participant-minutes/month.
 *
 * Sign up at https://dashboard.daily.co → copy your API key and domain.
 * Set in .env:
 *   DAILY_CO_API_KEY=your_key
 *   DAILY_CO_DOMAIN=your-subdomain   (the part before .daily.co)
 */
class DailyCoService
{
    private string $apiKey;
    private string $domain;

    public function __construct()
    {
        // Use env() directly to avoid config cache issues
        $this->apiKey = env('DAILY_CO_API_KEY', '');
        $this->domain = env('DAILY_CO_DOMAIN', '');
    }

    /**
     * Create a Daily.co room and return ['url' => ..., 'name' => ...].
     *
     * @throws \Exception if Daily.co API key is not configured
     */
    public function createRoom(string $roomName, int $expireSeconds = 3600): array
    {
        if (empty($this->apiKey)) {
            throw new \Exception('Daily.co API key not configured. Please set DAILY_CO_API_KEY in .env file.');
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(10)
                ->post('https://api.daily.co/v1/rooms', [
                    'name'       => $roomName,
                    'properties' => [
                        'exp'                => time() + $expireSeconds,
                        'max_participants'   => 2,
 'enable_chat'        => false,
                        'enable_screenshare' => false,
                        'start_audio_off'    => false,
                        'start_video_off'    => true,
                    ],
                ]);

            $data = $response->json();

            if (! $response->successful() || empty($data['url'])) {
                Log::warning('DailyCoService: createRoom failed', ['body' => $data]);
                throw new \Exception('Failed to create Daily.co room: ' . ($data['error'] ?? 'Unknown error'));
            }

            return ['url' => $data['url'], 'name' => $roomName];
        } catch (\Throwable $e) {
            Log::error('DailyCoService: createRoom exception', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Create a short-lived meeting token for a participant.
     *
     * @throws \Exception if Daily.co API key is not configured
     */
    public function createToken(string $roomName, int $userId, bool $isOwner = false, int $expireSeconds = 3600): string
    {
        if (empty($this->apiKey)) {
            throw new \Exception('Daily.co API key not configured. Please set DAILY_CO_API_KEY in .env file.');
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(10)
                ->post('https://api.daily.co/v1/meeting-tokens', [
                    'properties' => [
                        'room_name'       => $roomName,
                        'user_id'         => (string) $userId,
                        'exp'             => time() + $expireSeconds,
                        'is_owner'        => $isOwner,
                        'start_audio_off' => false,
                        'start_video_off' => true,
                    ],
                ]);

            $token = $response->json('token', '');
            if (empty($token)) {
                throw new \Exception('Failed to create Daily.co token');
            }

            return $token;
        } catch (\Throwable $e) {
            Log::error('DailyCoService: createToken exception', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Delete a Daily.co room after the call ends to free up space.
     * Non-fatal — silently ignores errors.
     */
    public function deleteRoom(string $roomName): void
    {
        if (empty($this->apiKey) || empty($roomName)) return;

        try {
            Http::withToken($this->apiKey)
                ->timeout(8)
                ->delete('https://api.daily.co/v1/rooms/' . $roomName);
        } catch (\Throwable $e) {
            // Non-fatal
        }
    }

    public function isConfigured(): bool
    {
        return ! empty($this->apiKey);
    }
}
