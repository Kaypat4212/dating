<x-guest-layout>
    <h4 class="fw-bold mb-1 text-center auth-heading">Forgot Password?</h4>
    <p class="text-center mb-4 auth-subtitle">Enter your email and we'll send you a reset link.</p>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="mb-4">
            <x-input-label for="email" :value="__('Email Address')" />
            <div class="input-group">
                <span class="input-group-text" style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-right:0;color:rgba(255,255,255,0.4);border-radius:.85rem 0 0 .85rem">
                    <i class="bi bi-envelope"></i>
                </span>
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus
                    style="border-left:0;border-radius:0 .85rem .85rem 0" placeholder="you@example.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <x-primary-button class="w-100 justify-content-center py-2">
            <i class="bi bi-send me-2"></i>{{ __('Send Reset Link') }}
        </x-primary-button>
    </form>

    {{-- Divider --}}
    <div class="d-flex align-items-center gap-2 my-4">
        <hr class="flex-grow-1 m-0" style="border-color:rgba(255,255,255,0.12)">
        <span class="small" style="color:rgba(255,255,255,0.3);white-space:nowrap">or</span>
        <hr class="flex-grow-1 m-0" style="border-color:rgba(255,255,255,0.12)">
    </div>

    {{-- Secret-word alternative --}}
    <a href="{{ route('password.secret') }}"
       class="btn w-100 py-2 fw-semibold"
       style="background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.14);color:rgba(255,255,255,0.75);border-radius:.85rem">
        <i class="bi bi-key me-2"></i>Reset with Secret Word
    </a>

    <p class="text-center mt-4 mb-0 small" style="color:rgba(255,255,255,0.45)">
        <a href="{{ route('login') }}" class="auth-link"><i class="bi bi-arrow-left me-1"></i>Back to Sign In</a>
    </p>
</x-guest-layout>


