<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    private string $secretKey;
    private string $publicKey;
    private string $baseUrl;

    public function __construct()
    {
        $this->secretKey = config('services.paystack.secret_key');
        $this->publicKey = config('services.paystack.public_key');
        $this->baseUrl = config('services.paystack.url');
    }

    /**
     * Initialize a payment transaction
     *
     * @param string $email User's email
     * @param float $amount Amount in Naira (will be converted to kobo)
     * @param string $reference Unique transaction reference
     * @param array $metadata Additional transaction metadata
     * @return array|null
     */
    public function initializeTransaction(string $email, float $amount, string $reference, array $metadata = []): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . '/transaction/initialize', [
                'email' => $email,
                'amount' => $amount * 100, // Convert to kobo
                'reference' => $reference,
                'callback_url' => route('paystack.callback'),
                'metadata' => $metadata,
            ]);

            if ($response->successful() && $response->json('status')) {
                return $response->json('data');
            }

            Log::error('Paystack initialization failed', [
                'response' => $response->json(),
                'status' => $response->status(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Paystack initialization exception', [
                'message' => $e->getMessage(),
                'email' => $email,
                'amount' => $amount,
            ]);
            return null;
        }
    }

    /**
     * Verify a payment transaction
     *
     * @param string $reference Transaction reference
     * @return array|null
     */
    public function verifyTransaction(string $reference): ?array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->secretKey,
            ])->get($this->baseUrl . '/transaction/verify/' . $reference);

            if ($response->successful() && $response->json('status')) {
                return $response->json('data');
            }

            Log::error('Paystack verification failed', [
                'reference' => $reference,
                'response' => $response->json(),
            ]);

            return null;
        } catch (\Exception $e) {
            Log::error('Paystack verification exception', [
                'message' => $e->getMessage(),
                'reference' => $reference,
            ]);
            return null;
        }
    }

    /**
     * Get public key for frontend integration
     *
     * @return string
     */
    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    /**
     * Generate a unique transaction reference
     *
     * @param int $userId
     * @param string $plan
     * @return string
     */
    public static function generateReference(int $userId, string $plan): string
    {
        return 'PAY_' . $userId . '_' . strtoupper($plan) . '_' . time() . '_' . uniqid();
    }

    /**
     * Convert USD to Naira (NGN)
     * You should update this with a real exchange rate API or admin setting
     *
     * @param float $usd
     * @return float
     */
    public static function convertUsdToNgn(float $usd): float
    {
        // Default exchange rate (update this based on current rates or fetch from API)
        $rate = 1600; // 1 USD = 1600 NGN (approximate, update as needed)
        return round($usd * $rate, 2);
    }
}
