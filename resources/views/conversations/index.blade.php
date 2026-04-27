@extends('layouts.app')
@section('title', 'Messages')
@section('content')
<div class="container py-4">
    @php $totalUnread = $conversations->sum(fn($c) => $c->unreadCountFor(auth()->id())); @endphp

    {{-- Page header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-chat-heart text-primary me-2"></i>Messages
            @if($totalUnread > 0)
                <span class="badge bg-primary rounded-pill ms-1" style="font-size:.7rem;vertical-align:middle">{{ $totalUnread }}</span>
            @endif
        </h4>
    </div>

    @if($conversations->isEmpty())
    {{-- Empty state --}}
    <div class="card border-0 shadow-sm text-center py-5 px-3">
        <div class="display-4 mb-3">💬</div>
        <h5 class="fw-semibold mb-2">No conversations yet</h5>
        <p class="text-muted small mb-4">Match with someone and start chatting!</p>
        <a href="{{ route('matches.index') }}" class="btn btn-primary mx-auto px-4" style="width:fit-content">
            <i class="bi bi-hearts me-2"></i>View Matches
        </a>
    </div>
    @else

    {{-- Search --}}
    <div class="position-relative mb-3" style="max-width:480px">
        <i class="bi bi-search position-absolute text-muted" style="left:.9rem;top:50%;transform:translateY(-50%);font-size:.9rem;pointer-events:none"></i>
        <input id="inboxSearch" type="search" class="form-control ps-5 rounded-pill" placeholder="Search conversations…">
    </div>

    <div class="card border-0 shadow-sm overflow-hidden" id="convList">
        {{-- AI Dating Assistant (pinned at top) --}}
        <a href="{{ route('ai.chat') }}" class="conv-item">
            <div class="conv-avatar d-flex align-items-center justify-content-center"
                 style="background:linear-gradient(135deg,#667eea,#764ba2);min-width:52px;height:52px;border-radius:50%;flex-shrink:0">
                <span style="font-size:1.4rem;line-height:1">🤖</span>
            </div>
            <div class="conv-body ms-3 overflow-hidden">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="conv-name fw-semibold">AI Dating Assistant</span>
                    <span class="badge rounded-pill text-bg-primary" style="font-size:.65rem">AI</span>
                </div>
                <div class="conv-preview text-muted small text-truncate">Ask for dating advice, bio help, and more…</div>
            </div>
        </a>
        @foreach($conversations as $conv)
        @php
            $other   = $conv->match->user1_id === auth()->id() ? $conv->match->user2 : $conv->match->user1;
            $lastMsg = $conv->messages->first();
            $unread  = $conv->unreadCountFor(auth()->id());
            $online  = $other?->last_active_at && $other->last_active_at->gt(now()->subMinutes(10));
            $isPinned = $conv->isPinnedFor(auth()->id());
        @endphp
        @if(!$other) @continue @endif
        <div class="conv-item-wrapper position-relative" data-conv-id="{{ $conv->id }}">
            <a href="{{ route('conversations.show', $conv->id) }}"
               class="conv-item {{ $unread > 0 ? 'unread' : '' }}"
               data-name="{{ strtolower($other->name) }}">

                {{-- Avatar --}}
                <div class="conv-avatar">
                    @if($other->primaryPhoto)
                        <img src="{{ $other->primaryPhoto->thumbnail_url }}" alt="{{ $other->name }}">
                    @else
                        <div class="conv-avatar-ph">{{ strtoupper(mb_substr($other->name, 0, 1)) }}</div>
                    @endif
                    @if($online)<div class="conv-online" title="Online now"></div>@endif
                </div>

                {{-- Body --}}
                <div class="conv-body">
                    <div class="conv-name">
                        @if($isPinned)
                            <i class="bi bi-pin-angle-fill text-primary me-1" style="font-size:.8rem" title="Pinned"></i>
                        @endif
                        {{ $other->name }}
                        @if($other->is_verified ?? false)<i class="bi bi-patch-check-fill text-info ms-1" style="font-size:.8rem"></i>@endif
                    </div>
                    <div class="conv-preview">
                        @if($lastMsg)
                            @if($lastMsg->sender_id === auth()->id())<span class="text-muted">You: </span>@endif{{ Str::limit($lastMsg->body, 55) }}
                        @else
                            <em>Start the conversation…</em>
                        @endif
                    </div>
                </div>

                {{-- Meta --}}
                <div class="conv-meta">
                    @if($lastMsg)
                    <div class="conv-time">
                        @if($lastMsg->created_at->isToday())
                            {{ $lastMsg->created_at->format('g:i A') }}
                        @elseif($lastMsg->created_at->isYesterday())
                            Yesterday
                        @else
                            {{ $lastMsg->created_at->format('M j') }}
                        @endif
                    </div>
                    @endif
                    @if($unread > 0)
                    <div class="conv-unread-badge">{{ $unread > 9 ? '9+' : $unread }}</div>
                @endif
            </div>
        </a>
        
        {{-- Action Dropdown --}}
        <div class="dropdown position-absolute" style="top:.75rem;right:.75rem;z-index:10">
            <button class="btn btn-sm btn-light border-0 p-1 rounded-circle" 
                    data-bs-toggle="dropdown" 
                    onclick="event.preventDefault();event.stopPropagation();"
                    style="width:28px;height:28px;line-height:1">
                <i class="bi bi-three-dots-vertical"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li>
                    <a class="dropdown-item pin-conv" href="#" data-conv-id="{{ $conv->id }}" data-pinned="{{ $isPinned ? '1' : '0' }}">
                        <i class="bi bi-pin-angle{{ $isPinned ? '-fill' : '' }} me-2"></i>
                        {{ $isPinned ? 'Unpin' : 'Pin' }} conversation
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger hide-conv" href="#" data-conv-id="{{ $conv->id }}">
                        <i class="bi bi-eye-slash me-2"></i>Hide conversation
                    </a>
                </li>
            </ul>
        </div>
    </div>
        @endforeach
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
document.getElementById('inboxSearch')?.addEventListener('input', function () {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#convList .conv-item').forEach(el => {
        el.style.display = el.dataset.name.includes(q) ? '' : 'none';
    });
});

// Pin/Unpin conversation
document.querySelectorAll('.pin-conv').forEach(btn => {
    btn.addEventListener('click', async function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        const convId = this.dataset.convId;
        const isPinned = this.dataset.pinned === '1';
        
        try {
            const response = await fetch(`/messages/${convId}/pin`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                location.reload();
            }
        } catch (error) {
            console.error('Error pinning conversation:', error);
        }
    });
});

// Hide conversation
document.querySelectorAll('.hide-conv').forEach(btn => {
    btn.addEventListener('click', async function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (!confirm('Hide this conversation? You can still access it from the match profile.')) {
            return;
        }
        
        const convId = this.dataset.convId;
        
        try {
            const response = await fetch(`/messages/${convId}/hide`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                // Smoothly remove the conversation from view
                const wrapper = this.closest('.conv-item-wrapper');
                wrapper.style.transition = 'opacity 0.3s';
                wrapper.style.opacity = '0';
                setTimeout(() => wrapper.remove(), 300);
            }
        } catch (error) {
            console.error('Error hiding conversation:', error);
        }
    });
});
</script>
@endpush
