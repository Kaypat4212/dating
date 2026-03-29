@extends('layouts.app')
@section('title', $title)

@section('content')
<div class="container py-5" style="max-width:700px;">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4 p-md-5 text-center">
            @if($status === 'success')
                <div class="display-5 mb-3">✅</div>
            @elseif($status === 'danger')
                <div class="display-5 mb-3">❌</div>
            @else
                <div class="display-5 mb-3">ℹ️</div>
            @endif

            <h3 class="fw-bold mb-2">{{ $title }}</h3>
            <p class="text-muted mb-4">{{ $message }}</p>

            <a href="{{ url('/admin') }}" class="btn btn-primary">
                Go to Admin Panel
            </a>
        </div>
    </div>
</div>
@endsection
