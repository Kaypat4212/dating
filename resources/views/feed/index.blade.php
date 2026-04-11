@extends('layouts.app')

@section('title', 'Feed')

@section('content')
<div class="container-xl py-4">
<div class="row g-4 justify-content-center">

    {{-- ── Left sidebar (desktop) ─────────────────────────────────────────── --}}
    <div class="col-lg-3 d-none d-lg-block">
        <div class="sticky-top" style="top:80px">
            {{-- Current user card --}}
            <div class="card border-0 shadow-sm rounded-4 mb-3">
                <div class="card-body d-flex align-items-center gap-3">
                    @if(auth()->user()->primaryPhoto)
                        <img src="{{ auth()->user()->primaryPhoto->thumbnail_url }}"
                             class="rounded-circle object-fit-cover flex-shrink-0"
                             width="52" height="52" alt="me">
                    @else
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:52px;height:52px">
                            <i class="bi bi-person-fill text-white fs-4"></i>
                        </div>
                    @endif
                    <div class="overflow-hidden">
                        <div class="fw-semibold text-truncate">{{ auth()->user()->name }}</div>
                        <a href="{{ route('profile.show', auth()->user()->username ?? auth()->user()->id) }}"
                           class="text-muted text-decoration-none small">{{ '@' . (auth()->user()->username ?? 'user' . auth()->user()->id) }}</a>
                    </div>
                </div>
            </div>
            {{-- Streak card --}}
            @if(auth()->user()->login_streak > 0)
            <div class="card border-0 shadow-sm rounded-4 mb-3"
                 style="background:linear-gradient(135deg,#ff6b2b,#e91e8c);color:#fff">
                <div class="card-body d-flex align-items-center gap-3">
                    <span style="font-size:2rem">🔥</span>
                    <div>
                        <div class="fw-bold" style="font-size:1.3rem">{{ auth()->user()->login_streak }}-day streak</div>
                        <div style="font-size:.78rem;opacity:.85">Keep logging in daily!</div>
                    </div>
                </div>
            </div>
            @endif
            {{-- Quick links --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="list-group list-group-flush rounded-4">
                    <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2 px-3 border-0">
                        <i class="bi bi-house-heart text-primary"></i><span class="small">Home</span>
                    </a>
                    <a href="{{ route('stories.index') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2 px-3 border-0">
                        <i class="bi bi-camera-video text-warning"></i><span class="small">Stories</span>
                    </a>
                    <a href="{{ route('discover.index') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2 px-3 border-0">
                        <i class="bi bi-search-heart text-danger"></i><span class="small">Browse</span>
                    </a>
                    <a href="{{ route('matches.index') }}" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2 px-3 border-0">
                        <i class="bi bi-hearts text-success"></i><span class="small">Matches</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Main feed column ────────────────────────────────────────────────── --}}
    <div class="col-12 col-sm-10 col-md-8 col-lg-6">

        {{-- ── Create post card ──────────────────────────────────────────── --}}
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3">
                <div class="d-flex align-items-center gap-3">
                    @if(auth()->user()->primaryPhoto)
                        <img src="{{ auth()->user()->primaryPhoto->thumbnail_url }}"
                             class="rounded-circle flex-shrink-0 object-fit-cover"
                             width="42" height="42" alt="me">
                    @else
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:42px;height:42px">
                            <i class="bi bi-person-fill text-white"></i>
                        </div>
                    @endif
                    <button class="btn btn-outline-secondary rounded-pill flex-grow-1 text-start text-muted px-3 py-2"
                            data-bs-toggle="modal" data-bs-target="#createPostModal"
                            style="font-size:.9rem">
                        What's on your mind, {{ auth()->user()->name }}?
                    </button>
                </div>
                <hr class="my-2">
                <div class="d-flex justify-content-between">
                    <button class="btn btn-sm text-muted d-flex align-items-center gap-2"
                            data-bs-toggle="modal" data-bs-target="#createPostModal"
                            data-trigger="photo">
                        <i class="bi bi-image text-success fs-5"></i>
                        <span class="d-none d-sm-inline small">Photo/Video</span>
                    </button>
                    <button class="btn btn-sm text-muted d-flex align-items-center gap-2"
                            data-bs-toggle="modal" data-bs-target="#createPostModal">
                        <i class="bi bi-emoji-smile text-warning fs-5"></i>
                        <span class="d-none d-sm-inline small">Feeling</span>
                    </button>
                    <button class="btn btn-sm text-muted d-flex align-items-center gap-2"
                            data-bs-toggle="modal" data-bs-target="#createPostModal">
                        <i class="bi bi-geo-alt text-danger fs-5"></i>
                        <span class="d-none d-sm-inline small">Location</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="alert alert-success alert-dismissible rounded-4 small py-2 px-3 mb-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert"></button>
        </div>
        @endif

        {{-- ── Post list ─────────────────────────────────────────────────── --}}
        <div id="feedList">
        @forelse($posts as $post)
            @include('feed._post', ['post' => $post, 'likedIds' => $likedIds, 'repostedIds' => $repostedIds])
        @empty
        <div class="text-center py-5">
            <div style="font-size:3.5rem">📭</div>
            <h5 class="fw-bold mt-3">Nothing here yet</h5>
            <p class="text-muted small">Be the first to post something!</p>
            <button class="btn btn-primary rounded-pill mt-2" data-bs-toggle="modal" data-bs-target="#createPostModal">
                Create first post
            </button>
        </div>
        @endforelse
        </div>

        {{-- Pagination --}}
        @if($posts->hasMorePages())
        <div class="text-center mt-4 pb-3">
            <button id="loadMoreBtn" class="btn btn-outline-primary rounded-pill px-4"
                    data-url="{{ $posts->nextPageUrl() }}">
                <i class="bi bi-arrow-down-circle me-1"></i>Load more
            </button>
        </div>
        @endif

    </div>{{-- /main column --}}

    {{-- ── Right sidebar (desktop) ────────────────────────────────────────── --}}
    <div class="col-lg-3 d-none d-lg-block">
        <div class="sticky-top" style="top:80px">
            {{-- Suggested profiles placeholder --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3 text-muted" style="font-size:.75rem;letter-spacing:.05em;text-transform:uppercase">Suggested for you</h6>
                    <a href="{{ route('discover.index') }}" class="btn btn-outline-primary rounded-pill w-100 btn-sm">
                        <i class="bi bi-search-heart me-1"></i>Browse Profiles
                    </a>
                    <hr class="my-3">
                    <a href="{{ route('swipe.deck') }}" class="btn btn-primary rounded-pill w-100 btn-sm">
                        <i class="bi bi-fire me-1"></i>Start Swiping
                    </a>
                </div>
            </div>
        </div>
    </div>

</div>{{-- /row --}}
</div>{{-- /container --}}

{{-- ── Create Post Modal ──────────────────────────────────────────────────── --}}
<div class="modal fade" id="createPostModal" tabindex="-1" aria-labelledby="createPostModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:520px">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <div class="d-flex align-items-center gap-2 w-100">
                    @if(auth()->user()->primaryPhoto)
                        <img src="{{ auth()->user()->primaryPhoto->thumbnail_url }}"
                             class="rounded-circle flex-shrink-0 object-fit-cover"
                             width="38" height="38" alt="me">
                    @else
                        <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:38px;height:38px">
                            <i class="bi bi-person-fill text-white"></i>
                        </div>
                    @endif
                    <div>
                        <div class="fw-semibold" style="font-size:.9rem">{{ auth()->user()->name }}</div>
                        <span class="badge bg-secondary-subtle text-secondary" style="font-size:.65rem">Public post</span>
                    </div>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal"></button>
                </div>
            </div>
            <form method="POST" action="{{ route('feed.store') }}" enctype="multipart/form-data" id="createPostForm">
                @csrf
                <div class="modal-body pt-2">
                    <textarea name="body" id="postBody" class="form-control border-0 shadow-none p-0"
                              rows="4" placeholder="What's on your mind?"
                              maxlength="2000" style="resize:none;font-size:1.1rem"></textarea>
                    {{-- Media preview --}}
                    <div id="mediaPreviewWrap" class="mt-2 d-none">
                        <div class="position-relative d-inline-block">
                            <img id="mediaPreviewImg" src="" alt="preview"
                                 class="rounded-3 img-fluid" style="max-height:240px;object-fit:cover">
                            <video id="mediaPreviewVid" src="" class="rounded-3 d-none"
                                   controls style="max-height:240px;max-width:100%"></video>
                            <button type="button" id="removeMedia"
                                    class="btn btn-sm btn-dark position-absolute top-0 end-0 m-1 rounded-circle"
                                    style="width:28px;height:28px;padding:0;line-height:1">
                                <i class="bi bi-x" style="font-size:.85rem"></i>
                            </button>
                        </div>
                    </div>
                    <input type="file" name="media" id="mediaInput" class="d-none"
                           accept="image/jpeg,image/png,image/gif,image/webp,video/mp4">
                </div>
                <div class="modal-footer border-0 pt-0 d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-1">
                        <button type="button" class="btn btn-sm text-muted rounded-pill" id="addMediaBtn"
                                onclick="document.getElementById('mediaInput').click()">
                            <i class="bi bi-image text-success fs-5"></i>
                        </button>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-muted" id="charCount">0 / 2000</small>
                        <button type="submit" class="btn btn-primary rounded-pill px-4" id="postSubmitBtn">
                            Post
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

// ── Create post: char counter + media preview ─────────────────────────────
const postBody    = document.getElementById('postBody');
const charCount   = document.getElementById('charCount');
const mediaInput  = document.getElementById('mediaInput');
const previewWrap = document.getElementById('mediaPreviewWrap');
const previewImg  = document.getElementById('mediaPreviewImg');
const previewVid  = document.getElementById('mediaPreviewVid');
const removeBtn   = document.getElementById('removeMedia');

if (postBody) {
    postBody.addEventListener('input', () => {
        charCount.textContent = postBody.value.length + ' / 2000';
    });
}

if (mediaInput) {
    mediaInput.addEventListener('change', () => {
        const file = mediaInput.files[0];
        if (!file) return;
        const url = URL.createObjectURL(file);
        if (file.type.startsWith('video')) {
            previewImg.classList.add('d-none');
            previewVid.src = url;
            previewVid.classList.remove('d-none');
        } else {
            previewVid.classList.add('d-none');
            previewImg.src = url;
            previewImg.classList.remove('d-none');
        }
        previewWrap.classList.remove('d-none');
    });
}

if (removeBtn) {
    removeBtn.addEventListener('click', () => {
        mediaInput.value = '';
        previewWrap.classList.add('d-none');
        previewImg.src = '';
        previewVid.src = '';
    });
}

// Open modal directly to file picker when clicking Photo/Video shortcut
document.querySelectorAll('[data-trigger="photo"]').forEach(btn => {
    btn.addEventListener('click', () => setTimeout(() => document.getElementById('mediaInput')?.click(), 350));
});

// ── Like a post ──────────────────────────────────────────────────────────────
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-like-post]');
    if (!btn) return;
    const postId = btn.dataset.likePost;
    const url    = `/feed/${postId}/like`;

    fetch(url, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
        const icon  = btn.querySelector('i');
        const count = btn.querySelector('[data-like-count]');
        if (data.liked) {
            btn.classList.add('text-danger');
            btn.classList.remove('text-muted');
            if (icon) { icon.classList.remove('bi-heart'); icon.classList.add('bi-heart-fill'); }
        } else {
            btn.classList.remove('text-danger');
            btn.classList.add('text-muted');
            if (icon) { icon.classList.remove('bi-heart-fill'); icon.classList.add('bi-heart'); }
        }
        if (count) count.textContent = data.count;
    });
});

