{{-- Feed post card partial. Variables: $post, $likedIds, $repostedIds --}}
@php
    $me      = auth()->id();
    $isLiked = isset($likedIds[$post->id]);
    $isRep   = isset($repostedIds[$post->original_post_id ?? $post->id]);
    $isOwn   = $post->user_id === $me;
    $poster  = $post->user;
@endphp
<div class="card border-0 shadow-sm rounded-4 mb-4 feed-post-card" id="post-{{ $post->id }}">

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <div class="card-body pb-2">
        <div class="d-flex align-items-center gap-3">
            {{-- Avatar --}}
            <a href="{{ route('profile.show', $poster->username) }}" class="flex-shrink-0">
                @if($poster->primaryPhoto)
                    <img src="{{ $poster->primaryPhoto->thumbnail_url }}"
                         class="rounded-circle object-fit-cover"
                         width="44" height="44" alt="{{ $poster->name }}"
                         style="border:2px solid var(--bs-primary)">
                @else
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center"
                         style="width:44px;height:44px;border:2px solid var(--bs-border-color)">
                        <i class="bi bi-person-fill text-white fs-5"></i>
                    </div>
                @endif
            </a>
            {{-- Name / meta --}}
            <div class="flex-grow-1 overflow-hidden">
                <div class="fw-semibold text-truncate" style="font-size:.93rem">
                    <a href="{{ route('profile.show', $poster->username) }}"
                       class="text-body text-decoration-none">{{ $poster->name }}</a>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="text-muted" style="font-size:.72rem">
                        {{ $post->created_at->diffForHumans() }}
                    </span>
                    @if($post->original_post_id)
                        <span class="badge bg-success-subtle text-success rounded-pill" style="font-size:.6rem">
                            <i class="bi bi-repeat me-1"></i>Repost
                        </span>
                    @endif
                    <i class="bi bi-globe text-muted" style="font-size:.7rem" title="Public"></i>
                </div>
            </div>
            {{-- Options --}}
            @if($isOwn)
            <div class="dropdown ms-auto">
                <button class="btn btn-sm text-muted border-0" data-bs-toggle="dropdown">
                    <i class="bi bi-three-dots"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                    <li>
                        <button class="dropdown-item text-danger small"
                                data-delete-post="{{ $post->id }}">
                            <i class="bi bi-trash me-2"></i>Delete post
                        </button>
                    </li>
                </ul>
            </div>
            @endif
        </div>

        {{-- ── Body text ────────────────────────────────────────────────── --}}
        @if($post->body)
        <p class="mt-3 mb-0 post-body" style="font-size:.93rem;white-space:pre-wrap;line-height:1.55">{{ $post->body }}</p>
        @endif
    </div>

    {{-- ── Reposted original embed ──────────────────────────────────────── --}}
    @if($post->original_post_id && $post->originalPost)
    @php $orig = $post->originalPost; @endphp
    <div class="mx-3 mb-3 p-3 border rounded-3" style="background:var(--bs-secondary-bg)">
        <div class="d-flex align-items-center gap-2 mb-2">
            @if($orig->user->primaryPhoto)
                <img src="{{ $orig->user->primaryPhoto->thumbnail_url }}"
                     class="rounded-circle object-fit-cover flex-shrink-0"
                     width="28" height="28" alt="{{ $orig->user->name }}">
            @else
                <div class="rounded-circle bg-secondary flex-shrink-0" style="width:28px;height:28px"></div>
            @endif
            <a href="{{ route('profile.show', $orig->user->username) }}"
               class="fw-semibold text-body text-decoration-none" style="font-size:.82rem">
                {{ $orig->user->name }}
            </a>
            <span class="text-muted ms-auto" style="font-size:.68rem">{{ $orig->created_at->diffForHumans() }}</span>
        </div>
        @if($orig->body)
        <p class="mb-0" style="font-size:.85rem;white-space:pre-wrap">{{ $orig->body }}</p>
        @endif
        @if($orig->media_path)
        <div class="mt-2">
            @if($orig->media_type === 'video')
                <video src="{{ $orig->media_url }}" controls class="rounded-3 w-100" style="max-height:200px;object-fit:cover"></video>
            @else
                <img src="{{ $orig->media_url }}" alt="media"
                     class="rounded-3 w-100" style="max-height:200px;object-fit:cover">
            @endif
        </div>
        @endif
    </div>
    @endif

    {{-- ── Media ───────────────────────────────────────────────────────── --}}
    @if($post->media_path && !$post->original_post_id)
    <div style="max-height:480px;overflow:hidden">
        @if($post->media_type === 'video')
            <video src="{{ $post->media_url }}" controls class="w-100"
                   style="max-height:480px;object-fit:cover;display:block">
            </video>
        @else
            <img src="{{ $post->media_url }}" alt="post media"
                 class="w-100"
                 style="max-height:480px;object-fit:cover;display:block">
        @endif
    </div>
    @endif

    {{-- ── Counts bar ──────────────────────────────────────────────────── --}}
    @php $likeCount = $post->likes_count; $cmtCount = $post->comments_count; $repCount = $post->reposts_count; @endphp
    @if($likeCount || $cmtCount || $repCount)
    <div class="px-3 pt-2 pb-1 d-flex justify-content-between align-items-center">
        @if($likeCount)
        <span class="text-muted" style="font-size:.78rem">
            ❤️ {{ number_format($likeCount) }}
        </span>
        @else <span></span> @endif
        <div class="d-flex gap-3">
            @if($cmtCount)
            <button class="btn btn-link btn-sm p-0 text-muted text-decoration-none" style="font-size:.78rem"
                    data-toggle-comments="{{ $post->id }}">
                {{ number_format($cmtCount) }} {{ Str::plural('comment', $cmtCount) }}
            </button>
            @endif
            @if($repCount)
            <span class="text-muted" style="font-size:.78rem">
                {{ number_format($repCount) }} {{ Str::plural('repost', $repCount) }}
            </span>
            @endif
        </div>
    </div>
    @endif

    {{-- ── Action buttons ──────────────────────────────────────────────── --}}
    <div class="px-2 py-1 border-top d-flex">
        {{-- Like --}}
        <button class="btn flex-fill d-flex align-items-center justify-content-center gap-2 py-2
                       {{ $isLiked ? 'text-danger' : 'text-muted' }}"
                data-like-post="{{ $post->id }}"
                style="font-size:.85rem;border:none;background:none">
            <i class="bi {{ $isLiked ? 'bi-heart-fill' : 'bi-heart' }}"></i>
            <span class="d-none d-sm-inline">Like</span>
            <span data-like-count="{{ $post->id }}" style="font-size:.78rem">
                {{ $likeCount > 0 ? $likeCount : '' }}
            </span>
        </button>

        {{-- Comment --}}
        <button class="btn flex-fill d-flex align-items-center justify-content-center gap-2 py-2 text-muted"
                data-toggle-comments="{{ $post->id }}"
                style="font-size:.85rem;border:none;background:none">
            <i class="bi bi-chat"></i>
            <span class="d-none d-sm-inline">Comment</span>
            <span data-comment-count="{{ $post->id }}" style="font-size:.78rem">
                {{ $cmtCount > 0 ? $cmtCount : '' }}
            </span>
        </button>

        {{-- Repost --}}
        @if(!$isOwn)
        <button class="btn flex-fill d-flex align-items-center justify-content-center gap-2 py-2
                       {{ $isRep ? 'text-success' : 'text-muted' }}"
                data-repost="{{ $post->original_post_id ?? $post->id }}"
                style="font-size:.85rem;border:none;background:none">
            <i class="bi bi-repeat"></i>
            <span class="d-none d-sm-inline">{{ $isRep ? 'Reposted' : 'Repost' }}</span>
            <span data-repost-count="{{ $post->original_post_id ?? $post->id }}" style="font-size:.78rem">
                {{ $repCount > 0 ? $repCount : '' }}
            </span>
        </button>
        @endif

        {{-- Share --}}
        <button class="btn flex-fill d-flex align-items-center justify-content-center gap-2 py-2 text-muted"
                style="font-size:.85rem;border:none;background:none"
                onclick="navigator.share ? navigator.share({title:'Check this out!',url:window.location.href}) : navigator.clipboard?.writeText(window.location.href)">
            <i class="bi bi-box-arrow-up"></i>
            <span class="d-none d-sm-inline">Share</span>
        </button>
    </div>

    {{-- ── Comments section ─────────────────────────────────────────────── --}}
    <div id="comments-{{ $post->id }}" class="{{ $post->comments_count > 0 ? '' : 'd-none' }} border-top px-3 pt-3 pb-2">

        {{-- Comment input --}}
        <form data-comment-form="{{ $post->id }}" class="d-flex gap-2 mb-3 align-items-start">
            @if(auth()->user()->primaryPhoto)
                <img src="{{ auth()->user()->primaryPhoto->thumbnail_url }}"
                     class="rounded-circle flex-shrink-0 object-fit-cover mt-1"
                     width="32" height="32" alt="me">
            @else
                <div class="rounded-circle bg-secondary flex-shrink-0 mt-1"
                     style="width:32px;height:32px"></div>
            @endif
            <div class="flex-grow-1 d-flex gap-2">
                <input type="text"
                       data-comment-input
                       class="form-control rounded-pill"
                       placeholder="Write a comment…"
                       maxlength="1000"
                       style="font-size:.87rem">
                <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3">
                    Post
                </button>
            </div>
        </form>

        {{-- Existing comments --}}
        <div id="comment-list-{{ $post->id }}">
            @foreach($post->comments->take(3) as $comment)
            <div class="d-flex gap-2 mb-3 align-items-start">
                @if($comment->author->primaryPhoto)
                    <img src="{{ $comment->author->primaryPhoto->thumbnail_url }}"
                         class="rounded-circle flex-shrink-0 object-fit-cover"
                         width="30" height="30" alt="{{ $comment->author->name }}">
                @else
                    <div class="rounded-circle bg-secondary flex-shrink-0"
                         style="width:30px;height:30px"></div>
                @endif
                <div class="flex-grow-1">
                    <div class="rounded-3 px-3 py-2" style="background:var(--bs-secondary-bg)">
                        <a href="{{ route('profile.show', $comment->author->username) }}"
                           class="fw-semibold text-body text-decoration-none" style="font-size:.82rem">
                            {{ $comment->author->name }}
                        </a>
                        <p class="mb-0 mt-1" style="font-size:.88rem;white-space:pre-wrap">{{ $comment->body }}</p>
                    </div>
                    <div class="d-flex gap-3 mt-1 ps-1 align-items-center">
                        <button class="btn btn-link btn-sm p-0 text-muted" data-like-comment="{{ $comment->id }}"
                                style="font-size:.72rem;text-decoration:none">
                            ❤️ <span data-clikes>{{ $comment->likes_count ?: '' }}</span>
                        </button>
                        <span class="text-muted" style="font-size:.72rem">{{ $comment->created_at->diffForHumans() }}</span>
                        @if($comment->author->id === auth()->id())
                        <button class="btn btn-link btn-sm p-0 text-danger" style="font-size:.72rem;text-decoration:none"
                                onclick="if(confirm('Delete comment?')) fetch('/feed/comments/{{ $comment->id }}',{method:'DELETE',headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}'}}).then(()=>this.closest('.d-flex.gap-2.mb-3').remove())">
                            Delete
                        </button>
                        @endif
                    </div>
                    {{-- Replies --}}
                    @if($comment->replies->count())
                    <div class="ms-0 mt-2">
                        @foreach($comment->replies as $reply)
                        <div class="d-flex gap-2 mb-2 align-items-start">
                            @if($reply->author->primaryPhoto)
                                <img src="{{ $reply->author->primaryPhoto->thumbnail_url }}"
                                     class="rounded-circle flex-shrink-0 object-fit-cover"
                                     width="24" height="24" alt="{{ $reply->author->name }}">
                            @else
                                <div class="rounded-circle bg-secondary flex-shrink-0"
                                     style="width:24px;height:24px"></div>
                            @endif
                            <div>
                                <div class="rounded-3 px-3 py-1" style="background:var(--bs-secondary-bg)">
                                    <a href="{{ route('profile.show', $reply->author->username) }}"
                                       class="fw-semibold text-body text-decoration-none" style="font-size:.78rem">
                                        {{ $reply->author->name }}
                                    </a>
                                    <p class="mb-0" style="font-size:.83rem">{{ $reply->body }}</p>
                                </div>
                                <span class="text-muted ms-1" style="font-size:.67rem">{{ $reply->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Load more comments button --}}
        @if($post->comments_count > 3)
        <button class="btn btn-link btn-sm text-muted p-0 mb-2 d-block"
                style="font-size:.78rem;text-decoration:none"
                onclick="loadMoreComments({{ $post->id }}, this)">
            View all {{ $post->comments_count }} comments
        </button>
        @endif
    </div>

