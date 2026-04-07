@extends('layouts.app')
@section('title', 'Stories')
@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h4 class="fw-bold mb-0"><i class="bi bi-camera-video text-warning me-2"></i>Stories</h4>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addStoryModal">
            <i class="bi bi-plus-circle me-1"></i>Add Story
        </button>
    </div>

    @if(session('success'))<div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif
    @if(session('error'))<div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>@endif

    {{-- Story Circles Row --}}
    @if($stories->isEmpty())
    <div class="card border-0 shadow-sm text-center py-5">
        <div class="display-1 mb-3">📸</div>
        <h5>No stories yet</h5>
        <p class="text-muted">Your matches' stories will appear here. Add your own story above!</p>
    </div>
    @else
    <div class="row g-4">
        @foreach($stories as $userId => $userStories)
        @php $storyUser = $userStories->first()->user; $first = $userStories->first(); @endphp
        <div class="col-auto">
            <div class="story-circle text-center" style="cursor:pointer" data-bs-toggle="modal" data-bs-target="#storyModal{{ $userId }}">
                <div class="story-ring {{ $storyUser->id === auth()->id() ? 'story-ring-mine' : '' }} mx-auto mb-1"
                     style="width:68px;height:68px;border-radius:50%;padding:3px;background:linear-gradient(135deg,#f09433 0%,#e6683c 25%,#dc2743 50%,#cc2366 75%,#bc1888 100%)">
                    @if($storyUser->primaryPhoto)
                    <img src="{{ $storyUser->primaryPhoto->thumbnail_url }}"
                         class="rounded-circle object-fit-cover" width="62" height="62"
                         style="border:3px solid var(--bs-body-bg)"
                         alt="{{ $storyUser->name }}">
                    @else
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center" style="width:62px;height:62px;border:3px solid var(--bs-body-bg)">
                        <i class="bi bi-person-fill text-white fs-4"></i>
                    </div>
                    @endif
                </div>
                <div style="font-size:.72rem;max-width:70px" class="text-truncate">{{ $storyUser->name }}</div>
            </div>

            {{-- Own story view count badge --}}
            @if($storyUser->id === auth()->id())
            <div class="text-center mt-1" style="font-size:.68rem;color:#888">
                <span class="story-vc-{{ $userId }}">👁 {{ $userStories->sum('views_count') }}</span>
            </div>
            @endif

            {{-- Story Modal --}}
            <div class="modal fade" id="storyModal{{ $userId }}" tabindex="-1"
                 data-owner-id="{{ $userId }}" data-current-user="{{ auth()->id() }}">
                <div class="modal-dialog modal-dialog-centered" style="max-width:400px">
                    <div class="modal-content border-0 bg-black">
                        <div class="modal-body p-0 position-relative" style="height:600px">
                            @foreach($userStories as $i => $story)
                            <div class="story-slide {{ $i === 0 ? 'd-flex' : 'd-none' }} align-items-center justify-content-center h-100 flex-column"
                                 data-index="{{ $i }}" data-total="{{ $userStories->count() }}"
                                 data-story-id="{{ $story->id }}"
                                 data-is-mine="{{ $story->user_id === auth()->id() ? '1' : '0' }}"
                                 data-view-count="{{ $story->views_count }}">
                                @if($story->media_type === 'image')
                                <img src="{{ asset('storage/'.$story->media_path) }}" class="mw-100 mh-100 object-fit-contain">
                                @else
                                <video src="{{ asset('storage/'.$story->media_path) }}" class="mw-100 mh-100 object-fit-contain" autoplay muted loop></video>
                                @endif
                                @if($story->caption)
                                <div class="position-absolute bottom-0 start-0 end-0 p-3 text-white fw-semibold" style="background:linear-gradient(transparent,rgba(0,0,0,.7))">
                                    {{ $story->caption }}
                                </div>
                                @endif
                                {{-- Story header --}}
                                <div class="position-absolute top-0 start-0 end-0 p-3 d-flex align-items-center gap-2" style="background:linear-gradient(rgba(0,0,0,.5),transparent)">
                                    <span class="text-white fw-semibold small">{{ $storyUser->name }}</span>
                                    <span class="text-white-50" style="font-size:.7rem">{{ $story->created_at->diffForHumans() }}</span>
                                    @if($story->user_id === auth()->id())
                                    <button class="btn btn-sm btn-outline-light py-0 ms-1"
                                            onclick="showViewers({{ $story->id }}, event)" title="Who viewed?">
                                        <i class="bi bi-eye"></i> <span id="vc-{{ $story->id }}">{{ $story->views_count }}</span>
                                    </button>
                                    <form method="POST" action="{{ route('stories.destroy', $story->id) }}" class="ms-1" onsubmit="return confirm('Delete this story?');">@csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-light py-0"><i class="bi bi-trash"></i></button>
                                    </form>
                                    @endif
                                </div>
                                {{-- Progress dots --}}
                                <div class="position-absolute" style="top:8px;left:12px;right:12px;display:flex;gap:4px">
                                    @for($j = 0; $j < $userStories->count(); $j++)
                                    <div style="flex:1;height:3px;border-radius:2px;background:{{ $j <= $i ? 'white' : 'rgba(255,255,255,.4)' }}"></div>
                                    @endfor
                                </div>
                            </div>
                            @endforeach
                            {{-- Tap to advance --}}
                            <div class="position-absolute top-0 start-50 end-0 h-100" style="cursor:pointer" onclick="advanceStory(this.closest('.modal'))"></div>
                            <button class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal"></button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Add Story Modal --}}
