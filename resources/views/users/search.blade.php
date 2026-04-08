@extends('layouts.app')
@section('title', 'Find People')

@push('styles')
<style>
/* ── Page variables ──────────────────────────────────────────────────────── */
:root {
    --sp-bg:   linear-gradient(135deg,#0f172a 0%,#1e1b4b 60%,#3b0764 100%);
    --sp-card: rgba(255,255,255,.06);
    --sp-brd:  rgba(255,255,255,.10);
    --sp-txt:  #f1f5f9;
    --sp-muted:#94a3b8;
    --sp-pink: #f43f5e;
    --sp-purp: #a855f7;
    --sp-grad: linear-gradient(135deg,var(--sp-pink),var(--sp-purp));
}

.sp-hero {
    background: var(--sp-bg);
    padding: 3rem 0 2rem;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}
.sp-hero::before {
    content:'';
    position:absolute; inset:0;
    background: radial-gradient(circle at 30% 50%, rgba(168,85,247,.15) 0%, transparent 60%),
                radial-gradient(circle at 80% 20%, rgba(244,63,94,.12) 0%, transparent 50%);
    pointer-events:none;
}
.sp-hero-inner { position:relative; z-index:1; text-align:center; }
.sp-hero-inner h1 {
    font-size: clamp(1.6rem,4vw,2.6rem);
    font-weight: 800;
    background: var(--sp-grad);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: .5rem;
}
.sp-hero-inner p { color: var(--sp-muted); font-size:.98rem; margin-bottom:0; }

/* ── Search bar ──────────────────────────────────────────────────────────── */
.sp-search-wrap {
    max-width: 540px;
    margin: 0 auto;
    position: relative;
}
.sp-search-wrap .sp-search-icon {
    position:absolute; left:16px; top:50%; transform:translateY(-50%);
    color:var(--sp-muted); font-size:1.1rem; pointer-events:none; z-index:2;
}
#spSearchInput {
    width: 100%;
    padding: .85rem 1.1rem .85rem 3rem;
    border-radius: 50px;
    border: 1.5px solid rgba(255,255,255,.18);
    background: rgba(255,255,255,.08);
    color: var(--sp-txt);
    font-size: 1rem;
    outline: none;
    transition: border-color .2s, box-shadow .2s;
    backdrop-filter: blur(10px);
}
#spSearchInput::placeholder { color: var(--sp-muted); }
#spSearchInput:focus {
    border-color: var(--sp-purp);
    box-shadow: 0 0 0 3px rgba(168,85,247,.2);
}

/* ── Results section ─────────────────────────────────────────────────────── */
.sp-results-header {
    display:flex; align-items:baseline; justify-content:space-between;
    margin-bottom:1rem;
}
.sp-results-title { font-weight:700; font-size:1.05rem; color:var(--sp-txt); }
.sp-results-count { font-size:.82rem; color:var(--sp-muted); }

