<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\SiteSetting;
use App\Notifications\FeatureUsageNotification;
use App\Services\ActivityLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    private const IMAGE_MIMES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private const AUDIO_MIMES = ['audio/mpeg', 'audio/mp3', 'audio/ogg', 'audio/wav',
                                  'audio/x-wav', 'audio/mp4', 'audio/aac', 'audio/x-m4a',
                                  'audio/webm'];
    private const IMAGE_EXT   = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private const AUDIO_EXT   = ['mp3', 'ogg', 'wav', 'm4a', 'aac', 'webm'];

    public function store(Request $request, Conversation $conversation): JsonResponse
    {
        $user  = $request->user();
        $match = $conversation->match;

        abort_unless(
            $match->user1_id === $user->id || $match->user2_id === $user->id,
            403
        );

        // Rate limit: 30 messages per minute
        $key = "messages:{$user->id}";
        if (RateLimiter::tooManyAttempts($key, 30)) {
            return response()->json(['error' => 'Slow down! Too many messages.'], 429);
        }
        RateLimiter::hit($key, 60);

        // ── Attachment message ────────────────────────────────────────────────
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $mime = $file->getMimeType() ?? '';
            $ext  = strtolower($file->getClientOriginalExtension());

            $isImage = in_array($mime, self::IMAGE_MIMES) || in_array($ext, self::IMAGE_EXT);
            $isAudio = in_array($mime, self::AUDIO_MIMES) || in_array($ext, self::AUDIO_EXT);

            if (! $isImage && ! $isAudio) {
                return response()->json(['error' => 'Unsupported file type. Send images (jpg/png/gif/webp) or audio (mp3/ogg/wav/m4a/aac).'], 422);
            }

            $maxBytes = $isImage ? 10 * 1024 * 1024 : 25 * 1024 * 1024; // 10 MB / 25 MB
            if ($file->getSize() > $maxBytes) {
                $limit = $isImage ? '10 MB' : '25 MB';
                return response()->json(['error' => "File too large. Max size is {$limit}."], 422);
            }

            $type     = $isImage ? 'image' : 'audio';
            $dir      = "message-attachments/{$conversation->id}";
            $filename = Str::uuid() . '.' . $ext;
            $path     = $file->storeAs($dir, $filename, 'public');

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => $user->id,
                'body'            => '',
                'type'            => $type,
                'attachment_path' => $path,
                'attachment_name' => $file->getClientOriginalName(),
                'attachment_mime' => $mime,
            ]);

        } elseif ($request->input('type') === 'gift') {
            // ── Gift message ─────────────────────────────────────────────────
            abort_unless($user->isPremiumActive(), 403, 'Gifts require a Premium account.');

            // Allowed gift labels (must match JS GIFTS array label values)
            $allowedLabels = ['Rose', 'Heart', 'Gift Box', 'Chocolate', 'Star', 'Diamond', 'Flower', 'Love'];
            $body = $request->input('body', '');
            // Validate: body must be "{emoji} {Label}" where Label is in allowedLabels
            $parts = explode(' ', $body, 2);
            if (count($parts) < 2 || ! in_array($parts[1], $allowedLabels, true)) {
                return response()->json(['error' => 'Invalid gift selection.'], 422);
            }

            // Deduct gift credits from sender, credit recipient
            $recipient = $match->getOtherUser($user->id);
            $priceKey  = 'gift_price_' . strtolower(str_replace(' ', '_', $parts[1]));
            $price     = (int) SiteSetting::get($priceKey, 10);
            if ($user->credit_balance < $price) {
                return response()->json(['error' => "Insufficient credits. This gift costs {$price} credits."], 422);
            }
            \Illuminate\Support\Facades\DB::transaction(function () use ($user, $recipient, $price) {
                $user->decrement('credit_balance', $price);
                $recipient->increment('credit_balance', $price);
            });

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => $user->id,
                'body'            => $body,
                'type'            => 'gift',
            ]);
        } else {
            // ── Text message ──────────────────────────────────────────────────
            $request->validate(['body' => 'required|string|max:2000']);

            $message = Message::create([
                'conversation_id' => $conversation->id,
                'sender_id'       => $user->id,
                'body'            => $request->input('body'),
                'type'            => 'text',
            ]);
        }

        $message->load('sender.primaryPhoto');

        // Notify partner about new message
        $partner = $match->getOtherUser($user->id);
        try { $partner->notify(new \App\Notifications\NewMessageNotification($message, $user)); } catch (\Throwable) {}

        if (SiteSetting::get('email_feature_usage_enabled', true)) {
            try {
                $user->notify(new FeatureUsageNotification(
                    feature: 'Message Sent',
                    summary: "You sent a message to {$partner->name}.",
                    url: route('conversations.show', $conversation),
                ));
            } catch (\Throwable) {}
        }

        return response()->json([
            'message' => [
                'id'             => $message->id,
                'body'           => $message->body,
                'type'           => $message->type,
                'attachment_url' => $message->attachment_url,
                'attachment_name'=> $message->attachment_name,
                'sender_id'      => $message->sender_id,
                'created_at'     => $message->created_at,
                'is_me'          => true,
            ],
        ]);
    }

    public function typing(Request $request, Conversation $conversation): JsonResponse
    {
        $user  = $request->user();
        $match = $conversation->match;

        abort_unless(
            $match->user1_id === $user->id || $match->user2_id === $user->id,
            403
        );

        broadcast(new \App\Events\UserTyping($conversation->id, $user))->toOthers();

        return response()->json(['ok' => true]);
    }
}