// ── Repost ────────────────────────────────────────────────────────────────────
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-repost]');
    if (!btn) return;
    const postId = btn.dataset.repost;

    fetch(`/feed/${postId}/repost`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) { alert(data.error); return; }
        const icon  = btn.querySelector('i');
        const count = btn.querySelector('[data-repost-count]');
        if (data.reposted) {
            btn.classList.add('text-success');
            btn.classList.remove('text-muted');
        } else {
            btn.classList.remove('text-success');
            btn.classList.add('text-muted');
        }
        if (count) count.textContent = data.count;
    });
});

// ── Toggle comment section ────────────────────────────────────────────────────
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-toggle-comments]');
    if (!btn) return;
    const postId = btn.dataset.toggleComments;
    const section = document.getElementById('comments-' + postId);
    if (!section) return;
    section.classList.toggle('d-none');
    if (!section.classList.contains('d-none')) {
        section.querySelector('[data-comment-input]')?.focus();
    }
});

// ── Submit comment ────────────────────────────────────────────────────────────
document.addEventListener('submit', function (e) {
    const form = e.target.closest('[data-comment-form]');
    if (!form) return;
    e.preventDefault();
    const postId  = form.dataset.commentForm;
    const input   = form.querySelector('[data-comment-input]');
    const body    = input?.value?.trim();
    const parent  = form.dataset.parentId ?? '';
    if (!body) return;

    fetch(`/feed/${postId}/comment`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrf,
            'Accept':       'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ body, parent_id: parent || null }),
    })
    .then(r => r.json())
    .then(data => {
        if (!data.ok) return;
        const list = document.getElementById('comment-list-' + postId);
        if (list) list.insertAdjacentHTML('afterbegin', renderComment(data.comment, true));
        input.value = '';
        // update comment count badge
        const countEl = document.querySelector(`[data-comment-count="${postId}"]`);
        if (countEl) countEl.textContent = parseInt(countEl.textContent || 0) + 1;
    });
});

