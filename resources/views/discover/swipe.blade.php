@extends('layouts.app')
@section('title', 'Swipe')
@section('content')
<div class="container py-4" style="max-width:520px">

    @include('partials.safety-banner')

    {{-- Fallback notice: no users found within distance preference --}}
    @if(!empty($fallbackToGlobal) && $fallbackToGlobal)
    <div class="alert alert-info border-0 rounded-3 d-flex align-items-center gap-2 mb-3 py-2 px-3" style="font-size:.85rem;background:#e8f4fd;">
        <i class="bi bi-geo-alt-fill text-info"></i>
        <span>No one found within your distance setting — showing profiles from farther away.
            <a href="{{ route('preferences.edit') }}" class="alert-link ms-1">Expand distance</a></span>
    </div>
    @endif

    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold mb-0"><i class="bi bi-fire text-danger me-2"></i>Discover</h5>
        <div class="d-flex align-items-center gap-2">
            {{-- Verified-only quick filter (only for verified users) --}}
            @if(auth()->user()->is_verified)
            <a href="{{ route('swipe.deck', array_merge(request()->query(), ['verified_only' => request('verified_only') ? 0 : 1])) }}"
               class="btn btn-sm rounded-pill d-inline-flex align-items-center gap-1 px-3 fw-semibold
                      {{ request('verified_only') ? 'btn-primary' : 'btn-outline-secondary' }}"
               style="font-size:.78rem;transition:all .2s"
               title="{{ request('verified_only') ? 'Showing verified members only — click to show all' : 'Show verified members only' }}">
                <i class="bi bi-patch-check-fill" style="color:{{ request('verified_only') ? '#fff' : '#1d9bf0' }};font-size:.85rem"></i>
                Verified
                @if(request('verified_only'))
                <span class="badge bg-white text-primary rounded-pill ms-1" style="font-size:.6rem">ON</span>
                @endif
            </a>
            @endif
            <a href="{{ route('discover.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-grid me-1"></i>Browse All
            </a>
        </div>
    </div>

    @if($profiles->isEmpty())
        <div class="card border-0 shadow-sm text-center p-5 rounded-4">
            <div class="display-1 mb-3">🌍</div>
            <h5 class="fw-bold">You've seen everyone nearby!</h5>
            <p class="text-muted">Check back later or expand your distance preference.</p>
            <a href="{{ route('preferences.edit') }}" class="btn btn-primary mx-auto" style="width:fit-content">
                <i class="bi bi-sliders me-2"></i>Adjust Preferences
            </a>
        </div>
    @else

    {{-- Card stack --}}
    <div id="swipe-stack" class="position-relative mb-4" style="height:520px">

        @foreach($profiles->reverse() as $index => $profile)
        @php
            $photo     = $profile->primaryPhoto;
            $prof      = $profile->profile;
            $age       = $profile->date_of_birth ? \Carbon\Carbon::parse($profile->date_of_birth)->age : null;
            $interests = $prof?->interests?->take(4) ?? collect();
        @endphp
        <div class="swipe-card position-absolute w-100 h-100 rounded-4 shadow overflow-hidden"
             data-user-id="{{ $profile->id }}"
             data-username="{{ $profile->username }}"
             data-name="{{ $profile->name }}"
             data-photo="{{ $photo?->url ?? '' }}"
             data-photos="{{ json_encode($profile->photos->where('is_approved', true)->values()->map(fn($p) => $p->url)) }}"
             style="background:#1a1a2e;cursor:grab;touch-action:none;will-change:transform;
                    {{ $loop->last ? '' : 'transform:scale('.( 1 - ($loop->remaining * 0.02) ).');top:'.($loop->remaining * 8).'px;z-index:'.$loop->index.';' }}
                    {{ $loop->last ? 'z-index:100;' : '' }}">

            {{-- Photo (swipeable gallery) --}}
            <div class="position-absolute inset-0 w-100 h-100">
                @if($photo)
                <img src="{{ $photo->url }}"
                     class="swipe-card-img w-100 h-100"
                     style="object-fit:cover;display:block"
                     alt="{{ $profile->name }}">
                @else
                <div class="w-100 h-100 d-flex align-items-center justify-content-center"
                     style="background:linear-gradient(135deg,#3a0a4a,#1a1a2e)">
                    <i class="bi bi-person-circle text-white opacity-25" style="font-size:8rem"></i>
                </div>
                @endif

                {{-- Photo tap zones (left 30% = prev, right 30% = next) --}}
                @php $allPhotos = $profile->photos->where('is_approved', true)->values(); @endphp
                @if($allPhotos->count() > 1)
                <div class="photo-tap-prev position-absolute top-0 start-0 h-100" style="width:30%;z-index:5"></div>
                <div class="photo-tap-next position-absolute top-0 end-0 h-100" style="width:30%;z-index:5"></div>
                {{-- Dot indicators --}}
                <div class="photo-dots position-absolute top-0 start-0 end-0 d-flex gap-1 justify-content-center pt-2" style="z-index:6">
                    @foreach($allPhotos as $di => $dp)
                    <div class="photo-dot {{ $di === 0 ? 'active' : '' }}" style="height:3px;border-radius:2px;background:rgba(255,255,255,{{ $di === 0 ? '1' : '.4' }});flex:1;max-width:40px;transition:background .2s"></div>
                    @endforeach
                </div>
                @endif

                {{-- Gradient overlay --}}
                <div class="position-absolute bottom-0 start-0 end-0"
                     style="height:65%;background:linear-gradient(to top,rgba(0,0,0,.92) 0%,transparent 100%);pointer-events:none"></div>
            </div>

            {{-- Like / Nope overlays --}}
            <div class="like-stamp position-absolute top-0 start-0 m-4 border border-3 border-success text-success fw-black px-3 py-1 rounded-3 opacity-0"
                 style="font-size:1.8rem;transform:rotate(-20deg);user-select:none">LIKE ❤️</div>
            <div class="nope-stamp position-absolute top-0 end-0 m-4 border border-3 border-danger text-danger fw-black px-3 py-1 rounded-3 opacity-0"
                 style="font-size:1.8rem;transform:rotate(20deg);user-select:none">NOPE ✕</div>

            {{-- Profile info --}}
            <div class="position-absolute bottom-0 start-0 end-0 p-3 text-white" style="pointer-events:none;z-index:10">
                <div class="d-flex align-items-end justify-content-between">
                    <div style="pointer-events:none">
                        <h4 class="fw-bold mb-0 d-flex align-items-center gap-2">
                            {{ $profile->name }}{{ $age ? ', '.$age : '' }}
                            @if($profile->is_verified)
                            <span title="ID Verified"
                                  style="display:inline-flex;align-items:center;justify-content:center;
                                         width:26px;height:26px;background:rgba(255,255,255,.15);
                                         border-radius:50%;backdrop-filter:blur(4px)">
                                <i class="bi bi-patch-check-fill" style="color:#60c5ff;font-size:.9rem"></i>
                            </span>
                            @endif
                        </h4>
                        @if($prof?->city || $prof?->country)
                        <small class="opacity-75">
                            <i class="bi bi-geo-alt me-1"></i>{{ implode(', ', array_filter([$prof->city, $prof->country])) }}
                        </small>
                        @endif
                        @if($prof?->headline)
                        <p class="mt-1 mb-0 opacity-90 small">{{ $prof->headline }}</p>
                        @endif
                        @if($interests->isNotEmpty())
                        <div class="d-flex flex-wrap gap-1 mt-2">
                            @foreach($interests as $int)
                            <span class="badge rounded-pill px-2 py-1"
                                  style="background:rgba(255,255,255,.15);font-size:.72rem">
                                {{ $int->icon ?? '' }} {{ $int->name }}
                            </span>
                            @endforeach
                        </div>
                        @endif
                        @if(($profile->compat_score ?? 0) > 0)
                        <div class="mt-2">
                            <span class="badge" style="background:rgba(99,102,241,.75);font-size:.72rem">
                                <i class="bi bi-magic me-1"></i>{{ $profile->compat_score }}% Match
                            </span>
                        </div>
                        @endif
                    </div>

                    {{-- Card action buttons (pointer-events re-enabled) --}}
                    <div class="d-flex flex-column gap-2 align-items-center" style="pointer-events:all">
                        {{-- Info toggle --}}
                        <button class="btn btn-light btn-sm rounded-circle shadow card-btn-info"
                                data-target="info-{{ $profile->id }}"
                                style="width:40px;height:40px" title="More info">
                            <i class="bi bi-info-lg"></i>
                        </button>
                        {{-- View full profile --}}
                        @if($profile->username)
                        <a href="{{ route('profile.show', $profile->username) }}"
                           class="btn btn-light btn-sm rounded-circle shadow"
                           style="width:40px;height:40px;display:flex;align-items:center;justify-content:center"
                           title="View full profile"
                           target="_blank">
                            <i class="bi bi-person-fill"></i>
                        </a>
                        @endif
                        {{-- Photos count (if > 1) --}}
                        @if($allPhotos->count() > 1)
                        <div class="badge rounded-pill"
                             style="background:rgba(0,0,0,.55);font-size:.7rem;pointer-events:none">
                            <i class="bi bi-images me-1"></i>{{ $allPhotos->count() }}
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Expanded bio (hidden by default) --}}
                <div id="info-{{ $profile->id }}" class="d-none mt-3 p-3 rounded-3"
                     style="background:rgba(0,0,0,.65);max-height:160px;overflow-y:auto;font-size:.88rem;pointer-events:all">
                    @if($prof?->bio)<p class="mb-2">{{ $prof->bio }}</p>@endif
                    <div class="row g-1 small opacity-75">
                        @if($prof?->body_type)<div class="col-6"><i class="bi bi-person me-1"></i>{{ ucfirst($prof->body_type) }}</div>@endif
                        @if($prof?->relationship_goal)<div class="col-6"><i class="bi bi-heart me-1"></i>{{ ucfirst($prof->relationship_goal) }}</div>@endif
                        @if($prof?->education)<div class="col-6"><i class="bi bi-mortarboard me-1"></i>{{ ucfirst(str_replace('_',' ',$prof->education)) }}</div>@endif
                        @if($prof?->occupation)<div class="col-6"><i class="bi bi-briefcase me-1"></i>{{ $prof->occupation }}</div>@endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- Action buttons --}}
    <div class="d-flex justify-content-center align-items-center gap-4 mb-4">
        <button id="btn-pass" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                style="width:64px;height:64px;font-size:1.6rem" title="Pass (←)">
            ✕
        </button>
        <button id="btn-super-like" class="btn btn-warning rounded-circle shadow-sm d-flex align-items-center justify-content-center"
                style="width:56px;height:56px;font-size:1.4rem" title="Super Like (↑)">
            ⭐
        </button>
        <button id="btn-like" class="btn btn-danger rounded-circle shadow d-flex align-items-center justify-content-center"
                style="width:76px;height:76px;font-size:1.9rem" title="Like (→)">
            ❤️
        </button>
        <a href="{{ route('discover.index') }}" class="btn btn-light rounded-circle shadow-sm d-flex align-items-center justify-content-center"
           style="width:52px;height:52px;font-size:1.2rem" title="Browse">
            <i class="bi bi-grid"></i>
        </a>
    </div>

    {{-- Match modal --}}
    <div class="modal fade" id="matchModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 rounded-4 text-center overflow-hidden">
                <div class="modal-body p-5" style="background:linear-gradient(135deg,#3a0a4a,#1a1a2e)">
                    <div class="d-flex justify-content-center gap-3 mb-3">
                        <div id="match-my-photo" class="rounded-circle overflow-hidden border border-3 border-warning"
                             style="width:72px;height:72px;background:#555;flex-shrink:0">
                            <img src="{{ auth()->user()->primaryPhoto?->thumbnail_url ?? '' }}" class="w-100 h-100 object-fit-cover" alt="" id="matchMyPhotoImg">
                        </div>
                        <div style="font-size:2rem;line-height:72px">💞</div>
                        <div id="match-their-photo" class="rounded-circle overflow-hidden border border-3 border-warning"
                             style="width:72px;height:72px;background:#555;flex-shrink:0">
                            <img src="" class="w-100 h-100 object-fit-cover" alt="" id="matchTheirPhotoImg" style="display:none">
                            <span id="matchPhotoFallback" style="color:#fff;font-size:2rem;line-height:72px">🧡</span>
                        </div>
                    </div>
                    <h3 class="fw-bold text-white">It's a Match!</h3>
                    <p class="text-white opacity-75">You and <span id="match-name" class="fw-bold"></span> liked each other.</p>

                    {{-- Icebreaker prompt --}}
                    <div class="mt-3 p-3 rounded-3 text-start" style="background:rgba(255,255,255,.1)">
                        <div class="text-white-50 small mb-1"><i class="bi bi-lightbulb me-1"></i>Icebreaker suggestion:</div>
                        <div id="icebreaker-prompt" class="text-white fw-semibold small fst-italic"></div>
                        <button class="btn btn-link btn-sm text-white-50 p-0 mt-1" id="refreshIcebreaker">
                            <i class="bi bi-arrow-clockwise me-1"></i>Different prompt
                        </button>
                    </div>

                    <div class="d-flex justify-content-center gap-3 mt-4">
                        <button class="btn btn-outline-light" data-bs-dismiss="modal">Keep Swiping</button>
                        <a href="{{ route('conversations.index') }}" class="btn btn-light fw-bold" id="matchChatBtn">
                            <i class="bi bi-chat-heart me-2"></i>Send a Message
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Like-with-message modal --}}
    <div class="modal fade" id="likeMessageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 rounded-4 overflow-hidden shadow-lg">
                <div class="modal-body p-4" style="background:linear-gradient(135deg,#1a1a2e,#3a0a4a)">
                    <div class="text-center mb-3">
                        <div id="likeModalPhoto" class="rounded-circle overflow-hidden mx-auto mb-3 border border-2 border-danger"
                             style="width:64px;height:64px;background:#444">
                            <img src="" id="likeModalImg" class="w-100 h-100 object-fit-cover" alt="">
                        </div>
                        <h6 class="fw-bold text-white mb-0">Like <span id="likeModalName"></span>?</h6>
                        <p class="text-white-50 small mt-1 mb-0">Add a note to stand out 💌</p>
                    </div>
                    <textarea id="likeMessageInput"
                              class="form-control bg-dark text-white border-secondary mb-3"
                              rows="3" maxlength="200"
                              placeholder="Say something nice… (optional)"
                              oninput="this.classList.remove('is-invalid');document.getElementById('likeNoteError')?.remove()"></textarea>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-outline-light btn-sm flex-fill" id="likeJustBtn">
                            ❤️ Just Like
                        </button>
                        <button type="button" class="btn btn-danger btn-sm flex-fill fw-bold" id="likeWithNoteBtn">
                            💌 Send Note
                        </button>
                    </div>
                    <button type="button" class="btn btn-link btn-sm text-white-50 d-block w-100 mt-2 text-center p-0"
                            data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast notification --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">
        <div id="swipe-toast" class="toast align-items-center text-white border-0 rounded-4" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div id="swipe-toast-body" class="toast-body fw-semibold fs-6"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    {{-- Daily like limit overlay --}}
    <div id="limit-overlay"
         style="display:none;position:fixed;inset:0;background:rgba(10,10,30,.92);z-index:3000;
                 flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:2rem">
        <div style="font-size:4rem" class="mb-3">⏳</div>
        <h4 class="fw-bold text-white mb-2">Daily Like Limit Reached</h4>
        <p class="text-white opacity-75 mb-3">You've used all your free likes today.<br>Your limit resets in:</p>
        <div class="display-4 fw-bold text-warning mb-3" id="swipe-countdown">--:--:--</div>
        <p class="text-white-50 small mb-4">We'll email you when your likes reset!</p>
        <a href="{{ route('premium.show') }}" class="btn btn-warning fw-semibold px-4 mb-2">
            <i class="bi bi-star-fill me-1"></i>Get Premium — Unlimited Likes
        </a>
        <a href="{{ route('discover.index') }}" class="btn btn-sm btn-outline-light mt-2">Browse Profiles Instead</a>
    </div>

    {{-- Admin restriction overlay --}}
    <div id="restriction-overlay"
         style="display:{{ ($isSwipeRestricted ?? false) ? 'flex' : 'none' }};position:fixed;inset:0;background:rgba(10,10,30,.92);z-index:3000;
                 flex-direction:column;align-items:center;justify-content:center;text-align:center;padding:2rem">
        <div style="font-size:4rem" class="mb-3">🚫</div>
        <h4 class="fw-bold text-white mb-2">Swipes Restricted</h4>
        <p class="text-white opacity-75">Your ability to swipe and send likes has been restricted by an administrator.</p>
        <a href="{{ route('pages.contact') }}" class="btn btn-outline-light mt-3">Contact Support</a>
    </div>

    @endif
