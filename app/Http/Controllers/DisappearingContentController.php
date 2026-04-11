<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\DisappearingContent;
use App\Models\Streak;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DisappearingContentController extends Controller
{
    private const IMAGE_MIMES = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    private const VIDEO_MIMES = ['video/mp4', 'video/quicktime', 'video/webm'];
    private const IMAGE_EXT   = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    private const VIDEO_EXT   = ['mp4', 'mov', 'webm'];

    /**
     * Send disappearing content (Snapchat-style)
     */
    public function store(Request $request, Conversation $conversation): JsonResponse
    {
        $user  = $request->user();
        $match = $conversation->match;

        abort_unless(
            $match->user1_id === $user->id || $match->user2_id === $user->id,
            403,
            'You do not have access to this conversation.'
        );

        // Rate limit: 10 snaps per minute
        $key = "snaps:{$user->id}";
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json(['error' => 'Slow down! Too many snaps.'], 429);
        }
        RateLimiter::hit($key, 60);

        $request->validate([
            'media' => 'required|file|max:20480', // 20 MB max
        ]);

        $file = $request->file('media');
        $mime = $file->getMimeType() ?? '';
        $ext  = strtolower($file->getClientOriginalExtension());

        $isImage = in_array($mime, self::IMAGE_MIMES) || in_array($ext, self::IMAGE_EXT);
        $isVideo = in_array($mime, self::VIDEO_MIMES) || in_array($ext, self::VIDEO_EXT);

        if (!$isImage && !$isVideo) {
            return response()->json(['error' => 'Only images and videos are supported.'], 422);
        }

        $type     = $isImage ? 'image' : 'video';
        $dir      = "disappearing-content/{$user->id}";
        $filename = Str::uuid() . '.' . $ext;
        $path     = $file->storeAs($dir, $filename, 'public');

        $recipient = $match->getOtherUser($user->id);

        $content = DisappearingContent::create([
            'sender_id'    => $user->id,
            'recipient_id' => $recipient->id,
            'media_path'   => $path,
            'media_type'   => $type,
        ]);

        // Update streak
        Streak::recordInteraction($user->id, $recipient->id);

        // Broadcast event
        broadcast(new \App\Events\DisappearingContentSent($content))->toOthers();

        // Notify recipient
        try {
            $recipient->notify(new \App\Notifications\DisappearingContentNotification($content, $user));
        } catch (\Throwable) {}

        return response()->json([
            'success' => true,
            'content' => [
                'id'         => $content->id,
                'media_type' => $content->media_type,
                'created_at' => $content->created_at->toISOString(),
            ],
        ]);
    }

    /**
     * View disappearing content (marks as viewed and schedules deletion)
     */
    public function view(Request $request, DisappearingContent $content): JsonResponse
    {
        $user = $request->user();

        abort_unless(
            $content->recipient_id === $user->id,
            403,
            'You cannot view this content.'
        );

        if ($content->viewed) {
            return response()->json(['error' => 'Content already viewed and deleted.'], 410);
        }

        $content->markAsViewed();

        return response()->json([
            'media_url'  => $content->media_url,
            'media_type' => $content->media_type,
            'sender'     => $content->sender->name,
        ]);
    }

    /**
     * Get unviewed disappearing content for current user
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $content = DisappearingContent::where('recipient_id', $user->id)
            ->where('viewed', false)
            ->with('sender:id,name')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(fn($item) => [
                'id'         => $item->id,
                'sender'     => $item->sender->name,
                'sender_id'  => $item->sender_id,
                'media_type' => $item->media_type,
                'created_at' => $item->created_at->diffForHumans(),
            ]);

        return response()->json(['content' => $content]);
    }

    /**
     * Get streak count between current user and another user
     */
    public function streak(Request $request, int $userId): JsonResponse
    {
        $currentUser = $request->user();
        $streakCount = Streak::getStreakCount($currentUser->id, $userId);

        return response()->json(['streak' => $streakCount]);
    }
}
