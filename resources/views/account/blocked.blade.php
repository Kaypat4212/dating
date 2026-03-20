@extends('layouts.app')
@section('title', 'Blocked Users')
@section('content')
<div class="container py-4" style="max-width:760px">
    <div class="d-flex align-items-center gap-3 mb-4">
        <a href="{{ route('account.show') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back
        </a>
        <h4 class="fw-bold mb-0"><i class="bi bi-slash-circle text-danger me-2"></i>Blocked Users</h4>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @if($blocks->isEmpty())
        <div class="card border-0 shadow-sm text-center py-5">
            <div class="display-1 mb-3">🚫</div>
            <h5>No blocked users</h5>
            <p class="text-muted">Users you block will appear here. You can unblock them any time.</p>
        </div>
    @else
        <p class="text-muted small mb-3">{{ $blocks->total() }} {{ Str::plural('user', $blocks->total()) }} blocked. Blocked users cannot like, message, or see your profile.</p>
        <div class="row g-3">
            @foreach($blocks as $block)
            @php $blocked = $block->blocked; @endphp
            @if(!$blocked) @continue @endif
            <div class="col-6 col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="ratio ratio-1x1 overflow-hidden bg-light">
                        @if($blocked->primaryPhoto)
                            <img src="{{ $blocked->primaryPhoto->thumbnail_url }}"
                                 class="object-fit-cover w-100 h-100" alt="{{ $blocked->name }}">
                        @else
                            <div class="d-flex align-items-center justify-content-center h-100">
                                <i class="bi bi-person-circle display-3 text-muted"></i>
                            </div>
                        @endif
                    </div>
                    <div class="card-body p-2">
                        <div class="fw-semibold text-truncate">{{ $blocked->name }}</div>
                        <div class="text-muted small">@{{ $blocked->username }}</div>
                    </div>
                    <div class="card-footer bg-transparent border-0 p-2 pt-0">
                        <form method="POST" action="{{ route('block.destroy', $blocked->id) }}"
                              onsubmit="return confirm('Unblock {{ $blocked->name }}?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger w-100">
                                <i class="bi bi-slash-circle me-1"></i>Unblock
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="mt-4 d-flex justify-content-center">
            {{ $blocks->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
