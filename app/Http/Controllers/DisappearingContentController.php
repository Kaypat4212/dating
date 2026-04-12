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

        if (!$match) {
            return response()->json(['error' => 'Conversation not found.'], 404);
        }

        abort_unless(
            $match->user1_id === $user->id || $match->user2_id === $user->id,
            403,
            'You do not have access to this conversation.'
        );

        // Rate limit: 10 snaps per minute
        $key = "snaps:{$user->id}";
        if (RateLimiter::tooManyAttempts($key, 10)) {
            \Illuminate\Support\Facades\Log::warning('Snap rate limit hit', ['user' => $user->id]);
            return response()->json(['error' => 'Slow down! Too many snaps.'], 429);
        }
        RateLimiter::hit($key, 60);

        // Validate file - no size limit, PHP ini controls max upload
        $request->validate([
            'media' => 'required|file|mimes:jpg,jpeg,png,gif,webp,mp4,mov,webm',
        ]);

        \Illuminate\Support\Facades\Log::info('Snap upload attempt', [
            'user' => $user->id,
            'conversation' => $conversation->id,
            'file_exists' => $request->hasFile('media'),
            'file_valid' => $request->file('media') ? $request->file('media')->isValid() : false,
        ]);

        $file = $request->file('media');
        
        if (!$file || !$file->isValid()) {
            \Illuminate\Support\Facades\Log::error('Invalid file upload', [
                'user' => $user->id,
                'has_file' => $request->hasFile('media'),
                'error' => $file ? $file->getErrorMessage() : 'No file',
            ]);
            return response()->json(['error' => 'Invalid file upload. Please try again.'], 400);
        }
        
        $mime = $file->getMimeType() ?? '';
        $ext  = strtolower($file->getClientOriginalExtension());
        $originalName = $file->getClientOriginalName();
        $fileSize = $file->getSize();

        \Illuminate\Support\Facades\Log::info('Snap file details', [
            'mime' => $mime,
            'ext' => $ext,
            'name' => $originalName,
            'size' => $fileSize,
        ]);

        // Fallback mime detection by extension when PHP fileinfo is unavailable
        if (empty($mime) || $mime === 'application/octet-stream') {
            $mimeMap = [
                'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
                'gif' => 'image/gif',  'webp' => 'image/webp',
                'mp4' => 'video/mp4',  'mov'  => 'video/quicktime', 'webm' => 'video/webm',
            ];
            $mime = $mimeMap[$ext] ?? $mime;
        }

        $isImage = in_array($mime, self::IMAGE_MIMES) || in_array($ext, self::IMAGE_EXT);
        $isVideo = in_array($mime, self::VIDEO_MIMES) || in_array($ext, self::VIDEO_EXT);

        if (!$isImage && !$isVideo) {
            return response()->json(['error' => 'Only images and videos are supported.'], 422);
        }

        $type     = $isImage ? 'image' : 'video';
        $dir      = "disappearing-content/{$user->id}";
        $filename = Str::uuid() . '.' . ($ext ?: ($isImage ? 'jpg' : 'mp4'));

        try {
            $path = $file->storeAs($dir, $filename, 'public');
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Snap upload failed', ['error' => $e->getMessage(), 'user' => $user->id]);
            return response()->json(['error' => 'Upload failed. Please check storage permissions.'], 500);
        }

        if (!$path) {
            \Illuminate\Support\Facades\Log::error('Snap storeAs returned false', ['user' => $user->id, 'dir' => $dir]);
            return response()->json(['error' => 'Could not save file. Please contact support.'], 500);
        }

        $recipient = $match->getOtherUser($user->id);

        try {
            $content = DisappearingContent::create([
                'sender_id'    => $user->id,
                'recipient_id' => $recipient->id,
                'media_path'   => $path,
                'media_type'   => $type,
            ]);
        } catch (\Throwable $e) {
            Storage::disk('public')->delete($path);
            \Illuminate\Support\Facades\Log::error('Snap DB create failed', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Failed to save snap. Please try again.'], 500);
        }

        // Update streak
        try {
            Streak::recordInteraction($user->id, $recipient->id);
        } catch (\Throwable) {}

        // Broadcast event
        try {
            broadcast(new \App\Events\DisappearingContentSent($content))->toOthers();
        } catch (\Throwable) {}

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
