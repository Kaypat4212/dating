@extends('layouts.app')
@section('title', 'Discover')

@push('styles')
<style>
/* ── Root tokens ─────────────────────────────────────────── */
:root {
  --hc-pink:    #ff3e6c;
  --hc-rose:    #ff6b91;
  --hc-purple:  #7c3aed;
  --hc-gold:    #f59e0b;
  --hc-pass:    #64748b;
  --hc-dark:    #0f0a1e;
  --hc-dark2:   #1a1133;
  --card-r:     20px;
  --btn-size-lg: 68px;
  --btn-size-md: 54px;
  --btn-size-sm: 44px;
}

/* ── Page shell ──────────────────────────────────────────── */
.swipe-page { max-width: 480px; margin: 0 auto; padding: 0 12px 32px; }

/* ── Stack wrapper ───────────────────────────────────────── */
#swipe-stack { height: 540px; position: relative; }

/* ── Card ────────────────────────────────────────────────── */
.swipe-card {
  position: absolute; inset: 0;
  border-radius: var(--card-r);
  overflow: hidden;
  cursor: grab;
  touch-action: none;
  will-change: transform;
  box-shadow: 0 12px 40px rgba(0,0,0,.45), 0 2px 8px rgba(0,0,0,.3);
  background: var(--hc-dark);
  transition: box-shadow .2s;
  user-select: none;
}
.swipe-card:active { cursor: grabbing; }
.swipe-card.is-top { box-shadow: 0 20px 60px rgba(0,0,0,.55), 0 4px 16px rgba(0,0,0,.35); }

/* Photo */
.swipe-card-img { width:100%; height:100%; object-fit:cover; display:block; pointer-events:none; }

/* Multiple-photo tap zones */
.photo-tap-prev, .photo-tap-next { position:absolute; top:0; height:100%; z-index:5; }
.photo-tap-prev { left:0;  width:28%; }
.photo-tap-next { right:0; width:28%; }

