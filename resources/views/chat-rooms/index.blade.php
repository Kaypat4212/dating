@extends('layouts.app')
@section('title', 'Chat Rooms')
@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-chat-dots me-2 text-primary"></i>Chat Rooms</h2>
            <p class="text-muted small mb-0">Join public rooms or create your own — private rooms need an invite link</p>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#createRoomModal">
            <i class="bi bi-plus-lg me-1"></i>Create Room
        </button>
    </div>

    @if($myRooms->isNotEmpty())
    <h6 class="text-muted fw-semibold mb-2 text-uppercase small">My Rooms</h6>
    <div class="row g-3 mb-4">
        @foreach($myRooms as $room)
        <div class="col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100 border-start border-primary border-3">
                <div class="card-body py-2 d-flex align-items-center justify-content-between gap-2">
                    <a href="{{ route('chat-rooms.show', $room->slug) }}" class="text-decoration-none flex-grow-1 min-w-0">
                        <div class="fw-semibold text-dark text-truncate d-flex align-items-center gap-1">
                            @if($room->is_private)<i class="bi bi-lock-fill text-secondary" style="font-size:.75rem"></i>@endif
                            {{ $room->name }}
                        </div>
                        <small class="text-muted"><i class="bi bi-people me-1"></i>{{ $room->members_count }} members</small>
                    </a>
                    @if($room->is_private && $room->invite_token)
                    <button type="button" class="btn btn-sm btn-outline-secondary p-1 flex-shrink-0"
                            title="Copy invite link"
                            onclick="copyInviteLink('{{ url('/chat-rooms/join/' . $room->invite_token) }}', this)">
                        <i class="bi bi-link-45deg"></i>
                    </button>
                    @else
                    <button type="button" class="btn btn-sm btn-outline-secondary p-1 flex-shrink-0"
                            title="Copy share link"
                            onclick="copyInviteLink('{{ route('chat-rooms.show', $room->slug) }}', this)">
                        <i class="bi bi-share"></i>
                    </button>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <h6 class="text-muted fw-semibold mb-2 text-uppercase small">All Public Rooms</h6>
    @if($rooms->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-chat-square fs-1"></i>
        <p class="mt-2">No rooms yet. Create the first one!</p>
    </div>
    @else
    <div class="row g-3">
        @foreach($rooms as $room)
        <div class="col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:40px;height:40px;">
                            <i class="bi bi-chat-dots"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="fw-semibold text-truncate">{{ $room->name }}</div>
                            <small class="badge bg-secondary">{{ $room->type }}</small>
                        </div>
                    </div>
                    @if($room->description)
                    <p class="text-muted small flex-grow-1 mb-2">{{ Str::limit($room->description, 80) }}</p>
                    @endif
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <small class="text-muted"><i class="bi bi-people me-1"></i>{{ $room->members_count }}</small>
                        <div class="d-flex gap-1">
                            <button type="button" class="btn btn-outline-secondary btn-sm"
                                    title="Copy share link"
                                    onclick="copyInviteLink('{{ route('chat-rooms.show', $room->slug) }}', this)">
                                <i class="bi bi-share"></i>
                            </button>
                            <a href="{{ route('chat-rooms.show', $room->slug) }}" class="btn btn-outline-primary btn-sm">
                                Join <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4 d-flex justify-content-center">{{ $rooms->links() }}</div>
    @endif
</div>

{{-- Create Room Modal --}}
<div class="modal fade" id="createRoomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Create Chat Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('chat-rooms.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Room Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required minlength="3" maxlength="80"
                               placeholder="e.g. Singles in NYC, Dog Lovers...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="3" maxlength="500"
                                  placeholder="What's this room about?"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Type</label>
                        <select name="type" class="form-select" id="roomTypeSelect">
                            <option value="public">Public — Anyone can find and join</option>
                            <option value="private">Private — Invite link only</option>
                            <option value="interest">Interest-based</option>
                            <option value="location">Location-based</option>
                        </select>
                    </div>
                    <div class="alert alert-info py-2 px-3 small d-none" id="privateRoomNote">
                        <i class="bi bi-lock-fill me-1"></i>
                        <strong>Private Room:</strong> Only people with your invite link can join. You'll get a shareable link after creation.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Create Room</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('roomTypeSelect')?.addEventListener('change', function () {
    document.getElementById('privateRoomNote').classList.toggle('d-none', this.value !== 'private');
});

function copyInviteLink(url, btn) {
    navigator.clipboard.writeText(url).then(function () {
        var icon = btn.querySelector('i');
        var prev = icon.className;
        icon.className = 'bi bi-check-lg';
        setTimeout(function () { icon.className = prev; }, 2000);
    });
}
</script>
@endpush

@endsection
