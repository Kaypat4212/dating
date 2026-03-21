@extends('layouts.app')
@section('title', 'Waves Received')
@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center gap-2 mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-hand-wave text-warning me-2"></i>Waves Received</h4>
        <span class="badge bg-primary">{{ $waves->total() }}</span>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show" role="alert">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    @if($waves->isEmpty())
    <div class="card border-0 shadow-sm text-center py-5">
        <div class="display-1 mb-3">👋</div>
        <h5>No waves yet</h5>
        <p class="text-muted">Browse profiles and someone will wave back!</p>
        <a href="{{ route('discover.index') }}" class="btn btn-primary mx-auto" style="width:fit-content">Browse Profiles</a>
    </div>
    @else
    <div class="row g-3">
        @foreach($waves as $wave)
        @php $sender = $wave->sender; @endphp
        @if(!$sender) @continue @endif
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100 {{ !$wave->seen ? 'border-start border-3 border-warning' : '' }}">
                <div class="ratio ratio-1x1">
                    @if($sender->primaryPhoto)
                    <img src="{{ $sender->primaryPhoto->thumbnail_url }}" class="card-img-top object-fit-cover" alt="{{ $sender->name }}">
                    @else
                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center"><i class="bi bi-person-circle display-4 text-muted"></i></div>
                    @endif
                </div>
                <div class="card-body p-2">
                    <div class="fw-semibold">{{ $sender->name }} <span class="fs-5">{{ $wave->emoji }}</span></div>
                    <div class="text-muted" style="font-size:.75rem">{{ $wave->created_at->diffForHumans() }}</div>
                    @if(!$wave->seen)<span class="badge bg-warning text-dark">New</span>@endif
                    <div class="mt-2 d-flex gap-1">
                        @if($sender && $sender->username)
                        <a href="{{ route('profile.show', $sender->username) }}" class="btn btn-sm btn-outline-primary flex-fill">View</a>
                        @endif
                        {{-- Wave back --}}
                        <button class="btn btn-sm btn-warning wave-back-btn" data-user="{{ $sender->id }}" title="Wave back 👋">👋</button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">{{ $waves->links() }}</div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Mark all waves as seen
fetch('{{ route('wave.seen') }}', {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
});

// Wave back buttons
document.querySelectorAll('.wave-back-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const userId = btn.dataset.user;
        const csrf   = document.querySelector('meta[name="csrf-token"]').content;
        const res    = await fetch(`{{ url('wave') }}/${userId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' },
            body: JSON.stringify({ emoji: '👋' })
        });
        if (res.ok) {
            btn.textContent = '✅';
            btn.disabled = true;
        }
    });
});
</script>
@endpush
