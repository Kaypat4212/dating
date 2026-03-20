@extends('layouts.app')
@section('title', 'Swipe')
@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="fw-bold mb-0"><i class="bi bi-fire text-danger me-2"></i>Swipe Mode</h5>
        <a href="{{ route('discover.index') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-grid me-1"></i>Grid View</a>
    </div>

    <div id="swipeDeck" class="swipe-deck mx-auto" style="max-width:420px;min-height:580px;position:relative">
        <div id="deckContainer"></div>
        <div id="emptyState" class="text-center py-5 d-none">
            <div class="display-1 mb-3">🎉</div>
            <h5>You have seen everyone nearby!</h5>
            <p class="text-muted">Check back later for new profiles.</p>
            <a href="{{ route('discover.index') }}" class="btn btn-primary">Browse All Profiles</a>
        </div>

        {{-- Limit reached overlay --}}
        <div id="limitOverlay" class="d-none text-center py-5 px-3">
            <div class="display-1 mb-3">⏳</div>
            <h5 class="fw-bold">Daily Like Limit Reached</h5>
            <p class="text-muted">You've used all 15 free likes today.<br>Your limit resets in:</p>
            <div class="display-4 fw-bold text-primary mb-3" id="countdownTimer">--:--:--</div>
            <p class="text-muted small">We'll email you when your likes reset!</p>
            <a href="{{ route('premium.show') }}" class="btn btn-warning fw-semibold mb-2">
                <i class="bi bi-star-fill me-1"></i>Get Premium — Unlimited Likes
            </a>
            <div><a href="{{ route('discover.index') }}" class="btn btn-sm btn-outline-secondary mt-2">Browse Profiles Instead</a></div>
        </div>

        {{-- Admin restriction overlay --}}
        <div id="restrictionOverlay" class="d-none text-center py-5 px-3">
            <div class="display-1 mb-3">🚫</div>
            <h5 class="fw-bold text-danger">Likes Restricted</h5>
            <p class="text-muted">Your ability to send likes has been restricted by an administrator.</p>
            <a href="{{ route('pages.contact') ?? '#' }}" class="btn btn-outline-secondary">Contact Support</a>
        </div>
    </div>

    <div class="d-flex justify-content-center gap-4 mt-4" id="actionButtons">
        <button class="btn btn-outline-secondary btn-lg rounded-circle shadow" style="width:60px;height:60px" id="btnPass" title="Pass">
            <i class="bi bi-x-lg text-danger fs-4"></i>
        </button>
        <button class="btn btn-outline-secondary btn-lg rounded-circle shadow" style="width:60px;height:60px" id="btnSuperLike" title="Super Like">
            <i class="bi bi-star-fill text-warning fs-4"></i>
        </button>
        <button class="btn btn-primary btn-lg rounded-circle shadow" style="width:60px;height:60px" id="btnLike" title="Like">
            <i class="bi bi-heart-fill text-white fs-4"></i>
        </button>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function(){
    let deck = [];
    let currentIndex = 0;
    const container = document.getElementById('deckContainer');
    const emptyState = document.getElementById('emptyState');
    const csrf = document.querySelector('meta[name="csrf-token"]').content;

    async function loadDeck() {
        const res = await fetch('{{ route("swipe.fetch") }}');
        deck = await res.json();
        currentIndex = 0;
        renderCurrent();
    }

    function renderCurrent() {
        container.innerHTML = '';
        if (currentIndex >= deck.length) {
            emptyState.classList.remove('d-none');
            document.getElementById('actionButtons').classList.add('d-none');
            return;
        }
        const p = deck[currentIndex];
        const card = document.createElement('div');
        card.className = 'swipe-card card border-0 shadow-lg rounded-4 overflow-hidden';
        card.style.cssText = 'position:absolute;width:100%;cursor:grab';
        card.innerHTML = `
            <div style="height:500px;background:#eee;overflow:hidden;position:relative">
                ${p.photo ? `<img src="${p.photo}" class="w-100 h-100 object-fit-cover">` : `<div class="w-100 h-100 d-flex align-items-center justify-content-center"><i class="bi bi-person-circle display-1 text-muted"></i></div>`}
                <div class="position-absolute bottom-0 start-0 end-0 p-3" style="background:linear-gradient(transparent,rgba(0,0,0,.7));color:#fff">
                    <h4 class="mb-0 fw-bold">${p.name}, ${p.age}</h4>
                    <div class="small opacity-75">${p.city || ''} ${p.distance_km ? '· ' + Math.round(p.distance_km) + ' km' : ''}</div>
                    ${p.tagline ? `<p class="mb-0 mt-1 small">${p.tagline}</p>` : ''}
                    ${p.compatibility ? `<div class="mt-2"><span class="badge bg-primary">${p.compatibility}% Match</span></div>` : ''}
                    <div class="mt-2 d-flex gap-2 align-items-center">
                        <button class="btn btn-light btn-sm rounded-circle shadow card-btn-info" data-info="${p.id}" style="width:36px;height:36px" title="More info">
                            <i class="bi bi-info-lg"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div id="info-${p.id}" class="d-none mt-2 p-3 rounded-3" style="background:rgba(0,0,0,.65);color:#fff;max-height:160px;overflow-y:auto;font-size:.95rem;">
                ${p.bio ? `<p class='mb-2'>${p.bio}</p>` : ''}
                <div class='row g-1 small opacity-75'>
                    ${p.body_type ? `<div class='col-6'><i class='bi bi-person me-1'></i>${p.body_type}</div>` : ''}
                    ${p.relationship_goal ? `<div class='col-6'><i class='bi bi-heart me-1'></i>${p.relationship_goal}</div>` : ''}
                    ${p.job_title ? `<div class='col-6'><i class='bi bi-briefcase me-1'></i>${p.job_title}</div>` : ''}
                    ${p.education ? `<div class='col-6'><i class='bi bi-mortarboard me-1'></i>${p.education}</div>` : ''}
                    ${p.height_cm ? `<div class='col-6'><i class='bi bi-arrows-vertical me-1'></i>${p.height_cm} cm</div>` : ''}
                    ${p.smoking ? `<div class='col-6'><i class='bi bi-wind me-1'></i>${p.smoking}</div>` : ''}
                    ${p.drinking ? `<div class='col-6'><i class='bi bi-cup-straw me-1'></i>${p.drinking}</div>` : ''}
                </div>
            </div>`;
        container.appendChild(card);
        // Info button toggle
        const infoBtn = card.querySelector('.card-btn-info');
        if (infoBtn) {
            infoBtn.addEventListener('click', function(e) {
                e.stopPropagation();
                const infoBox = card.querySelector(`#info-${p.id}`);
                if (infoBox) {
                    infoBox.classList.toggle('d-none');
                }
            });
        }
        // Swipe touch/mouse drag
        let startX = null;
        card.addEventListener('mousedown', e => { startX = e.clientX; });
        card.addEventListener('mouseup', e => {
            if (startX === null) return;
            const diff = e.clientX - startX;
            if (diff > 80) doLike();
            else if (diff < -80) doPass();
            startX = null;
        });
    }

    async function doLike() {
        const p = deck[currentIndex];
        card_animate('like');
        const res = await fetch(`/like/${p.id}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' }
        });

        if (res.status === 429) {
            const data = await res.json();
            showLimitOverlay(data.reset_at ? new Date(data.reset_at) : null);
            return;
        }

        if (res.status === 403) {
            document.getElementById('restrictionOverlay').classList.remove('d-none');
            document.getElementById('deckContainer').classList.add('d-none');
            document.getElementById('actionButtons').classList.add('d-none');
            return;
        }

        if (res.ok) {
            const data = await res.json();
            if (data.matched) {
                showMatchToast(data.match_name);
            }
        }

        currentIndex++;
        setTimeout(renderCurrent, 350);
    }
    async function doPass() {
        card_animate('pass');
        currentIndex++;
        setTimeout(renderCurrent, 350);
    }
    function card_animate(type) {
        const card = container.querySelector('.swipe-card');
        if (!card) return;
        card.style.transition = 'transform .35s,opacity .35s';
        card.style.transform = type === 'like' ? 'translateX(120%) rotate(20deg)' : 'translateX(-120%) rotate(-20deg)';
        card.style.opacity = '0';
    }

    document.getElementById('btnLike').addEventListener('click', doLike);
    document.getElementById('btnPass').addEventListener('click', doPass);
    document.getElementById('btnSuperLike').addEventListener('click', function() {
        doSuperLike();
    });

    // ── Super Like Animation/Modal ──
    function doSuperLike() {
        const p = deck[currentIndex];
        card_animate('like');
        showSuperLikeModal(p);
        fetch(`/superlike/${p.id}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrf, 'Content-Type': 'application/json' }
        });
        currentIndex++;
        setTimeout(renderCurrent, 900);
    }

    function showSuperLikeModal(profile) {
        let modal = document.getElementById('superLikeModal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'superLikeModal';
            modal.style.cssText = 'position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:99999;display:flex;align-items:center;justify-content:center;background:rgba(0,0,0,0.55);';
            modal.innerHTML = `
                <div style="background:rgba(255,255,255,0.97);border-radius:2rem;padding:2.5rem 2.5rem 2rem 2.5rem;box-shadow:0 8px 40px rgba(0,0,0,.18);text-align:center;max-width:340px;animation:popIn .5s cubic-bezier(.68,-0.55,.27,1.55);">
                    <div style="font-size:3.5rem;line-height:1;animation:superStar 1.2s infinite alternate"><i class='bi bi-star-fill text-warning'></i></div>
                    <h4 class="fw-bold mt-3 mb-2" style="color:#f59e42">Super Like Sent!</h4>
                    <div class="mb-2">You sent a Super Like to <span class="fw-semibold">${profile.name}</span>!</div>
                    <button class="btn btn-primary mt-2 px-4" id="closeSuperLikeModal">OK</button>
                </div>
                <style>
                @keyframes popIn { 0% { transform:scale(0.7); opacity:0; } 100% { transform:scale(1); opacity:1; } }
                @keyframes superStar { 0% { filter:drop-shadow(0 0 0 #f59e42);} 100% { filter:drop-shadow(0 0 16px #f59e42);} }
                </style>
            `;
            document.body.appendChild(modal);
        } else {
            modal.style.display = 'flex';
        }
        document.getElementById('closeSuperLikeModal').onclick = function() {
            modal.style.display = 'none';
        };
        setTimeout(() => { if(modal) modal.style.display = 'none'; }, 1800);
    }

    // ── Countdown timer helpers ───────────────────────────────────────
    let countdownInterval = null;

    function showLimitOverlay(resetDate) {
        document.getElementById('deckContainer').classList.add('d-none');
        document.getElementById('emptyState').classList.add('d-none');
        document.getElementById('limitOverlay').classList.remove('d-none');
        document.getElementById('actionButtons').classList.add('d-none');

        if (resetDate) {
            startCountdown(resetDate);
        } else {
            document.getElementById('countdownTimer').textContent = 'tomorrow';
        }
    }

    function startCountdown(resetDate) {
        if (countdownInterval) clearInterval(countdownInterval);

        function tick() {
            const diff = resetDate - Date.now();
            if (diff <= 0) {
                clearInterval(countdownInterval);
                document.getElementById('countdownTimer').textContent = '00:00:00';
                // Auto-reload the deck when the limit has reset
                setTimeout(() => location.reload(), 1500);
                return;
            }
            const h = Math.floor(diff / 3600000).toString().padStart(2, '0');
            const m = Math.floor((diff % 3600000) / 60000).toString().padStart(2, '0');
            const s = Math.floor((diff % 60000) / 1000).toString().padStart(2, '0');
            document.getElementById('countdownTimer').textContent = `${h}:${m}:${s}`;
        }

        tick();
        countdownInterval = setInterval(tick, 1000);
    }

    function showMatchToast(name) {
        const toast = document.createElement('div');
        toast.style.cssText = 'position:fixed;top:20px;left:50%;transform:translateX(-50%);z-index:9999;padding:12px 24px;background:linear-gradient(135deg,#f43f5e,#ec4899);color:#fff;border-radius:50px;font-weight:600;box-shadow:0 4px 20px rgba(0,0,0,.2);animation:fadeInDown .4s ease';
        toast.innerHTML = `🎉 It's a Match with ${name}!`;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    }

    loadDeck();
})();
</script>
@endpush
