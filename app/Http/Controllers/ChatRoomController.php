<?php

namespace App\Http\Controllers;

use App\Models\ChatRoom;
use App\Models\ChatRoomMember;
use App\Models\ChatRoomMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ChatRoomController extends Controller
{
    public function index(): View
    {
        $rooms = ChatRoom::where('is_active', true)
            ->withCount('members')
            ->with('creator')
            ->orderByDesc('messages_count')
            ->paginate(16);

        $myRooms = ChatRoom::whereHas('members', fn($q) => $q->where('user_id', Auth::id()))
            ->where('is_active', true)
            ->with('creator')
            ->limit(6)
            ->get();

        return view('chat-rooms.index', compact('rooms', 'myRooms'));
    }

    public function show(ChatRoom $chatRoom): View|\Illuminate\Http\RedirectResponse
    {
        abort_unless($chatRoom->is_active, 404);

        $user = Auth::user();

        // Location-type rooms require the user to have their location set
        if ($chatRoom->type === 'location') {
            $profile = $user->profile;
            if (!$profile || empty($profile->city)) {
                return redirect()->route('profile.edit')
                    ->with('error', 'You need to set your location in your profile before joining location-based chat rooms.');
            }
        }

        /** @var ChatRoomMember|null $member */
        $member = $chatRoom->members()->where('user_id', $user->id)->first();

        // Auto-join public rooms
        if (!$member && $chatRoom->type === 'public') {
            $chatRoom->members()->create([
                'user_id' => $user->id,
                'role'    => 'member',
            ]);
            $chatRoom->increment('members_count');
            $member = $chatRoom->members()->where('user_id', $user->id)->first();
        }

        abort_unless($member && !$member->is_banned, 403, 'You are not a member of this room.');

        $messages = $chatRoom->messages()
            ->where('is_deleted', false)
            ->with('author')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get()
            ->reverse()
            ->values();

        // Mark as read
        $member->update(['last_read_at' => now()]);

        return view('chat-rooms.show', compact('chatRoom', 'messages', 'member'));
    }

    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'name'        => ['required', 'string', 'min:3', 'max:80'],
            'description' => ['nullable', 'string', 'max:500'],
            'type'        => ['required', 'in:public,private,interest,location'],
        ]);

        $slug = Str::slug($request->name) . '-' . time();

        $room = ChatRoom::create([
            'creator_id'  => Auth::id(),
            'name'        => $request->name,
            'slug'        => $slug,
            'description' => $request->description,
            'type'        => $request->type,
        ]);

        $room->members()->create([
            'user_id' => Auth::id(),
            'role'    => 'admin',
        ]);
        $room->increment('members_count');

        return redirect()->route('chat-rooms.show', $room->slug)
            ->with('success', 'Room created!');
    }

    public function sendMessage(Request $request, ChatRoom $chatRoom)
    {
        $user   = Auth::user();
        /** @var ChatRoomMember|null $member */
        $member = $chatRoom->members()->where('user_id', $user->id)->first();
        abort_unless($member && !$member->is_banned && !$member->is_muted, 403, 'You are banned or muted in this room and cannot send messages.');

        $request->validate([
            'content' => ['required', 'string', 'max:1000'],
        ]);

        $msg = $chatRoom->messages()->create([
            'user_id' => $user->id,
            'content' => $request->input('content'),
            'type'    => 'text',
        ]);

        $chatRoom->increment('messages_count');

        if ($request->expectsJson()) {
            return response()->json([
                'id'         => $msg->id,
                'content'    => $msg->content,
                'user_name'  => $user->name,
                'created_at' => $msg->created_at->diffForHumans(),
            ]);
        }

        return back();
    }

    public function join(ChatRoom $chatRoom): \Illuminate\Http\RedirectResponse
    {
        abort_unless($chatRoom->is_active && in_array($chatRoom->type, ['public', 'location']), 403, 'This room is not open for joining.');

        // Location rooms require the user to have their location set
        if ($chatRoom->type === 'location') {
            $profile = Auth::user()->profile;
            if (!$profile || empty($profile->city)) {
                return redirect()->route('profile.edit')
                    ->with('error', 'Please set your location in your profile before joining location-based chat rooms.');
            }
        }

        $existing = $chatRoom->members()->where('user_id', Auth::id())->first();
        if (!$existing) {
            $chatRoom->members()->create(['user_id' => Auth::id(), 'role' => 'member']);
            $chatRoom->increment('members_count');
        }

        return redirect()->route('chat-rooms.show', $chatRoom->slug);
    }

    public function leave(ChatRoom $chatRoom): \Illuminate\Http\RedirectResponse
    {
        $chatRoom->members()->where('user_id', Auth::id())->delete();
        $chatRoom->decrement('members_count');

        return redirect()->route('chat-rooms.index')->with('success', 'You left the room.');
    }

    public function messages(ChatRoom $chatRoom, Request $request)
    {
        $member = $chatRoom->members()->where('user_id', Auth::id())->first();

        $after = $request->integer('after', 0);

        $messages = $chatRoom->messages()
            ->where('is_deleted', false)
            ->when($after, fn($q) => $q->where('id', '>', $after))
            ->with('author')
            ->orderBy('created_at')
            ->limit(50)
            ->get()
            ->map(fn($m) => [
                'id'         => $m->id,
                'content'    => e($m->content),
                'user_name'  => $m->author->name,
                'user_id'    => $m->user_id,
                'created_at' => $m->created_at->format('H:i'),
            ]);

        return response()->json($messages);
    }
}
