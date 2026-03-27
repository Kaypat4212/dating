@extends('layouts.app')
@section('title', $chatRoom->name)
@section('content')
<div class="container-fluid py-3" style="max-width:1200px;">
    <div class="row g-3" style="height:calc(100vh - 120px);">

        {{-- Chat panel --}}
        <div class="col-lg-9 d-flex flex-column h-100">
            <div class="card border-0 shadow-sm d-flex flex-column h-100">
                {{-- Header --}}
                <div class="card-header d-flex align-items-center justify-content-between py-2">
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('chat-rooms.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i>
                        </a>
                        <div>
                            <div class="fw-bold">{{ $chatRoom->name }}</div>
                            <small class="text-muted"><i class="bi bi-people me-1"></i>{{ $chatRoom->members_count }} members</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        @if($member->role !== 'admin')
                        <form action="{{ route('chat-rooms.leave', $chatRoom->slug) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Leave this room?')">
                                <i class="bi bi-box-arrow-right"></i> Leave
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                {{-- Messages --}}
                <div class="card-body overflow-auto flex-grow-1 p-3" id="messagesContainer" style="background:#f8f9fa;">
                    @foreach($messages as $message)
                    <div class="d-flex gap-2 mb-3 {{ $message->user_id === auth()->id() ? 'flex-row-reverse' : '' }}">
                        <div class="rounded-circle bg-{{ $message->user_id === auth()->id() ? 'primary' : 'secondary' }} text-white d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:32px;height:32px;font-size:0.75rem;align-self:flex-end;">
                            {{ strtoupper(substr($message->author->name, 0, 1)) }}
                        </div>
                        <div style="max-width:70%;">
                            @if($message->user_id !== auth()->id())
                            <div class="small text-muted mb-1">{{ $message->author->name }}</div>
                            @endif
                            <div class="p-2 rounded-3 {{ $message->user_id === auth()->id() ? 'bg-primary text-white' : 'bg-white border' }}"
                                 style="word-break:break-word;">
                                {{ $message->content }}
                            </div>
                            <div class="small text-muted mt-1 {{ $message->user_id === auth()->id() ? 'text-end' : '' }}">
                                {{ $message->created_at->format('H:i') }}
                            </div>
                        </div>
                    </div>
                    @endforeach
                    <div id="messagesEnd"></div>
                </div>

                {{-- Message input --}}
                <div class="card-footer p-2">
                    @if(!$member->is_muted)
                    <form id="sendForm" class="d-flex gap-2">
                        @csrf
                        <input type="text" id="messageInput" class="form-control form-control-sm"
                               placeholder="Type a message..." maxlength="1000" autocomplete="off">
                        <button type="submit" class="btn btn-primary btn-sm px-3">
                            <i class="bi bi-send"></i>
                        </button>
                    </form>
                    @else
                    <div class="text-muted small text-center py-1"><i class="bi bi-mic-mute me-1"></i>You are muted in this room.</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Members sidebar --}}
        <div class="col-lg-3 d-none d-lg-flex flex-column h-100">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header fw-semibold py-2">
                    <i class="bi bi-people me-2"></i>Members ({{ $chatRoom->members_count }})
                </div>
                <div class="card-body overflow-auto p-2">
                    @foreach($chatRoom->members()->with('user')->limit(50)->get() as $m)
                    <div class="d-flex align-items-center gap-2 py-1">
                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:28px;height:28px;font-size:0.7rem;">
                            {{ strtoupper(substr($m->user->name, 0, 1)) }}
                        </div>
                        <span class="small text-truncate">{{ $m->user->name }}</span>
                        @if($m->role !== 'member')
                        <span class="badge bg-warning text-dark ms-auto" style="font-size:0.6rem;">{{ $m->role }}</span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const roomSlug = @json($chatRoom->slug);
    const messagesUrl = '/chat-rooms/' + roomSlug + '/messages';
    const sendUrl = '/chat-rooms/' + roomSlug + '/send';
    const currentUserId = @json(auth()->id());
    const container = document.getElementById('messagesContainer');
    const form = document.getElementById('sendForm');
    const input = document.getElementById('messageInput');
    let lastId = @json($messages->last()?->id ?? 0);

    // Scroll to bottom
    function scrollBottom() {
        container.scrollTop = container.scrollHeight;
    }
    scrollBottom();

    function appendMessage(msg) {
        const isMe = msg.user_id === currentUserId;
        const html = `
        <div class="d-flex gap-2 mb-3 ${isMe ? 'flex-row-reverse' : ''}">
            <div class="rounded-circle bg-${isMe ? 'primary' : 'secondary'} text-white d-flex align-items-center justify-content-center flex-shrink-0"
                 style="width:32px;height:32px;font-size:.75rem;align-self:flex-end;">
                ${msg.user_name.charAt(0).toUpperCase()}
            </div>
            <div style="max-width:70%;">
                ${!isMe ? `<div class="small text-muted mb-1">${msg.user_name}</div>` : ''}
                <div class="p-2 rounded-3 ${isMe ? 'bg-primary text-white' : 'bg-white border'}" style="word-break:break-word;">
                    ${msg.content}
                </div>
                <div class="small text-muted mt-1 ${isMe ? 'text-end' : ''}">${msg.created_at}</div>
            </div>
        </div>`;
        const end = document.getElementById('messagesEnd');
        end.insertAdjacentHTML('beforebegin', html);
        scrollBottom();
    }

    // Send message
    if (form) {
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            const content = input.value.trim();
            if (!content) return;
            input.value = '';
            input.disabled = true;
            try {
                const resp = await fetch(sendUrl, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ content })
                });
                const data = await resp.json();
                if (data.id) {
                    lastId = data.id;
                    appendMessage({ ...data, user_id: currentUserId });
                }
            } catch(err) { console.error(err); }
            input.disabled = false;
            input.focus();
        });
    }

    // Poll for new messages every 3 seconds
    setInterval(async function() {
        try {
            const resp = await fetch(`${messagesUrl}?after=${lastId}`, {
                headers: { 'Accept': 'application/json' }
            });
            const msgs = await resp.json();
            msgs.forEach(msg => {
                if (msg.user_id !== currentUserId) {
                    appendMessage(msg);
                }
                lastId = Math.max(lastId, msg.id);
            });
        } catch(e) {}
    }, 3000);
})();
</script>
@endpush
@endsection