</div>

<script>
const csrf    = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const likeUrl = '{{ url("like") }}';
let isAnimating = false;

const _toast     = document.getElementById('swipe-toast');
const _toastBody = document.getElementById('swipe-toast-body');
let   _bsToast   = null;

function showToast(msg, color = '#6f42c1') {
    if (!_toast) return;
    _toastBody.textContent = msg;
    _toast.style.background = color;
    if (!_bsToast) _bsToast = new bootstrap.Toast(_toast, { delay: 2200 });
    _bsToast.show();
}

// Cards are rendered bottom-first in DOM; the last DOM node is the visual top card.
function getTopCard() {
    const cards = Array.from(document.querySelectorAll('.swipe-card'))
        .filter(c => !c.classList.contains('d-none') && !c.dataset.gone);
    return cards.length ? cards[cards.length - 1] : null;
}

// After a card is removed, smoothly promote remaining cards to their new positions.
function updateStack() {
    const cards = Array.from(document.querySelectorAll('.swipe-card'))
        .filter(c => !c.classList.contains('d-none') && !c.dataset.gone);
    const total = cards.length;
    cards.forEach((card, i) => {
        const fromTop = total - 1 - i; // 0 = top card, 1 = second, …
        card.style.transition = 'transform .3s ease, top .3s ease';
        if (fromTop === 0) {
            card.style.transform = '';
            card.style.top       = '0px';
            card.style.zIndex    = '100';
        } else {
            card.style.transform = `scale(${1 - fromTop * 0.02})`;
            card.style.top       = `${fromTop * 8}px`;
            card.style.zIndex    = String(i);
        }
    });
}

