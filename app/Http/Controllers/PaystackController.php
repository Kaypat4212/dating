<?php

namespace App\Http\Controllers;

use App\Models\PremiumPayment;
use App\Models\User;
use App\Notifications\PremiumPurchasedNotification;
use App\Services\PaystackService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PaystackController extends Controller
{
    private const PLANS = [
        '30day'  => ['label' => '1 Month',  'price' => 9.99, 'days' => 30],
        '90day'  => ['label' => '3 Months', 'price' => 19.99, 'days' => 90],
        '365day' => ['label' => '1 Year',   'price' => 49.99, 'days' => 365],
    ];

    public function __construct(private readonly PaystackService $paystack) {}

    /**
     * Initialize Paystack payment
     */
    public function initialize(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'plan' => 'required|in:30day,90day,365day',
        ]);

        $user = $request->user();
        $plan = self::PLANS[$data['plan']];

        // Check for pending payments
        if (PremiumPayment::where('user_id', $user->id)->where('status', 'pending')->exists()) {
            return back()->withErrors(['payment' => 'You already have a pending payment. Please wait for it to be processed.']);
        }

        // Generate unique reference
        $reference = PaystackService::generateReference($user->id, $data['plan']);

        // Convert USD to NGN
        $amountNgn = PaystackService::convertUsdToNgn($plan['price']);

        // Create pending payment record
        $payment = PremiumPayment::create([
            'user_id' => $user->id,
            'plan' => $data['plan'],
            'amount' => $plan['price'],
            'payment_method' => 'paystack',
            'paystack_reference' => $reference,
            'status' => 'pending',
        ]);

        // Initialize Paystack transaction
        $paystackData = $this->paystack->initializeTransaction(
            $user->email,
            $amountNgn,
            $reference,
            [
                'user_id' => $user->id,
                'plan' => $data['plan'],
                'plan_label' => $plan['label'],
                'payment_id' => $payment->id,
            ]
        );

        if (!$paystackData) {
            $payment->update(['status' => 'failed', 'notes' => 'Failed to initialize Paystack transaction']);
            return back()->withErrors(['payment' => 'Unable to initialize payment. Please try again or contact support.']);
        }

        // Update payment with Paystack access code
        $payment->update([
            'paystack_access_code' => $paystackData['access_code'] ?? null,
        ]);

        // Redirect to Paystack payment page
        return redirect($paystackData['authorization_url']);
    }

    /**
     * Handle Paystack callback
     */
    public function callback(Request $request): RedirectResponse
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('premium.show')->withErrors(['payment' => 'Invalid payment reference.']);
        }

        // Find payment record
        $payment = PremiumPayment::where('paystack_reference', $reference)->first();

        if (!$payment) {
            return redirect()->route('premium.show')->withErrors(['payment' => 'Payment record not found.']);
        }

        // Verify transaction with Paystack
        $verificationData = $this->paystack->verifyTransaction($reference);

        if (!$verificationData) {
            $payment->update([
                'status' => 'failed',
                'notes' => 'Payment verification failed',
            ]);
            return redirect()->route('premium.show')->withErrors(['payment' => 'Payment verification failed. If you were charged, please contact support.']);
        }

        // Check if payment was successful
        if ($verificationData['status'] !== 'success') {
            $payment->update([
                'status' => 'failed',
                'notes' => 'Payment not successful: ' . ($verificationData['gateway_response'] ?? 'Unknown reason'),
            ]);
            return redirect()->route('premium.show')->with('error', 'Payment was not successful. Please try again.');
        }

        // Payment successful - activate premium
        DB::transaction(function () use ($payment, $verificationData) {
            $user = $payment->user;
            $plan = self::PLANS[$payment->plan];

            // Calculate expiry date
            $expiresAt = $user->isPremiumActive() && $user->premium_expires_at > now()
                ? $user->premium_expires_at->addDays($plan['days'])
                : now()->addDays($plan['days']);

            // Update user premium status
            $user->update([
                'is_premium' => true,
                'premium_plan' => $payment->plan,
                'premium_expires_at' => $expiresAt,
            ]);

            // Update payment status
            $payment->update([
                'status' => 'approved',
                'approved_at' => now(),
                'approved_by' => null, // Auto-approved by Paystack
                'notes' => 'Payment verified and approved automatically by Paystack',
            ]);

            // Send notification
            try {
                $user->notify(new PremiumPurchasedNotification($payment->plan));
            } catch (\Throwable $e) {
                Log::error('Failed to send premium purchased notification', [
                    'user_id' => $user->id,
                    'error' => $e->getMessage(),
                ]);
            }
        });

        return redirect()->route('premium.show')->with('success', '🎉 Payment successful! Your premium subscription is now active. Welcome to Premium!');
    }

    /**
     * Handle Paystack webhook
     */
    public function webhook(Request $request)
    {
        // Verify webhook signature
        $signature = $request->header('x-paystack-signature');
        $body = $request->getContent();
        
        if (!$this->verifyWebhookSignature($signature, $body)) {
            Log::warning('Invalid Paystack webhook signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $event = $request->input('event');
        $data = $request->input('data');

        // Handle different event types
        if ($event === 'charge.success') {
            $reference = $data['reference'] ?? null;
            
            if ($reference) {
                $payment = PremiumPayment::where('paystack_reference', $reference)
                    ->where('status', 'pending')
                    ->first();

                if ($payment && $data['status'] === 'success') {
                    DB::transaction(function () use ($payment, $data) {
                        $user = $payment->user;
                        $plan = self::PLANS[$payment->plan];

                        $expiresAt = $user->isPremiumActive() && $user->premium_expires_at > now()
                            ? $user->premium_expires_at->addDays($plan['days'])
                            : now()->addDays($plan['days']);

                        $user->update([
                            'is_premium' => true,
                            'premium_plan' => $payment->plan,
                            'premium_expires_at' => $expiresAt,
                        ]);

                        $payment->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                            'notes' => 'Auto-approved via Paystack webhook',
                        ]);

                        try {
                            $user->notify(new PremiumPurchasedNotification($payment->plan));
                        } catch (\Throwable) {}
                    });
                }
            }
        }

        return response()->json(['status' => 'success']);
    }

    /**
     * Verify webhook signature
     */
    private function verifyWebhookSignature(?string $signature, string $body): bool
    {
        if (!$signature) {
            return false;
        }

        $secretKey = config('services.paystack.secret_key');
        $hash = hash_hmac('sha512', $body, $secretKey);

        return hash_equals($hash, $signature);
    }
}
