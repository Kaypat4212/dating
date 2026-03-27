@extends('layouts.app')
@section('title', 'Travel Buddy')
@section('content')
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-0"><i class="bi bi-airplane me-2 text-primary"></i>Travel Buddy</h2>
            <p class="text-muted small mb-0">Find people exploring the same destinations</p>
        </div>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPlanModal">
            <i class="bi bi-plus-lg me-1"></i>Add My Trip
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    {{-- My Plans --}}
    @if($myPlans->isNotEmpty())
    <div class="mb-4">
        <h6 class="text-muted fw-semibold text-uppercase small mb-2">My Travel Plans</h6>
        <div class="row g-2">
            @foreach($myPlans as $plan)
            <div class="col-md-4">
                <div class="card border-0 shadow-sm border-start border-primary border-3">
                    <div class="card-body py-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold">{{ $plan->destination }}</div>
                                <small class="text-muted">{{ $plan->travel_from->format('M j') }} – {{ $plan->travel_to->format('M j, Y') }}</small>
                                <div><span class="badge bg-info text-dark">{{ $plan->travel_type }}</span></div>
                            </div>
                            <form action="{{ route('travel.destroy', $plan->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Remove this plan?')"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Community plans --}}
    @if($plans->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-airplane fs-1"></i>
        <p class="mt-2">No travel plans yet. Add yours and find travel buddies!</p>
    </div>
    @else
    <h6 class="text-muted fw-semibold text-uppercase small mb-2">Community Travel Plans</h6>
    <div class="row g-3">
        @foreach($plans as $plan)
        <div class="col-sm-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex align-items-start gap-3 mb-3">
                        @if($plan->user->primaryPhoto)
                        <img src="{{ $plan->user->primaryPhoto->url }}" class="rounded-circle object-fit-cover" width="44" height="44" alt="">
                        @else
                        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center flex-shrink-0"
                             style="width:44px;height:44px;">{{ strtoupper(substr($plan->user->name, 0, 1)) }}</div>
                        @endif
                        <div>
                            <div class="fw-semibold">{{ $plan->user->name }}</div>
                            <small class="text-muted">{{ $plan->user->profile?->country ?? 'Unknown' }}</small>
                        </div>
                    </div>
                    <h6 class="fw-bold mb-1">
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $plan->destination }}
                    </h6>
                    <p class="text-muted small mb-2">
                        <i class="bi bi-calendar me-1"></i>
                        {{ $plan->travel_from->format('M j') }} – {{ $plan->travel_to->format('M j, Y') }}
                    </p>
                    <div class="mb-2">
                        <span class="badge bg-light text-dark border">{{ $plan->travel_type }}</span>
                    </div>
                    @if($plan->description)
                    <p class="text-muted small">{{ Str::limit($plan->description, 100) }}</p>
                    @endif
                    <form action="{{ route('travel.interest', $plan->id) }}" method="POST" class="mt-auto">
                        @csrf
                        <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                            <i class="bi bi-person-plus me-1"></i>Express Interest
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4 d-flex justify-content-center">{{ $plans->links() }}</div>
    @endif
</div>

{{-- Add Plan Modal --}}
<div class="modal fade" id="addPlanModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-airplane me-2"></i>Add Travel Plan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('travel.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Destination City *</label>
                            <input type="text" name="destination" class="form-control" required maxlength="150" placeholder="e.g. Paris">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Country *</label>
                            <input type="text" name="destination_country" class="form-control" required maxlength="100" placeholder="France">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">From Date *</label>
                            <input type="date" name="travel_from" class="form-control" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">To Date *</label>
                            <input type="date" name="travel_to" class="form-control" required min="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Travel Type</label>
                            <select name="travel_type" class="form-select">
                                <option value="solo">Solo traveler looking for company</option>
                                <option value="couple">Couple looking to meet other couples</option>
                                <option value="group">Group trip</option>
                                <option value="open">Open to anything</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="3" maxlength="1000"
                                      placeholder="What are you planning to do there?"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Add Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