async function swipe(direction) {
    if (isAnimating) return;
    const card = getTopCard();
    if (!card) return;
    isAnimating = true;

    const userId = card.dataset.userId;
    const isSuperLike = direction === 'super_like';
    const isLike      = direction === 'like' || isSuperLike;
    const xOut        = isLike ? window.innerWidth + 200
                               : direction === 'pass' ? -(window.innerWidth + 200)
                               : 0; // super_like flies up

    // Show stamp
    if (isSuperLike) {
        const stamp = card.querySelector('.like-stamp');
        if (stamp) { stamp.style.opacity = '1'; stamp.style.color = '#ffc107'; stamp.style.borderColor = '#ffc107'; stamp.textContent = 'SUPER ⭐'; }
    } else {
        const stamp = card.querySelector(isLike ? '.like-stamp' : '.nope-stamp');
        if (stamp) stamp.style.opacity = '1';
    }

    // Animate card out
    card.style.transition = 'transform .38s ease, opacity .38s ease';
    if (isSuperLike) {
        card.style.transform = 'translateY(-' + (window.innerHeight + 200) + 'px) scale(1.05)';
    } else {
        card.style.transform = `translateX(${xOut}px) rotate(${isLike ? 25 : -25}deg)`;
    }
    card.style.opacity = '0';
    card.dataset.gone  = '1';

    // Immediately promote the next card while current flies away
    updateStack();

    // Show pass toast immediately — no API call needed
    if (direction === 'pass') showToast('👋 Passed', '#6c757d');

    // API call for like / super_like only
    const apiCallPromise = isLike ? (async () => {
        try {
            const body = { action: 'like', super_like: isSuperLike };
            if (window._pendingLikeMessage) {
                body.like_message = window._pendingLikeMessage;
                window._pendingLikeMessage = null;
            }
            const res = await fetch(`${likeUrl}/${userId}`, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json', 'Content-Type': 'application/json' },
                body: JSON.stringify(body),
            });
            if (!res.ok) {
                const err = await res.json().catch(() => ({}));
                if (res.status === 429) {
                    showLimitOverlay(err.reset_at ? new Date(err.reset_at) : null);
                } else if (res.status === 403) {
                    document.getElementById('restriction-overlay').style.display = 'flex';
                } else {
                    showToast(err.error ?? '⚠️ Could not send like. Please try again.', '#dc3545');
                }
            } else {
                const data = await res.json();
                if (data.matched) {
                    document.getElementById('match-name').textContent = data.match_name ?? 'them';
                    // Update "Send a Message" to link directly to the new conversation
                    if (data.conversation_url) {
                        document.getElementById('matchChatBtn').href = data.conversation_url;
                    }
                    // Show their photo if available
                    if (data.match_photo) {
                        const img = document.getElementById('matchTheirPhotoImg');
                        img.src = data.match_photo;
                        img.style.display = '';
                        document.getElementById('matchPhotoFallback').style.display = 'none';
                    }
                    setTimeout(() => new bootstrap.Modal(document.getElementById('matchModal')).show(), 420);
                } else {
                    showToast(isSuperLike ? '⭐ Super Liked!' : '❤️ Liked!', isSuperLike ? '#ffc107' : '#6f42c1');
                }
            }
        } catch (e) {
            showToast('⚠️ Network error. Check your connection.', '#dc3545');
        }
    })() : Promise.resolve();

    setTimeout(async () => {
        card.remove();
        isAnimating = false;
        if (!getTopCard()) {
            await apiCallPromise; // make sure API finished before we reload
            fetchMoreCards();
        }
    }, 400);
}

