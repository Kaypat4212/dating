@extends('layouts.app')
@section('title', 'Create Profile — Step 4')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            @include('setup._progress', ['current' => 4])
            <div class="card border-0 shadow p-4">
                <h4 class="fw-bold mb-1">Where are you?</h4>
                <p class="text-muted small mb-4">Step 4: Location & discovery preferences</p>
                <form method="POST" action="{{ route('setup.store', 4) }}">
                    @csrf
                    <div class="row g-3 mb-3">
                        @php
                            $setupCountry = old('country', session('setup.country', ''));
                            $setupState   = old('state',   session('setup.state',   ''));
                        @endphp
                        <div class="col-6">
                            <label class="form-label fw-semibold">City</label>
                            <input type="text" name="city" class="form-control" value="{{ old('city', session('setup.city')) }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Country</label>
                            <select name="country" id="setupCountry" class="form-select" required>
                                <option value="">Select country…</option>
                                @foreach(\App\Helpers\CountryHelper::list() as $name => $code)
                                    <option value="{{ $name }}"
                                        {{ $setupCountry === $name ? 'selected' : '' }}>
                                        {{ $name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6" id="setupStateWrapper">
                            <label class="form-label fw-semibold">State / Province <span class="text-muted fw-normal">(optional)</span></label>
                            <select name="state" id="setupStateSelect" class="form-select">
                                <option value="">Select state…</option>
                                @foreach(\App\Helpers\StateHelper::forCountry($setupCountry) as $s)
                                    <option value="{{ $s }}" {{ $setupState === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="state" id="setupStateText"
                                   class="form-control d-none"
                                   placeholder="State / Province"
                                   value="{{ $setupState }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Preferred State <span class="text-muted fw-normal">(match pref, optional)</span></label>
                            <select name="preferred_state" id="setupPrefStateSelect" class="form-select">
                                <option value="">Any state</option>
                                @foreach(\App\Helpers\StateHelper::forCountry($setupCountry) as $s)
                                    <option value="{{ $s }}" {{ old('preferred_state', session('setup.preferred_state', '')) === $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="preferred_state" id="setupPrefStateText"
                                   class="form-control d-none"
                                   placeholder="Preferred state"
                                   value="{{ old('preferred_state', session('setup.preferred_state', '')) }}">
                        </div>
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <label class="form-label fw-semibold mb-0">Max Discovery Distance <span id="distLabel">({{ old('max_distance_km', session('setup.max_distance_km')) ? old('max_distance_km', session('setup.max_distance_km')).' km' : 'Any' }})</span></label>
                                <button type="button" id="reset-dist-btn" class="btn btn-sm btn-link p-0 text-decoration-none" style="color:#e91e8c;font-size:.8rem">
                                    <i class="bi bi-x-circle me-1"></i>Reset (show all)
                                </button>
                            </div>
                            <input type="range" id="setup-dist" name="max_distance_km" class="form-range" min="5" max="500" step="5" value="{{ old('max_distance_km', session('setup.max_distance_km', 50)) }}" oninput="document.getElementById('distLabel').textContent='(' + this.value + ' km)'; this.name='max_distance_km';">
                            <div id="dist-any-note" style="display:{{ old('max_distance_km', session('setup.max_distance_km')) ? 'none' : 'none' }}">
                                <small class="text-success"><i class="bi bi-check-circle me-1"></i>No distance limit set.</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Min Age Preference</label>
                            <input type="number" name="min_age" class="form-control" value="{{ old('min_age', session('setup.min_age', 18)) }}" min="18" max="80">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Max Age Preference</label>
                            <input type="number" name="max_age" class="form-control" value="{{ old('max_age', session('setup.max_age', 60)) }}" min="18" max="99">
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('setup.step', 3) }}" class="btn btn-outline-secondary flex-fill"><i class="bi bi-arrow-left me-2"></i>Back</a>
                        <button type="submit" class="btn btn-primary flex-fill fw-bold">Continue <i class="bi bi-arrow-right ms-2"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const countryEl  = document.getElementById('setupCountry');
    const stateEl    = document.getElementById('setupStateSelect');
    const stateText  = document.getElementById('setupStateText');
    const prefEl     = document.getElementById('setupPrefStateSelect');
    const prefText   = document.getElementById('setupPrefStateText');
    if (!countryEl) return;

    async function loadStates(country, stateVal, prefVal) {
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

        stateEl.dataset.name = 'state';        stateText.dataset.name = 'state';
        prefEl.dataset.name  = 'preferred_state'; prefText.dataset.name = 'preferred_state';
        populate(stateEl, stateText, stateVal,  'Select state…');
        populate(prefEl,  prefText,  prefVal,   'Any state');
    }

    countryEl.addEventListener('change', function () {
        loadStates(this.value, '', '');
    });

    const init = countryEl.value;
    if (init && stateEl.options.length <= 1) {
        loadStates(init,
            '{{ old('state', session('setup.state', '')) }}',
            '{{ old('preferred_state', session('setup.preferred_state', '')) }}'
        );
    }
})();
</script>
@endpush
