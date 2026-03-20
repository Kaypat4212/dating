<x-guest-layout>
    <h4 class="fw-bold mb-1 text-center auth-heading">Reset Password</h4>
    <p class="text-center mb-4 auth-subtitle">Create a strong new password</p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="mb-3">
            <x-input-label for="email" :value="__('Email Address')" />
            <div class="input-group">
                <span class="input-group-text" style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-right:0;color:rgba(255,255,255,0.4);border-radius:.85rem 0 0 .85rem">
                    <i class="bi bi-envelope"></i>
                </span>
                <x-text-input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus
                    style="border-left:0;border-radius:0 .85rem .85rem 0" />
            </div>
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="mb-3">
            <x-input-label for="password" :value="__('New Password')" />
            <div class="input-group">
                <span class="input-group-text" style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-right:0;color:rgba(255,255,255,0.4);border-radius:.85rem 0 0 .85rem">
                    <i class="bi bi-lock"></i>
                </span>
                <x-text-input id="password" type="password" name="password" required autocomplete="new-password"
                    style="border-left:0;border-radius:0 .85rem .85rem 0" placeholder="Min. 8 characters" />
            </div>
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="mb-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <div class="input-group">
                <span class="input-group-text" style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-right:0;color:rgba(255,255,255,0.4);border-radius:.85rem 0 0 .85rem">
                    <i class="bi bi-lock-fill"></i>
                </span>
                <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    style="border-left:0;border-radius:0 .85rem .85rem 0" placeholder="Repeat password" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <x-primary-button class="w-100 justify-content-center py-2">
            <i class="bi bi-check-circle me-2"></i>{{ __('Reset Password') }}
        </x-primary-button>
    </form>
</x-guest-layout>