async function fetchMoreCards() {
    try {
        const res  = await fetch('{{ route("swipe.fetch") }}', {
            headers: { 'Accept': 'application/json' }
        });
        const data = await res.json();
        if (!data.profiles?.length) {
            document.getElementById('swipe-stack').innerHTML =
                `<div class="text-center p-5 text-white"><div class="display-1">🌍</div>
                 <h5 class="mt-3 fw-bold">You've seen everyone nearby!</h5>
                 <p class="opacity-75">Check back later or adjust your preferences.</p>
                 <a href="{{ route('preferences.edit') }}" class="btn btn-primary mt-2">
                    <i class="bi bi-sliders me-2"></i>Adjust Preferences</a></div>`;
        } else {
            location.reload();
        }
    } catch (e) { /* silent */ }
}

document.getElementById('btn-like')?.addEventListener('click', () => {
    // Show like-with-message modal before sending the like
    const card = getTopCard();
    if (!card) return;
    const name  = card.dataset.name  || 'this person';
    const photo = card.dataset.photo || '';
    document.getElementById('likeModalName').textContent = name;
    const likeImg = document.getElementById('likeModalImg');
    if (photo) { likeImg.src = photo; likeImg.style.display = ''; }
    else { likeImg.style.display = 'none'; }
    document.getElementById('likeMessageInput').value = '';
    window._pendingLikeMessage = null;
    bootstrap.Modal.getOrCreateInstance(document.getElementById('likeMessageModal')).show();
});

