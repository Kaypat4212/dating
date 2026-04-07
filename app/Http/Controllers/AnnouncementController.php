<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\AnnouncementRead;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    /**
     * Full "What's New" page — returns all published announcements
     * with read status for the authenticated user.
     */
    public function index(Request $request): \Illuminate\View\View
    {
        $user = $request->user();

        $announcements = Announcement::published()
            ->forUser($user->id)
            ->with(['reads' => fn ($q) => $q->where('user_id', $user->id)])
            ->latest('published_at')
            ->get()
            ->map(fn ($a) => $this->formatAnnouncement($a, $user->id));

        // Mark all as read when user visits the full page
        $this->markAllRead($user->id);

        return view('announcements.index', compact('announcements'));
    }

    /**
     * JSON payload for the "What's New" modal —
     * returns only unread announcements (or recent 10 if all read).
     */
    public function unread(Request $request): JsonResponse
    {
        $user = $request->user();

        $readIds = AnnouncementRead::where('user_id', $user->id)
            ->pluck('announcement_id');

        $unread = Announcement::published()
            ->forUser($user->id)
            ->whereNotIn('id', $readIds)
            ->latest('published_at')
            ->get()
            ->map(fn ($a) => $this->formatAnnouncement($a, $user->id));

        return response()->json([
            'unread_count' => $unread->count(),
            'items'        => $unread,
        ]);
    }

    /**
     * Mark a single announcement as read.
     */
    public function markRead(Request $request, Announcement $announcement): JsonResponse
    {
        $user = $request->user();

        AnnouncementRead::firstOrCreate([
            'user_id'         => $user->id,
            'announcement_id' => $announcement->id,
        ], [
            'read_at' => now(),
        ]);

        $remaining = Announcement::published()
            ->forUser($user->id)
            ->whereNotIn('id', AnnouncementRead::where('user_id', $user->id)->pluck('announcement_id'))
            ->count();

        return response()->json(['success' => true, 'remaining_unread' => $remaining]);
    }

    /**
     * Mark ALL announcements as read.
     */
    public function markAllRead(int $userId): void
    {
        $published = Announcement::published()->forUser($userId)->pluck('id');
        $alreadyRead = AnnouncementRead::where('user_id', $userId)->pluck('announcement_id');
        $toMark = $published->diff($alreadyRead);

        foreach ($toMark as $announcementId) {
            AnnouncementRead::firstOrCreate(
                ['user_id' => $userId, 'announcement_id' => $announcementId],
                ['read_at' => now()]
            );
        }
    }

    /**
     * HTTP endpoint to mark all as read (called from the modal "Mark all read" button).
     */
    public function readAll(Request $request): JsonResponse
    {
        $this->markAllRead($request->user()->id);
        return response()->json(['success' => true]);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function formatAnnouncement(Announcement $a, int $userId): array
    {
        return [
            'id'          => $a->id,
            'title'       => $a->title,
            'body'        => $a->body,
            'type'        => $a->type,
            'type_icon'   => $a->typeIcon(),
            'type_color'  => $a->typeColor(),
            'version'     => $a->version,
            'badge_label' => $a->badge_label,
            'badge_color' => $a->badge_color,
            'published_at'=> $a->published_at?->diffForHumans(),
            'is_read'     => $a->reads->isNotEmpty(),
        ];
    }
}