// ── Like a comment ────────────────────────────────────────────────────────────
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-like-comment]');
    if (!btn) return;
    const commentId = btn.dataset.likeComment;

    fetch(`/feed/comments/${commentId}/like`, {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
    })
    .then(r => r.json())
    .then(data => {
        const count = btn.querySelector('[data-clikes]');
        if (count) count.textContent = data.count || '';
        btn.classList.toggle('text-danger', data.liked);
        btn.classList.toggle('text-muted', !data.liked);
    });
});

// ── Delete post ───────────────────────────────────────────────────────────────
document.addEventListener('click', function (e) {
    const btn = e.target.closest('[data-delete-post]');
    if (!btn) return;
    if (!confirm('Delete this post?')) return;
    const postId = btn.dataset.deletePost;
    const card   = document.getElementById('post-' + postId);

    fetch(`/feed/${postId}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
    })
    .then(() => card?.remove());
});

// ── Load more (infinite scroll button) ───────────────────────────────────────
const loadMoreBtn = document.getElementById('loadMoreBtn');
if (loadMoreBtn) {
    loadMoreBtn.addEventListener('click', function () {
        const url = this.dataset.url;
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.text())
            .then(html => {
                const parser = new DOMParser();
                const doc    = parser.parseFromString(html, 'text/html');
                const items  = doc.querySelectorAll('.feed-post-card');
                const list   = document.getElementById('feedList');
                items.forEach(item => list.appendChild(item));
                const nextBtn = doc.getElementById('loadMoreBtn');
                if (nextBtn) {
                    loadMoreBtn.dataset.url = nextBtn.dataset.url;
                    loadMoreBtn.disabled    = false;
                    loadMoreBtn.innerHTML   = '<i class="bi bi-arrow-down-circle me-1"></i>Load more';
                } else {
                    loadMoreBtn.closest('.text-center')?.remove();
                }
            });
    });
}

function renderComment(c, highlight) {
    const photo = c.author.photo
        ? `<img src="${c.author.photo}" class="rounded-circle object-fit-cover flex-shrink-0" width="30" height="30" alt="">`
        : `<div class="rounded-circle bg-secondary flex-shrink-0" style="width:30px;height:30px"></div>`;

    return `<div class="d-flex gap-2 mb-2 align-items-start${highlight ? ' new-comment' : ''}" style="${highlight ? 'animation:fadeIn .3s' : ''}">
        ${photo}
        <div class="flex-grow-1">
            <div class="rounded-3 px-3 py-2" style="background:var(--bs-secondary-bg)">
                <a href="${c.author.url}" class="fw-semibold text-body text-decoration-none" style="font-size:.82rem">${c.author.name}</a>
                <p class="mb-0 mt-1" style="font-size:.88rem;white-space:pre-wrap">${c.body}</p>
            </div>
            <div class="d-flex gap-3 mt-1 ps-1">
                <button class="btn btn-link btn-sm p-0 text-muted" data-like-comment="${c.id}" style="font-size:.72rem">
                    ❤️ <span data-clikes>${c.likes || ''}</span>
                </button>
                <span class="text-muted" style="font-size:.72rem">${c.time}</span>
            </div>
        </div>
    </div>`;
}
</script>
<style>
@keyframes fadeIn { from { opacity:0; transform:translateY(-6px) } to { opacity:1; transform:none } }
.feed-post-card { animation: fadeIn .25s; }
.feed-action-btn { border:none;background:none;padding:0;font-size:.85rem;cursor:pointer; }
</style>
@endpush
