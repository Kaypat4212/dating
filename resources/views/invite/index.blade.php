@extends('layouts.app')
@section('title', 'Invite Friends')

@push('styles')
<style>
/* ── Invite Page Design ── */
.invite-hero {
    background: linear-gradient(135deg, #f48fb1 0%, #ce93d8 50%, #90caf9 100%);
    border-radius: 1.25rem;
    padding: 2.5rem 2rem;
    color: #fff;
    text-align: center;
    position: relative;
    overflow: hidden;
}
.invite-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.06'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    pointer-events: none;
}
.invite-hero .big-icon {
    width: 80px;
    height: 80px;
    background: rgba(255,255,255,0.25);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.25rem;
    backdrop-filter: blur(8px);
    font-size: 2.25rem;
}
.ref-code-box {
    background: rgba(255,255,255,0.18);
    border: 1.5px dashed rgba(255,255,255,0.5);
    border-radius: .85rem;
    padding: .6rem 1.25rem;
    font-family: 'Courier New', monospace;
    font-size: 1.5rem;
    font-weight: 700;
    letter-spacing: 4px;
    display: inline-block;
    cursor: default;
    user-select: all;
}
.invite-link-group {
    display: flex;
    gap: .5rem;
}
.invite-link-group input {
    flex: 1;
    min-width: 0;
    font-size: .85rem;
    background: var(--bs-body-bg);
    border: 1px solid var(--bs-border-color);
    border-radius: .75rem;
    padding: .5rem .85rem;
    color: var(--bs-body-color);
}
.invite-link-group input:focus { outline: none; box-shadow: none; border-color: #f48fb1; }
.step-card {
    border: 1.5px solid var(--bs-border-color);
    border-radius: 1rem;
    padding: 1.25rem 1rem;
    text-align: center;
    background: var(--bs-body-bg);
    transition: border-color .2s, transform .2s;
    height: 100%;
}
.step-card:hover { border-color: #f48fb1; transform: translateY(-2px); }
.step-icon {
    width: 52px; height: 52px;
    background: linear-gradient(135deg, #fce4ec, #f3e5f5);
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto .85rem;
    font-size: 1.4rem;
}
[data-bs-theme="dark"] .step-icon { background: linear-gradient(135deg,#3d1a26,#2d1b3d); }
.share-btn {
    display: flex; align-items: center; gap: .5rem;
    padding: .55rem 1.1rem;
    border-radius: .75rem;
    font-size: .875rem;
    font-weight: 500;
    border: 1.5px solid var(--bs-border-color);
    text-decoration: none;
    color: var(--bs-body-color);
    background: var(--bs-body-bg);
    transition: all .15s;
    cursor: pointer;
}
.share-btn:hover { color: #f48fb1; border-color: #f48fb1; }
.share-btn.whatsapp:hover { color: #25D366; border-color: #25D366; }
.share-btn.telegram:hover { color: #2CA5E0; border-color: #2CA5E0; }
.share-btn.twitter:hover  { color: #1DA1F2; border-color: #1DA1F2; }
.ref-row {
    display: flex; align-items: center; gap: .75rem;
    padding: .75rem 0;
    border-bottom: 1px solid var(--bs-border-color);
}
.ref-row:last-child { border-bottom: none; }
.ref-avatar {
    width: 40px; height: 40px; border-radius: 50%;
    background: linear-gradient(135deg,#f48fb1,#ce93d8);
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-weight: 700; font-size: 1rem; flex-shrink: 0;
}
</style>
@endpush

@section('content')
<div class="container py-4">
    <div class="row g-4">

        {{-- ── Hero card ── --}}
        <div class="col-12">
            <div class="invite-hero">
                <div class="big-icon">🎁</div>
                <h2 class="fw-bold mb-2" style="font-size:1.75rem">Invite Friends &amp; Grow Together</h2>
                <p class="mb-3" style="opacity:.88;max-width:500px;margin:0 auto">
                    Share your personal invite link. Every friend who signs up is counted under your referral.
                </p>
                <div class="ref-code-box mb-3">{{ $user->referral_code }}</div>
                <p class="small mb-0" style="opacity:.72">Your unique referral ID — share this code or the link below</p>
            </div>
        </div>

        {{-- ── Invite link + share ── --}}
        <div class="col-12 col-lg-7">
            <div class="card border-0 shadow-sm" style="border-radius:1.1rem">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3"><i class="bi bi-link-45deg me-2 text-primary"></i>Your Invite Link</h5>

                    <div class="invite-link-group mb-3">
                        <input type="text" id="inviteLink" value="{{ $inviteLink }}" readonly aria-label="Invite link">
                        <button class="btn btn-sm px-3" id="copyBtn"
                            style="background:linear-gradient(135deg,#f48fb1,#ce93d8);color:#fff;border:none;border-radius:.75rem;font-weight:600;white-space:nowrap"
                            onclick="copyInviteLink()">
                            <i class="bi bi-clipboard me-1"></i><span id="copyBtnText">Copy</span>
                        </button>
                    </div>

                    <p class="text-muted small mb-3">Or share directly via:</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a class="share-btn whatsapp" id="shareWhatsApp" href="#" target="_blank" rel="noopener">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            WhatsApp
                        </a>
                        <a class="share-btn telegram" id="shareTelegram" href="#" target="_blank" rel="noopener">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                            Telegram
                        </a>
                        <a class="share-btn twitter" id="shareTwitter" href="#" target="_blank" rel="noopener">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            X / Twitter
                        </a>
                        <button class="share-btn" onclick="nativeShare()">
                            <i class="bi bi-share"></i> More
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Stats ── --}}
        <div class="col-12 col-lg-5">
            <div class="card border-0 shadow-sm h-100" style="border-radius:1.1rem">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3"><i class="bi bi-bar-chart-line me-2 text-success"></i>Your Referral Stats</h5>
                    <div class="row g-3 mb-2">
                        <div class="col-6">
                            <div class="p-3 rounded-3 text-center h-100" style="background:var(--bs-secondary-bg);border:1px solid var(--bs-border-color)">
                                <div class="fs-2 fw-bold" style="color:#f48fb1">{{ $referrals->count() }}</div>
                                <div class="small text-muted">Friends Invited</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 rounded-3 text-center h-100" style="background:var(--bs-secondary-bg);border:1px solid var(--bs-border-color)">
                                <div class="fs-2 fw-bold" style="color:#ce93d8">{{ $referrals->where('rewarded', true)->count() }}</div>
                                <div class="small text-muted">Rewards Earned</div>
                            </div>
                        </div>
                    </div>
                    @if($user->referredByUser)
                    <div class="alert alert-info d-flex align-items-center gap-2 py-2 px-3 mb-0 mt-3" style="border-radius:.75rem;font-size:.875rem">
                        <i class="bi bi-person-check-fill"></i>
                        <span>You were invited by <strong>{{ $user->referredByUser->name }}</strong></span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── How it works ── --}}
        <div class="col-12">
            <h5 class="fw-semibold mb-3">How it Works</h5>
            <div class="row g-3">
                <div class="col-12 col-sm-4">
                    <div class="step-card">
                        <div class="step-icon">🔗</div>
                        <h6 class="fw-semibold mb-1">1. Share Your Link</h6>
                        <p class="small text-muted mb-0">Send your unique referral link to friends via WhatsApp, Telegram, social media — anywhere.</p>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="step-card">
                        <div class="step-icon">👤</div>
                        <h6 class="fw-semibold mb-1">2. Friend Signs Up</h6>
                        <p class="small text-muted mb-0">When they register using your link, they are automatically linked to your account.</p>
                    </div>
                </div>
                <div class="col-12 col-sm-4">
                    <div class="step-card">
                        <div class="step-icon">🎉</div>
                        <h6 class="fw-semibold mb-1">3. Both Benefit</h6>
                        <p class="small text-muted mb-0">You grow your network and both of you enjoy a better connection on the platform.</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Referral list ── --}}
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius:1.1rem">
                <div class="card-body p-4">
                    <h5 class="fw-semibold mb-3"><i class="bi bi-people me-2 text-primary"></i>People You've Invited
                        <span class="badge rounded-pill ms-2" style="background:#f48fb1;font-size:.75rem">{{ $referrals->count() }}</span>
                    </h5>

                    @forelse($referrals as $ref)
                    <div class="ref-row">
                        <div class="ref-avatar">{{ strtoupper(substr($ref->referred->name ?? '?', 0, 1)) }}</div>
                        <div class="flex-grow-1 min-width-0">
                            <div class="fw-semibold small">{{ $ref->referred->name ?? 'Unknown' }}</div>
                            <div class="text-muted" style="font-size:.78rem">Joined {{ $ref->created_at->diffForHumans() }}</div>
                        </div>
                        @if($ref->rewarded)
                        <span class="badge bg-success-subtle text-success rounded-pill px-2 py-1" style="font-size:.72rem"><i class="bi bi-check-circle me-1"></i>Rewarded</span>
                        @else
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-1" style="font-size:.72rem">Joined</span>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-4">
                        <div style="font-size:2.5rem;margin-bottom:.5rem">✉️</div>
                        <p class="text-muted mb-0 small">No referrals yet. Share your link above to get started!</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div><!-- /row -->
</div><!-- /container -->
@endsection

@push('scripts')
<script>
(function () {
    const link = document.getElementById('inviteLink').value;
    const text = encodeURIComponent("Join me on {{ \App\Models\SiteSetting::get('site_name', config('app.name')) }} — find real connections! Sign up with my link: " + link);

    document.getElementById('shareWhatsApp').href  = 'https://wa.me/?text=' + text;
    document.getElementById('shareTelegram').href  = 'https://t.me/share/url?url=' + encodeURIComponent(link) + '&text=' + encodeURIComponent("Join me on {{ \App\Models\SiteSetting::get('site_name', config('app.name')) }}!");
    document.getElementById('shareTwitter').href   = 'https://twitter.com/intent/tweet?text=' + text;
})();

function copyInviteLink() {
    const input = document.getElementById('inviteLink');
    const btn   = document.getElementById('copyBtnText');
    input.select();
    input.setSelectionRange(0, 99999);
    try {
        navigator.clipboard.writeText(input.value).catch(() => document.execCommand('copy'));
    } catch(e) {
        document.execCommand('copy');
    }
    btn.textContent = 'Copied!';
    setTimeout(() => btn.textContent = 'Copy', 2000);
}

function nativeShare() {
    const link = document.getElementById('inviteLink').value;
    if (navigator.share) {
        navigator.share({
            title: '{{ \App\Models\SiteSetting::get("site_name", config("app.name")) }}',
            text:  'Join me and find real connections!',
            url:   link,
        }).catch(() => {});
    } else {
        copyInviteLink();
    }
}
</script>
@endpush
