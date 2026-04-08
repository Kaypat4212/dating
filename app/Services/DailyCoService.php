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
 *
 * Falls back to Jitsi Meet (https://meet.jit.si) when no API key is configured
 * so calls still work out-of-the-box without any account.
 */
class DailyCoService
{
    private string $apiKey;
    private string $domain;

    public function __construct()
    {
        $this->apiKey = config('services.dailyco.api_key', '');
        $this->domain = config('services.dailyco.domain', '');
    }

    /**
     * Create a Daily.co room and return ['url' => ..., 'name' => ...].
     *
     * If no API key is configured, falls back to a Jitsi Meet room URL
     * (completely free, no account needed).
     */
    public function createRoom(string $roomName, int $expireSeconds = 3600): array
    {
        if (empty($this->apiKey)) {
            return $this->jitsiFallback($roomName);
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
                return $this->jitsiFallback($roomName);
            }

            return ['url' => $data['url'], 'name' => $roomName];
        } catch (\Throwable $e) {
            Log::error('DailyCoService: createRoom exception', ['error' => $e->getMessage()]);
            return $this->jitsiFallback($roomName);
        }
    }

    /**
     * Create a short-lived meeting token for a participant.
     * Returns empty string when using Jitsi fallback (no token needed).
     */
    public function createToken(string $roomName, int $userId, bool $isOwner = false, int $expireSeconds = 3600): string
    {
        if (empty($this->apiKey)) {
            return '';
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

            return $response->json('token', '');
        } catch (\Throwable $e) {
            Log::error('DailyCoService: createToken exception', ['error' => $e->getMessage()]);
            return '';
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

    // ── Fallback ───────────────────────────────────────────────────────────

    private function jitsiFallback(string $roomName): array
    {
        return [
            'url'  => 'https://meet.jit.si/' . $roomName,
            'name' => $roomName,
        ];
    }
}
