<x-guest-layout>
    <h4 class="fw-bold mb-1 text-center">Verify Email</h4>
    <p class="text-muted text-center small mb-4">{{ __("We sent a verification link to your email. Click it to get started!") }}</p>
    @if (session('status') == 'verification-link-sent')
    <div class="alert alert-success small">A new verification link has been sent to your email.</div>
    @endif
    <div class="d-flex gap-2">
        <form method="POST" action="{{ route('verification.send') }}" class="flex-grow-1">@csrf<x-primary-button class="w-100 justify-content-center">Resend Email</x-primary-button></form>
        <form method="POST" action="{{ route('logout') }}">@csrf<x-secondary-button>Log Out</x-secondary-button></form>
    </div>
</x-guest-layout>
