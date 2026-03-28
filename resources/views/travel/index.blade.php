@extends('layouts.app')
@section('title', 'Travel Buddy')
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp
@push('styles')
<style>
.travel-card { border-radius:16px; transition: transform .25s ease, box-shadow .25s ease; }
.travel-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.12) !important; }
.interest-badge { display:inline-flex;align-items:center;gap:4px;font-size:.72rem;border-radius:20px;padding:3px 10px;font-weight:600; }
.plan-type-badge { font-size:.68rem; font-weight:700; letter-spacing:.04em; text-transform:uppercase; }
.received-card { border-left: 3px solid #e11d74; border-radius: 12px; }
</style>
@endpush

@section('content')
<div class="container py-4">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-airplane me-2 text-primary"></i>Travel Buddy</h2>
            <p class="text-muted small mb-0">Find people exploring the same destinations</p>
        </div>
        <button class="btn btn-primary btn-sm rounded-pill px-3 fw-semibold" data-bs-toggle="modal" data-bs-target="#addPlanModal">
            <i class="bi bi-plus-lg me-1"></i>Add My Trip
        </button>
    </div>

    @foreach(['success','info','error'] as $flashType)
    @if(session($flashType))
    <div class="alert alert-{{ $flashType === 'error' ? 'danger' : $flashType }} alert-dismissible fade show">
        {{ session($flashType) }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @endforeach

    {{-- ── Received Interests ──────────────────────────────────────── --}}
    @if($receivedInterests->isNotEmpty())
    <div class="mb-4">
        <h6 class="fw-bold mb-2 d-flex align-items-center gap-2">
            <i class="bi bi-bell-fill text-warning"></i>
            Pending Connection Requests
            <span class="badge rounded-pill bg-danger">{{ $receivedInterests->count() }}</span>
        </h6>
        <div class="row g-2">
            @foreach($receivedInterests as $interest)
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 shadow-sm received-card">
                    <div class="card-body py-2 px-3">
                        <div class="d-flex align-items-center gap-3">
                            @if($interest->user->primaryPhoto)
                            <img src="{{ $interest->user->primaryPhoto->url }}" class="rounded-circle object-fit-cover flex-shrink-0" width="42" height="42" alt="">
                            @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 fw-bold" style="width:42px;height:42px;font-size:.85rem">{{ strtoupper(substr($interest->user->name,0,1)) }}</div>
                            @endif
                            <div class="flex-grow-1 min-w-0">
                                <div class="fw-semibold text-truncate">{{ $interest->user->name }}</div>
                                <small class="text-muted">Interested in your trip to <strong>{{ $interest->plan->destination }}</strong></small><br>
                                <small class="text-muted" style="font-size:.7rem">{{ $interest->expressed_at->diffForHumans() }}</small>
                            </div>
                            <div class="d-flex flex-column gap-1">
                                <form action="{{ route('travel.respond', [$interest->id, 'accepted']) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm rounded-pill px-3" style="font-size:.75rem">
                                        <i class="bi bi-check-lg me-1"></i>Accept
                                    </button>
                                </form>
                                <form action="{{ route('travel.respond', [$interest->id, 'declined']) }}" method="POST">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-outline-secondary btn-sm rounded-pill px-3" style="font-size:.75rem">
                                        <i class="bi bi-x-lg me-1"></i>Decline
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── My Plans ─────────────────────────────────────────────────── --}}
    @if($myPlans->isNotEmpty())
    <div class="mb-4">
        <h6 class="text-muted fw-semibold text-uppercase small mb-2">My Travel Plans</h6>
        <div class="row g-2">
            @foreach($myPlans as $plan)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm border-start border-primary border-3">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1 min-w-0 me-2">
                                <div class="fw-semibold text-truncate">
                                    <i class="bi bi-geo-alt-fill text-danger me-1" style="font-size:.8rem"></i>{{ $plan->destination }}
                                </div>
                                <small class="text-muted">{{ $plan->travel_from->format('M j') }} – {{ $plan->travel_to->format('M j, Y') }}</small>
                                <div class="d-flex gap-1 flex-wrap mt-1">
                                    <span class="badge bg-light text-dark border plan-type-badge">{{ str_replace('_', ' ', $plan->travel_type) }}</span>
                                    @if($plan->accommodation)
                                    <span class="badge bg-light text-dark border" style="font-size:.65rem">{{ $plan->accommodation }}</span>
                                    @endif
                                    @if($plan->travel_interests_count > 0)
                                    <span class="badge bg-warning text-dark" style="font-size:.65rem">
                                        <i class="bi bi-person-raised-hand me-1"></i>{{ $plan->travel_interests_count }} interested
                                    </span>
                                    @endif
                                </div>
                            </div>
                            <form action="{{ route('travel.destroy', $plan->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this plan?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Search / Filters ─────────────────────────────────────────── --}}
    <form method="GET" action="{{ route('travel.index') }}" class="card border-0 shadow-sm mb-4">
        <div class="card-body py-2">
            <div class="row g-2 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold mb-1">
                        <i class="bi bi-search me-1 text-muted"></i>Destination or Country
                    </label>
                    <input type="text" name="destination" class="form-control form-control-sm" value="{{ request('destination') }}" placeholder="e.g. Paris, Japan…">
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Travel Type</label>
                    <select name="travel_type" class="form-select form-select-sm">
                        <option value="">Any</option>
                        <option value="solo"               {{ request('travel_type') === 'solo'               ? 'selected':'' }}>Solo</option>
                        <option value="with_friends"       {{ request('travel_type') === 'with_friends'       ? 'selected':'' }}>With Friends</option>
                        <option value="seeking_companion"  {{ request('travel_type') === 'seeking_companion'  ? 'selected':'' }}>Seeking Companion</option>
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Month</label>
                    <select name="month" class="form-select form-select-sm">
                        <option value="">Any month</option>
                        @foreach(range(1,12) as $m)
                        <option value="{{ $m }}" {{ request('month') == $m ? 'selected':'' }}>{{ date('F', mktime(0,0,0,$m,1)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-6 col-md-2">
                    <label class="form-label small fw-semibold mb-1">Accommodation</label>
                    <select name="accommodation" class="form-select form-select-sm">
                        <option value="">Any</option>
                        <option value="hotel"    {{ request('accommodation') === 'hotel'    ? 'selected':'' }}>Hotel</option>
                        <option value="hostel"   {{ request('accommodation') === 'hostel'   ? 'selected':'' }}>Hostel</option>
                        <option value="airbnb"   {{ request('accommodation') === 'airbnb'   ? 'selected':'' }}>Airbnb</option>
                        <option value="camping"  {{ request('accommodation') === 'camping'  ? 'selected':'' }}>Camping</option>
                        <option value="flexible" {{ request('accommodation') === 'flexible' ? 'selected':'' }}>Flexible</option>
                    </select>
                </div>
                <div class="col-6 col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill flex-grow-1 fw-semibold">
                        <i class="bi bi-funnel me-1"></i>Filter
                    </button>
                    <a href="{{ route('travel.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-2" title="Clear">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>

    {{-- ── Community Plans ─────────────────────────────────────────── --}}
    @if($plans->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-airplane fs-1 d-block mb-2"></i>
        @if(request()->hasAny(['destination','travel_type','month','accommodation']))
            <p>No plans match your filters. <a href="{{ route('travel.index') }}">Clear filters</a></p>
        @else
            <p>No travel plans yet. Add yours and find travel buddies!</p>
        @endif
    </div>
    @else
    <h6 class="text-muted fw-semibold text-uppercase small mb-2">
        Community Travel Plans
        <span class="badge bg-secondary ms-1">{{ $plans->total() }}</span>
    </h6>
    <div class="row g-3">
        @foreach($plans as $plan)
        @php
            $alreadyExpressed = in_array($plan->id, $myInterestPlanIds);
            $interestedCount  = $plan->travelInterests->count();
        @endphp
        <div class="col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 travel-card">
                <div class="card-body d-flex flex-column">
                    {{-- User header --}}
                    <div class="d-flex align-items-start gap-3 mb-2">
                        @if($plan->user->primaryPhoto)
                        <img src="{{ $plan->user->primaryPhoto->url }}" class="rounded-circle object-fit-cover flex-shrink-0" width="44" height="44" alt="">
                        @else
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0 fw-bold" style="width:44px;height:44px">{{ strtoupper(substr($plan->user->name,0,1)) }}</div>
                        @endif
                        <div class="min-w-0">
                            <div class="fw-semibold text-truncate">{{ $plan->user->name }}</div>
                            <small class="text-muted d-flex align-items-center gap-1">
                                <i class="bi bi-geo-alt" style="font-size:.72rem"></i>
                                {{ $plan->user->profile?->city ? $plan->user->profile->city.', ' : '' }}{{ $plan->user->profile?->country ?? 'Unknown location' }}
                            </small>
                        </div>
                    </div>

                    {{-- Destination --}}
                    <h6 class="fw-bold mb-1">
                        <i class="bi bi-send-fill text-primary me-1" style="font-size:.85rem"></i>
                        {{ $plan->destination }}
                        @if($plan->destination_country)
                        <span class="text-muted fw-normal" style="font-size:.8rem">, {{ $plan->destination_country }}</span>
                        @endif
                    </h6>

                    {{-- Dates --}}
                    <p class="text-muted small mb-2">
                        <i class="bi bi-calendar-range me-1"></i>
                        {{ $plan->travel_from->format('M j') }} – {{ $plan->travel_to->format('M j, Y') }}
                        <span class="ms-1 text-info">({{ $plan->travel_from->diffForHumans() }})</span>
                    </p>

                    {{-- Badges --}}
                    <div class="d-flex flex-wrap gap-1 mb-2">
                        <span class="badge bg-info text-dark plan-type-badge">{{ str_replace('_', ' ', $plan->travel_type) }}</span>
                        @if($plan->accommodation)
                        <span class="badge bg-light text-dark border" style="font-size:.65rem"><i class="bi bi-house me-1"></i>{{ $plan->accommodation }}</span>
                        @endif
                        @if($interestedCount > 0)
                        <span class="badge bg-warning text-dark" style="font-size:.65rem"><i class="bi bi-people me-1"></i>{{ $interestedCount }} interested</span>
                        @endif
                    </div>

                    {{-- Description --}}
                    @if($plan->description)
                    <p class="text-muted small flex-grow-1" style="font-size:.82rem;line-height:1.45">{{ Str::limit($plan->description, 120) }}</p>
                    @endif

                    {{-- Action --}}
                    @if($alreadyExpressed)
                    <div class="btn btn-success btn-sm w-100 rounded-pill mt-auto" style="cursor:default">
                        <i class="bi bi-check-circle me-1"></i>Interest Expressed
                    </div>
                    @else
                    <form action="{{ route('travel.interest', $plan->id) }}" method="POST" class="mt-auto">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100 rounded-pill">
                            <i class="bi bi-person-plus me-1"></i>Connect as Travel Buddy
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4 d-flex justify-content-center">{{ $plans->links() }}</div>
    @endif
</div>

{{-- ── Add Plan Modal ──────────────────────────────────────────────── --}}
<div class="modal fade" id="addPlanModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-airplane me-2"></i>Add Travel Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('travel.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    @if($errors->any())
                    <div class="alert alert-danger py-2 small">
                        <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    </div>
                    @endif
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Destination City *</label>
                            <input type="text" name="destination" class="form-control" required maxlength="150" placeholder="e.g. Paris" value="{{ old('destination') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Country *</label>
                            <input type="text" name="destination_country" class="form-control" required maxlength="100" placeholder="France" value="{{ old('destination_country') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">From Date *</label>
                            <input type="date" name="travel_from" class="form-control" required min="{{ date('Y-m-d') }}" value="{{ old('travel_from') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">To Date *</label>
                            <input type="date" name="travel_to" class="form-control" required min="{{ date('Y-m-d') }}" value="{{ old('travel_to') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Travel Type *</label>
                            <select name="travel_type" class="form-select">
                                <option value="seeking_companion" {{ old('travel_type') === 'seeking_companion' ? 'selected':'' }}>Seeking a travel companion</option>
                                <option value="solo"              {{ old('travel_type') === 'solo'              ? 'selected':'' }}>Solo traveler</option>
                                <option value="with_friends"      {{ old('travel_type') === 'with_friends'      ? 'selected':'' }}>With friends</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Accommodation</label>
                            <select name="accommodation" class="form-select">
                                <option value="">Not specified</option>
                                <option value="hotel"    {{ old('accommodation') === 'hotel'    ? 'selected':'' }}>Hotel</option>
                                <option value="hostel"   {{ old('accommodation') === 'hostel'   ? 'selected':'' }}>Hostel</option>
                                <option value="airbnb"   {{ old('accommodation') === 'airbnb'   ? 'selected':'' }}>Airbnb</option>
                                <option value="camping"  {{ old('accommodation') === 'camping'  ? 'selected':'' }}>Camping</option>
                                <option value="flexible" {{ old('accommodation') === 'flexible' ? 'selected':'' }}>Flexible / Open</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">What are you planning to do there?</label>
                            <textarea name="description" class="form-control" rows="3" maxlength="1000"
                                      placeholder="e.g. Hiking, food tours, exploring museums, looking for a cultural exchange...">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4"><i class="bi bi-check-lg me-1"></i>Add Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->any())
<script>
document.addEventListener('DOMContentLoaded', () => {
    const modal = new bootstrap.Modal(document.getElementById('addPlanModal'));
    modal.show();
});
</script>
@endif

@endsection
