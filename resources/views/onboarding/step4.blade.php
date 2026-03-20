@extends('layouts.app')
@section('title', isset($is_edit) ? 'Match Preferences' : 'Setup — Step 4 of 5')
@section('content')
<div class="container py-5" style="max-width:640px">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Progress --}}
    <div class="mb-4">
        <div class="d-flex justify-content-between align-items-center mb-1">
            <span class="small fw-semibold text-muted">Step {{ $step }} of {{ $total }}</span>
            <span class="small text-muted">{{ round(($step/$total)*100) }}% complete</span>
        </div>
        <div class="progress" style="height:6px;border-radius:10px">
            <div class="progress-bar bg-primary" style="width:{{ round(($step/$total)*100) }}%"></div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5">
        <div class="text-center mb-4">
            <div class="display-5 mb-2">📍</div>
            <h3 class="fw-bold">Location &amp; Preferences</h3>
            <p class="text-muted">Tell us where you are and who you're looking for.</p>
        </div>

        <form method="POST" action="{{ isset($is_edit) ? route('preferences.update') : route('setup.store', ['step' => 4]) }}">
            @csrf

            <h6 class="fw-bold text-uppercase text-muted small mb-3" style="letter-spacing:.06em">Your Location</h6>

            @error('location')
                <div class="alert alert-danger py-2"><i class="bi bi-lock-fill me-2"></i>{{ $message }} <a href="{{ route('premium.show') }}" class="alert-link">Upgrade →</a></div>
            @enderror

            @php
                $isPremium       = $is_premium ?? false;
                $locationUpdates = $location_updates ?? 0;
                $locationLocked  = ! $isPremium && $locationUpdates >= 2;
            @endphp

            @if($locationLocked)
            {{-- Free user hit the 2-update cap --}}
            <div class="alert alert-warning rounded-3 mb-3 d-flex align-items-start gap-2">
                <i class="bi bi-lock-fill fs-5 mt-1 text-warning"></i>
                <div>
                    <strong>Location updates used (2/2)</strong><br>
                    <span class="small">Free accounts can only update their location twice.
                    <a href="{{ route('premium.show') }}" class="fw-semibold">⭐ Upgrade to Premium</a> for unlimited location updates and multiple saved locations.</span>
                </div>
            </div>
            @elseif(! $isPremium && $locationUpdates === 1)
            <div class="alert alert-info rounded-3 py-2 mb-3 small">
                <i class="bi bi-info-circle me-1"></i>
                <strong>1 of 2 free location updates used.</strong>
                <a href="{{ route('premium.show') }}" class="ms-1">⭐ Go Premium</a> for unlimited updates.
            </div>
            @endif

            <div class="row g-3 mb-4">
                @php
                    $onbCountry = old('country', $profile?->country ?? '');
                    $onbState   = old('state',   $profile?->state   ?? '');
                @endphp
                <div class="col-sm-4">
                    <label for="city" class="form-label fw-semibold">City</label>
                    <input type="text" name="city" id="city"
                        class="form-control @error('city') is-invalid @enderror"
                        placeholder="Your city"
                        value="{{ old('city', $profile?->city ?? '') }}"
                        {{ $locationLocked ? 'readonly' : '' }}>
                    @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-4">
                    <label for="state" class="form-label fw-semibold">State / Province</label>
                    <select name="state" id="onbStateSelect"
                        class="form-select @error('state') is-invalid @enderror"
                        {{ $locationLocked ? 'disabled' : '' }}>
                        <option value="">Select state…</option>
                        @foreach(\App\Helpers\StateHelper::forCountry($onbCountry) as $s)
                            <option value="{{ $s }}" {{ $onbState === $s ? 'selected' : '' }}>{{ $s }}</option>
                        @endforeach
                    </select>
                    <input type="text" name="state" id="onbStateText"
                        class="form-control mt-1 @error('state') is-invalid @enderror
                               {{ \App\Helpers\StateHelper::hasStates($onbCountry) ? 'd-none' : '' }}"
                        placeholder="State or region"
                        value="{{ $onbState }}"
                        {{ $locationLocked ? 'readonly' : '' }}>
                    @if($locationLocked)
                        <input type="hidden" name="state" value="{{ $onbState }}">
                    @endif
                    @error('state')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-sm-4">
                    <label for="country" class="form-label fw-semibold">Country</label>
                    <select name="country" id="onbCountry"
                        class="form-select @error('country') is-invalid @enderror"
                        {{ $locationLocked ? 'disabled' : '' }}>
                        <option value="">Select country…</option>
                        @foreach(\App\Helpers\CountryHelper::list() as $name => $code)
                            <option value="{{ $name }}"
                                {{ $onbCountry === $name ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                    {{-- Keep a hidden input when disabled so value is still submitted --}}
                    @if($locationLocked)
                        <input type="hidden" name="country" value="{{ $profile?->country ?? '' }}">
                    @endif
                    @error('country')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-12">
                    @if(! $locationLocked)
                    <button type="button" id="detect-location" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-geo-alt me-1"></i> Detect my location
                    </button>
                    @else
                    <a href="{{ route('premium.show') }}" class="btn btn-sm btn-warning fw-semibold">
                        <i class="bi bi-lock-fill me-1"></i> Unlock location updates — Go Premium
                    </a>
                    @endif
                    <input type="hidden" name="latitude"  id="latitude"  value="{{ old('latitude',  $profile?->latitude  ?? '') }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $profile?->longitude ?? '') }}">
                    <small id="geo-status" class="text-muted ms-2"></small>
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold text-uppercase text-muted small mb-3" style="letter-spacing:.06em">Match Preferences</h6>

            {{-- Seeking Gender --}}
            <div class="mb-4">
                <label for="seeking_gender" class="form-label fw-semibold">Interested in</label>
                <select name="seeking_gender" id="seeking_gender" class="form-select @error('seeking_gender') is-invalid @enderror">
                    <option value="">Select…</option>
                    @foreach(['male'=>'Men','female'=>'Women','everyone'=>'Everyone'] as $v=>$l)
                    <option value="{{ $v }}" {{ old('seeking_gender', $preference->seeking_gender ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
                @error('seeking_gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>

            {{-- Preferred State --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">Preferred State / Province <small class="text-muted fw-normal">(show me people from this state first)</small></label>
                <select name="preferred_state" id="onbPrefStateSelect" class="form-select">
                    <option value="">Any state (no preference)</option>
                    @foreach(\App\Helpers\StateHelper::forCountry($onbCountry) as $s)
                        <option value="{{ $s }}" {{ old('preferred_state', $preference->preferred_state ?? '') === $s ? 'selected' : '' }}>{{ $s }}</option>
                    @endforeach
                </select>
                <input type="text" name="preferred_state" id="onbPrefStateText"
                       class="form-control mt-1 {{ \App\Helpers\StateHelper::hasStates($onbCountry) ? 'd-none' : '' }}"
                       placeholder="Preferred state (optional)"
                       value="{{ old('preferred_state', $preference->preferred_state ?? '') }}">
            </div>

            {{-- Age Range --}}
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    Age Range:
                    <span id="age-label">
                        {{ old('min_age', $preference->min_age ?? 18) }} – {{ old('max_age', $preference->max_age ?? 50) }}
                    </span>
                </label>
                <div class="d-flex gap-3 align-items-center">
                    <input type="range" class="form-range" name="min_age" id="min_age"
                        min="18" max="80"
                        value="{{ old('min_age', $preference->min_age ?? 18) }}">
                    <input type="range" class="form-range" name="max_age" id="max_age"
                        min="18" max="100"
                        value="{{ old('max_age', $preference->max_age ?? 50) }}">
                </div>
                <div class="d-flex justify-content-between">
                    <small class="text-muted">Min: <span id="min-age-val">{{ old('min_age', $preference->min_age ?? 18) }}</span></small>
                    <small class="text-muted">Max: <span id="max-age-val">{{ old('max_age', $preference->max_age ?? 50) }}</span></small>
                </div>
                @error('min_age')<div class="text-danger small">{{ $message }}</div>@enderror
                @error('max_age')<div class="text-danger small">{{ $message }}</div>@enderror
            </div>

            {{-- Max Distance --}}
            <div class="mb-4">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <label for="max_distance_km" class="form-label fw-semibold mb-0">
                        Maximum Distance: <span id="dist-val">{{ old('max_distance_km', $preference->max_distance_km ?? 'Any') }}</span>
                        <span id="dist-unit">{{ old('max_distance_km', $preference->max_distance_km) ? 'km' : '' }}</span>
                    </label>
                    <button type="button" id="reset-distance" class="btn btn-sm btn-link p-0 text-decoration-none"
                            style="color:#e91e8c;font-size:.8rem"
                            title="Remove distance filter — show all users">
                        <i class="bi bi-x-circle me-1"></i>Reset (show all)
                    </button>
                </div>
                <input type="range" class="form-range" name="max_distance_km" id="max_distance_km"
                    min="1" max="500" step="5"
                    value="{{ old('max_distance_km', $preference->max_distance_km ?? 100) }}">
                <div class="d-flex justify-content-between">
                    <small class="text-muted">1 km</small>
                    <small class="text-muted">500 km</small>
                </div>
                <div id="dist-reset-note" class="mt-1" style="display:{{ old('max_distance_km', $preference->max_distance_km) ? 'none' : 'block' }}">
                    <small class="text-success"><i class="bi bi-check-circle me-1"></i>No distance limit — showing all users regardless of location.</small>
                </div>
            </div>

            <div class="d-flex justify-content-between mt-4">
                @if(isset($is_edit))
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                @else
                    <a href="{{ route('setup.step', 2) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back
                    </a>
                @endif
                <button type="submit" class="btn btn-primary px-4 fw-bold">
                    {{ isset($is_edit) ? 'Save Preferences' : 'Continue' }}
                    <i class="bi bi-{{ isset($is_edit) ? 'check-lg' : 'arrow-right' }} ms-1"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// ── Range label sync ─────────────────────────────────────────────────
const minA     = document.getElementById('min_age');
const maxA     = document.getElementById('max_age');
const minAV    = document.getElementById('min-age-val');
const maxAV    = document.getElementById('max-age-val');
const ageLabel = document.getElementById('age-label');
function syncAge() {
    if (parseInt(minA.value) > parseInt(maxA.value)) maxA.value = minA.value;
    minAV.textContent = minA.value;
    maxAV.textContent = maxA.value;
    ageLabel.textContent = `${minA.value} – ${maxA.value}`;
}
minA.addEventListener('input', syncAge);
maxA.addEventListener('input', syncAge);

const dist = document.getElementById('max_distance_km');
dist.addEventListener('input', () => {
    document.getElementById('dist-val').textContent = dist.value;
    document.getElementById('dist-unit').textContent = ' km';
    dist.name = 'max_distance_km';
    document.getElementById('dist-reset-note').style.display = 'none';
});

document.getElementById('reset-distance').addEventListener('click', function () {
    // Clear the input name so null is submitted (no distance preference)
    dist.name = '';
    document.getElementById('dist-val').textContent = 'Any';
    document.getElementById('dist-unit').textContent = '';
    document.getElementById('dist-reset-note').style.display = 'block';
    // Send a hidden field with empty value to signal the reset
    let hidden = document.getElementById('dist-reset-input');
    if (!hidden) {
        hidden = document.createElement('input');
        hidden.type = 'hidden';
        hidden.id = 'dist-reset-input';
        hidden.name = 'max_distance_km';
        hidden.value = '';
        dist.closest('form').appendChild(hidden);
    }
});

// ── Geolocation: GPS (HTTPS) with IP-API fallback (HTTP) ────────────
const detectBtn = document.getElementById('detect-location');
if (detectBtn) {
    detectBtn.addEventListener('click', function () {
        const btn    = this;
        const status = document.getElementById('geo-status');

        const isSecure = location.protocol === 'https:'
            || location.hostname === 'localhost'
            || location.hostname === '127.0.0.1';

        btn.disabled  = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Detecting…';
        status.textContent = '';

        // ── Helper: fill all location fields ──
        function fillFields(city, state, country, lat, lng) {
            if (city)    document.getElementById('city').value    = city;
            if (state)   document.getElementById('state').value   = state;
            if (country) document.getElementById('country').value = country;
            if (lat)     document.getElementById('latitude').value  = lat;
            if (lng)     document.getElementById('longitude').value = lng;
            const parts = [city, state, country].filter(Boolean);
            status.innerHTML = '<span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Detected: ' + parts.join(', ') + '</span>';
            btn.innerHTML = '<i class="bi bi-geo-alt-fill me-1"></i> Location detected!';
            btn.disabled  = false;
        }

        // ── IP-based fallback via ipapi.co (works on HTTP) ──
        async function ipFallback() {
            status.innerHTML = '<span class="text-muted"><i class="bi bi-globe me-1"></i> GPS unavailable — detecting via network…</span>';
            try {
                const res  = await fetch('https://ipapi.co/json/');
                const data = await res.json();
                if (data && data.city) {
                    fillFields(
                        data.city         || '',
                        data.region       || '',
                        data.country_name || '',
                        data.latitude     || null,
                        data.longitude    || null
                    );
                } else {
                    throw new Error('empty');
                }
            } catch (_) {
                status.innerHTML = '<span class="text-warning">⚠️ Could not auto-detect. Please type your city, state and country manually.</span>';
                btn.innerHTML = '<i class="bi bi-geo-alt me-1"></i> Detect my location';
                btn.disabled  = false;
            }
        }

        // ── If on HTTP + IP address, skip GPS and go straight to IP fallback ──
        if (!isSecure) {
            ipFallback();
            return;
        }

        if (!navigator.geolocation) {
            ipFallback();
            return;
        }

        // ── GPS path (HTTPS only) ──
        navigator.geolocation.getCurrentPosition(
            async function (pos) {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                document.getElementById('latitude').value  = lat;
                document.getElementById('longitude').value = lng;

                // Reverse-geocode via Nominatim for human-readable address
                try {
                    const res  = await fetch(
                        'https://nominatim.openstreetmap.org/reverse?lat=' + lat + '&lon=' + lng + '&format=json&accept-language=en',
                        { headers: { 'Accept': 'application/json' } }
                    );
                    const data = await res.json();
                    const addr    = data.address ?? {};
                    const city    = addr.city || addr.town || addr.village || addr.municipality || addr.county || '';
                    const state   = addr.state || addr.state_district || '';
                    const country = addr.country || '';
                    fillFields(city, state, country, lat, lng);
                } catch (_) {
                    status.innerHTML = '<span class="text-warning">✓ Coordinates saved — please confirm your city, state &amp; country above.</span>';
                    btn.innerHTML = '<i class="bi bi-geo-alt me-1"></i> Detect my location';
                    btn.disabled  = false;
                }
            },
            async function (err) {
                // Permission denied or unavailable → try IP fallback
                if (err.code === 1 || err.code === 2) {
                    await ipFallback();
                } else {
                    btn.disabled  = false;
                    btn.innerHTML = '<i class="bi bi-geo-alt me-1"></i> Detect my location';
                    status.innerHTML = '<span class="text-warning">⚠️ Request timed out — please try again.</span>';
                }
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 300000 }
        );
    });
}

// ── State cascade for onboarding ──────────────────────────────────────────────
(function () {
    const countryEl = document.getElementById('onbCountry');
    const stateEl   = document.getElementById('onbStateSelect');
    const stateText = document.getElementById('onbStateText');
    const prefEl    = document.getElementById('onbPrefStateSelect');
    const prefText  = document.getElementById('onbPrefStateText');
    if (!countryEl) return;

    async function loadStates(country, stateVal, prefVal) {
        try {
            const res    = await fetch(`{{ route('api.states') }}?country=${encodeURIComponent(country)}`);
            const states = await res.json();

            function populate(sel, txt, selected, anyLabel) {
                if (!sel) return;
                if (states.length) {
                    sel.innerHTML = `<option value="">${anyLabel}</option>` +
                        states.map(s => `<option value="${s}"${s === selected ? ' selected' : ''}>${s}</option>`).join('');
                    sel.classList.remove('d-none'); sel.name = sel.dataset.name || sel.name;
                    if (txt) { txt.classList.add('d-none'); txt.removeAttribute('name'); }
                } else {
                    if (sel) { sel.innerHTML = ''; sel.classList.add('d-none'); sel.removeAttribute('name'); }
                    if (txt) { txt.classList.remove('d-none'); txt.name = txt.dataset.name || 'state'; txt.value = selected || ''; }
                }
            }

            populate(stateEl, stateText, stateVal, 'Select state…');
            if (prefEl) populate(prefEl, prefText, prefVal, 'Any state (no preference)');
        } catch (_) {}
    }

    countryEl.addEventListener('change', function () {
        loadStates(this.value, '', '');
    });

    const init = countryEl.value;
    if (init && stateEl && stateEl.options.length <= 1) {
        loadStates(init,
            '{{ old('state', $profile?->state ?? '') }}',
            '{{ old('preferred_state', $preference->preferred_state ?? '') }}'
        );
    }
})();
</script>
@endsection