/* Progress dots */
.photo-dots {
  position:absolute; top:10px; left:10px; right:10px;
  display:flex; gap:4px; z-index:6; padding:0 4px;
}
.photo-dot {
  flex:1; height:3px; max-width:48px; border-radius:2px;
  background: rgba(255,255,255,.35);
  transition: background .2s, transform .15s;
  pointer-events: none;
}
.photo-dot.active { background: #fff; transform: scaleY(1.4); }

/* Gradient overlays */
.card-gradient-top {
  position:absolute; top:0; left:0; right:0; height:120px;
  background: linear-gradient(to bottom, rgba(0,0,0,.55) 0%, transparent 100%);
  pointer-events: none; z-index: 4;
}
.card-gradient-bottom {
  position:absolute; bottom:0; left:0; right:0; height:72%;
  background: linear-gradient(to top, rgba(10,5,25,.97) 0%, rgba(10,5,25,.55) 55%, transparent 100%);
  pointer-events: none; z-index: 4;
}

/* Swipe stamps */
.stamp {
  position:absolute; top:26px; padding:6px 16px;
  border-width:3px !important; border-style:solid;
  border-radius:8px; font-size:1.6rem; font-weight:900;
  opacity:0; pointer-events:none; z-index:20;
  letter-spacing:.04em; transition: opacity .05s;
}
.stamp-like { left:20px;  border-color:#22c55e; color:#22c55e; transform:rotate(-16deg); }
.stamp-nope { right:20px; border-color:#ef4444; color:#ef4444; transform:rotate(16deg); }
.stamp-super { left:50%; transform:translateX(-50%) rotate(0deg); top:22px; border-color:#f59e0b; color:#f59e0b; white-space:nowrap; }

/* Profile info area */
.card-info {
  position:absolute; bottom:0; left:0; right:0;
  padding:0 16px 80px; z-index:10; pointer-events:none;
  color:#fff;
}
.card-name {
  font-size:1.55rem; font-weight:800; line-height:1.1;
  text-shadow:0 2px 8px rgba(0,0,0,.6);
}
.card-meta { font-size:.82rem; opacity:.78; margin-top:3px; }
.card-headline { font-size:.9rem; opacity:.88; margin-top:5px; line-height:1.35; }
.card-tags { margin-top:8px; display:flex; flex-wrap:wrap; gap:5px; }
.card-tag {
  font-size:.72rem; padding:3px 10px; border-radius:20px;
  background:rgba(255,255,255,.13); backdrop-filter:blur(6px);
  border:1px solid rgba(255,255,255,.15);
}

/* Compat badge */
.compat-badge {
  display:inline-flex; align-items:center; gap:5px;
  font-size:.75rem; font-weight:700; padding:3px 10px;
  border-radius:20px; margin-top:7px;
  background:linear-gradient(135deg,rgba(124,58,237,.8),rgba(236,72,153,.6));
  backdrop-filter:blur(6px); border:1px solid rgba(255,255,255,.2);
}

/* Online dot */
.online-dot {
  width:10px; height:10px; background:#22c55e; border-radius:50%;
  border:2px solid #fff; flex-shrink:0;
  box-shadow:0 0 0 0 rgba(34,197,94,.6);
  animation: pulse-green 2s infinite;
}
@keyframes pulse-green {
  0%   { box-shadow:0 0 0 0 rgba(34,197,94,.6); }
  70%  { box-shadow:0 0 0 6px rgba(34,197,94,0); }
  100% { box-shadow:0 0 0 0 rgba(34,197,94,0); }
}

/* Verified badge (chip style) */
.verified-chip {
  display:inline-flex; align-items:center; gap:3px;
  font-size:.7rem; font-weight:700; padding:2px 8px;
  border-radius:20px; background:rgba(29,155,240,.25);
  border:1px solid rgba(29,155,240,.45); color:#93d5ff;
}

/* Card top-right mini actions */
.card-corner-actions {
  position:absolute; top:14px; right:14px;
  display:flex; flex-direction:column; gap:7px;
  z-index:15; pointer-events:all;
}
.corner-btn {
  width:36px; height:36px; border-radius:50%; border:none;
  background:rgba(255,255,255,.14); backdrop-filter:blur(8px);
  color:#fff; font-size:.9rem; cursor:pointer;
  display:flex; align-items:center; justify-content:center;
  transition:background .15s, transform .15s;
  box-shadow:0 2px 8px rgba(0,0,0,.3);
}
.corner-btn:hover { background:rgba(255,255,255,.28); transform:scale(1.08); }

/* Expandable info sheet */
.card-sheet {
  position:absolute; bottom:0; left:0; right:0;
  background:rgba(12,6,30,.93); backdrop-filter:blur(14px);
  border-top:1px solid rgba(255,255,255,.08);
  border-radius:0 0 var(--card-r) var(--card-r);
  padding:14px 16px 16px;
  max-height:0; overflow:hidden;
  transition:max-height .35s cubic-bezier(.4,0,.2,1);
  z-index:30; pointer-events:all;
}
.card-sheet.open { max-height:220px; overflow-y:auto; }
.sheet-row { font-size:.82rem; color:rgba(255,255,255,.75); display:flex; gap:6px; align-items:center; }
.sheet-row + .sheet-row { margin-top:5px; }
.sheet-row i { color:var(--hc-rose); width:14px; text-align:center; flex-shrink:0; }

/* ── Action buttons ──────────────────────────────────────── */
.swipe-actions {
  display:flex; align-items:center; justify-content:center;
  gap:16px; padding:4px 0 8px;
}
.action-btn {
  border:none; border-radius:50%; display:flex;
  align-items:center; justify-content:center;
  cursor:pointer; transition:transform .15s cubic-bezier(.34,1.56,.64,1), box-shadow .2s;
  position:relative; flex-shrink:0;
}
.action-btn:active { transform:scale(.9) !important; }

.btn-pass {
  width:var(--btn-size-md); height:var(--btn-size-md);
  background:#1e1836; color:var(--hc-pass);
  box-shadow:0 4px 16px rgba(0,0,0,.3), 0 0 0 1px rgba(100,116,139,.2);
  font-size:1.45rem;
}
.btn-pass:hover { transform:scale(1.08); box-shadow:0 6px 20px rgba(100,116,139,.35), 0 0 0 1px rgba(100,116,139,.4); }
.btn-pass.active-glow { box-shadow:0 0 0 3px #ef4444, 0 6px 24px rgba(239,68,68,.45); color:#ef4444; }

.btn-super {
  width:var(--btn-size-sm); height:var(--btn-size-sm);
  background:#1e1836; color:var(--hc-gold);
  box-shadow:0 4px 16px rgba(0,0,0,.3), 0 0 0 1px rgba(245,158,11,.2);
  font-size:1.2rem;
}
.btn-super:hover { transform:scale(1.1); box-shadow:0 6px 22px rgba(245,158,11,.4), 0 0 0 1px rgba(245,158,11,.5); }

.btn-like {
  width:var(--btn-size-lg); height:var(--btn-size-lg);
  background:linear-gradient(135deg, var(--hc-pink), var(--hc-rose));
  color:#fff; font-size:1.9rem;
  box-shadow:0 6px 24px rgba(255,62,108,.5), 0 2px 8px rgba(255,62,108,.3);
}
.btn-like:hover { transform:scale(1.1); box-shadow:0 8px 32px rgba(255,62,108,.65); }
.btn-like.active-glow { box-shadow:0 0 0 3px #22c55e, 0 8px 32px rgba(34,197,94,.5); }

.btn-browse {
  width:var(--btn-size-sm); height:var(--btn-size-sm);
  background:#1e1836; color:rgba(255,255,255,.6);
  box-shadow:0 4px 16px rgba(0,0,0,.3), 0 0 0 1px rgba(255,255,255,.08);
  font-size:1.1rem; text-decoration:none;
}
.btn-browse:hover { transform:scale(1.08); color:#fff; }

/* Heart burst on like */
@keyframes heart-burst {
  0%   { transform:scale(1); opacity:1; }
  60%  { transform:scale(1.55); opacity:.8; }
  100% { transform:scale(1); opacity:1; }
}
.btn-like.burst { animation:heart-burst .3s cubic-bezier(.34,1.56,.64,1); }

/* ── Top bar ─────────────────────────────────────────────── */
.swipe-topbar {
  display:flex; align-items:center; justify-content:space-between;
  padding:8px 0 14px;
}
.swipe-topbar-title { font-size:1.2rem; font-weight:800; color:#fff; display:flex; align-items:center; gap:8px; }
.swipe-topbar-title .fire { font-size:1.3rem; }

/* Filter chips */
.filter-chip {
  font-size:.75rem; font-weight:600; padding:5px 13px;
  border-radius:20px; border:1.5px solid rgba(255,255,255,.15);
  background:rgba(255,255,255,.06); color:rgba(255,255,255,.7);
  cursor:pointer; transition:all .2s; text-decoration:none;
  display:inline-flex; align-items:center; gap:5px;
}
.filter-chip:hover { background:rgba(255,255,255,.12); color:#fff; }
.filter-chip.active {
  background:linear-gradient(135deg,var(--hc-purple),var(--hc-pink));
  border-color:transparent; color:#fff;
  box-shadow:0 2px 12px rgba(124,58,237,.4);
}

/* ── Empty state ─────────────────────────────────────────── */
.empty-state {
  text-align:center; padding:40px 20px;
  background:rgba(255,255,255,.03); border-radius:20px;
  border:1px solid rgba(255,255,255,.06);
}

/* ── Fallback alert ──────────────────────────────────────── */
.location-fallback {
  display:flex; align-items:center; gap:9px;
  padding:9px 14px; border-radius:12px; margin-bottom:12px;
  background:rgba(14,165,233,.1); border:1px solid rgba(14,165,233,.2);
  font-size:.8rem; color:rgba(255,255,255,.75);
}

/* ── Match modal ─────────────────────────────────────────── */
#matchModal .modal-content {
  border:none; border-radius:24px; overflow:hidden;
}
.match-bg {
  background:linear-gradient(160deg, #1a0533 0%, #2d0a4e 40%, #1a0533 100%);
  position:relative; overflow:hidden;
}
.match-bg::before {
  content:''; position:absolute; inset:0;
  background:radial-gradient(ellipse at 50% 0%, rgba(255,62,108,.25) 0%, transparent 70%);
  pointer-events:none;
}
.match-avatars { display:flex; align-items:center; justify-content:center; gap:0; margin-bottom:12px; }
.match-avatar {
  width:88px; height:88px; border-radius:50%; overflow:hidden;
  border:3px solid rgba(255,255,255,.3);
  box-shadow:0 4px 20px rgba(0,0,0,.5);
  flex-shrink:0;
}
.match-avatar:first-child { transform:translateX(14px) rotate(-4deg); z-index:1; border-color:var(--hc-pink); }
.match-avatar:last-child  { transform:translateX(-14px) rotate(4deg); z-index:1; border-color:var(--hc-rose); }
.match-heart-icon { font-size:2.2rem; z-index:2; position:relative; margin:0 -4px; }
.match-title { font-size:2rem; font-weight:900; letter-spacing:-.02em; }
.match-title span { background:linear-gradient(90deg,#ff6b91,#f59e0b); -webkit-background-clip:text; -webkit-text-fill-color:transparent; }

.icebreaker-box {
  background:rgba(255,255,255,.07); border:1px solid rgba(255,255,255,.12);
  border-radius:14px; padding:14px 16px; margin-top:16px; text-align:left;
}
.icebreaker-label { font-size:.72rem; color:rgba(255,255,255,.5); display:flex; align-items:center; gap:5px; margin-bottom:6px; }
.icebreaker-text  { font-size:.9rem; color:#fff; font-style:italic; line-height:1.4; }

/* ── Like-with-message modal ─────────────────────────────── */
#likeMessageModal .modal-content { border:none; border-radius:22px; overflow:hidden; }
.like-msg-bg {
  background:linear-gradient(160deg,#0f0a1e,#1a0533);
  border:1px solid rgba(255,255,255,.07);
}
.like-msg-photo {
  width:72px; height:72px; border-radius:50%; overflow:hidden; margin:0 auto 10px;
  border:2.5px solid var(--hc-pink);
  box-shadow:0 0 0 4px rgba(255,62,108,.2);
}

/* ── Toast ───────────────────────────────────────────────── */
#swipe-toast {
  border-radius:14px !important; min-width:180px;
  box-shadow:0 8px 30px rgba(0,0,0,.4) !important;
}

/* ── Limit overlay ───────────────────────────────────────── */
.swipe-overlay {
  position:fixed; inset:0; z-index:3000;
  background:rgba(10,5,25,.94); backdrop-filter:blur(8px);
  display:flex; flex-direction:column;
  align-items:center; justify-content:center;
  text-align:center; padding:2rem;
}
.swipe-overlay .overlay-icon { font-size:4.5rem; margin-bottom:16px; }
.swipe-overlay h4 { color:#fff; font-weight:800; }
.countdown-display {
  font-size:3rem; font-weight:900; letter-spacing:.05em;
  background:linear-gradient(90deg,var(--hc-gold),var(--hc-pink));
  -webkit-background-clip:text; -webkit-text-fill-color:transparent;
  margin:8px 0 16px;
}

/* ── Keyboard hint ───────────────────────────────────────── */
.key-hints {
  display:flex; justify-content:center; gap:18px;
  margin-top:8px; opacity:.4; font-size:.72rem; color:rgba(255,255,255,.6);
}
.key-hints kbd {
  background:rgba(255,255,255,.1); border-radius:5px;
  padding:2px 7px; font-size:.7rem; color:inherit;
  border:1px solid rgba(255,255,255,.15); margin-right:3px;
}

/* confetti particle */
.confetti-particle {
  position:fixed; width:8px; height:8px; border-radius:2px;
  pointer-events:none; z-index:9999; animation:confetti-fall linear forwards;
}
@keyframes confetti-fall {
  0%   { transform:translateY(-20px) rotate(0deg); opacity:1; }
  100% { transform:translateY(100vh) rotate(720deg); opacity:0; }
}
</style>
@endpush

@section('content')
<div class="swipe-page">

    @include('partials.safety-banner')

    {{-- Location fallback notice --}}
    @if(!empty($fallbackToGlobal) && $fallbackToGlobal)
    <div class="location-fallback">
        <i class="bi bi-geo-alt-fill" style="color:#38bdf8;flex-shrink:0"></i>
        <span>No one found within your distance — showing profiles farther away.
            <a href="{{ route('preferences.edit') }}" style="color:#7dd3fc;font-weight:600" class="ms-1">Expand distance</a>
        </span>
    </div>
    @endif

    {{-- Top bar --}}
    <div class="swipe-topbar">
        <div class="swipe-topbar-title">
            <span class="fire">🔥</span> Discover
        </div>
        <div class="d-flex align-items-center gap-2">
            @if(auth()->user()->is_verified)
            <a href="{{ route('swipe.deck', array_merge(request()->query(), ['verified_only' => request('verified_only') ? 0 : 1])) }}"
               class="filter-chip {{ request('verified_only') ? 'active' : '' }}">
                <i class="bi bi-patch-check-fill" style="font-size:.8rem"></i>
                Verified
            </a>
            @endif
            <a href="{{ route('discover.index') }}" class="filter-chip">
                <i class="bi bi-grid-3x3-gap" style="font-size:.8rem"></i>Browse
            </a>
        </div>
    </div>

    @if($profiles->isEmpty())
    <div class="empty-state">
        <div style="font-size:4rem;margin-bottom:14px">🌍</div>
        <h5 class="fw-bold text-white mb-2">You've seen everyone nearby!</h5>
        <p style="color:rgba(255,255,255,.5);font-size:.9rem">Check back later or expand your preferences.</p>
        <a href="{{ route('preferences.edit') }}" class="btn btn-sm fw-semibold mt-2 px-4"
           style="background:linear-gradient(135deg,var(--hc-purple),var(--hc-pink));color:#fff;border:none;border-radius:20px">
            <i class="bi bi-sliders me-2"></i>Adjust Preferences
        </a>
    </div>
    @else

    {{-- ══ Card stack ══════════════════════════════════════ --}}
    <div id="swipe-stack">

        @foreach($profiles->reverse() as $index => $profile)
        @php
            $photo     = $profile->primaryPhoto;
            $prof      = $profile->profile;
            $age       = $profile->date_of_birth ? \Carbon\Carbon::parse($profile->date_of_birth)->age : null;
            $interests = $prof?->interests?->take(4) ?? collect();
            $allPhotos = $profile->photos->where('is_approved', true)->values();
            $isTop     = $loop->last;
        @endphp

        <div class="swipe-card {{ $isTop ? 'is-top' : '' }}"
             data-user-id="{{ $profile->id }}"
             data-username="{{ $profile->username }}"
             data-name="{{ $profile->name }}"
             data-photo="{{ $photo?->url ?? '' }}"
             data-photos="{{ json_encode($allPhotos->map(fn($p) => $p->url)->values()) }}"
             style="{{ $isTop
                 ? 'z-index:100;'
                 : 'transform:scale('.( 1 - ($loop->remaining * 0.022) ).');top:'.($loop->remaining * 9).'px;z-index:'.$loop->index.';' }}">

            {{-- Photo --}}
            <div class="position-absolute inset-0 w-100 h-100">
                @if($photo)
                <img src="{{ $photo->url }}"
                     class="swipe-card-img"
                     data-photos="{{ json_encode($allPhotos->map(fn($p) => $p->url)->values()) }}"
                     data-idx="0" alt="{{ $profile->name }}">
                @else
                <div class="w-100 h-100 d-flex align-items-center justify-content-center"
                     style="background:linear-gradient(135deg,#2d0a4e,#0f0a1e)">
                    <i class="bi bi-person-circle text-white" style="font-size:7rem;opacity:.15"></i>
                </div>
                @endif

                {{-- Photo navigation tap zones --}}
                @if($allPhotos->count() > 1)
                <div class="photo-tap-prev" data-action="prev"></div>
                <div class="photo-tap-next" data-action="next"></div>
                {{-- Progress dots --}}
                <div class="photo-dots">
                    @foreach($allPhotos as $di => $dp)
                    <div class="photo-dot {{ $di === 0 ? 'active' : '' }}"></div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Gradient overlays --}}
            <div class="card-gradient-top"></div>
            <div class="card-gradient-bottom"></div>

            {{-- Swipe stamps --}}
            <div class="stamp stamp-like">LIKE ❤</div>
            <div class="stamp stamp-nope">NOPE ✕</div>
            <div class="stamp stamp-super">⭐ SUPER</div>

            {{-- Corner actions --}}
            <div class="card-corner-actions">
                <button class="corner-btn card-btn-info" data-target="sheet-{{ $profile->id }}" title="More info">
                    <i class="bi bi-info-lg"></i>
                </button>
                @if($profile->username)
                <a href="{{ route('profile.show', $profile->username) }}"
                   class="corner-btn" title="Full profile" target="_blank"
                   style="text-decoration:none">
                    <i class="bi bi-person-fill" style="font-size:.85rem"></i>
                </a>
                @endif
                @if($allPhotos->count() > 1)
                <div class="corner-btn" style="font-size:.68rem;gap:2px;cursor:default;pointer-events:none">
                    <i class="bi bi-images" style="font-size:.7rem"></i>
                    {{ $allPhotos->count() }}
                </div>
                @endif
            </div>

            {{-- Profile info --}}
            <div class="card-info">
                {{-- Name, age, online --}}
                <div class="d-flex align-items-center gap-8 flex-wrap" style="gap:8px">
                    <span class="card-name">{{ $profile->name }}{{ $age ? ', '.$age : '' }}</span>
                    @if($profile->is_verified)
                    <span class="verified-chip"><i class="bi bi-patch-check-fill" style="font-size:.72rem"></i>ID</span>
                    @endif
                    @if(isset($profile->last_active_at) && \Carbon\Carbon::parse($profile->last_active_at)->diffInMinutes() < 15)
                    <span class="online-dot ms-1"></span>
                    @endif
                </div>

                {{-- Location --}}
                @if($prof?->city || $prof?->country)
                <div class="card-meta">
                    <i class="bi bi-geo-alt me-1"></i>{{ implode(', ', array_filter([$prof->city, $prof->country])) }}
                </div>
                @endif

                {{-- Headline --}}
                @if($prof?->headline)
                <div class="card-headline">{{ Str::limit($prof->headline, 70) }}</div>
                @endif

                {{-- Tags --}}
                @if($interests->isNotEmpty())
                <div class="card-tags">
                    @foreach($interests as $int)
                    <span class="card-tag">{{ $int->icon ?? '' }} {{ $int->name }}</span>
                    @endforeach
                </div>
                @endif

                {{-- Compat score --}}
                @if(($profile->compat_score ?? 0) > 0)
                <div class="mt-1">
                    <span class="compat-badge">
                        <i class="bi bi-magic" style="font-size:.75rem"></i>
                        {{ $profile->compat_score }}% Match
                    </span>
                </div>
                @endif
            </div>

            {{-- Expandable info sheet --}}
            <div id="sheet-{{ $profile->id }}" class="card-sheet">
                @if($prof?->bio)
                <p style="font-size:.84rem;color:rgba(255,255,255,.82);margin-bottom:10px;line-height:1.5">{{ $prof->bio }}</p>
                @endif
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:5px">
                    @if($prof?->relationship_goal)<div class="sheet-row"><i class="bi bi-heart-fill"></i><span>{{ ucfirst($prof->relationship_goal) }}</span></div>@endif
                    @if($prof?->education)<div class="sheet-row"><i class="bi bi-mortarboard-fill"></i><span>{{ ucfirst(str_replace('_',' ',$prof->education)) }}</span></div>@endif
                    @if($prof?->occupation)<div class="sheet-row"><i class="bi bi-briefcase-fill"></i><span>{{ $prof->occupation }}</span></div>@endif
                    @if($prof?->body_type)<div class="sheet-row"><i class="bi bi-person-fill"></i><span>{{ ucfirst($prof->body_type) }}</span></div>@endif
                    @if($prof?->wants_children)<div class="sheet-row"><i class="bi bi-emoji-smile-fill"></i><span>{{ ucfirst(str_replace('_',' ',$prof->wants_children)) }}</span></div>@endif
                    @if($prof?->religion)<div class="sheet-row"><i class="bi bi-moon-stars-fill"></i><span>{{ ucfirst($prof->religion) }}</span></div>@endif
                </div>
            </div>

        </div>
        @endforeach
    </div>

    {{-- ══ Action buttons ══════════════════════════════════ --}}
    <div class="swipe-actions mt-2">
        {{-- Pass --}}
        <button id="btn-pass" class="action-btn btn-pass" title="Pass  ←">
            <i class="bi bi-x-lg"></i>
        </button>
        {{-- Super Like --}}
        <button id="btn-super-like" class="action-btn btn-super" title="Super Like  ↑">
            <i class="bi bi-star-fill"></i>
        </button>
        {{-- Like --}}
        <button id="btn-like" class="action-btn btn-like" title="Like  →">
            <i class="bi bi-heart-fill"></i>
        </button>
        {{-- Browse --}}
        <a href="{{ route('discover.index') }}" class="action-btn btn-browse" title="Browse all">
            <i class="bi bi-grid-3x3-gap-fill"></i>
        </a>
    </div>

    {{-- Keyboard hints --}}
    <div class="key-hints d-none d-md-flex">
        <span><kbd>←</kbd>Pass</span>
        <span><kbd>↑</kbd>Super Like</span>
        <span><kbd>→</kbd>Like</span>
    </div>

    {{-- ══ Match modal ══════════════════════════════════════ --}}
    <div class="modal fade" id="matchModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="match-bg p-4 p-sm-5 text-center">
                    <div class="match-avatars">
                        <div class="match-avatar">
                            <img src="{{ auth()->user()->primaryPhoto?->thumbnail_url ?? '' }}"
                                 class="w-100 h-100" style="object-fit:cover" alt="You">
                        </div>
                        <span class="match-heart-icon">💞</span>
                        <div class="match-avatar" id="matchTheirAvatar">
                            <img src="" id="matchTheirPhotoImg"
                                 class="w-100 h-100" style="object-fit:cover;display:none" alt="">
                            <div id="matchPhotoFallback"
                                 class="w-100 h-100 d-flex align-items-center justify-content-center"
                                 style="background:#2d0a4e;font-size:2.5rem">🧡</div>
                        </div>
                    </div>

                    <div class="match-title mt-2 mb-1 text-white">It's a <span>Match!</span></div>
                    <p style="color:rgba(255,255,255,.65);font-size:.9rem">
                        You and <strong class="text-white" id="match-name"></strong> liked each other 🎉
                    </p>

                    <div class="icebreaker-box">
                        <div class="icebreaker-label">
                            <i class="bi bi-lightbulb-fill" style="color:var(--hc-gold)"></i>
                            Icebreaker suggestion
                        </div>
                        <div class="icebreaker-text" id="icebreaker-prompt"></div>
                        <button class="btn btn-link p-0 mt-2" id="refreshIcebreaker"
                                style="font-size:.75rem;color:rgba(255,255,255,.45);text-decoration:none">
                            <i class="bi bi-arrow-clockwise me-1"></i>Try another
                        </button>
                    </div>

                    <div class="d-flex gap-3 mt-4 justify-content-center">
                        <button class="btn btn-sm px-4 fw-semibold" data-bs-dismiss="modal"
                                style="background:rgba(255,255,255,.1);color:#fff;border:1px solid rgba(255,255,255,.2);border-radius:20px">
                            Keep Swiping
                        </button>
                        <a href="{{ route('conversations.index') }}"
                           class="btn btn-sm px-4 fw-bold" id="matchChatBtn"
                           style="background:linear-gradient(135deg,var(--hc-pink),var(--hc-rose));color:#fff;border:none;border-radius:20px;box-shadow:0 4px 16px rgba(255,62,108,.4)">
                            <i class="bi bi-chat-heart-fill me-2"></i>Send a Message
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ Like-with-message modal ══════════════════════════ --}}
    <div class="modal fade" id="likeMessageModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
                <div class="like-msg-bg p-4">
                    <div class="like-msg-photo">
                        <img src="" id="likeModalImg" class="w-100 h-100" style="object-fit:cover" alt="">
                    </div>
                    <div class="text-center mb-3">
                        <h6 class="fw-bold text-white mb-1">Like <span id="likeModalName"></span>?</h6>
                        <p class="mb-0" style="color:rgba(255,255,255,.45);font-size:.8rem">Add a note to stand out 💌</p>
                    </div>
                    <textarea id="likeMessageInput" rows="3" maxlength="200"
                              class="form-control mb-3"
                              style="background:rgba(255,255,255,.06);color:#fff;border:1px solid rgba(255,255,255,.12);border-radius:12px;resize:none;font-size:.88rem"
                              placeholder="Say something nice… (optional)"
                              oninput="this.classList.remove('is-invalid');document.getElementById('likeNoteError')?.remove()"></textarea>
                    <div class="d-flex gap-2">
                        <button id="likeJustBtn" class="btn btn-sm flex-fill fw-semibold"
                                style="background:rgba(255,255,255,.08);color:#fff;border:1px solid rgba(255,255,255,.15);border-radius:12px">
                            ❤️ Just Like
                        </button>
                        <button id="likeWithNoteBtn" class="btn btn-sm flex-fill fw-bold"
                                style="background:linear-gradient(135deg,var(--hc-pink),var(--hc-rose));color:#fff;border:none;border-radius:12px">
                            💌 Send Note
                        </button>
                    </div>
                    <button class="btn btn-link w-100 mt-2 p-0 text-center"
                            style="font-size:.78rem;color:rgba(255,255,255,.3);text-decoration:none"
                            data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ Toast ════════════════════════════════════════════ --}}
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">
        <div id="swipe-toast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div id="swipe-toast-body" class="toast-body fw-semibold" style="color:#fff"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    {{-- ══ Like limit overlay ═══════════════════════════════ --}}
    <div id="limit-overlay" class="swipe-overlay" style="display:none">
        <div class="overlay-icon">⏳</div>
        <h4>Daily Like Limit Reached</h4>
        <p style="color:rgba(255,255,255,.55);max-width:280px;font-size:.9rem">
            You've used all your free likes today. Your limit resets in:
        </p>
        <div class="countdown-display" id="swipe-countdown">--:--:--</div>
        <p style="color:rgba(255,255,255,.4);font-size:.78rem;margin-bottom:20px">We'll email you when your likes reset!</p>
        <a href="{{ route('premium.show') }}"
           class="btn fw-bold px-5 py-2"
           style="background:linear-gradient(135deg,var(--hc-gold),#f97316);color:#fff;border:none;border-radius:24px;box-shadow:0 4px 20px rgba(245,158,11,.4)">
            <i class="bi bi-star-fill me-2"></i>Get Premium — Unlimited Likes
        </a>
        <a href="{{ route('discover.index') }}"
           class="btn btn-sm btn-outline-light mt-3"
           style="border-radius:20px;font-size:.82rem">Browse Profiles Instead</a>
    </div>

    {{-- ══ Admin restriction overlay ═══════════════════════ --}}
    <div id="restriction-overlay" class="swipe-overlay"
         style="display:{{ ($isSwipeRestricted ?? false) ? 'flex' : 'none' }}">
        <div class="overlay-icon">🚫</div>
        <h4>Swipes Restricted</h4>
        <p style="color:rgba(255,255,255,.55);max-width:280px">
            Your ability to swipe has been restricted by an administrator.
        </p>
        <a href="{{ route('pages.contact') }}"
           class="btn btn-outline-light mt-3" style="border-radius:20px">Contact Support</a>
    </div>

    @endif
</div>

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
