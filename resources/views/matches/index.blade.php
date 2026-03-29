@extends('layouts.app')
@section('title', 'My Matches')
@section('content')
<div class="container py-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-hearts text-danger me-2"></i>My Matches <span class="badge bg-primary">{{ $matches->total() }}</span></h4>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($matches->isEmpty())
    <div class="text-center py-5">
        <div class="display-1 mb-3">💔</div>
        <h5>No matches yet</h5>
        <p class="text-muted">Start liking profiles to find your matches!</p>
        <a href="{{ route('swipe.deck') }}" class="btn btn-primary"><i class="bi bi-fire me-2"></i>Start Swiping</a>
    </div>
    @else
    <div class="row g-3">
        @foreach($matches as $match)
        @php $other = $match->getOtherUser(auth()->id()) @endphp
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100 profile-card">
                <div class="ratio ratio-1x1 overflow-hidden">
                    @if($other->primaryPhoto)
                    <img src="{{ $other->primaryPhoto->thumbnail_url }}" class="object-fit-cover w-100 h-100" alt="{{ $other->name }}">
                    @else
                    <div class="bg-light d-flex align-items-center justify-content-center"><i class="bi bi-person-circle display-3 text-muted"></i></div>
                    @endif
                </div>
                <div class="card-body p-2">
                    <div class="fw-semibold">{{ $other->name }}, {{ $other->age }}</div>
                    @if($match->lastMessage)
                    <p class="mb-0 text-muted text-truncate" style="font-size:.75rem">{{ $match->lastMessage->body }}</p>
                    @else
                    <p class="mb-0 text-muted" style="font-size:.75rem"><em>No messages yet — say hi!</em></p>
                    @endif
                </div>
                <div class="card-footer bg-transparent p-2 d-flex gap-1">
                    @if($other->username)
                    <a href="{{ route('profile.show', $other->username) }}" class="btn btn-outline-secondary btn-sm flex-fill"><i class="bi bi-person"></i></a>
                    @endif
                    @if($match->conversation)
                    <a href="{{ route('conversations.show', $match->conversation->id) }}" class="btn btn-primary btn-sm flex-fill"><i class="bi bi-chat-heart"></i></a>
                    @else
                    <a href="{{ route('conversations.index') }}" class="btn btn-primary btn-sm flex-fill"><i class="bi bi-chat-heart"></i></a>
                    @endif
                    <button type="button" class="btn btn-outline-danger btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#unmatchModal{{ $match->id }}"
                            title="Unmatch">
                        <i class="bi bi-heartbreak"></i>
                    </button>
                </div>
            </div>
        </div>

        {{-- Unmatch confirm modal --}}
        <div class="modal fade" id="unmatchModal{{ $match->id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-sm">
            <div class="modal-content">
              <div class="modal-header border-0 pb-0">
                <h5 class="modal-title text-danger"><i class="bi bi-heartbreak me-2"></i>Unmatch {{ $other->name }}?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body pt-2">
                <p class="text-muted small mb-0">This will end your match and you won't be able to message each other. This cannot be undone.</p>
              </div>
              <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <form method="POST" action="{{ route('matches.unmatch', $match->id) }}" class="d-inline">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-danger btn-sm"><i class="bi bi-heartbreak me-1"></i>Yes, Unmatch</button>
                </form>
              </div>
            </div>
          </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4 d-flex justify-content-center">{{ $matches->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
