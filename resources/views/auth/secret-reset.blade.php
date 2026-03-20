<x-guest-layout>
    <h4 class="fw-bold mb-1 text-center auth-heading">
        <i class="bi bi-shield-lock text-pink me-2" style="color:#f48fb1"></i>Secret Reset
    </h4>
    <p class="text-center mb-4 auth-subtitle">Enter your email, the secret word, and a new password.</p>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('password.secret.store') }}">
        @csrf

        {{-- Email --}}
        <div class="mb-3">
            <x-input-label for="email" :value="__('Email Address')" />
            <div class="input-group">
                <span class="input-group-text"
                      style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-right:0;color:rgba(255,255,255,0.4);border-radius:.85rem 0 0 .85rem">
                    <i class="bi bi-envelope"></i>
                </span>
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus
                    style="border-left:0;border-radius:0 .85rem .85rem 0" placeholder="you@example.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" />
        </div>

        {{-- Secret word --}}
        <div class="mb-3">
            <x-input-label for="secret_word" value="Secret Word" />
            <div class="input-group">
                <span class="input-group-text"
                      style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-right:0;color:rgba(255,255,255,0.4);border-radius:.85rem 0 0 .85rem">
                    <i class="bi bi-key"></i>
                </span>
                <x-text-input id="secret_word" type="password" name="secret_word" required
                    style="border-left:0;border-radius:0 .85rem .85rem 0" placeholder="Enter secret word" />
                <button type="button" class="input-group-text" id="toggleSecret"
                    style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-left:0;color:rgba(255,255,255,0.4);cursor:pointer;border-radius:0 .85rem .85rem 0">
                    <i class="bi bi-eye" id="toggleSecretIcon"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('secret_word')" />
        </div>

        {{-- New password --}}
        <div class="mb-3">
            <x-input-label for="password" :value="__('New Password')" />
            <div class="input-group">
                <span class="input-group-text"
                      style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-right:0;color:rgba(255,255,255,0.4);border-radius:.85rem 0 0 .85rem">
                    <i class="bi bi-lock"></i>
                </span>
                <x-text-input id="password" type="password" name="password" required autocomplete="new-password"
                    style="border-left:0;border-radius:0 .85rem .85rem 0" placeholder="Min. 8 characters" />
                <button type="button" class="input-group-text" id="togglePwd"
                    style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-left:0;color:rgba(255,255,255,0.4);cursor:pointer;border-radius:0 .85rem .85rem 0">
                    <i class="bi bi-eye" id="togglePwdIcon"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" />
        </div>

        {{-- Confirm password --}}
        <div class="mb-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <div class="input-group">
                <span class="input-group-text"
                      style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-right:0;color:rgba(255,255,255,0.4);border-radius:.85rem 0 0 .85rem">
                    <i class="bi bi-lock-fill"></i>
                </span>
                <x-text-input id="password_confirmation" type="password" name="password_confirmation" required
                    style="border-left:0;border-radius:0 .85rem .85rem 0" placeholder="Repeat new password" />
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <x-primary-button class="w-100 justify-content-center py-2">
            <i class="bi bi-check-circle me-2"></i>Reset Password
        </x-primary-button>

        <p class="text-center mt-4 mb-0 small" style="color:rgba(255,255,255,0.45)">
            <a href="{{ route('login') }}" class="auth-link">
                <i class="bi bi-arrow-left me-1"></i>Back to Sign In
            </a>
        </p>
    </form>

    <script>
    document.getElementById('toggleSecret').addEventListener('click', function () {
        const inp  = document.getElementById('secret_word');
        const icon = document.getElementById('toggleSecretIcon');
        const show = inp.type === 'password';
        inp.type   = show ? 'text' : 'password';
        icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
    document.getElementById('togglePwd').addEventListener('click', function () {
        const inp  = document.getElementById('password');
        const icon = document.getElementById('togglePwdIcon');
        const show = inp.type === 'password';
        inp.type   = show ? 'text' : 'password';
        icon.className = show ? 'bi bi-eye-slash' : 'bi bi-eye';
    });
    </script>
</x-guest-layout>
