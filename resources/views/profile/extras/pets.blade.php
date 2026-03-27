@extends('layouts.app')
@section('title', 'My Pets')
@section('content')
<div class="container py-4" style="max-width:900px;">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('profile.edit') }}">Profile</a></li>
            <li class="breadcrumb-item active">My Pets</li>
        </ol>
    </nav>

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h2 class="fw-bold mb-0"><i class="bi bi-heart-fill text-danger me-2"></i>My Pets</h2>
        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addPetModal">
            <i class="bi bi-plus-lg me-1"></i>Add Pet
        </button>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif

    @if($pets->isEmpty())
    <div class="text-center py-5 text-muted">
        <i class="bi bi-emoji-smile fs-1"></i>
        <p class="mt-2">No pets added yet. Add your furry (or scaly!) friends to your profile.</p>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPetModal">
            <i class="bi bi-plus-lg me-1"></i>Add My First Pet
        </button>
    </div>
    @else
    <div class="row g-3">
        @foreach($pets as $pet)
        <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm h-100">
                @if($pet->photo_path)
                <img src="{{ Storage::url($pet->photo_path) }}" class="card-img-top" style="height:180px;object-fit:cover;" alt="{{ $pet->name }}">
                @else
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height:120px;">
                    <i class="bi bi-emoji-smile text-muted" style="font-size:3rem;"></i>
                </div>
                @endif
                <div class="card-body">
                    <h5 class="fw-bold mb-1">{{ $pet->name }}</h5>
                    <p class="text-muted small mb-2">
                        {{ ucfirst($pet->type) }}
                        @if($pet->breed) – {{ $pet->breed }}@endif
                        @if($pet->age_years || $pet->age_months)
                            ({{ $pet->age_years ? $pet->age_years . 'y' : '' }}{{ $pet->age_months ? ' ' . $pet->age_months . 'm' : '' }})
                        @endif
                    </p>
                    @if($pet->about)<p class="small text-muted">{{ $pet->about }}</p>@endif
                </div>
                <div class="card-footer bg-transparent">
                    <form action="{{ route('extras.pets.destroy', $pet->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Remove {{ $pet->name }}?')">
                            <i class="bi bi-trash me-1"></i>Remove
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>

{{-- Add Pet Modal --}}
<div class="modal fade" id="addPetModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add Pet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('extras.pets.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Pet's Name *</label>
                            <input type="text" name="name" class="form-control" required maxlength="80" placeholder="e.g. Buddy">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Type *</label>
                            <select name="type" class="form-select" required>
                                <option value="dog">Dog</option>
                                <option value="cat">Cat</option>
                                <option value="bird">Bird</option>
                                <option value="rabbit">Rabbit</option>
                                <option value="fish">Fish</option>
                                <option value="reptile">Reptile</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Breed</label>
                            <input type="text" name="breed" class="form-control" maxlength="100" placeholder="Golden Retriever">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Age (years)</label>
                            <input type="number" name="age_years" class="form-control" min="0" max="50" placeholder="3">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Months</label>
                            <input type="number" name="age_months" class="form-control" min="0" max="11" placeholder="6">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Size</label>
                            <select name="size" class="form-select">
                                <option value="">Select size</option>
                                <option value="tiny">Tiny</option>
                                <option value="small">Small</option>
                                <option value="medium">Medium</option>
                                <option value="large">Large</option>
                                <option value="extra_large">Extra Large</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">About</label>
                            <textarea name="about" class="form-control" rows="2" maxlength="500" placeholder="Tell us about your pet..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Photo</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                            <small class="text-muted">Max 2MB</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i>Add Pet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