#spGrid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}
@media(max-width:480px) { #spGrid { grid-template-columns: 1fr 1fr; } }

/* ── User card ───────────────────────────────────────────────────────────── */
.sp-card {
    background: var(--sp-card);
    border: 1.5px solid var(--sp-brd);
    border-radius: 16px;
    overflow: hidden;
    transition: transform .2s, box-shadow .2s;
    cursor: pointer;
    backdrop-filter: blur(12px);
}
.sp-card:hover { transform: translateY(-3px); box-shadow: 0 8px 32px rgba(0,0,0,.35); }

.sp-card-photo {
    width: 100%; aspect-ratio: 1/1;
    background: linear-gradient(135deg,#1e1b4b,#3b0764);
    position: relative; overflow:hidden;
}
.sp-card-photo img {
    width:100%; height:100%; object-fit:cover;
    display:block; transition: transform .3s;
}
.sp-card:hover .sp-card-photo img { transform: scale(1.05); }
.sp-card-initials {
    position:absolute; inset:0;
    display:flex; align-items:center; justify-content:center;
    font-size:2.4rem; font-weight:800; color:#fff;
    background: var(--sp-grad);
}
.sp-premium-badge {
    position:absolute; top:8px; right:8px;
    background:linear-gradient(135deg,#f59e0b,#ef4444);
    color:#fff; font-size:.62rem; font-weight:700;
    padding:2px 7px; border-radius:20px;
    box-shadow:0 2px 8px rgba(0,0,0,.3);
}
.sp-verified-badge {
    position:absolute; top:8px; left:8px;
    color:#60a5fa; font-size:1rem;
    text-shadow:0 1px 4px rgba(0,0,0,.5);
}

.sp-card-body {
    padding: .85rem .9rem .9rem;
}
.sp-card-name {
    font-weight: 700; font-size:.95rem; color:var(--sp-txt);
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
    margin-bottom:2px;
}
.sp-card-meta {
    font-size:.78rem; color:var(--sp-muted); margin-bottom:.75rem;
    white-space:nowrap; overflow:hidden; text-overflow:ellipsis;
}
.sp-card-username { color:var(--sp-purp); font-weight:600; }

.sp-card-actions { display:flex; gap:.5rem; }

.sp-btn-add {
    flex: 1;
    padding:.42rem .6rem;
    border-radius:30px;
    border: 1.5px solid var(--sp-pink);
    background: transparent;
    color: var(--sp-pink);
    font-size:.8rem; font-weight:700;
    cursor: pointer;
    transition: background .2s, color .2s;
    white-space: nowrap;
}
.sp-btn-add:hover, .sp-btn-add.liked {
    background: var(--sp-grad);
    border-color: transparent;
    color: #fff;
}
.sp-btn-add.liked { cursor:default; }

.sp-btn-view {
    padding:.42rem .7rem;
    border-radius:30px;
    border: 1.5px solid rgba(255,255,255,.2);
    background: transparent;
    color: var(--sp-muted);
    font-size:.8rem;
    cursor: pointer;
    text-decoration: none;
    display:inline-flex; align-items:center;
    transition: border-color .2s, color .2s;
}
.sp-btn-view:hover { border-color:rgba(255,255,255,.5); color:var(--sp-txt); }

/* ── States ──────────────────────────────────────────────────────────────── */
.sp-empty {
    text-align:center; padding:4rem 1rem; color:var(--sp-muted);
    grid-column: 1/-1;
}
.sp-empty-icon { font-size:3rem; margin-bottom:1rem; opacity:.4; }
.sp-empty h5 { color:var(--sp-txt); font-weight:700; margin-bottom:.5rem; }

.sp-loading {
    display:flex; align-items:center; justify-content:center;
    gap:.5rem; padding:3rem; color:var(--sp-muted); grid-column:1/-1;
}
.sp-spinner {
    width:22px; height:22px; border-radius:50%;
    border:2.5px solid rgba(255,255,255,.15);
    border-top-color:var(--sp-purp);
    animation:sp-spin .7s linear infinite;
}
@keyframes sp-spin { to { transform:rotate(360deg); } }

/* ── Toast feedback ──────────────────────────────────────────────────────── */
.sp-toast {
    position:fixed; bottom:24px; left:50%; transform:translateX(-50%) translateY(80px);
    background:rgba(22,22,36,.95); color:#fff; padding:.7rem 1.4rem;
    border-radius:30px; font-size:.88rem; font-weight:600;
    box-shadow:0 6px 24px rgba(0,0,0,.4); z-index:9999;
    transition:transform .3s cubic-bezier(.34,1.56,.64,1);
    pointer-events:none;
}
.sp-toast.show { transform:translateX(-50%) translateY(0); }
</style>
@endpush

@section('content')

{{-- Hero / Search bar ─────────────────────────────────────────────────────── --}}
<div class="sp-hero">
    <div class="container sp-hero-inner">
        <h1><i class="bi bi-person-search me-2"></i>Find People</h1>
        <p class="mb-4">Search by name, @username or email address</p>

        <div class="sp-search-wrap">
            <i class="bi bi-search sp-search-icon"></i>
            <input id="spSearchInput"
                   type="search"
                   placeholder="e.g. Jane, @janedoe, jane@example.com…"
                   autocomplete="off"
                   autofocus>
        </div>
    </div>
</div>

{{-- Results container ────────────────────────────────────────────────────── --}}
<div class="container pb-5">

    <div id="spResultsWrapper" style="display:none;">
        <div class="sp-results-header">
            <span class="sp-results-title">Results</span>
            <span class="sp-results-count" id="spResultsCount"></span>
        </div>
    </div>

    <div id="spGrid"></div>

</div>

{{-- Toast notification ──────────────────────────────────────────────────── --}}
<div class="sp-toast" id="spToast"></div>

@endsection

@push('scripts')
<script>
(function () {
    const input      = document.getElementById('spSearchInput');
    const grid       = document.getElementById('spGrid');
    const wrapper    = document.getElementById('spResultsWrapper');
    const countEl    = document.getElementById('spResultsCount');
    const toast      = document.getElementById('spToast');
    const CSRF       = document.querySelector('meta[name="csrf-token"]').content;
    const SEARCH_URL = '{{ route("users.search.results") }}';

    let debounce;

    // ── Debounced search ──────────────────────────────────────────────────
    input.addEventListener('input', () => {
        clearTimeout(debounce);
        const q = input.value.trim();

        if (q.length < 2) {
            grid.innerHTML    = '';
            wrapper.style.display = 'none';
            return;
        }

        showLoading();
        debounce = setTimeout(() => doSearch(q), 350);
    });

    async function doSearch(q) {
        try {
            const res  = await fetch(`${SEARCH_URL}?q=${encodeURIComponent(q)}`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
            });
            const data = await res.json();
            render(data.users, data.total);
        } catch (e) {
            grid.innerHTML = `<div class="sp-empty"><div class="sp-empty-icon">⚠️</div><h5>Search failed</h5><p>Please try again</p></div>`;
        }
    }

    function showLoading() {
        wrapper.style.display = 'block';
        grid.innerHTML = `<div class="sp-loading"><div class="sp-spinner"></div>Searching…</div>`;
        countEl.textContent = '';
    }

    function render(users, total) {
        wrapper.style.display = 'block';
        countEl.textContent = total === 0 ? '' : `${total} found`;

        if (!users.length) {
            grid.innerHTML = `
                <div class="sp-empty">
                    <div class="sp-empty-icon"><i class="bi bi-person-x"></i></div>
                    <h5>No one found</h5>
                    <p>Try a different name, username or email.</p>
                </div>`;
            return;
        }

        grid.innerHTML = users.map(u => buildCard(u)).join('');

        // Attach like handlers
        grid.querySelectorAll('.sp-btn-add[data-like-url]').forEach(btn => {
            if (btn.dataset.liked === '1') return; // already liked
            btn.addEventListener('click', () => sendLike(btn));
        });
    }

    function buildCard(u) {
        const initials = (u.name || '?').charAt(0).toUpperCase();
        const photoHtml = u.photo
            ? `<img src="${escHtml(u.photo)}" alt="${escHtml(u.name)}" loading="lazy">`
            : `<div class="sp-card-initials">${initials}</div>`;

        const premBadge  = u.is_premium  ? `<span class="sp-premium-badge">✨ Premium</span>` : '';
        const verBadge   = u.is_verified ? `<i class="bi bi-patch-check-fill sp-verified-badge" title="Verified"></i>` : '';
        const metaParts  = [];
        if (u.username) metaParts.push(`<span class="sp-card-username">@${escHtml(u.username)}</span>`);
        if (u.age)      metaParts.push(`${u.age} yrs`);
        if (u.city)     metaParts.push(escHtml(u.city));

        const likedClass = u.liked ? ' liked' : '';
        const likedText  = u.liked ? '<i class="bi bi-heart-fill me-1"></i>Added' : '<i class="bi bi-heart me-1"></i>Add';

        return `
        <div class="sp-card">
            <div class="sp-card-photo">
                ${photoHtml}
                ${premBadge}
                ${verBadge}
            </div>
            <div class="sp-card-body">
                <div class="sp-card-name">${escHtml(u.name)}</div>
                <div class="sp-card-meta">${metaParts.join(' · ') || '&nbsp;'}</div>
                <div class="sp-card-actions">
                    <button class="sp-btn-add${likedClass}"
                            data-like-url="${escHtml(u.like_url)}"
                            data-liked="${u.liked ? '1' : '0'}"
                            data-name="${escHtml(u.name)}">
                        ${likedText}
                    </button>
                    <a href="${escHtml(u.profile_url)}" class="sp-btn-view" title="View profile">
                        <i class="bi bi-person"></i>
                    </a>
                </div>
            </div>
        </div>`;
    }

    async function sendLike(btn) {
        if (btn.dataset.liked === '1' || btn.disabled) return;
        btn.disabled = true;

        try {
            const res = await fetch(btn.dataset.likeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF,
                    'Accept':       'application/json',
                },
            });
            if (res.ok || res.status === 422) {
                // 422 = already liked (idempotent)
                btn.dataset.liked = '1';
                btn.classList.add('liked');
                btn.innerHTML = '<i class="bi bi-heart-fill me-1"></i>Added';
                showToast(`💖 You added ${btn.dataset.name}!`);
            } else {
                btn.disabled = false;
                showToast('Could not add — please try again.');
            }
        } catch (e) {
            btn.disabled = false;
            showToast('Network error — please try again.');
        }
    }

    // ── Toast ─────────────────────────────────────────────────────────────
    let toastTimer;
    function showToast(msg) {
        toast.textContent = msg;
        toast.classList.add('show');
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => toast.classList.remove('show'), 3000);
    }

    // ── HTML escape ───────────────────────────────────────────────────────
    function escHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g,'&amp;')
            .replace(/</g,'&lt;')
            .replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;');
    }
})();
</script>
@endpush