<div class="modal fade" id="addStoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold"><i class="bi bi-camera-video me-2"></i>Add a Story</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('stories.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Photo or Video <span class="text-danger">*</span></label>
                        <input type="file" name="media" class="form-control" accept="image/*,video/mp4" required>
                        <div class="form-text">Max 20MB · JPG, PNG, GIF, WebP, MP4 · Disappears in 24h</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Caption <span class="text-muted">(optional)</span></label>
                        <input type="text" name="caption" class="form-control" maxlength="120" placeholder="Say something…">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i>Post Story</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ── Story view tracking ── */
const _viewedStories = new Set();

function trackView(storyId) {
    if (!storyId || _viewedStories.has(storyId)) return;
    _viewedStories.add(storyId);
    fetch(`/stories/${storyId}/view`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content },
    });
}

function advanceStory(modal) {
    const slides = modal.querySelectorAll('.story-slide');
    let current  = [...slides].findIndex(s => s.classList.contains('d-flex'));
    if (current < slides.length - 1) {
        slides[current].classList.replace('d-flex', 'd-none');
        const next = slides[current + 1];
        next.classList.replace('d-none', 'd-flex');
        trackView(next.dataset.storyId);
    } else {
        bootstrap.Modal.getInstance(modal).hide();
    }
}

/* Auto-track first slide when a story modal opens */
document.addEventListener('show.bs.modal', function(e) {
    const modal = e.target;
    if (!modal.id.startsWith('storyModal')) return;
    const firstSlide = modal.querySelector('.story-slide[data-index="0"]');
    if (firstSlide) trackView(firstSlide.dataset.storyId);
});

/* ── Who viewed? ── */
function showViewers(storyId, evt) {
    if (evt) evt.stopPropagation();
    const panel = document.getElementById('viewersPanel');
    const body  = document.getElementById('viewersList');
    body.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm"></div></div>';
    panel.classList.add('show');
    fetch(`/stories/${storyId}/viewers`)
        .then(r => r.json())
        .then(data => {
            if (!data.viewers.length) {
                body.innerHTML = '<p class="text-muted text-center py-3">No views yet</p>';
                return;
            }
            body.innerHTML = data.viewers.map(v => `
                <div class="d-flex align-items-center gap-2 mb-2">
                    <img src="${v.photo || '/img/default-avatar.png'}" width="40" height="40"
                         class="rounded-circle object-fit-cover" alt="${v.name}">
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">${v.name}</div>
                        <div class="text-muted" style="font-size:.7rem">${v.viewed_at}</div>
                    </div>
                </div>`).join('');
        })
        .catch(() => { body.innerHTML = '<p class="text-danger text-center py-3">Failed to load</p>'; });
}
</script>

{{-- Viewers slide-up side panel --}}
<div id="viewersPanel" style="position:fixed;bottom:0;right:0;width:300px;max-height:60vh;
     background:var(--bs-body-bg);border-radius:12px 0 0 0;box-shadow:-4px -4px 20px rgba(0,0,0,.15);
     transform:translateX(100%);transition:transform .3s ease;z-index:2000;display:flex;flex-direction:column"
     class="">
    <div class="d-flex align-items-center justify-content-between p-3 border-bottom">
        <strong>👁 Who Viewed</strong>
        <button class="btn-close" onclick="document.getElementById('viewersPanel').classList.remove('show')"></button>
    </div>
    <div id="viewersList" class="p-3 overflow-auto flex-grow-1"></div>
</div>
<style>#viewersPanel.show{transform:translateX(0)}</style>
@endpush
