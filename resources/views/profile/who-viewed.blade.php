@extends('layouts.app')
@section('title', 'Who Viewed Me')
@section('content')
<div class="container py-4">
    <h4 class="fw-bold mb-4"><i class="bi bi-eye text-info me-2"></i>Who Viewed Your Profile</h4>
    @if($views->isEmpty())
    <div class="text-center py-5"><div class="display-1 mb-3">👀</div><h5>No views yet</h5><p class="text-muted">Complete your profile and add photos to get more visits!</p></div>
    @else
    <div class="row g-3">
        @foreach($views as $view)
        @php $viewer = $view->viewer @endphp
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100 profile-card {{ ! auth()->user()->isPremiumActive() ? 'position-relative' : '' }}">
                <div class="ratio ratio-1x1 overflow-hidden">
                    @if($viewer->primaryPhoto)
                    <img src="{{ $viewer->primaryPhoto->thumbnail_url }}"
                         class="object-fit-cover w-100 h-100 {{ ! auth()->user()->isPremiumActive() ? 'blur-premium' : '' }}" alt="">
                    @else
                    <div class="bg-light d-flex align-items-center justify-content-center"><i class="bi bi-person-circle display-3 text-muted"></i></div>
                    @endif
                </div>
                <div class="card-body p-2">
                    @if(auth()->user()->isPremiumActive())
                    <div class="fw-semibold">{{ $viewer->name }}, {{ $viewer->age }}</div>
                    <div class="text-muted" style="font-size:.72rem">{{ $view->viewed_at->diffForHumans() }}</div>
                    @else
                    <div class="fw-semibold text-muted">Premium Required</div>
                    @endif
                </div>
                @if(auth()->user()->isPremiumActive() && $viewer && $viewer->username)
                <a href="{{ route('profile.show', $viewer->username) }}" class="stretched-link"></a>
                @elseif(! auth()->user()->isPremiumActive())
                <div class="position-absolute top-0 start-0 end-0 bottom-0 d-flex align-items-center justify-content-center rounded" style="background:rgba(255,255,255,.6)">
                    <a href="{{ route('premium.show') }}" class="btn btn-warning btn-sm fw-bold shadow"><i class="bi bi-star-fill me-1"></i>Unlock</a>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4 d-flex justify-content-center">{{ $views->links('pagination::bootstrap-5') }}</div>
    @endif
</div>
@endsection
