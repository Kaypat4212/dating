<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;
use App\Models\User;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $user      = $request->user();
        $isPremium = $user->isPremiumActive();

        $notifications = $user->notifications()
            ->when(
                ! $isPremium,
                fn ($q) => $q->whereRaw(
                    "JSON_UNQUOTE(JSON_EXTRACT(data, '$.type')) NOT IN ('premium_expired', 'premium_purchased')"
                )
            )
            ->latest()
            ->paginate(20);

        // Batch-load user actors referenced in notifications so premium users see real names
        // even for old notifications that pre-date storing viewer_name/liker_username.
        $actorIds = $notifications->flatMap(function ($n) {
            $ids = [];
            if (isset($n->data['liker_id']))  $ids[] = $n->data['liker_id'];
            if (isset($n->data['viewer_id'])) $ids[] = $n->data['viewer_id'];
            return $ids;
        })->unique()->filter()->values();

        $actors = $actorIds->isNotEmpty()
            ? User::whereIn('id', $actorIds)->with('primaryPhoto')->get()->keyBy('id')
            : collect();

        return view('notifications.index', compact('notifications', 'isPremium', 'actors'));
    }

    public function markRead(Request $request, DatabaseNotification $notification): RedirectResponse|JsonResponse
    {
        abort_if($notification->notifiable_id !== $request->user()->id, 403);

        $notification->markAsRead();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }

    public function markAllRead(Request $request): RedirectResponse|JsonResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        if ($request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'All notifications marked as read.');
    }

    public function unreadCount(Request $request): JsonResponse
    {
        return response()->json([
            'count' => $request->user()->unreadNotifications()->count(),
        ]);
    }
}
