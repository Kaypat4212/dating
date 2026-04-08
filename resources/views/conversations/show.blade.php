@extends('layouts.app')
@section('title', 'Chat with ' . $other->name)

@push('head')
<style>
main { padding-bottom: 0 !important; }
/* ── Gift bubble ────────────────────────────────────────── */
.gift-bubble {
    background: linear-gradient(135deg,#fff0f6,#ffe4ef);
    border-radius: 16px;
    border: 1px solid rgba(194,24,91,.2);
    min-width: 120px;
    animation: giftPop .35s cubic-bezier(.34,1.56,.64,1) both;
}
@keyframes giftPop { from { transform:scale(.6); opacity:0; } to { transform:scale(1); opacity:1; } }
/* ── Gift popover ───────────────────────────────────────── */
.chat-gift-popover {
    position: absolute;
    bottom: calc(100% + 8px);
    left: 50%;
    transform: translateX(-50%);
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 16px;
    padding: 12px;
    width: 220px;
    box-shadow: 0 4px 20px rgba(0,0,0,.15);
    z-index: 200;
}
.gift-popover-title {
    font-size: .75rem;
    font-weight: 600;
    color: #888;
    text-transform: uppercase;
    letter-spacing: .05em;
    margin-bottom: 10px;
    text-align: center;
}
.gift-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
}
.gift-choice {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: #f8f8f8;
    border: none;
    border-radius: 12px;
    padding: 8px 4px;
    cursor: pointer;
    transition: background .15s;
    line-height: 1;
}
.gift-choice:hover { background: #fce4ec; }
.gift-emoji { font-size: 1.4rem; }
.gift-label { font-size: .6rem; color: #888; margin-top: 3px; }
/* ── Matches Stories (Instagram-style) ──────────────────────── */
.matches-stories-container {
    background: #fff;
    border-bottom: 1px solid var(--bs-border-color);
    padding: 12px 8px;
    overflow-x: auto;
    overflow-y: hidden;
    white-space: nowrap;
    scrollbar-width: none;
    -ms-overflow-style: none;
}
.matches-stories-container::-webkit-scrollbar { display: none; }
.matches-stories {
    display: inline-flex;
    gap: 12px;
    padding: 0 4px;
}
.story-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    text-decoration: none;
}
.story-avatar-ring {
    width: 66px;
    height: 66px;
    border-radius: 50%;
    background: linear-gradient(135deg, #f43f5e, #ec4899, #a855f7);
    padding: 3px;
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    transition: transform .2s;
}
.story-item:hover .story-avatar-ring { transform: scale(1.05); }
.story-avatar {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 3px solid #fff;
    object-fit: cover;
    background: linear-gradient(135deg, #7c3aed, #a855f7);
}
.story-avatar-ph {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 3px solid #fff;
    background: linear-gradient(135deg, #7c3aed, #a855f7);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 700;
    font-size: 1.3rem;
}
.story-online-dot {
    position: absolute;
    bottom: 2px;
    right: 2px;
    width: 16px;
    height: 16px;
    background: #10b981;
    border: 3px solid #fff;
    border-radius: 50%;
}
.story-name {
    font-size: 0.7rem;
    color: #666;
    max-width: 66px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
/* ── Redesigned 2-row chat footer ──────────────────────────── */
.chat-footer {
    flex-direction: column !important;
    align-items: stretch !important;
    gap: 0 !important;
    padding: 0 !important;
}
/* Row 1 – action icon strip */
.chat-actions-bar {
    display: flex;
    align-items: center;
    gap: .2rem;
    padding: .38rem .8rem;
    border-bottom: 1px solid var(--bs-border-color);
}
.chat-act-btn {
    width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    padding: 0; border: none;
    background: transparent; color: var(--bs-secondary-color);
    font-size: 1rem; cursor: pointer;
    transition: background .15s, color .15s;
}
.chat-act-btn:hover,
.chat-act-btn:focus { background: var(--bs-secondary-bg); color: var(--bs-body-color); outline: none; }
.chat-act-tip { color: #f59e0b !important; }
.chat-act-tip:hover { background: rgba(245,158,11,.12) !important; color: #d97706 !important; }
/* Row 2 – textarea + send */
.chat-input-row {
    display: flex;
    align-items: flex-end;
    gap: .45rem;
    padding: .5rem .75rem .6rem;
}
.chat-textarea {
    min-height: 52px !important;
    max-height: 160px !important;
    font-size: .95rem !important;
    line-height: 1.5 !important;
}
.chat-send-btn {
    width: 44px !important; height: 44px !important;
    border-radius: 50% !important; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center; padding: 0 !important;
}
.chat-ai-popover {
    position: absolute;
    bottom: calc(100% + 8px);
    left: 50%;
    transform: translateX(-50%);
    background: #fff;
    border: 1px solid #f3c0d5;
    border-radius: 16px;
    padding: 10px;
    width: 185px;
    box-shadow: 0 4px 20px rgba(0,0,0,.15);
    z-index: 200;
}
.ai-option-btn {
    display: block;
    width: 100%;
    background: none;
    border: none;
    text-align: left;
    padding: 8px 10px;
    border-radius: 10px;
    font-size: .85rem;
    cursor: pointer;
    transition: background .15s;
}
.ai-option-btn:hover { background: #fce4ec; }
.ai-suggestion-card {
    background: #fff9fb;
    border: 1px solid #f3c0d5;
    border-radius: 12px;
    padding: 10px 14px;
    margin-bottom: 8px;
    font-size: .9rem;
    cursor: pointer;
    transition: background .15s;
    position: relative;
}
.ai-suggestion-card:hover { background: #fce4ec; }
.ai-suggestion-card .ai-use-btn {
    display: block;
    margin-top: 6px;
    font-size: .75rem;
    color: #c2185b;
    font-weight: 600;
}
/* ── Travel buddy banner ───────────────────────────────── */
.travel-match-banner {
    background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 50%, #0369a1 100%);
    color: #fff;
    padding: 10px 14px;
    font-size: .85rem;
    border-bottom: 2px solid rgba(255,255,255,.2);
    flex-shrink: 0;
}
.travel-match-banner a { color: #bae6fd; text-decoration: underline; }
.travel-match-banner .travel-banner-row1 {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    margin-bottom: 4px;
}
.travel-match-banner .travel-banner-cta {
    font-size: .78rem;
    opacity: .9;
    line-height: 1.4;
}

/* ── Call event banners (inline in chat) ─────────────────── */
.call-event-row {
    display: flex;
    justify-content: center;
    margin: .5rem 0;
}
.call-event {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    font-size: .78rem;
    font-weight: 500;
    padding: .35rem .85rem;
    border-radius: 20px;
    max-width: 92%;
}
.call-event.missed {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fecaca;
}
.call-event.missed.outgoing {
    background: #fef3c7;
    color: #92400e;
    border-color: #fde68a;
}
.call-event.rejected {
    background: #fef3c7;
    color: #92400e;
    border: 1px solid #fde68a;
}
.call-event.ended {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #a7f3d0;
}
[data-bs-theme="dark"] .call-event.missed    { background: #7f1d1d; color: #fca5a5; border-color: #991b1b; }
[data-bs-theme="dark"] .call-event.missed.outgoing { background: #78350f; color: #fde68a; border-color: #92400e; }
[data-bs-theme="dark"] .call-event.rejected  { background: #78350f; color: #fde68a; border-color: #92400e; }
[data-bs-theme="dark"] .call-event.ended     { background: #064e3b; color: #6ee7b7; border-color: #065f46; }
.call-event-time     { font-size: .7rem; opacity: .7; margin-left: .2rem; }
.call-event-duration { font-weight: 700; margin-left: .1rem; }
</style>
@endpush

@section('content')
@php
    /** @var \App\Models\User $me */
    $me           = auth()->user();
    /** @var \App\Models\User $other */
    $visibleLastSeen = $other->visibleLastSeenTo($me);
    $isOnline        = $visibleLastSeen && $visibleLastSeen->gt(now()->subMinutes(10));
@endphp

<div class="chat-page">

    {{-- -- Header ------------------------------------------------------------ --}}
    <div class="chat-header">
        <a href="{{ route('conversations.index') }}"
           class="chat-footer-btn flex-shrink-0"
           style="text-decoration:none;display:flex;align-items:center;justify-content:center">
            <i class="bi bi-arrow-left"></i>
        </a>

        <a href="{{ $other->username ? route('profile.show', $other->username) : '#' }}"
           class="d-flex align-items-center gap-2 text-decoration-none text-reset flex-grow-1 overflow-hidden">
            @if($other->primaryPhoto)
                <img src="{{ $other->primaryPhoto->thumbnail_url }}"
                     class="chat-header-avatar" alt="{{ $other->name }}">
            @else
                <div class="chat-header-avatar-ph">{{ strtoupper(mb_substr($other->name, 0, 1)) }}</div>
            @endif
            <div class="overflow-hidden">
                <div class="fw-semibold lh-1 mb-1 text-truncate">
                    {{ $other->name }}
                    @if($other->is_verified ?? false)
                        <i class="bi bi-patch-check-fill text-info ms-1" style="font-size:.8rem" title="Verified"></i>
                    @endif
                </div>
                <div id="chatStatusLine" class="chat-status-line {{ $isOnline ? 'chat-status-online' : '' }}">
                    @if($isOnline)
                        <span class="d-inline-block rounded-circle bg-success me-1"
                              style="width:7px;height:7px;vertical-align:middle"></span>Online now
                    @elseif($visibleLastSeen)
                        Active {{ $visibleLastSeen->diffForHumans() }}
                    @else
                        {{-- last seen hidden or never set --}}
                        Tap to view profile
                    @endif
                </div>
                <div id="typingStatusLine" class="chat-status-line d-none" style="color:#c2185b">
                    <i class="bi bi-three-dots"></i> typing�
                </div>
            </div>
        </a>

        <div class="dropdown flex-shrink-0">
            {{-- Voice call button (hidden when feature is disabled by admin) --}}
            @if(filter_var(\App\Models\SiteSetting::get('voice_calls_enabled', '1'), FILTER_VALIDATE_BOOLEAN))
            <button id="callBtn"
                    class="chat-footer-btn me-1"
                    style="display:flex;align-items:center;justify-content:center;background:none;border:none;color:#10b981;"
                    title="Voice call"
                    onclick="voiceCall.initiate()">
                <i class="bi bi-telephone-fill"></i>
            </button>
            @endif
            <button class="chat-footer-btn"
                    style="display:flex;align-items:center;justify-content:center;background:none;border:none;"
                    data-bs-toggle="dropdown" aria-expanded="false" title="More options">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                @if($other->username)
                <li>
                    <a class="dropdown-item" href="{{ route('profile.show', $other->username) }}">
                        <i class="bi bi-person me-2"></i>View Profile
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                @endif
                <li>
                    <button type="button" class="dropdown-item text-danger"
                            data-bs-toggle="modal" data-bs-target="#unmatchModal">
                        <i class="bi bi-heartbreak me-2"></i>Unmatch
                    </button>
                </li>
            </ul>
        </div>
    </div>

    {{-- -- Matches Stories (Instagram-style) --------------------------------- --}}
    @php
        // Get all user's matches with primary photos
        $allMatches = \App\Models\UserMatch::where(function($q) use ($me) {
            $q->where('user1_id', $me->id)->orWhere('user2_id', $me->id);
        })->where('is_active', true)
          ->with(['user1.primaryPhoto', 'user2.primaryPhoto'])
          ->latest('matched_at')
          ->limit(20)
          ->get()
          ->map(function($match) use ($me) {
              $matchUser = $match->user1_id === $me->id ? $match->user2 : $match->user1;
              $isOnline = $matchUser->last_active_at && $matchUser->last_active_at->gt(now()->subMinutes(10));
              return (object)[
                  'user' => $matchUser,
                  'isOnline' => $isOnline,
                  'conversationId' => $match->conversation?->id,
              ];
          })
          ->filter(fn($item) => $item->user && $item->conversationId);
    @endphp

    @if($allMatches->isNotEmpty())
    <div class="matches-stories-container">
        <div class="matches-stories">
            @foreach($allMatches as $matchItem)
                @php
                    $matchUser = $matchItem->user;
                    $isCurrent = $matchUser->id === $other->id;
                @endphp
                <a href="{{ route('conversations.show', $matchItem->conversationId) }}" 
                   class="story-item {{ $isCurrent ? 'opacity-50' : '' }}">
                    <div class="story-avatar-ring">
                        @if($matchUser->primaryPhoto)
                            <img src="{{ $matchUser->primaryPhoto->thumbnail_url }}" 
                                 alt="{{ $matchUser->name }}" 
                                 class="story-avatar">
                        @else
                            <div class="story-avatar-ph">
                                {{ strtoupper(substr($matchUser->name, 0, 1)) }}
                            </div>
                        @endif
                        @if($matchItem->isOnline)
                            <div class="story-online-dot"></div>
                        @endif
                    </div>
                    <span class="story-name">{{ $matchUser->name }}</span>
                </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Travel buddy banner ─────────────────────────────────────────────── --}}
    @if($match->travelPlan && $match->is_active)
    @php $plan = $match->travelPlan; @endphp
    <div class="travel-match-banner">
        <div class="travel-banner-row1">
            <span style="font-size:1.1rem">✈️</span>
            <span>Travel Buddy Match — {{ $plan->destination }}, {{ $plan->destination_country }}</span>
            <a href="{{ route('travel.index') }}" class="ms-auto" style="font-size:.75rem;white-space:nowrap">View plan</a>
        </div>
        <div class="travel-banner-cta">
            📅 {{ $plan->travel_from->format('M j') }} – {{ $plan->travel_to->format('M j, Y') }}
            @if($plan->travel_type)
            &nbsp;·&nbsp; {{ ucfirst(str_replace('_', ' ', $plan->travel_type)) }}
            @endif
            &nbsp;· Start discussing your trip — accommodation, activities, meeting points and more!
        </div>
    </div>
    @endif

    {{-- -- Messages ---------------------------------------------------------- --}}
    <div class="chat-body-scroll" id="chatBody">
        @php
            $prevDate = null;

            // Build a merged timeline: messages + call events, sorted by time
            $timeline = collect();
            foreach ($messages->values() as $msg) {
                $timeline->push((object)['type' => 'message', 'ts' => $msg->created_at, 'item' => $msg]);
            }
            foreach ($voiceCalls as $vc) {
                // Only show ended/missed/rejected calls in the timeline
                if (in_array($vc->status, ['ended', 'missed', 'rejected'])) {
                    $timeline->push((object)['type' => 'call', 'ts' => $vc->created_at, 'item' => $vc]);
                }
            }
            $timeline = $timeline->sortBy('ts')->values();
            $msgs     = $timeline->filter(fn($t) => $t->type === 'message')->pluck('item')->values();
            $total    = $msgs->count();
            $msgIdx   = 0; // separate index for bubble grouping (only messages)
        @endphp

        @foreach($timeline as $entry)
        @php $date = $entry->ts->toDateString(); @endphp

        {{-- Date separator --}}
        @if($date !== $prevDate)
            @php $prevDate = $date; @endphp
            <div class="date-sep">
                {{ $entry->ts->isToday() ? 'Today' : ($entry->ts->isYesterday() ? 'Yesterday' : $entry->ts->format('M j, Y')) }}
            </div>
        @endif

        @if($entry->type === 'call')
        {{-- ── Call event banner ─────────────────────────────────────────── --}}
        @php
            $vc       = $entry->item;
            $isCaller = $vc->caller_id === $me->id;
            $vcOther  = $isCaller ? $vc->callee : $vc->caller;
            $dur      = $vc->durationSeconds();
        @endphp
        <div class="call-event-row">
            @if($vc->status === 'missed')
                @if($vc->callee_id === $me->id)
                    {{-- I missed a call from them --}}
                    <div class="call-event missed">
                        <i class="bi bi-telephone-missed-fill"></i>
                        <span>Missed call from <strong>{{ $vc->caller->name ?? 'them' }}</strong></span>
                        <span class="call-event-time">{{ $vc->created_at->format('g:i A') }}</span>
                    </div>
                @else
                    {{-- They didn't answer my call --}}
                    <div class="call-event missed outgoing">
                        <i class="bi bi-telephone-missed-fill"></i>
                        <span>{{ $vc->callee->name ?? 'They' }} didn't answer</span>
                        <span class="call-event-time">{{ $vc->created_at->format('g:i A') }}</span>
                    </div>
                @endif
            @elseif($vc->status === 'rejected')
                <div class="call-event rejected">
                    <i class="bi bi-telephone-x-fill"></i>
                    <span>{{ $isCaller ? ($vc->callee->name ?? 'They').' declined the call' : 'You declined the call' }}</span>
                    <span class="call-event-time">{{ $vc->created_at->format('g:i A') }}</span>
                </div>
            @elseif($vc->status === 'ended')
                <div class="call-event ended">
                    <i class="bi bi-telephone-fill"></i>
                    <span>Voice call</span>
                    @if($dur)
                        @php $m = floor($dur/60); $s = $dur % 60; @endphp
                        <span class="call-event-duration">{{ $m > 0 ? $m.'m ' : '' }}{{ str_pad($s,2,'0',STR_PAD_LEFT) }}s</span>
                    @endif
                    <span class="call-event-time">{{ $vc->created_at->format('g:i A') }}</span>
                </div>
            @endif
        </div>

        @else
        {{-- ── Regular message bubble ────────────────────────────────────── --}}
        @php
            $msg     = $entry->item;
            $isMe    = $msg->sender_id === $me->id;
            $isFirst = ($msgIdx === 0 || $msgs[$msgIdx-1]->sender_id !== $msg->sender_id);
            $isLast  = ($msgIdx === $total-1 || $msgs[$msgIdx+1]->sender_id !== $msg->sender_id);
            $reactions = $msg->reactions ?? collect();
            $msgIdx++;
        @endphp

        <div class="msg-row {{ $isMe ? 'me' : 'them' }} {{ $isFirst ? 'group-start' : '' }}">

            @if(!$isMe)
            <div class="msg-avatar-cell {{ $isLast ? '' : 'hidden' }}">
                @if($msg->sender?->primaryPhoto)
                    <img src="{{ $msg->sender->primaryPhoto->thumbnail_url }}" alt="{{ $msg->sender->name ?? '' }}">
                @else
                    <div class="av-ph">{{ strtoupper(mb_substr($msg->sender->name ?? '?', 0, 1)) }}</div>
                @endif
            </div>
            @endif

            <div class="message-bubble {{ $isMe ? 'me' : 'them' }} {{ !$isFirst ? 'not-first' : '' }} {{ !$isLast ? 'not-last' : '' }} position-relative {{ $msg->isImage() || $msg->isAudio() ? 'media-bubble' : '' }}"
                 data-msg-id="{{ $msg->id }}">

                @if($msg->isImage())
                    {{-- Image attachment --}}
                    <a href="{{ $msg->attachment_url }}" target="_blank" class="msg-img-link">
                        <img src="{{ $msg->attachment_url }}"
                             alt="{{ $msg->attachment_name ?? 'Image' }}"
                             class="msg-image"
                             loading="lazy">
                    </a>
                @elseif($msg->isAudio())
                    {{-- Audio attachment --}}
                    <div class="msg-audio">
                        <i class="bi bi-music-note-beamed me-2 text-primary"></i>
                        <audio controls preload="none" class="msg-audio-player">
                            <source src="{{ $msg->attachment_url }}" type="{{ $msg->attachment_mime ?? 'audio/mpeg' }}">
                        </audio>
                        <div class="msg-audio-name text-truncate small mt-1 text-muted">{{ $msg->attachment_name }}</div>
                    </div>
                @elseif($msg->type === 'gift')
                    {{-- Virtual gift --}}
                    <div class="gift-bubble text-center px-3 py-2">
                        <div style="font-size:2.5rem;line-height:1">{{ explode(' ', $msg->body)[0] }}</div>
                        <div class="small fw-semibold mt-1" style="color:#c2185b">{{ implode(' ', array_slice(explode(' ', $msg->body), 1)) }}</div>
                        <div class="tiny text-muted" style="font-size:.65rem">Virtual gift 🎁</div>
                    </div>
                @else
                    {{-- Text --}}
                    {{ $msg->body }}
                @endif

                <div class="message-meta d-flex align-items-center gap-1">
                    {{ $msg->created_at->format('g:i A') }}
                    @if($isMe)
                        @if($msg->read_at && $me->isPremiumActive() && $other->read_receipts_enabled !== false)
                            <i class="bi bi-check2-all text-primary read-receipt-icon"
                               data-msg-id="{{ $msg->id }}"
                               title="Seen {{ $msg->read_at->format('g:i A') }}"></i>
                        @elseif($msg->read_at)
                            <i class="bi bi-check2-all text-muted read-receipt-icon"
                               data-msg-id="{{ $msg->id }}" title="Delivered"></i>
                        @else
                            <i class="bi bi-check2 text-muted read-receipt-icon"
                               data-msg-id="{{ $msg->id }}" title="Sent"></i>
                        @endif
                    @endif
                </div>

                @if($msg->expires_at)
                <div class="disappear-countdown text-muted d-flex align-items-center gap-1"
                     style="font-size:.65rem;margin-top:2px;"
                     data-expires="{{ $msg->expires_at->toISOString() }}">
                    🔥 <span class="disappear-remaining"></span>
                </div>
                @endif

                @if($reactions->isNotEmpty())
                <div class="msg-reactions">
                    @foreach($reactions->groupBy('emoji') as $emoji => $group)
                        <span class="reaction-badge">{{ $emoji }}{{ $group->count() > 1 ? ' '.$group->count() : '' }}</span>
                    @endforeach
                </div>
                @endif

                @if($msg->type === 'text')
                <div class="reaction-picker-trigger" title="React">+</div>
                <div class="reaction-picker d-none" data-msg="{{ $msg->id }}"></div>
                @endif
            </div>

            @if($isMe)
            <div class="msg-avatar-cell" style="visibility:hidden"></div>
            @endif

        </div>
        @endif {{-- end call/message branch --}}
        @endforeach

        {{-- Typing indicator --}}
        <div id="typingRow" class="msg-row them group-start d-none">
            <div class="msg-avatar-cell">
                @if($other->primaryPhoto)
                    <img src="{{ $other->primaryPhoto->thumbnail_url }}" alt="{{ $other->name }}">
                @else
                    <div class="av-ph">{{ strtoupper(mb_substr($other->name, 0, 1)) }}</div>
                @endif
            </div>
            <div class="typing-bubble">
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
                <div class="typing-dot"></div>
            </div>
        </div>
    </div>

    {{-- -- Attachment preview bar -------------------------------------------- --}}
    <div id="attachPreviewBar" class="chat-attach-preview d-none">
        <div id="attachPreviewInner"></div>
        <button type="button" id="attachCancel" class="btn btn-sm btn-outline-secondary ms-2">
            <i class="bi bi-x-lg"></i>
        </button>
    </div>

    {{-- -- Footer / Input --------------------------------------------------- --}}
    <div class="chat-footer">
        {{-- Hidden file input --}}
        <input type="file" id="attachInput"
               accept="image/jpeg,image/png,image/gif,image/webp,audio/mpeg,audio/ogg,audio/wav,audio/mp4,audio/aac,audio/x-m4a,audio/webm"
               class="d-none">

        {{-- ── Row 1: action icon strip ─────────────────────────────── --}}
        <div class="chat-actions-bar">

            {{-- Attachment --}}
            <button type="button" class="chat-act-btn" id="attachBtn" title="Send image or audio">
                <i class="bi bi-paperclip"></i>
            </button>

            @if(auth()->user()->isPremiumActive())
            {{-- Virtual gift (Premium) --}}
            <div class="position-relative">
                <button type="button" class="chat-act-btn" id="giftBtn" title="Send a virtual gift">
                    <i class="bi bi-gift"></i>
                </button>
                <div id="giftPopover" class="chat-gift-popover d-none">
                    <div class="gift-popover-title">Send a gift</div>
                    <div class="gift-grid" id="giftGrid"></div>
                </div>
            </div>
            @else
            {{-- Placeholder so layout is consistent for non-premium --}}
            <div id="giftPopover" class="d-none"><div id="giftGrid"></div></div>
            @endif

            {{-- Tip credits --}}
            <button type="button" class="chat-act-btn chat-act-tip" id="chatTipBtn" title="Send a credit tip">
                <i class="bi bi-coin"></i>
            </button>

            {{-- Emoji --}}
            <div class="position-relative">
                <button type="button" class="chat-act-btn" id="emojiBtn" title="Emoji">
                    <i class="bi bi-emoji-smile"></i>
                </button>
                <div id="emojiPopover" class="chat-emoji-popover d-none"></div>
            </div>

            {{-- AI Assistant ✨ --}}
            <div class="position-relative">
                <button type="button" class="chat-act-btn" id="aiBtn" title="AI Writing Helper">✨</button>
                <div id="aiPopover" class="chat-ai-popover d-none">
                    <div class="gift-popover-title">AI Writing Helper</div>
                    <button type="button" class="ai-option-btn" data-ai-type="reply">💬 Suggest a reply</button>
                    <button type="button" class="ai-option-btn" data-ai-type="topics">🎯 Topic ideas</button>
                    <button type="button" class="ai-option-btn" data-ai-type="icebreaker">👋 Icebreaker</button>
                </div>
            </div>

            {{-- Rephrase with AI --}}
            <div class="ms-auto d-flex align-items-center gap-1">

                {{-- ⏱️ Disappearing timer toggle --}}
                <div class="position-relative">
                    <button type="button" class="chat-act-btn" id="disappearBtn"
                            title="Disappearing messages ({{ $disappearAfter === 'off' ? 'Off' : $disappearAfter }})"
                            style="{{ $disappearAfter !== 'off' ? 'color:#f97316' : '' }}">
                        <i class="bi bi-hourglass-split"></i>
                        @if($disappearAfter !== 'off')
                            <span style="font-size:.55rem;font-weight:700;position:absolute;bottom:0;right:0;background:#f97316;color:#fff;border-radius:4px;padding:0 2px;line-height:1.4">{{ $disappearAfter }}</span>
                        @endif
                    </button>
                    <div id="disappearPopover" class="chat-gift-popover d-none" style="width:150px;padding:8px;">
                        <div class="gift-popover-title mb-2">Auto-delete after</div>
                        @foreach(['off' => 'Off', '1h' => '1 Hour', '24h' => '24 Hours', '7d' => '7 Days'] as $val => $label)
                        <button type="button" class="d-block w-100 text-start btn btn-sm mb-1 disappear-opt {{ $disappearAfter === $val ? 'btn-warning' : 'btn-outline-secondary' }}"
                                data-mode="{{ $val }}">
                            {{ $label }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <button id="aiRephraseBtn" class="chat-act-btn" type="button" title="Rewrite with AI">
                    <span class="d-none spinner-border spinner-border-sm text-danger" id="aiRephraseSpinner" style="width:.8rem;height:.8rem"></span>
                    <i class="bi bi-pencil-square"></i>
                </button>
            </div>
        </div>

        {{-- ── Row 2: textarea + send ──────────────────────────────────── --}}
        <div class="chat-input-row">
            <textarea id="msgInput"
                      class="form-control chat-textarea"
                      placeholder="Type a message…"
                      rows="2"
                      autocomplete="off"
                      style="resize:none;"></textarea>
            <button id="btnSend" class="btn btn-primary chat-send-btn" title="Send (Enter)">
                <i class="bi bi-send-fill"></i>
            </button>
        </div>
    </div>

    {{-- ── Unmatch Confirm Modal ─────────────────────────────────────────── --}}
    <div class="modal fade" id="unmatchModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title text-danger"><i class="bi bi-heartbreak me-2"></i>Unmatch {{ $other->name }}?</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body pt-2">
            <p class="text-muted small mb-0">This will end your match and you won't be able to message each other anymore. This action cannot be undone.</p>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            <form method="POST" action="{{ route('matches.unmatch', $match->id) }}" class="d-inline">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-danger btn-sm">
                <i class="bi bi-heartbreak me-1"></i>Yes, Unmatch
              </button>
            </form>
          </div>
        </div>
      </div>
    </div>

    {{-- ── Tip Modal ─────────────────────────────────────────────────────── --}}
    <div class="modal fade" id="chatTipModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title"><i class="bi bi-coin text-warning me-2"></i>Send a Tip to {{ $other->name }}</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <form id="chatTipForm">
            @csrf
            <div class="modal-body">
              <div class="d-flex align-items-center gap-2 mb-3 p-2 rounded-3" style="background:var(--bs-secondary-bg)">
                @if($other->primaryPhoto)
                  <img src="{{ $other->primaryPhoto->thumbnail_url }}" class="rounded-circle" style="width:38px;height:38px;object-fit:cover" alt="">
                @else
                  <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width:38px;height:38px;background:linear-gradient(135deg,#7c3aed,#ec4899);color:#fff">{{ strtoupper(mb_substr($other->name,0,1)) }}</div>
                @endif
                <div>
                  <div class="fw-semibold lh-1">{{ $other->name }}</div>
                  <div class="small text-muted">Your balance: <strong id="chatTipBalance">…</strong> credits</div>
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label fw-semibold">Amount <span class="text-muted fw-normal">(credits)</span></label>
                <input type="number" class="form-control" id="chatTipAmount" name="amount" min="1" placeholder="e.g. 5" required>
              </div>
              <div class="mb-2">
                <label class="form-label fw-semibold">Note <span class="text-muted fw-normal">(optional)</span></label>
                <input type="text" class="form-control" id="chatTipMessage" maxlength="255" placeholder="e.g. You're amazing!">
              </div>
              <div id="chatTipAlert" class="d-none"></div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-warning fw-semibold" id="chatTipSubmitBtn" disabled>
                <span id="chatTipSpinner" class="spinner-border spinner-border-sm d-none me-1"></span>
                <i class="bi bi-send me-1"></i>Send Tip
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>

    {{-- AI Suggestions Tray --}}
    <div id="aiTray" class="d-none ai-tray-enhanced" style="
        position:fixed;bottom:0;left:0;right:0;
        background:#fff;border-top:1.5px solid #f0d0e0;
        padding:16px 10px 24px;z-index:300;
        box-shadow:0 -6px 28px rgba(244,63,94,.12);
        max-height:60vh;overflow-y:auto;">
        <div class="d-flex align-items-center justify-content-between mb-2">
            <span class="fw-semibold small" style="color:#c2185b;font-size:.98rem">✨ AI Suggestions</span>
            <button type="button" id="aiTrayClose" class="btn-close btn-close-sm"></button>
        </div>
        <div id="aiSuggestionsWrapper">
            <div id="aiLoadingSpinner" class="text-center py-3 d-none">
                <div class="spinner-border spinner-border-sm text-danger" role="status"></div>
                <span class="ms-2 small text-muted">✨ Generating suggestions…</span>
            </div>
            <div id="aiSuggestionsList" class="ai-suggestions-list-enhanced"></div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════════════════════════════
     VOICE CALL — Incoming call overlay (shown to callee)
═══════════════════════════════════════════════════════════════════════ --}}
<div id="incomingCallOverlay" style="display:none;position:fixed;inset:0;z-index:1000;
     background:rgba(0,0,0,.72);backdrop-filter:blur(6px);
     display:none;align-items:center;justify-content:center;flex-direction:column;gap:20px;">
    <div style="background:#1a1a2e;border-radius:28px;padding:40px 32px;text-align:center;
                max-width:320px;width:90%;box-shadow:0 20px 60px rgba(0,0,0,.5);">
        <div id="incomingCallerAvatar" style="width:88px;height:88px;border-radius:50%;
             background:linear-gradient(135deg,#f43f5e,#a855f7);margin:0 auto 16px;
             display:flex;align-items:center;justify-content:center;
             font-size:2.2rem;font-weight:700;color:#fff;overflow:hidden;">
        </div>
        <div id="incomingCallerName" style="color:#fff;font-size:1.25rem;font-weight:700;margin-bottom:6px;"></div>
        <div style="color:#9ca3af;font-size:.9rem;margin-bottom:32px;">Incoming voice call…</div>
        <div style="display:flex;gap:20px;justify-content:center;">
            <button onclick="voiceCall.reject()" style="width:64px;height:64px;border-radius:50%;
                    border:none;background:#ef4444;color:#fff;font-size:1.5rem;cursor:pointer;
                    box-shadow:0 4px 16px rgba(239,68,68,.5);transition:transform .15s;"
                    onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform=''">
                <i class="bi bi-telephone-x-fill"></i>
            </button>
            <button onclick="voiceCall.answer()" style="width:64px;height:64px;border-radius:50%;
                    border:none;background:#10b981;color:#fff;font-size:1.5rem;cursor:pointer;
                    box-shadow:0 4px 16px rgba(16,185,129,.5);transition:transform .15s;"
                    onmouseover="this.style.transform='scale(1.1)'" onmouseout="this.style.transform=''">
                <i class="bi bi-telephone-fill"></i>
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════
     VOICE CALL — Active in-call screen
═══════════════════════════════════════════════════════════════════════ --}}
<div id="activeCallOverlay" style="display:none;position:fixed;inset:0;z-index:1000;
     background:linear-gradient(160deg,#1a1a2e 0%,#16213e 50%,#0f3460 100%);
     align-items:center;justify-content:center;flex-direction:column;gap:0;">

    {{-- Remote video (fills top half when on, hidden otherwise) --}}
    <div id="remoteVideoWrapper" style="display:none;position:absolute;inset:0;bottom:180px;">
        <div id="remoteVideoEl" style="width:100%;height:100%;object-fit:cover;background:#000;"></div>
    </div>

    {{-- Local video PiP (bottom-right corner when camera is on) --}}
    <div id="localVideoWrapper" style="display:none;position:absolute;bottom:190px;right:16px;
         width:90px;height:130px;border-radius:12px;overflow:hidden;border:2px solid rgba(255,255,255,.3);
         z-index:10;background:#000;">
        <div id="localVideoEl" style="width:100%;height:100%;"></div>
    </div>

    <div style="text-align:center;padding:40px 24px 0;position:relative;z-index:2;">
        <div id="activeCallAvatar" style="width:100px;height:100px;border-radius:50%;
             background:linear-gradient(135deg,#f43f5e,#a855f7);margin:0 auto 18px;
             display:flex;align-items:center;justify-content:center;
             font-size:2.6rem;font-weight:700;color:#fff;overflow:hidden;
             box-shadow:0 0 0 8px rgba(168,85,247,.2),0 0 0 16px rgba(168,85,247,.1);">
        </div>
        <div id="activeCallName" style="color:#fff;font-size:1.35rem;font-weight:700;margin-bottom:8px;"></div>
        <div id="activeCallStatus" style="color:#a78bfa;font-size:.95rem;margin-bottom:4px;">Calling…</div>
        <div id="activeCallTimer" style="color:#6b7280;font-size:.9rem;font-variant-numeric:tabular-nums;">00:00</div>
    </div>

    {{-- Sound wave animation --}}
    <div id="callWaveAnim" style="display:flex;align-items:center;gap:4px;margin:32px auto;height:40px;position:relative;z-index:2;">
        @for($i=0;$i<5;$i++)
        <div style="width:5px;background:rgba(167,139,250,.6);border-radius:3px;
                    animation:waveBar .8s ease-in-out {{ $i * 0.12 }}s infinite alternate;"
             class="call-wave-bar"></div>
        @endfor
    </div>

    {{-- Controls --}}
    <div style="display:flex;gap:24px;justify-content:center;padding-bottom:60px;position:relative;z-index:2;">
        <button id="muteBtn" onclick="voiceCall.toggleMute()" title="Mute"
                style="width:60px;height:60px;border-radius:50%;border:2px solid rgba(255,255,255,.2);
                       background:rgba(255,255,255,.1);color:#fff;font-size:1.3rem;cursor:pointer;transition:.2s;">
            <i class="bi bi-mic-fill"></i>
        </button>
        <button id="videoBtn" onclick="voiceCall.toggleVideo()" title="Camera"
                style="width:60px;height:60px;border-radius:50%;border:2px solid rgba(255,255,255,.2);
                       background:rgba(255,255,255,.1);color:#fff;font-size:1.3rem;cursor:pointer;transition:.2s;">
            <i class="bi bi-camera-video-off-fill"></i>
        </button>
        <button onclick="voiceCall.hangUp()"
                style="width:72px;height:72px;border-radius:50%;border:none;
                       background:#ef4444;color:#fff;font-size:1.6rem;cursor:pointer;
                       box-shadow:0 6px 20px rgba(239,68,68,.5);transition:transform .15s;"
                onmouseover="this.style.transform='scale(1.08)'" onmouseout="this.style.transform=''">
            <i class="bi bi-telephone-x-fill"></i>
        </button>
        <button id="speakerBtn" onclick="voiceCall.toggleSpeaker()" title="Speaker"
                style="width:60px;height:60px;border-radius:50%;border:2px solid rgba(255,255,255,.2);
                       background:rgba(255,255,255,.1);color:#fff;font-size:1.3rem;cursor:pointer;transition:.2s;">
            <i class="bi bi-volume-up-fill"></i>
        </button>
    </div>
</div>

<style>
@keyframes waveBar {
    from { height: 8px;  opacity: .4; }
    to   { height: 36px; opacity: 1;  }
}
</style>

@push('scripts')
{{-- Agora RTC SDK (browser) --}}
<script src="https://download.agora.io/sdk/release/AgoraRTC_N-4.21.0.js"></script>
<script>
const voiceCall = (() => {
    // ── Config injected from Laravel ─────────────────────────────────────
    const CONVERSATION_ID = {{ $conversation->id }};
    const MY_USER_ID      = {{ auth()->id() }};
    const OTHER_NAME      = @json($other->name);
    const OTHER_AVATAR    = @json($other->primaryPhoto?->thumbnail_url ?? null);
    const CSRF            = document.querySelector('meta[name="csrf-token"]').content;

    const INITIATE_URL = '/calls/' + CONVERSATION_ID + '/initiate';

    // ── State ──────────────────────────────────────────────────────────────
    let callId        = null;
    let agoraClient      = null;
    let localTrack       = null;   // audio track
    let localVideoTrack  = null;   // camera track (optional)
    let isVideoOn        = false;
    let isMuted       = false;
    let timerInterval = null;
    let timerSeconds  = 0;
    let pendingCall   = null; // for incoming call data
    let ringInterval  = null; // ring tone interval

    // ── Ring tone (Web Audio API — no external file) ───────────────────
    function playRing(loop = false) {
        stopRing();
        function _ring() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.connect(gain); gain.connect(ctx.destination);
                osc.type = 'sine'; osc.frequency.setValueAtTime(440, ctx.currentTime);
                gain.gain.setValueAtTime(0.18, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.8);
                osc.start(ctx.currentTime); osc.stop(ctx.currentTime + 0.8);
            } catch (e) {}
        }
        _ring();
        if (loop) ringInterval = setInterval(_ring, 1200);
    }

    function playSoundEnd() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain); gain.connect(ctx.destination);
            osc.type = 'sine'; osc.frequency.setValueAtTime(330, ctx.currentTime);
            gain.gain.setValueAtTime(0.15, ctx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.5);
            osc.start(ctx.currentTime); osc.stop(ctx.currentTime + 0.5);
        } catch (e) {}
    }

    function stopRing() {
        if (ringInterval) { clearInterval(ringInterval); ringInterval = null; }
    }

    // ── DOM helpers ────────────────────────────────────────────────────────
    const $incoming = document.getElementById('incomingCallOverlay');
    const $active   = document.getElementById('activeCallOverlay');

    function showIncoming(callerName, callerAvatar) {
        setAvatar('incomingCallerAvatar', callerName, callerAvatar);
        document.getElementById('incomingCallerName').textContent = callerName;
        $incoming.style.display = 'flex';
        playRing(true); // play ringtone on loop for incoming
    }

    function showActive(name, avatar) {
        setAvatar('activeCallAvatar', name, avatar);
        document.getElementById('activeCallName').textContent = name;
        document.getElementById('activeCallStatus').textContent = 'Connecting…';
        document.getElementById('activeCallTimer').textContent = '00:00';
        $active.style.display = 'flex';
    }

    function hideAll() {
        $incoming.style.display = 'none';
        $active.style.display   = 'none';
        stopTimer();
        stopRing();
    }

    function setAvatar(elId, name, url) {
        const el = document.getElementById(elId);
        if (url) {
            el.innerHTML = `<img src="${url}" style="width:100%;height:100%;object-fit:cover;border-radius:50%">`;
        } else {
            el.textContent = name.charAt(0).toUpperCase();
        }
    }

    // ── Timer ──────────────────────────────────────────────────────────────
    function startTimer() {
        timerSeconds = 0;
        timerInterval = setInterval(() => {
            timerSeconds++;
            const m = String(Math.floor(timerSeconds / 60)).padStart(2,'0');
            const s = String(timerSeconds % 60).padStart(2,'0');
            const el = document.getElementById('activeCallTimer');
            if (el) el.textContent = m + ':' + s;
        }, 1000);
    }

    function stopTimer() {
        clearInterval(timerInterval);
        timerInterval = null;
    }

    // ── POST helper ───────────────────────────────────────────────────────
    async function post(url) {
        const r = await fetch(url, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }
        });
        if (!r.ok) throw new Error(await r.text());
        return r.json();
    }

    // ── Agora join ────────────────────────────────────────────────────────
    async function joinChannel(appId, channel, token, uid) {
        agoraClient = AgoraRTC.createClient({ mode: 'rtc', codec: 'vp8' });

        agoraClient.on('user-published', async (user, mediaType) => {
            await agoraClient.subscribe(user, mediaType);
            if (mediaType === 'audio') user.audioTrack.play();
            if (mediaType === 'video') {
                document.getElementById('remoteVideoWrapper').style.display = 'block';
                document.getElementById('activeCallAvatar').style.opacity   = '0';
                user.videoTrack.play('remoteVideoEl');
            }
        });

        agoraClient.on('user-unpublished', (user, mediaType) => {
            if (mediaType === 'video') {
                document.getElementById('remoteVideoWrapper').style.display = 'none';
                document.getElementById('activeCallAvatar').style.opacity   = '1';
            }
        });

        agoraClient.on('user-left', () => {
            hangUp(true); // other side left
        });

        await agoraClient.join(appId, channel, token, uid);
        localTrack = await AgoraRTC.createMicrophoneAudioTrack();
        await agoraClient.publish(localTrack);

        stopRing(); // stop outgoing/incoming ring once connected
        document.getElementById('activeCallStatus').textContent = 'Connected';
        startTimer();
    }

    // ── Leave channel ─────────────────────────────────────────────────────
    async function leaveChannel() {
        if (localVideoTrack) { localVideoTrack.stop(); localVideoTrack.close(); localVideoTrack = null; }
        if (localTrack)      { localTrack.stop(); localTrack.close(); localTrack = null; }
        if (agoraClient)     { await agoraClient.leave(); agoraClient = null; }
        // Reset video UI
        document.getElementById('remoteVideoWrapper').style.display = 'none';
        document.getElementById('localVideoWrapper').style.display  = 'none';
        document.getElementById('activeCallAvatar').style.opacity   = '1';
        isVideoOn = false;
        const vBtn = document.getElementById('videoBtn');
        if (vBtn) { vBtn.style.background = 'rgba(255,255,255,.1)'; vBtn.innerHTML = '<i class="bi bi-camera-video-off-fill"></i>'; }
    }

    // ── Public API ────────────────────────────────────────────────────────

    async function initiate() {
        document.getElementById('callBtn').disabled = true;
        showActive(OTHER_NAME, OTHER_AVATAR);
        playRing(true); // outgoing ring while waiting for answer
        try {
            const data = await post(INITIATE_URL);
            callId = data.call_id;
            await joinChannel(data.app_id, data.channel_name, data.token, data.uid);
        } catch (e) {
            let msg = 'Call failed';
            try { msg = JSON.parse(e.message).error ?? msg; } catch (_) {}
            console.error('Call initiate failed:', msg, e);
            hideAll();
            document.getElementById('callBtn').disabled = false;
            alert('Call failed: ' + msg);
        }
    }

    async function answer() {
        if (!pendingCall) return;
        stopRing();
        $incoming.style.display = 'none';
        showActive(pendingCall.caller_name, pendingCall.caller_photo);
        try {
            const data = await post('/calls/' + pendingCall.call_id + '/answer');
            callId = data.call_id;
            await joinChannel(data.app_id, data.channel_name, data.token, data.uid);
        } catch (e) {
            console.error('Answer failed', e);
            hideAll();
        }
    }

    async function reject() {
        if (!pendingCall) return;
        await post('/calls/' + pendingCall.call_id + '/reject').catch(() => {});
        pendingCall = null;
        hideAll();
    }

    async function hangUp(remote = false) {
        stopRing();
        playSoundEnd();
        await leaveChannel();
        if (callId && !remote) {
            post('/calls/' + callId + '/end').catch(() => {});
        }
        callId  = null;
        isMuted = false;
        updateMuteBtn();
        hideAll();
        const btn = document.getElementById('callBtn');
        if (btn) btn.disabled = false;
    }

    function toggleMute() {
        if (!localTrack) return;
        isMuted = !isMuted;
        localTrack.setMuted(isMuted);
        updateMuteBtn();
    }

    function updateMuteBtn() {
        const btn = document.getElementById('muteBtn');
        if (!btn) return;
        btn.style.background = isMuted ? 'rgba(239,68,68,.7)' : 'rgba(255,255,255,.1)';
        btn.innerHTML = isMuted
            ? '<i class="bi bi-mic-mute-fill"></i>'
            : '<i class="bi bi-mic-fill"></i>';
    }

    // Speaker toggle — on mobile this switches to speakerphone (best effort)
    function toggleSpeaker() {
        const btn = document.getElementById('speakerBtn');
        btn.style.background = btn.style.background.includes('255,255,255')
            ? 'rgba(16,185,129,.7)' : 'rgba(255,255,255,.1)';
    }

    // Camera toggle — starts/stops local video track
    async function toggleVideo() {
        const btn = document.getElementById('videoBtn');
        if (!agoraClient) return;
        if (!isVideoOn) {
            try {
                localVideoTrack = await AgoraRTC.createCameraVideoTrack();
                await agoraClient.publish(localVideoTrack);
                localVideoTrack.play('localVideoEl');
                document.getElementById('localVideoWrapper').style.display = 'block';
                isVideoOn = true;
                btn.style.background = 'rgba(16,185,129,.7)';
                btn.innerHTML = '<i class="bi bi-camera-video-fill"></i>';
            } catch (e) {
                console.warn('Camera unavailable:', e);
                btn.title = 'Camera unavailable';
            }
        } else {
            await agoraClient.unpublish(localVideoTrack);
            localVideoTrack.stop(); localVideoTrack.close(); localVideoTrack = null;
            document.getElementById('localVideoWrapper').style.display = 'none';
            isVideoOn = false;
            btn.style.background = 'rgba(255,255,255,.1)';
            btn.innerHTML = '<i class="bi bi-camera-video-off-fill"></i>';
        }
    }

    // ── Listen for incoming calls via Reverb ──────────────────────────────
    if (typeof window.Echo !== 'undefined') {
        window.Echo.private('user.' + MY_USER_ID)
            .listen('.incoming-call', (data) => {
                pendingCall = data;
                showIncoming(data.caller_name, data.caller_photo);
            })
            .listen('.call-status-changed', (data) => {
                if (data.call_id !== callId) return;
                if (data.status === 'rejected') {
                    playSoundEnd();
                    hangUp(true);
                } else if (data.status === 'missed' || data.status === 'ended') {
                    playSoundEnd();
                    hangUp(true);
                } else if (data.status === 'active') {
                    stopRing();
                    document.getElementById('activeCallStatus').textContent = 'Connected';
                    startTimer();
                }
            });
    }

    return { initiate, answer, reject, hangUp, toggleMute, toggleSpeaker, toggleVideo };
})();
</script>
@endpush

@endsection

@push('scripts')
<script>
(function () {
    const convId     = {{ $conversation->id }};
    const myId       = {{ auth()->id() }};
    const csrf       = document.querySelector('meta[name="csrf-token"]').content;
    const base       = window.location.pathname.replace(/\/messages.*$/, '');
    let   currentDisappearMode = '{{ $disappearAfter }}';

    // ── Disappear timer UI ─────────────────────────────────────────────────
    const disappearBtn     = document.getElementById('disappearBtn');
    const disappearPopover = document.getElementById('disappearPopover');
    if (disappearBtn && disappearPopover) {
        disappearBtn.addEventListener('click', e => {
            e.stopPropagation();
            disappearPopover.classList.toggle('d-none');
        });
        document.addEventListener('click', () => disappearPopover.classList.add('d-none'));
        disappearPopover.addEventListener('click', e => e.stopPropagation());
        disappearPopover.querySelectorAll('.disappear-opt').forEach(btn => {
            btn.addEventListener('click', async () => {
                const mode = btn.dataset.mode;
                disappearPopover.classList.add('d-none');
                try {
                    await fetch(`${base}/messages/${convId}/disappear`, {
                        method: 'PATCH',
                        headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                        body: JSON.stringify({ mode })
                    });
                    currentDisappearMode = mode;
                    // Update button appearance
                    disappearPopover.querySelectorAll('.disappear-opt').forEach(b => {
                        b.classList.toggle('btn-warning', b.dataset.mode === mode);
                        b.classList.toggle('btn-outline-secondary', b.dataset.mode !== mode);
                    });
                    const badge = disappearBtn.querySelector('span');
                    if (mode !== 'off') {
                        disappearBtn.style.color = '#f97316';
                        if (!badge) {
                            const s = document.createElement('span');
                            s.style.cssText = 'font-size:.55rem;font-weight:700;position:absolute;bottom:0;right:0;background:#f97316;color:#fff;border-radius:4px;padding:0 2px;line-height:1.4';
                            s.textContent = mode;
                            disappearBtn.appendChild(s);
                        } else { badge.textContent = mode; }
                    } else {
                        disappearBtn.style.color = '';
                        if (badge) badge.remove();
                    }
                } catch(e) { console.error(e); }
            });
        });
    }

    // ── Countdown ticks for existing disappearing messages ─────────────────
    function formatRemaining(ms) {
        if (ms <= 0) return 'Expired';
        const s = Math.floor(ms / 1000);
        if (s < 60) return s + 's';
        const m = Math.floor(s / 60);
        if (m < 60) return m + 'm ' + (s % 60) + 's';
        const h = Math.floor(m / 60);
        if (h < 24) return h + 'h ' + (m % 60) + 'm';
        return Math.floor(h / 24) + 'd';
    }
    function tickCountdowns() {
        document.querySelectorAll('.disappear-countdown').forEach(el => {
            const exp = new Date(el.dataset.expires).getTime();
            const rem = exp - Date.now();
            const span = el.querySelector('.disappear-remaining');
            if (span) span.textContent = formatRemaining(rem);
            if (rem <= 0) el.closest('.msg-row')?.remove();
        });
    }
    setInterval(tickCountdowns, 1000);
    tickCountdowns();
    const chatBody   = document.getElementById('chatBody');
    const msgInput   = document.getElementById('msgInput');
    const btnSend    = document.getElementById('btnSend');
    const typingRow  = document.getElementById('typingRow');
    const typingLine = document.getElementById('typingStatusLine');
    const statusLine = document.getElementById('chatStatusLine');
    const attachBtn  = document.getElementById('attachBtn');
    const attachInput= document.getElementById('attachInput');
    const previewBar = document.getElementById('attachPreviewBar');
    const previewInner = document.getElementById('attachPreviewInner');
    const attachCancel = document.getElementById('attachCancel');

    function scrollBottom(smooth) {
        chatBody.scrollTo({ top: chatBody.scrollHeight, behavior: smooth ? 'smooth' : 'instant' });
    }
    scrollBottom(false);

    // -- Auto-grow textarea --------------------------------------------------
    msgInput.addEventListener('input', () => {
        msgInput.style.height = 'auto';
        msgInput.style.height = Math.min(msgInput.scrollHeight, 160) + 'px';
    });

    // -- Emoji / Reaction definitions (Unicode escapes avoid file‑encoding issues) --
    const REACTIONS = ['\u2764\uFE0F','\uD83D\uDE02','\uD83D\uDC4D','\uD83D\uDE2E','\uD83D\uDE22','\uD83D\uDE21'];
    const EMOJIS    = ['\uD83D\uDE0A','\uD83D\uDE02','\u2764\uFE0F','\uD83D\uDE0D','\uD83D\uDC4D','\uD83D\uDE4F','\uD83D\uDE18','\uD83E\uDD7A','\uD83D\uDE2D','\uD83D\uDD25','\uD83D\uDE0E','\uD83E\uDD23','\uD83D\uDE01','\uD83D\uDCAF','\u2728','\uD83C\uDF89','\uD83D\uDE0B','\uD83E\uDD29','\uD83D\uDE07','\uD83D\uDE04'];

    // -- Emoji popover -------------------------------------------------------
    const emojiBtn     = document.getElementById('emojiBtn');
    const emojiPopover = document.getElementById('emojiPopover');

    // Populate emoji popover from JS (avoids file encoding issues)
    EMOJIS.forEach(e => {
        const s = document.createElement('span');
        s.dataset.emoji = e;
        s.textContent = e;
        emojiPopover.appendChild(s);
    });

    // Populate reaction pickers from PHP-rendered messages
    document.querySelectorAll('.reaction-picker[data-msg]').forEach(picker => {
        const msgId = picker.dataset.msg;
        REACTIONS.forEach(e => {
            const s = document.createElement('span');
            s.className = 'reaction-choice';
            s.dataset.emoji = e;
            s.dataset.msg = msgId;
            s.textContent = e;
            picker.appendChild(s);
        });
    });

    emojiBtn.addEventListener('click', e => { e.stopPropagation(); emojiPopover.classList.toggle('d-none'); });
    emojiPopover.querySelectorAll('span').forEach(s => {
        s.addEventListener('click', e => {
            e.stopPropagation();
            const pos = msgInput.selectionStart ?? msgInput.value.length;
            const v   = msgInput.value;
            msgInput.value = v.slice(0, pos) + s.dataset.emoji + v.slice(pos);
            msgInput.focus();
            msgInput.dispatchEvent(new Event('input'));
            emojiPopover.classList.add('d-none');
        });
    });
    document.addEventListener('click', () => emojiPopover.classList.add('d-none'));

    // -- Gift picker ---------------------------------------------------------
    // Gifts defined with Unicode escapes to avoid file-encoding corruption
    const GIFTS = [
        { emoji: '\uD83C\uDF39', label: 'Rose',      price: {{ (int) ($giftPrices['gift_price_rose']      ?? 10) }} },
        { emoji: '\uD83D\uDC96', label: 'Heart',     price: {{ (int) ($giftPrices['gift_price_heart']     ?? 10) }} },
        { emoji: '\uD83C\uDF81', label: 'Gift Box',  price: {{ (int) ($giftPrices['gift_price_gift_box']  ?? 10) }} },
        { emoji: '\uD83C\uDF6B', label: 'Chocolate', price: {{ (int) ($giftPrices['gift_price_chocolate'] ?? 10) }} },
        { emoji: '\u2B50',       label: 'Star',      price: {{ (int) ($giftPrices['gift_price_star']      ?? 10) }} },
        { emoji: '\uD83D\uDC8E', label: 'Diamond',   price: {{ (int) ($giftPrices['gift_price_diamond']   ?? 10) }} },
        { emoji: '\uD83C\uDF38', label: 'Flower',    price: {{ (int) ($giftPrices['gift_price_flower']    ?? 10) }} },
        { emoji: '\uD83D\uDC8C', label: 'Love',      price: {{ (int) ($giftPrices['gift_price_love']      ?? 10) }} },
    ];
    const giftGrid = document.getElementById('giftGrid');
    if (giftGrid) {
        GIFTS.forEach(g => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'gift-choice';
            btn.dataset.gift = g.emoji + ' ' + g.label;
            btn.dataset.price = g.price;
            btn.title = g.label;
            btn.innerHTML = `<span class="gift-emoji">${g.emoji}</span><span class="gift-label">${g.label}</span><span class="gift-label" style="color:#c2185b;font-size:.58rem">${g.price} cr</span>`;
            giftGrid.appendChild(btn);
        });
    }
    const giftBtn     = document.getElementById('giftBtn');
    const giftPopover = document.getElementById('giftPopover');
    function _giftToast(msg, type) {
        const container = document.getElementById('toastContainer');
        if (!container) return;
        const el = document.createElement('div');
        el.className = 'toast align-items-center text-bg-' + type + ' border-0';
        el.setAttribute('role', 'alert');
        el.setAttribute('aria-live', 'assertive');
        el.innerHTML = '<div class="d-flex"><div class="toast-body fw-semibold">' + msg + '</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div>';
        container.appendChild(el);
        new bootstrap.Toast(el, { delay: 5000 }).show();
        el.addEventListener('hidden.bs.toast', () => el.remove());
    }
    if (giftBtn && giftPopover) {
        giftBtn.addEventListener('click', e => { e.stopPropagation(); giftPopover.classList.toggle('d-none'); });
        giftPopover.addEventListener('click', async e => {
            const btn = e.target.closest('.gift-choice');
            if (!btn) return;
            e.stopPropagation();
            const gift  = btn.dataset.gift;
            const price = parseInt(btn.dataset.price, 10) || 0;
            giftPopover.classList.add('d-none');

            // Check balance before sending
            try {
                const balRes  = await fetch('{{ route("wallet.balance") }}');
                const balData = await balRes.json();
                const balance = parseInt(balData.balance ?? 0, 10);
                if (price > 0 && balance < price) {
                    _giftToast('Not enough credits to send this gift. <a href="{{ route("wallet.index") }}" class="toast-link text-white fw-semibold">Fund wallet</a>', 'danger');
                    return;
                }
            } catch {
                _giftToast('Could not verify balance. Please try again.', 'warning');
                return;
            }

            try {
                const body = { body: gift, type: 'gift' };
                if (price > 0) body.gift_price = price;
                const res = await fetch(`${base}/messages/${convId}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                    body: JSON.stringify(body)
                });
                if (!res.ok) {
                    const data = await res.json().catch(() => ({}));
                    _giftToast(data.error ?? 'Could not send gift. Please try again.', 'danger');
                    return;
                }
                appendBubble(gift, true, new Date().toISOString(), 'gift');
            } catch (err) {
                _giftToast('Network error. Please try again.', 'danger');
            }
        });
        document.addEventListener('click', () => giftPopover.classList.add('d-none'));
    }

    // -- Attachment picker ---------------------------------------------------
    let pendingFile = null;
    attachBtn.addEventListener('click', () => attachInput.click());
    attachInput.addEventListener('change', () => {
        const file = attachInput.files[0];
        if (!file) return;
        pendingFile = file;
        const isImg = file.type.startsWith('image/');
        previewInner.innerHTML = isImg
            ? `<img src="${URL.createObjectURL(file)}" class="chat-preview-img" alt="preview"> <span class="small text-muted ms-2">${esc(file.name)}</span>`
            : `<i class="bi bi-music-note-beamed fs-4 text-primary me-2"></i><span class="small text-muted">${esc(file.name)} (${(file.size/1024/1024).toFixed(1)} MB)</span>`;
        previewBar.classList.remove('d-none');
        msgInput.placeholder = isImg ? 'Add a caption (optional)�' : 'Add a message (optional)�';
    });
    attachCancel.addEventListener('click', () => {
        pendingFile = null;
        attachInput.value = '';
        previewBar.classList.add('d-none');
        previewInner.innerHTML = '';
        msgInput.placeholder = 'Type a message�';
    });

    // -- Send (text OR attachment) -------------------------------------------
    async function send() {
        const body = msgInput.value.trim();

        if (pendingFile) {
            const fd = new FormData();
            fd.append('attachment', pendingFile);
            if (body) fd.append('body', body);
            fd.append('_token', csrf);

            const isImg = pendingFile.type.startsWith('image/');
            appendBubble(null, true, new Date().toISOString(), isImg ? 'image' : 'audio',
                         isImg ? URL.createObjectURL(pendingFile) : null, pendingFile.name);

            // Reset attachment state
            pendingFile = null;
            attachInput.value = '';
            previewBar.classList.add('d-none');
            previewInner.innerHTML = '';
            msgInput.value = '';
            msgInput.style.height = 'auto';
            msgInput.placeholder = 'Type a message�';

            try {
                await fetch(`${base}/messages/${convId}`, { method: 'POST', headers: {'X-CSRF-TOKEN': csrf}, body: fd });
            } catch (err) { console.error(err); }
            return;
        }

        if (!body) return;
        msgInput.value = '';
        msgInput.style.height = 'auto';
        try {
            const res = await fetch(`${base}/messages/${convId}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                body: JSON.stringify({ body })
            });
            const data = res.ok ? await res.json() : null;
            appendBubble(body, true, new Date().toISOString(), 'text', null, null, data?.message?.id, data?.message?.expires_at ?? null);
        } catch (err) {
            appendBubble(body, true, new Date().toISOString(), 'text');
            console.error(err);
        }
    }

    btnSend.addEventListener('click', send);
    msgInput.addEventListener('keydown', e => {
        if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); send(); }
    });

    // -- Build bubble HTML ---------------------------------------------------
    function appendBubble(body, isMe, createdAt, type = 'text', attachUrl = null, attachName = null, id = null, expiresAt = null) {
        typingRow.classList.add('d-none');
        const t   = new Date(createdAt).toLocaleTimeString([], { hour: 'numeric', minute: '2-digit' });
        // Show sent-tick for my messages; premium users get read-receipt upgrades via broadcast
        const chk = isMe
            ? `<i class="bi bi-check2 text-muted read-receipt-icon"${id ? ` data-msg-id="${id}"` : ''} title="Sent"></i>`
            : '';

        const row = document.createElement('div');
        row.className = `msg-row ${isMe ? 'me' : 'them'} group-start`;

        if (!isMe) {
            const av = document.createElement('div');
            av.className = 'msg-avatar-cell';
            row.appendChild(av);
        }

        const bubble = document.createElement('div');
        bubble.className = `message-bubble ${isMe ? 'me' : 'them'} position-relative${type !== 'text' ? ' media-bubble' : ''}`;
        if (id) bubble.dataset.msgId = id;

        let content = '';
        if (type === 'image' && attachUrl) {
            content = `<a href="${attachUrl}" target="_blank" class="msg-img-link"><img src="${attachUrl}" alt="${esc(attachName ?? 'Image')}" class="msg-image" loading="lazy"></a>`;
        } else if (type === 'audio' && attachUrl) {
            content = `<div class="msg-audio"><i class="bi bi-music-note-beamed me-2 text-primary"></i><audio controls preload="none" class="msg-audio-player"><source src="${attachUrl}"></audio><div class="msg-audio-name text-truncate small mt-1 text-muted">${esc(attachName ?? '')}</div></div>`;
        } else if (type === 'gift') {
            const parts = (body ?? '').split(' ');
            const emoji = parts[0] ?? '';
            const label = parts.slice(1).join(' ');
            content = `<div class="gift-bubble text-center px-3 py-2"><div style="font-size:2.5rem;line-height:1">${esc(emoji)}</div><div class="small fw-semibold mt-1" style="color:#c2185b">${esc(label)}</div><div class="tiny text-muted" style="font-size:.65rem">Virtual gift \uD83C\uDF81</div></div>`;
        } else {
            content = esc(body ?? '');
        }

        bubble.innerHTML = content
            + `<div class="message-meta d-flex align-items-center gap-1">${t} ${chk}</div>`
            + (expiresAt
                ? `<div class="disappear-countdown text-muted d-flex align-items-center gap-1" style="font-size:.65rem;margin-top:2px;" data-expires="${expiresAt}">\uD83D\uDD25 <span class="disappear-remaining"></span></div>`
                : '')
            + (type === 'text'
                ? `<div class="reaction-picker-trigger" title="React">+</div><div class="reaction-picker d-none">${REACTIONS.map(e=>`<span class="reaction-choice" data-emoji="${e}">${e}</span>`).join('')}</div>`
                : '');

        row.appendChild(bubble);

        if (isMe) {
            const sp = document.createElement('div');
            sp.className = 'msg-avatar-cell';
            sp.style.visibility = 'hidden';
            row.appendChild(sp);
        }

        chatBody.appendChild(row);
        scrollBottom(true);
        if (type === 'text') attachReaction(bubble);
    }

    function esc(s) {
        if (!s) return '';
        return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    // -- Typing broadcast ----------------------------------------------------
    let typingDebounce;
    msgInput.addEventListener('input', () => {
        clearTimeout(typingDebounce);
        typingDebounce = setTimeout(() => {
            fetch(`${base}/messages/${convId}/typing`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' }
            }).catch(() => {});
        }, 300);
    });

    // -- WebSockets ----------------------------------------------------------
    let clearTypingTimer;
    if (window.Echo) {
        window.Echo.private(`conversation.${convId}`)
            .listen('.message.sent', e => {
                if (e.sender_id !== myId) {
                    typingRow.classList.add('d-none');
                    appendBubble(e.body, false, e.created_at, e.type ?? 'text',
                                 e.attachment_url ?? null, e.attachment_name ?? null, e.id ?? null);
                }
            })
            .listen('.messages.read', e => {
                // Recipient read the conversation — upgrade all my sent ticks to blue "seen"
                @if(auth()->user()->isPremiumActive())
                if (e.reader_user_id !== myId) {
                    document.querySelectorAll('.read-receipt-icon').forEach(icon => {
                        if (icon.classList.contains('bi-check2') || icon.classList.contains('bi-check2-all')) {
                            icon.className = 'bi bi-check2-all text-primary read-receipt-icon';
                            icon.title = 'Seen';
                        }
                    });
                }
                @endif
            })
            .listen('.user.typing', e => {
                if (e.user_id !== myId) {
                    typingLine.classList.remove('d-none');
                    statusLine.classList.add('d-none');
                    typingRow.classList.remove('d-none');
                    scrollBottom(true);
                    clearTimeout(clearTypingTimer);
                    clearTypingTimer = setTimeout(() => {
                        typingLine.classList.add('d-none');
                        statusLine.classList.remove('d-none');
                        typingRow.classList.add('d-none');
                    }, 2500);
                }
            });
    }

    // -- Reactions -----------------------------------------------------------
    function attachReaction(bubble) {
        const trigger = bubble.querySelector('.reaction-picker-trigger');
        const picker  = bubble.querySelector('.reaction-picker');
        if (!trigger || !picker) return;
        trigger.addEventListener('click', e => {
            e.stopPropagation();
            document.querySelectorAll('.reaction-picker').forEach(p => { if (p !== picker) p.classList.add('d-none'); });
            picker.classList.toggle('d-none');
        });
        picker.querySelectorAll('.reaction-choice').forEach(choice => {
            choice.addEventListener('click', async e => {
                e.stopPropagation();
                const msgId = bubble.dataset.msgId;
                if (!msgId) return;
                picker.classList.add('d-none');

                const emoji = choice.dataset.emoji;

                // ── Optimistic UI update ──────────────────────────────────
                let reactionsDiv = bubble.querySelector('.msg-reactions');
                if (!reactionsDiv) {
                    reactionsDiv = document.createElement('div');
                    reactionsDiv.className = 'msg-reactions';
                    // Insert before .reaction-picker-trigger
                    bubble.insertBefore(reactionsDiv, trigger);
                }
                const existing = reactionsDiv.querySelector(`[data-emoji="${CSS.escape(emoji)}"]`);
                if (existing) {
                    // Bump count
                    const cur = parseInt(existing.dataset.count ?? '1', 10) + 1;
                    existing.dataset.count = cur;
                    existing.textContent = emoji + (cur > 1 ? ' ' + cur : '');
                    // Restart bounce animation
                    existing.classList.remove('reaction-bump');
                    void existing.offsetWidth; // reflow
                    existing.classList.add('reaction-bump');
                } else {
                    const badge = document.createElement('span');
                    badge.className = 'reaction-badge reaction-new';
                    badge.dataset.emoji = emoji;
                    badge.dataset.count = '1';
                    badge.textContent   = emoji;
                    reactionsDiv.appendChild(badge);
                    // Remove animation class after it finishes so it can re-apply
                    badge.addEventListener('animationend', () => badge.classList.remove('reaction-new'), { once: true });
                }
                // ── Fire request async ────────────────────────────────────
                fetch(`${base}/messages/react/${msgId}`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                    body: JSON.stringify({ emoji })
                }).catch(() => {});
            });
        });
    }

    document.querySelectorAll('.message-bubble').forEach(attachReaction);
    document.addEventListener('click', () =>
        document.querySelectorAll('.reaction-picker:not(.d-none)').forEach(p => p.classList.add('d-none'))
    );

    // ── AI Writing Assistant ─────────────────────────────────────────────────
    const aiBtn       = document.getElementById('aiBtn');
    const aiPopover   = document.getElementById('aiPopover');
    const aiTray      = document.getElementById('aiTray');
    const aiTrayClose = document.getElementById('aiTrayClose');
    const aiList      = document.getElementById('aiSuggestionsList');
    const aiSpinner   = document.getElementById('aiLoadingSpinner');
    const aiRephraseBtn = document.getElementById('aiRephraseBtn');
    const aiRephraseSpinner = document.getElementById('aiRephraseSpinner');
    const partnerId   = {{ $other->id }};
    const aiRoute     = '{{ route('ai.suggest') }}';

    aiBtn.addEventListener('click', e => { e.stopPropagation(); aiPopover.classList.toggle('d-none'); });
    document.addEventListener('click', () => aiPopover.classList.add('d-none'));
    aiTrayClose.addEventListener('click', () => aiTray.classList.add('d-none'));

    document.querySelectorAll('.ai-option-btn').forEach(btn => {
        btn.addEventListener('click', async e => {
            e.stopPropagation();
            aiPopover.classList.add('d-none');
            aiList.innerHTML = '';
            aiSpinner.classList.remove('d-none');
            aiTray.classList.remove('d-none');

            try {
                const res = await fetch(aiRoute, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        type:            btn.dataset.aiType,
                        conversation_id: convId,
                        partner_id:      partnerId,
                    }),
                });
                const data = await res.json();
                aiSpinner.classList.add('d-none');

                (data.suggestions ?? []).forEach(s => {
                    const card = document.createElement('div');
                    card.className = 'ai-suggestion-card ai-suggestion-card-enhanced';
                    card.innerHTML = `<div class="ai-suggestion-main">${esc(s)}</div><div class="ai-card-footer"><button class="ai-use-btn btn btn-link p-0">Use ↑</button><button class="ai-copy-btn btn btn-link p-0 text-muted ms-2" title="Copy"><i class="bi bi-clipboard"></i></button></div>`;
                    card.querySelector('.ai-use-btn').addEventListener('click', (ev) => {
                        ev.stopPropagation();
                        msgInput.value = s;
                        msgInput.dispatchEvent(new Event('input'));
                        msgInput.focus();
                        aiTray.classList.add('d-none');
                    });
                    card.querySelector('.ai-copy-btn').addEventListener('click', (ev) => {
                        ev.stopPropagation();
                        navigator.clipboard.writeText(s);
                        card.querySelector('.ai-copy-btn').innerHTML = '<i class="bi bi-clipboard-check"></i>';
                        setTimeout(()=>{card.querySelector('.ai-copy-btn').innerHTML = '<i class="bi bi-clipboard"></i>';}, 1200);
                    });
                    aiList.appendChild(card);
                });

                if (!data.suggestions?.length) {
                    aiList.innerHTML = '<p class="text-muted small text-center mt-2">No suggestions available.</p>';
                }
            } catch {
                aiSpinner.classList.add('d-none');
                aiList.innerHTML = '<p class="text-danger small text-center mt-2">Failed to load suggestions.</p>';
            }
        });
    });

    // Rephrase button logic
    aiRephraseBtn.addEventListener('click', async () => {
        const text = msgInput.value.trim();
        if (!text) return;
        aiRephraseBtn.disabled = true;
        aiRephraseSpinner.classList.remove('d-none');
        msgInput.disabled = true;
        // Show typing animation in textarea
        let typingInterval;
        function showTypingAnim() {
            let dots = 0;
            typingInterval = setInterval(() => {
                msgInput.value = 'Rewriting'+'.'.repeat(dots%4);
                dots++;
            }, 350);
        }
        showTypingAnim();
        try {
            const res = await fetch(aiRoute, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    type: 'rephrase',
                    conversation_id: convId,
                    partner_id: partnerId,
                    draft: text
                }),
            });
            const data = await res.json();
            clearInterval(typingInterval);
            if (data.suggestions && data.suggestions[0]) {
                // Animate typing out the rewritten text
                let rewritten = data.suggestions[0];
                let i = 0;
                msgInput.value = '';
                function typeChar() {
                    if (i <= rewritten.length) {
                        msgInput.value = rewritten.slice(0, i);
                        i++;
                        setTimeout(typeChar, 18 + Math.random()*40);
                    } else {
                        msgInput.dispatchEvent(new Event('input'));
                        msgInput.disabled = false;
                    }
                }
                typeChar();
            } else {
                msgInput.value = text;
                msgInput.disabled = false;
            }
        } catch {
            clearInterval(typingInterval);
            msgInput.value = text;
            msgInput.disabled = false;
        }
        aiRephraseBtn.disabled = false;
        aiRephraseSpinner.classList.add('d-none');
    });
    // ── Chat Tip ─────────────────────────────────────────────────────────────
    (function () {
        const chatTipBtn    = document.getElementById('chatTipBtn');
        let _chatTipModal   = null;
        function getChatTipModal() {
            return _chatTipModal ??= new bootstrap.Modal(document.getElementById('chatTipModal'));
        }
        const chatTipForm   = document.getElementById('chatTipForm');
        const chatTipAmtEl  = document.getElementById('chatTipAmount');
        const chatTipMsgEl  = document.getElementById('chatTipMessage');
        const chatTipBalEl  = document.getElementById('chatTipBalance');
        const chatTipAlert  = document.getElementById('chatTipAlert');
        const chatTipSubmit = document.getElementById('chatTipSubmitBtn');
        const chatTipSpinner= document.getElementById('chatTipSpinner');
        let _tipBal = 0, _tipBalLoaded = false;

        function tipAlert(type, html) {
            if (!type) { chatTipAlert.className = 'd-none'; chatTipAlert.innerHTML = ''; return; }
            chatTipAlert.className = 'alert alert-' + type + ' py-2 small mt-2';
            chatTipAlert.innerHTML = html;
        }

        chatTipBtn?.addEventListener('click', () => {
            chatTipForm.reset();
            chatTipAmtEl.value = '';
            chatTipMsgEl.value = '';
            tipAlert(null);
            chatTipSubmit.disabled = true;
            _tipBalLoaded = false;
            chatTipBalEl.innerHTML = '<span class="spinner-border spinner-border-sm" style="width:.8rem;height:.8rem"></span>';

            fetch('{{ route("wallet.balance") }}')
                .then(r => r.json())
                .then(d => {
                    _tipBal = parseInt(d.balance ?? 0, 10);
                    _tipBalLoaded = true;
                    chatTipBalEl.textContent = _tipBal;
                    if (_tipBal <= 0) {
                        tipAlert('warning', '<i class="bi bi-exclamation-triangle me-1"></i>No credits. <a href="{{ route("wallet.index") }}" class="alert-link fw-semibold">Fund your wallet</a>.');
                        chatTipSubmit.disabled = true;
                    }
                })
                .catch(() => { _tipBalLoaded = true; chatTipBalEl.textContent = '?'; });

            getChatTipModal().show();
        });

        chatTipAmtEl?.addEventListener('input', function () {
            if (!_tipBalLoaded) return;
            const amt = parseInt(this.value, 10);
            if (isNaN(amt) || amt < 1) { tipAlert(null); chatTipSubmit.disabled = _tipBal <= 0; return; }
            if (amt > _tipBal) {
                tipAlert('warning', `<i class="bi bi-exclamation-triangle me-1"></i>Amount (${amt}) exceeds your balance of <strong>${_tipBal}</strong> credits.`);
                chatTipSubmit.disabled = true;
            } else {
                tipAlert(null);
                chatTipSubmit.disabled = false;
            }
        });

        chatTipForm?.addEventListener('submit', async (e) => {
            e.preventDefault();
            const amt = parseInt(chatTipAmtEl.value, 10);
            if (isNaN(amt) || amt < 1) { tipAlert('danger', 'Enter a valid amount (min 1 credit).'); return; }
            if (_tipBalLoaded && amt > _tipBal) { tipAlert('danger', 'Insufficient balance.'); return; }

            chatTipSubmit.disabled = true;
            chatTipSpinner.classList.remove('d-none');
            tipAlert(null);

            try {
                const res = await fetch('{{ route("tips.send") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json', 'Accept': 'application/json' },
                    body: JSON.stringify({ recipient_id: {{ $other->id }}, amount: amt, message: chatTipMsgEl.value.trim() || null }),
                });
                const data = await res.json();
                if (!res.ok) {
                    tipAlert('danger', '<i class="bi bi-x-circle me-1"></i>' + (data.error ?? 'Could not send tip.'));
                    chatTipSubmit.disabled = false;
                } else {
                    tipAlert('success', `<i class="bi bi-check-circle me-1"></i>Tip of <strong>${amt}</strong> credits sent to <strong>{{ $other->name }}</strong>!`);
                    _tipBal -= amt;
                    chatTipBalEl.textContent = _tipBal;
                    chatTipAmtEl.value = '';
                    chatTipMsgEl.value = '';
                    setTimeout(() => getChatTipModal().hide(), 1800);
                }
            } catch {
                tipAlert('danger', 'Network error. Please try again.');
                chatTipSubmit.disabled = false;
            }
            chatTipSpinner.classList.add('d-none');
        });
    })();
    // ────────────────────────────────────────────────────────────────────────
})();
</script>
@endpush
