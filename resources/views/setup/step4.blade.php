@extends('layouts.app')
@section('title', 'Create Profile — Step 4')

@push('styles')
<style>
/* ── Step 4 location card ─────────────────────────────── */
.loc-card {
    background: linear-gradient(160deg, #0f0520 0%, #1a0830 60%, #0d0420 100%);
    border: 1px solid rgba(233, 30, 140, .2);
    border-radius: 1.25rem;
    box-shadow: 0 8px 40px rgba(233, 30, 140, .08), 0 2px 8px rgba(0,0,0,.4);
    overflow: hidden;
}
.loc-card-header {
    background: linear-gradient(135deg, rgba(233,30,140,.18) 0%, rgba(168,85,247,.12) 100%);
    border-bottom: 1px solid rgba(233, 30, 140, .15);
    padding: 1.25rem 1.5rem 1rem;
}
.loc-field-label {
    font-size: .75rem;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
    color: rgba(255,255,255,.45);
    margin-bottom: .35rem;
}
.loc-input, .loc-select {
    background: rgba(255,255,255,.05) !important;
    border: 1.5px solid rgba(255,255,255,.1) !important;
    border-radius: .625rem !important;
    color: #fff !important;
    font-size: .875rem;
    transition: border-color .2s, box-shadow .2s;
}
.loc-input:focus, .loc-select:focus {
    background: rgba(255,255,255,.07) !important;
    border-color: rgba(233,30,140,.55) !important;
    box-shadow: 0 0 0 3px rgba(233,30,140,.15) !important;
    outline: none !important;
    color: #fff !important;
}
.loc-input::placeholder { color: rgba(255,255,255,.25) !important; }
.loc-select option { background: #1a0830; color: #fff; }
/* Auto-detect button */
.btn-autodetect {
    background: linear-gradient(135deg, rgba(233,30,140,.15), rgba(168,85,247,.12));
    border: 1.5px solid rgba(233,30,140,.4);
    border-radius: .75rem;
    color: #f472b6;
    font-weight: 700;
    font-size: .875rem;
    padding: .65rem 1.25rem;
    transition: all .2s;
    width: 100%;
}
.btn-autodetect:hover:not(:disabled) {
    background: linear-gradient(135deg, rgba(233,30,140,.3), rgba(168,85,247,.22));
    border-color: rgba(233,30,140,.7);
    color: #fff;
    box-shadow: 0 4px 16px rgba(233,30,140,.25);
    transform: translateY(-1px);
}
.btn-autodetect:disabled { opacity: .6; cursor: not-allowed; transform: none; }
/* Detect status banner */
#detectStatus {
    border-radius: .625rem;
    font-size: .8rem;
    padding: .5rem .9rem;
    display: none;
}
#detectStatus.show { display: flex; }
/* Distance slider */
.loc-range { accent-color: #e91e8c; }
/* Divider */
.loc-divider {
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(233,30,140,.2), transparent);
    margin: 1.25rem 0;
}
/* Continue button */
.btn-continue {
    background: linear-gradient(135deg, #e91e8c, #9c27b0);
    border: none;
    border-radius: .75rem;
    color: #fff;
    font-weight: 800;
    padding: .75rem 1.5rem;
    font-size: .925rem;
    box-shadow: 0 4px 20px rgba(233,30,140,.35);
    transition: all .2s;
}
.btn-continue:hover { box-shadow: 0 6px 28px rgba(233,30,140,.55); transform: translateY(-1px); color: #fff; }
.btn-back {
    background: rgba(255,255,255,.05);
    border: 1.5px solid rgba(255,255,255,.12);
    border-radius: .75rem;
    color: rgba(255,255,255,.6);
    font-weight: 600;
    padding: .75rem 1.5rem;
    transition: all .2s;
}
.btn-back:hover { background: rgba(255,255,255,.09); color: #fff; }
/* Filled indicator */
.field-filled-badge {
    display: inline-flex; align-items: center; gap: .3rem;
    background: rgba(16,185,129,.12); border: 1px solid rgba(16,185,129,.25);
    border-radius: 1rem; color: #34d399; font-size: .72rem; font-weight: 700;
    padding: .15rem .55rem; margin-left: .4rem;
    opacity: 0; transition: opacity .3s;
}
.field-filled-badge.visible { opacity: 1; }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            @include('setup._progress', ['current' => 4])

            <div class="loc-card">
                {{-- ── Card header ────────────────────────────────────────── --}}
                <div class="loc-card-header">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <div class="d-flex align-items-center justify-content-center rounded-2"
                             style="width:32px;height:32px;background:linear-gradient(135deg,#e91e8c,#9c27b0)">
                            <i class="bi bi-geo-alt-fill text-white" style="font-size:.9rem"></i>
                        </div>
                        <h5 class="fw-black mb-0 text-white" style="font-size:1.05rem">Where are you?</h5>
                    </div>
                    <p class="mb-0" style="font-size:.78rem;color:rgba(255,255,255,.4)">
                        Step 4 of 5 &nbsp;·&nbsp; Location &amp; discovery preferences
                    </p>
                </div>

                {{-- ── Form body ──────────────────────────────────────────── --}}
                <div class="p-4">
                    <form method="POST" action="{{ route('setup.store', 4) }}" id="step4Form">
                        @csrf
                        @php
                            $setupCountry  = old('country', session('setup.country', ''));
                            $setupState    = old('state',   session('setup.state',   ''));
                            $setupPrefState = old('preferred_state', session('setup.preferred_state', ''));
                        @endphp

                        {{-- ── Auto-detect button ─────────────────────────── --}}
                        <div class="mb-4">
                            <button type="button" id="detectBtn" class="btn-autodetect">
                                <span id="detectBtnInner">
                                    <i class="bi bi-crosshair2 me-2"></i>Auto-Detect My Location
                                </span>
                                <span id="detectBtnSpinner" class="d-none">
                                    <span class="spinner-border spinner-border-sm me-2" role="status"></span>Detecting…
                                </span>
                            </button>

                            {{-- Status messages --}}
                            <div id="detectStatus" class="mt-2 align-items-center gap-2" style="display:none!important">
                                <i id="detectIcon" class="bi"></i>
                                <span id="detectMsg"></span>
                            </div>
                        </div>

                        {{-- ── City + Country row ──────────────────────────── --}}
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <label class="loc-field-label">
                                    City
                                    <span class="field-filled-badge" id="cityBadge"><i class="bi bi-check2"></i> filled</span>
                                </label>
                                <input type="text" name="city" id="setupCity" class="form-control loc-input"
                                       placeholder="e.g. Lagos"
                                       value="{{ old('city', session('setup.city')) }}" required>
                            </div>
                            <div class="col-6">
                                <label class="loc-field-label">
                                    Country
                                    <span class="field-filled-badge" id="countryBadge"><i class="bi bi-check2"></i> filled</span>
                                </label>
                                <select name="country" id="setupCountry" class="form-select loc-select" required>
                                    <option value="">Select country…</option>
                                    @foreach(\App\Helpers\CountryHelper::list() as $name => $code)
                                        <option value="{{ $name }}" {{ $setupCountry === $name ? 'selected' : '' }}>
                                            {{ $name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- ── State + Preferred State row ────────────────── --}}
                        <div class="row g-3 mb-3">
                            <div class="col-6" id="setupStateWrapper">
                                <label class="loc-field-label">
                                    State / Province
                                    <span class="text-white-50 fw-normal" style="text-transform:none;letter-spacing:0;font-size:.7rem">(optional)</span>
                                    <span class="field-filled-badge" id="stateBadge"><i class="bi bi-check2"></i> filled</span>
                                </label>
                                <select name="state" id="setupStateSelect" class="form-select loc-select">
                                    <option value="">Select state…</option>
                                    @foreach(\App\Helpers\StateHelper::forCountry($setupCountry) as $s)
                                        <option value="{{ $s }}" {{ $setupState === $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="state" id="setupStateText"
                                       class="form-control loc-input d-none"
                                       placeholder="State / Province"
                                       value="{{ $setupState }}">
                            </div>
                            <div class="col-6">
                                <label class="loc-field-label">
                                    Preferred State
                                    <span class="text-white-50 fw-normal" style="text-transform:none;letter-spacing:0;font-size:.7rem">(match pref)</span>
                                </label>
                                <select name="preferred_state" id="setupPrefStateSelect" class="form-select loc-select">
                                    <option value="">Any state</option>
                                    @foreach(\App\Helpers\StateHelper::forCountry($setupCountry) as $s)
                                        <option value="{{ $s }}" {{ $setupPrefState === $s ? 'selected' : '' }}>{{ $s }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="preferred_state" id="setupPrefStateText"
                                       class="form-control loc-input d-none"
                                       placeholder="Preferred state"
                                       value="{{ $setupPrefState }}">
                            </div>
                        </div>

                        <div class="loc-divider"></div>

                        {{-- ── Distance slider ─────────────────────────────── --}}
                        <div class="mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <label class="loc-field-label mb-0">
                                    Max Discovery Distance
                                </label>
                                <div class="d-flex align-items-center gap-2">
                                    <span id="distLabel" class="fw-bold" style="color:#e91e8c;font-size:.85rem">
                                        {{ old('max_distance_km', session('setup.max_distance_km', 50)) }} km
                                    </span>
                                    <button type="button" id="reset-dist-btn"
                                            class="btn btn-sm text-decoration-none p-0 border-0"
                                            style="color:rgba(255,255,255,.3);font-size:.7rem;background:none">
                                        <i class="bi bi-x-circle me-1"></i>Any
                                    </button>
                                </div>
                            </div>
                            <input type="range" id="setup-dist" name="max_distance_km" class="form-range loc-range"
                                   min="5" max="500" step="5"
                                   value="{{ old('max_distance_km', session('setup.max_distance_km', 50)) }}">
                            <div class="d-flex justify-content-between" style="color:rgba(255,255,255,.2);font-size:.68rem;margin-top:.2rem">
                                <span>5 km</span><span>~250 km</span><span>500 km</span>
                            </div>
                        </div>

                        {{-- ── Age range ────────────────────────────────────── --}}
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <label class="loc-field-label">Min Age Preference</label>
                                <input type="number" name="min_age" class="form-control loc-input"
                                       value="{{ old('min_age', session('setup.min_age', 18)) }}" min="18" max="80">
                            </div>
                            <div class="col-6">
                                <label class="loc-field-label">Max Age Preference</label>
                                <input type="number" name="max_age" class="form-control loc-input"
                                       value="{{ old('max_age', session('setup.max_age', 60)) }}" min="18" max="99">
                            </div>
                        </div>

                        {{-- ── Navigation buttons ──────────────────────────── --}}
                        <div class="d-flex gap-2">
                            <a href="{{ route('setup.step', 3) }}" class="btn-back flex-fill d-flex align-items-center justify-content-center gap-2 text-decoration-none">
                                <i class="bi bi-arrow-left"></i> Back
                            </a>
                            <button type="submit" class="btn-continue flex-fill">
                                Continue <i class="bi bi-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    'use strict';

    const countryEl = document.getElementById('setupCountry');
    const cityEl    = document.getElementById('setupCity');
    const stateEl   = document.getElementById('setupStateSelect');
    const stateText = document.getElementById('setupStateText');
    const prefEl    = document.getElementById('setupPrefStateSelect');
    const prefText  = document.getElementById('setupPrefStateText');
    const distRange = document.getElementById('setup-dist');
    const distLabel = document.getElementById('distLabel');
    const resetBtn  = document.getElementById('reset-dist-btn');
    if (!countryEl) return;

    /* ── State AJAX loader ─────────────────────────────────────── */
    async function loadStates(country, stateVal, prefVal) {
        try {
            const res    = await fetch(`{{ route('api.states') }}?country=${encodeURIComponent(country)}`);
            const states = await res.json();

            function populate(sel, txt, selected, anyLabel) {
                if (states.length) {
                    sel.innerHTML = `<option value="">${anyLabel}</option>` +
                        states.map(s => `<option${s === selected ? ' selected' : ''} value="${s}">${s}</option>`).join('');
                    sel.classList.remove('d-none'); sel.name = sel.dataset.name;
                    txt.classList.add('d-none');    txt.removeAttribute('name');
                } else {
                    sel.innerHTML = ''; sel.classList.add('d-none'); sel.removeAttribute('name');
                    txt.classList.remove('d-none'); txt.name = txt.dataset.name;
                    txt.value = selected || '';
                }
            }

            stateEl.dataset.name = 'state';
            stateText.dataset.name = 'state';
            prefEl.dataset.name  = 'preferred_state';
            prefText.dataset.name = 'preferred_state';
            populate(stateEl, stateText, stateVal,  'Select state…');
            populate(prefEl,  prefText,  prefVal,   'Any state');
        } catch (e) {
            console.warn('loadStates error', e);
        }
    }

    countryEl.addEventListener('change', function () {
        loadStates(this.value, '', '');
    });

    // Init: reload states if country is pre-selected
    const init = countryEl.value;
    if (init && stateEl.options.length <= 1) {
        loadStates(init,
            '{{ old('state', session('setup.state', '')) }}',
            '{{ old('preferred_state', session('setup.preferred_state', '')) }}'
        );
    }

    /* ── Distance slider ───────────────────────────────────────── */
    if (distRange && distLabel) {
        distRange.addEventListener('input', function () {
            distLabel.textContent = this.value + ' km';
        });
    }
    if (resetBtn && distRange) {
        resetBtn.addEventListener('click', function () {
            distRange.removeAttribute('name');
            distLabel.textContent = 'Any';
            distRange.value = 50;
            this.style.display = 'none';
        });
    }

    /* ── Auto-detect ───────────────────────────────────────────── */
    const detectBtn     = document.getElementById('detectBtn');
    const detectInner   = document.getElementById('detectBtnInner');
    const detectSpinner = document.getElementById('detectBtnSpinner');
    const detectStatus  = document.getElementById('detectStatus');
    const detectIcon    = document.getElementById('detectIcon');
    const detectMsg     = document.getElementById('detectMsg');

    function showDetectStatus(type, msg) {
        // type: 'info' | 'success' | 'error'
        const colors   = { info: 'rgba(59,130,246,.12)', success: 'rgba(16,185,129,.12)', error: 'rgba(239,68,68,.12)' };
        const borders  = { info: 'rgba(59,130,246,.3)',  success: 'rgba(16,185,129,.3)',  error: 'rgba(239,68,68,.3)'  };
        const textClr  = { info: '#93c5fd',              success: '#34d399',              error: '#fca5a5'              };
        const icons    = { info: 'bi-info-circle',       success: 'bi-check2-circle',     error: 'bi-exclamation-circle' };

        detectStatus.style.cssText  = `display:flex!important;background:${colors[type]};border:1px solid ${borders[type]};color:${textClr[type]}`;
        detectIcon.className        = `bi ${icons[type]}`;
        detectMsg.textContent       = msg;
    }

    function flashBadge(id) {
        const b = document.getElementById(id);
        if (!b) return;
        b.classList.add('visible');
        setTimeout(() => b.classList.remove('visible'), 3500);
    }

    if (detectBtn) {
        detectBtn.addEventListener('click', function () {
            if (!navigator.geolocation) {
                showDetectStatus('error', 'Geolocation is not supported by your browser.');
                return;
            }
            detectBtn.disabled = true;
            detectInner.classList.add('d-none');
            detectSpinner.classList.remove('d-none');
            showDetectStatus('info', 'Requesting your location…');

            navigator.geolocation.getCurrentPosition(
                async function (pos) {
                    const lat = pos.coords.latitude;
                    const lon = pos.coords.longitude;
                    showDetectStatus('info', 'Reverse-geocoding your coordinates…');

                    try {
                        const resp = await fetch(
                            `https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lon}&format=json&addressdetails=1`,
                            { headers: { 'Accept-Language': 'en' } }
                        );
                        const data = await resp.json();
                        const addr = data.address || {};

                        /* ── Map Nominatim fields ─────────────────────── */
                        const detectedCity    = addr.city || addr.town || addr.village || addr.municipality || addr.hamlet || addr.suburb || '';
                        const detectedState   = addr.state || addr.region || addr.county || '';
                        const detectedCountry = addr.country || '';

                        /* ── Fill city ─────────────────────────────────── */
                        if (detectedCity && cityEl) {
                            cityEl.value = detectedCity;
                            flashBadge('cityBadge');
                        }

                        /* ── Fill country (match select by option text) ── */
                        let countryMatched = false;
                        if (detectedCountry && countryEl) {
                            for (const opt of countryEl.options) {
                                if (opt.text.toLowerCase() === detectedCountry.toLowerCase()) {
                                    countryEl.value = opt.value;
                                    countryMatched  = true;
                                    flashBadge('countryBadge');
                                    break;
                                }
                            }
                            // Partial fallback (country may include "Federal Republic of..." etc.)
                            if (!countryMatched) {
                                for (const opt of countryEl.options) {
                                    if (opt.text.toLowerCase().includes(detectedCountry.toLowerCase()) ||
                                        detectedCountry.toLowerCase().includes(opt.text.toLowerCase())) {
                                        countryEl.value = opt.value;
                                        countryMatched  = true;
                                        flashBadge('countryBadge');
                                        break;
                                    }
                                }
                            }
                        }

                        /* ── Load states then auto-select ──────────────── */
                        if (countryMatched && countryEl.value) {
                            await loadStates(countryEl.value, detectedState, detectedState);
                            if (detectedState) flashBadge('stateBadge');
                        }

                        /* ── Status message ────────────────────────────── */
                        const parts = [detectedCity, detectedState, detectedCountry].filter(Boolean);
                        if (parts.length) {
                            showDetectStatus('success', '📍 Filled: ' + parts.join(', '));
                        } else {
                            showDetectStatus('error', 'Could not determine your location details. Please fill in manually.');
                        }
                    } catch (err) {
                        console.error('Geocode error', err);
                        showDetectStatus('error', 'Failed to look up your location. Please fill in manually.');
                    } finally {
                        detectBtn.disabled = false;
                        detectInner.classList.remove('d-none');
                        detectSpinner.classList.add('d-none');
                    }
                },
                function (err) {
                    detectBtn.disabled = false;
                    detectInner.classList.remove('d-none');
                    detectSpinner.classList.add('d-none');
                    const msgs = {
                        1: 'Location permission denied. Please allow access or fill in manually.',
                        2: 'Location unavailable. Please fill in manually.',
                        3: 'Location request timed out. Please try again.'
                    };
                    showDetectStatus('error', msgs[err.code] || 'Location error. Please fill in manually.');
                },
                { timeout: 12000, maximumAge: 60000 }
            );
        });
    }
})();
</script>
@endpush