// "Just Like" — no message
document.getElementById('likeJustBtn')?.addEventListener('click', () => {
    bootstrap.Modal.getInstance(document.getElementById('likeMessageModal'))?.hide();
    window._pendingLikeMessage = null;
    swipe('like');
});

// "Send Note" — like with message (requires non-empty note)
document.getElementById('likeWithNoteBtn')?.addEventListener('click', () => {
    const input = document.getElementById('likeMessageInput');
    const msg   = input.value.trim().slice(0, 200);
    if (!msg) {
        input.classList.add('is-invalid');
        let err = document.getElementById('likeNoteError');
        if (!err) {
            err = document.createElement('div');
            err.id = 'likeNoteError';
            err.className = 'invalid-feedback d-block text-warning small';
            err.textContent = 'Please write a note before sending.';
            input.after(err);
        }
        input.focus();
        return;
    }
    input.classList.remove('is-invalid');
    document.getElementById('likeNoteError')?.remove();
    window._pendingLikeMessage = msg;
    bootstrap.Modal.getInstance(document.getElementById('likeMessageModal'))?.hide();
    swipe('like');
});
document.getElementById('btn-pass')?.addEventListener('click', () => swipe('pass'));
document.getElementById('btn-super-like')?.addEventListener('click', () => swipe('super_like'));

