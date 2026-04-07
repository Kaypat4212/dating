@extends('layouts.app')
@section('title', 'Account Settings')
@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <h4 class="fw-bold mb-4"><i class="bi bi-gear me-2 text-primary"></i>Account Settings</h4>

            {{-- Premium Status --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold"><i class="bi bi-star-fill text-warning me-2"></i>Membership</div>
                <div class="card-body">
                    @if($user->isPremiumActive())
                    <div class="d-flex justify-content-between align-items-center">
                        <div><span class="badge bg-warning text-dark me-2">Premium</span> Active until {{ $user->premium_expires_at->format('M j, Y') }}</div>
                        <a href="{{ route('premium.show') }}" class="btn btn-sm btn-outline-warning">Renew</a>
                    </div>
                    @else
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted">Free account</span>
                        <a href="{{ route('premium.show') }}" class="btn btn-sm btn-warning fw-bold"><i class="bi bi-star-fill me-1"></i>Upgrade</a>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Pause profile --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold"><i class="bi bi-pause-circle me-2"></i>Discovery Visibility</div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">{{ $user->profile?->is_paused ? 'Profile Hidden' : 'Profile Visible' }}</div>
                        <div class="text-muted small">When hidden, you will not appear in discovery or search results.</div>
                    </div>
                    <form method="POST" action="{{ route('account.pause') }}">@csrf<button class="btn btn-sm {{ $user->profile?->is_paused ? 'btn-success' : 'btn-outline-secondary' }}">{{ $user->profile?->is_paused ? 'Unhide' : 'Hide Profile' }}</button></form>
                </div>
            </div>

            {{-- Last Seen Privacy --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-eye-slash me-2"></i>Last Seen Privacy
                    <span class="badge bg-warning text-dark ms-2 small">Premium</span>
                </div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">{{ $user->hide_last_seen ? 'Last seen hidden' : 'Last seen visible' }}</div>
                        <div class="text-muted small">When hidden, other users cannot see when you were last active.</div>
                        @if(!$user->isPremiumActive())
                        <div class="text-warning small mt-1"><i class="bi bi-lock-fill me-1"></i>Requires any Premium plan</div>
                        @endif
                    </div>
                    @if($user->isPremiumActive())
                    <form method="POST" action="{{ route('account.last-seen') }}">@csrf
                        <button type="submit" class="btn btn-sm {{ $user->hide_last_seen ? 'btn-success' : 'btn-outline-secondary' }}">
                            {{ $user->hide_last_seen ? 'Unhide' : 'Hide' }}
                        </button>
                    </form>
                    @else
                    <a href="{{ route('premium.show') }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-star-fill me-1"></i>Upgrade
                    </a>
                    @endif
                </div>
            </div>

            {{-- Photo Privacy --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-eye-slash me-2 text-info"></i>Photo Privacy
                    <span class="badge bg-warning text-dark ms-2 small">Premium</span>
                </div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">{{ $user->profile?->private_photos ? 'Photos private' : 'Photos public' }}</div>
                        <div class="text-muted small">When enabled, your photos are blurred for people you haven't matched with.</div>
                        @if(!$user->isPremiumActive())
                        <div class="text-warning small mt-1"><i class="bi bi-lock-fill me-1"></i>Requires any Premium plan</div>
                        @endif
                    </div>
                    @if($user->isPremiumActive())
                    <form method="POST" action="{{ route('account.private-photos') }}">@csrf
                        <button type="submit" class="btn btn-sm {{ $user->profile?->private_photos ? 'btn-info text-white' : 'btn-outline-secondary' }}">
                            {{ $user->profile?->private_photos ? 'Make Public' : 'Make Private' }}
                        </button>
                    </form>
                    @else
                    <a href="{{ route('premium.show') }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-star-fill me-1"></i>Upgrade
                    </a>
                    @endif
                </div>
            </div>

            {{-- Profile Boost --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-rocket-takeoff me-2 text-danger"></i>Profile Boost
                    <span class="badge bg-warning text-dark ms-2 small">Premium</span>
                </div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    @php $boost = $user->activeBoost(); @endphp
                    <div>
                        @if($boost)
                        <div class="fw-semibold text-danger">
                            <i class="bi bi-fire me-1"></i>Boost active!
                        </div>
                        <div class="text-muted small">Your profile is at the top of the deck until {{ $boost->ends_at->format('g:i A') }}</div>
                        @else
                        <div class="fw-semibold">Appear first in everyone's discovery deck</div>
                        <div class="text-muted small">A boost puts you at the top for 30 minutes — great for getting more matches quickly.</div>
                        @if(!$user->isPremiumActive())
                        <div class="text-warning small mt-1"><i class="bi bi-lock-fill me-1"></i>Requires any Premium plan</div>
                        @endif
                        @endif
                    </div>
                    @if($boost)
                    <form method="POST" action="{{ route('boost.destroy') }}">@csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-secondary">Cancel</button>
                    </form>
                    @elseif($user->isPremiumActive())
                    <form method="POST" action="{{ route('boost.store') }}">@csrf
                        <button class="btn btn-sm btn-danger fw-bold">
                            <i class="bi bi-rocket-takeoff me-1"></i>Boost Now
                        </button>
                    </form>
                    @else
                    <a href="{{ route('premium.show') }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-star-fill me-1"></i>Upgrade
                    </a>
                    @endif
                </div>
            </div>

            {{-- Blocked Users --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold"><i class="bi bi-slash-circle me-2 text-danger"></i>Blocked Users</div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">Manage your block list</div>
                        <div class="text-muted small">View and unblock users you've blocked.</div>
                    </div>
                    <a href="{{ route('account.blocked') }}" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-list me-1"></i>View Blocks
                    </a>
                </div>
            </div>

            {{-- Secret Word for Password Recovery --}}
            <div class="card border-0 shadow-sm mb-4">

            {{-- Two-Factor Authentication --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold">
                    <i class="bi bi-shield-lock me-2 text-primary"></i>Two-Factor Authentication (2FA)
                    @if($user->totp_secret)
                    <span class="badge bg-success ms-2 small"><i class="bi bi-check-circle me-1"></i>Enabled</span>
                    @else
                    <span class="badge bg-secondary ms-2 small">Disabled</span>
                    @endif
                </div>
                <div class="card-body">
                    @if($user->totp_secret)
                    {{-- Disable form --}}
                    <p class="text-muted small mb-3">2FA is active. Each sign-in requires a code from your authenticator app.</p>
                    <form method="POST" action="{{ route('account.2fa.disable') }}" id="twoFaDisableForm">
                        @csrf
                        <div class="input-group" style="max-width:380px">
                            <input type="password" name="password" class="form-control @error('password','disable2fa') is-invalid @enderror"
                                   placeholder="Confirm current password" required>
                            <button type="submit" class="btn btn-danger" onclick="return confirm('Disable 2FA?')">
                                <i class="bi bi-shield-x me-1"></i>Disable 2FA
                            </button>
                        </div>
                        @error('password','disable2fa')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </form>
                    @else
                    {{-- Enable section --}}
                    <p class="text-muted small mb-3">
                        Protect your account by requiring a time-based one-time code from an app like
                        <strong>Google Authenticator</strong> or <strong>Authy</strong> at each sign-in.
                    </p>
                    <div id="twoFaSetupSection" style="display:none">
                        <div class="mb-3 d-flex flex-wrap align-items-start gap-4">
                            <div id="twoFaQr" class="border rounded p-2 bg-white"></div>
                            <div>
                                <div class="fw-semibold small mb-1">Or enter the key manually:</div>
                                <code id="twoFaSecret" class="user-select-all" style="font-size:.85rem;word-break:break-all"></code>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('account.2fa.enable') }}">
                            @csrf
                            <div class="input-group" style="max-width:340px">
                                <input type="text" name="totp_code" id="totpCodeInput"
                                       class="form-control @error('totp_code') is-invalid @enderror"
                                       inputmode="numeric" pattern="[0-9]{6}" maxlength="6"
                                       placeholder="6-digit code from app" autocomplete="one-time-code" required>
                                <button type="submit" class="btn btn-success">
                                    <i class="bi bi-check-circle me-1"></i>Verify & Enable
                                </button>
                            </div>
                            @error('totp_code')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                        </form>
                    </div>
                    <button id="twoFaStartBtn" class="btn btn-primary btn-sm" onclick="initTwoFa(this)">
                        <i class="bi bi-qr-code me-1"></i>Set Up 2FA
                    </button>
                    @endif
                </div>
            </div>
                <div class="card-header bg-transparent fw-semibold"><i class="bi bi-key-fill text-warning me-2"></i>Password Recovery Secret Word</div>
                <div class="card-body">
                    <p class="text-muted small mb-3">
                        Set a personal secret word. If you ever forget your password, you can use it to reset your password without needing an email link.
                        @if($user->secret_word)
                        <span class="badge bg-success ms-1"><i class="bi bi-check-lg me-1"></i>Set</span>
                        @else
                        <span class="badge bg-secondary ms-1">Not set</span>
                        @endif
                    </p>
                    <form method="POST" action="{{ route('account.secret-word') }}">
                        @csrf
                        <div class="input-group">
                            <input type="password" name="secret_word" id="secretWordInput"
                                   class="form-control @error('secret_word') is-invalid @enderror"
                                   placeholder="{{ $user->secret_word ? 'Enter new secret word to change it' : 'e.g. My first pet name' }}"
                                   minlength="3" maxlength="100" required>
                            <button type="button" class="btn btn-outline-secondary" id="toggleSecretWordBtn" title="Show/hide">
                                <i class="bi bi-eye" id="toggleSecretWordIcon"></i>
                            </button>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-floppy me-1"></i>Save</button>
                        </div>
                        @error('secret_word')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </form>
                </div>
            </div>

            {{-- Email Notification Preferences --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold"><i class="bi bi-bell me-2"></i>Email Notifications</div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Choose which events trigger an email notification. In-app notifications are always on.</p>
                    <form method="POST" action="{{ route('account.notification-prefs') }}">
                        @csrf
                        @php $prefs = auth()->user()->preferences; @endphp
                        <div class="row g-3">
                            @foreach ([
                                ['key' => 'email_new_message',     'label' => 'New message',            'desc' => 'Someone sends you a message'],
                                ['key' => 'email_new_match',       'label' => 'New match',              'desc' => 'You and someone like each other'],
                                ['key' => 'email_profile_liked',   'label' => 'Profile liked',          'desc' => 'Someone likes your profile'],
                                ['key' => 'email_wave_received',   'label' => 'Wave received',          'desc' => 'Someone sends you a wave'],
                                ['key' => 'email_travel_interest', 'label' => 'Travel interest',        'desc' => 'Someone is interested in your travel plan'],
                                ['key' => 'email_login_alert',     'label' => 'Login alert',            'desc' => 'A new login is detected on your account'],
                            ] as $item)
                            <div class="col-12 col-sm-6">
                                <div class="form-check form-switch d-flex align-items-start gap-2">
                                    <input class="form-check-input mt-1 flex-shrink-0" type="checkbox"
                                        role="switch"
                                        name="{{ $item['key'] }}"
                                        id="pref_{{ $item['key'] }}"
                                        value="1"
                                        {{ ($prefs?->wantsEmail($item['key']) ?? true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="pref_{{ $item['key'] }}">
                                        <span class="fw-semibold d-block">{{ $item['label'] }}</span>
                                        <span class="text-muted small">{{ $item['desc'] }}</span>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-3 text-end">
                            <button type="submit" class="btn btn-sm btn-primary"><i class="bi bi-floppy me-1"></i>Save preferences</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- GDPR Export --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold"><i class="bi bi-download me-2"></i>Your Data</div>
                <div class="card-body d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">Download your data</div>
                        <div class="text-muted small">Get a full export of everything stored about your account (GDPR).</div>
                    </div>
                    <a href="{{ route('account.export') }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-download me-1"></i>Export</a>
                </div>
            </div>

            {{-- Delete Account --}}
            <div class="card border-0 border-danger shadow-sm mb-4">
                <div class="card-header bg-transparent fw-semibold text-danger"><i class="bi bi-trash3-fill me-2"></i>Danger Zone</div>
                <div class="card-body">
                    <p class="text-muted small mb-3">Permanently delete your account, profile, photos, matches, and all messages. This cannot be undone.</p>
                    <button class="btn btn-danger btn-sm" data-bs-toggle="collapse" data-bs-target="#deleteForm"><i class="bi bi-trash3-fill me-2"></i>Delete My Account</button>
                    <div class="collapse mt-3" id="deleteForm">
                        <form method="POST" action="{{ route('account.destroy') }}" onsubmit="return confirm('Are you absolutely sure? This CANNOT be undone.')">
                            @csrf @method('DELETE')
                            <div class="mb-3">
                                <label class="form-label">Confirm your password to continue</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <button type="submit" class="btn btn-danger fw-bold">Yes, permanently delete my account</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
<script>
function initTwoFa(btn) {
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Loading…';
    fetch('{{ route("account.2fa.setup") }}')
        .then(r => r.json())
        .then(data => {
            document.getElementById('twoFaQr').innerHTML     = data.qr;
            document.getElementById('twoFaSecret').textContent = data.secret;
            document.getElementById('twoFaSetupSection').style.display = '';
            btn.style.display = 'none';
        })
        .catch(() => {
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-qr-code me-1"></i>Set Up 2FA';
        });
}
document.getElementById('toggleSecretWordBtn')?.addEventListener('click', function () {
    var inp  = document.getElementById('secretWordInput');
    var icon = document.getElementById('toggleSecretWordIcon');
    var show = inp.type === 'password';
    inp.type   = show ? 'text' : 'password';
    icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
});
</script>
@endpush
@endsection