</div>{{-- /card --}}

{{-- Load more comments function (inlined once per post to avoid ID conflicts) --}}
<script>
function loadMoreComments(postId, btn) {
    btn.disabled = true;
    fetch(`/feed/${postId}/comments`, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            const list = document.getElementById('comment-list-' + postId);
            if (!list) return;
            list.innerHTML = data.comments.map(c => `
                <div class="d-flex gap-2 mb-3 align-items-start">
                    ${c.author.photo ? `<img src="${c.author.photo}" class="rounded-circle flex-shrink-0 object-fit-cover" width="30" height="30">` : `<div class="rounded-circle bg-secondary flex-shrink-0" style="width:30px;height:30px"></div>`}
                    <div class="flex-grow-1">
                        <div class="rounded-3 px-3 py-2" style="background:var(--bs-secondary-bg)">
                            <a href="${c.author.url}" class="fw-semibold text-body text-decoration-none" style="font-size:.82rem">${c.author.name}</a>
                            <p class="mb-0 mt-1" style="font-size:.88rem">${c.body}</p>
                        </div>
                        <div class="d-flex gap-3 mt-1 ps-1">
                            <button class="btn btn-link btn-sm p-0 text-muted" data-like-comment="${c.id}" style="font-size:.72rem;text-decoration:none">❤️ <span data-clikes>${c.likes || ''}</span></button>
                            <span class="text-muted" style="font-size:.72rem">${c.time}</span>
                        </div>
                    </div>
                </div>
            `).join('');
            btn.remove();
        });
}
</script>