// ── Touch / drag swipe ───────────────────────────────────────────────────────
(function initDrag() {
    const stack = document.getElementById('swipe-stack');
    if (!stack) return;

    let startX, startY, isDragging = false, currentCard = null;

    stack.addEventListener('pointerdown', e => {
        if (isAnimating) return;
        // Don't start a drag when the user clicks a button, link, or form element
        if (e.target.closest('button, a, form, input, select')) return;
        currentCard = getTopCard();
        if (!currentCard) return;
        startX = e.clientX; startY = e.clientY;
        isDragging = true;
        currentCard.style.transition = 'none';
        stack.setPointerCapture(e.pointerId);
    });

    stack.addEventListener('pointermove', e => {
        if (!isDragging || !currentCard) return;
        const dx  = e.clientX - startX;
        const rot = dx / 18;
        currentCard.style.transform = `translateX(${dx}px) rotate(${rot}deg)`;

        const likeS = currentCard.querySelector('.like-stamp');
        const nopeS = currentCard.querySelector('.nope-stamp');
        if (likeS) likeS.style.opacity = dx > 0 ? Math.min(dx / 70, 1) : 0;
        if (nopeS) nopeS.style.opacity = dx < 0 ? Math.min(-dx / 70, 1) : 0;

        // Peek-promote the card behind
        const cards = Array.from(document.querySelectorAll('.swipe-card'))
            .filter(c => !c.classList.contains('d-none') && !c.dataset.gone);
        const total = cards.length;
        if (total >= 2) {
            const pct = Math.min(Math.abs(dx) / 150, 1);
            const next = cards[total - 2]; // second from top
            const fromTop = 1;
            const targetScale = 1 - fromTop * 0.02 + pct * fromTop * 0.02;
            const targetTop   = fromTop * 8 - pct * fromTop * 8;
            next.style.transition = 'none';
            next.style.transform = `scale(${targetScale})`;
            next.style.top       = `${targetTop}px`;
        }
    });

    stack.addEventListener('pointerup', e => {
        if (!isDragging || !currentCard) return;
        isDragging = false;
        const dx = e.clientX - startX;
        if (dx > 80) {
            swipe('like');
        } else if (dx < -80) {
            swipe('pass');
        } else {
            // Snap back
            currentCard.style.transition = 'transform .3s ease';
            currentCard.style.transform  = '';
            const likeS = currentCard.querySelector('.like-stamp');
            const nopeS = currentCard.querySelector('.nope-stamp');
            if (likeS) likeS.style.opacity = '0';
            if (nopeS) nopeS.style.opacity = '0';
            updateStack(); // restore peeked card
        }
        currentCard = null;
    });
})();

// Keyboard shortcuts
document.addEventListener('keydown', e => {
    if (e.key === 'ArrowRight') swipe('like');
    if (e.key === 'ArrowLeft')  swipe('pass');
    if (e.key === 'ArrowUp')    swipe('super_like');
});

// ── Icebreakers ──────────────────────────────────────────────────────────────
const icebreakers = [
    "What's the most spontaneous thing you've ever done? 🎲",
    "If you could only eat one meal for the rest of your life, what would it be? 🍕",
    "Dogs or cats — and if neither, explain yourself 😄",
    "What's a skill you're secretly proud of? 🏆",
    "What's your idea of a perfect Sunday? ☀️",
    "If you could travel anywhere tomorrow, where would you go? ✈️",
    "Coffee, tea, or energy drink person? ☕",
    "What's the last thing that made you laugh out loud? 😂",
    "What's your most unpopular opinion? 🔥",
    "Are you more of a planner or a spontaneous adventurer? 🗺️",
    "What's a hidden gem in your city worth checking out? 💎",
    "What show are you currently bingeing? 📺",
    "Morning person or night owl? 🌙",
    "What's one thing on your bucket list? 🪣",
    "What's your love language? 💕",
];
let icebreakerIndex = Math.floor(Math.random() * icebreakers.length);

function setIcebreaker() {
    const el = document.getElementById('icebreaker-prompt');
    if (el) el.textContent = '"' + icebreakers[icebreakerIndex] + '"';
}

const matchModal = document.getElementById('matchModal');
if (matchModal) {
    matchModal.addEventListener('show.bs.modal', () => {
        icebreakerIndex = Math.floor(Math.random() * icebreakers.length);
        setIcebreaker();
    });
}

const refreshBtn = document.getElementById('refreshIcebreaker');
if (refreshBtn) {
    refreshBtn.addEventListener('click', () => {
        icebreakerIndex = (icebreakerIndex + 1) % icebreakers.length;
        setIcebreaker();
    });
}

// ── Like limit countdown ─────────────────────────────────────────────────────
let swipeCountdownInterval = null;

function showLimitOverlay(resetDate) {
    const overlay = document.getElementById('limit-overlay');
    if (overlay) overlay.style.display = 'flex';
    if (resetDate) {
        startSwipeCountdown(resetDate);
    } else {
        const el = document.getElementById('swipe-countdown');
        if (el) el.textContent = 'tomorrow';
    }
}

function startSwipeCountdown(resetDate) {
    if (swipeCountdownInterval) clearInterval(swipeCountdownInterval);
    function tick() {
        const diff = resetDate - Date.now();
        if (diff <= 0) {
            clearInterval(swipeCountdownInterval);
            const el = document.getElementById('swipe-countdown');
            if (el) el.textContent = '00:00:00';
            setTimeout(() => location.reload(), 1500);
            return;
        }
        const h = Math.floor(diff / 3600000).toString().padStart(2, '0');
        const m = Math.floor((diff % 3600000) / 60000).toString().padStart(2, '0');
        const s = Math.floor((diff % 60000) / 1000).toString().padStart(2, '0');
        const el = document.getElementById('swipe-countdown');
        if (el) el.textContent = h + ':' + m + ':' + s;
    }
    tick();
    swipeCountdownInterval = setInterval(tick, 1000);
}

// ── Auto-show limit overlay if user already hit their limit on page load ─────
@if(!empty($limitResetAt))
showLimitOverlay(new Date('{{ $limitResetAt }}'));
@endif
</script>
@endsection
