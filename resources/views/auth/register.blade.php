<x-guest-layout>
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp
    <h4 class="fw-bold mb-1 text-center auth-heading">Create Your Account <i class="bi bi-hearts ms-1" style="color:#f48fb1"></i></h4>
    <p class="text-center mb-4 auth-subtitle">Free forever � find your match today</p>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="name" :value="__('Full Name')" />
            <div class="input-group">
                <span class="input-group-text" style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-right:0;color:rgba(255,255,255,0.4);border-radius:.85rem 0 0 .85rem">
                    <i class="bi bi-person"></i>
                </span>
                <x-text-input id="name" type="text" name="name" :value="old('name')" required autofocus autocomplete="name"
                    style="border-left:0;border-radius:0 .85rem .85rem 0" placeholder="Your full name" />
            </div>
            <x-input-error :messages="$errors->get('name')" />
        </div>

        <div class="mb-3">
            <x-input-label for="email" :value="__('Email Address')" />
            <div class="input-group">
                <span class="input-group-text" style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-right:0;color:rgba(255,255,255,0.4);border-radius:.85rem 0 0 .85rem">
                    <i class="bi bi-envelope"></i>
                </span>
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autocomplete="username"
                    style="border-left:0;border-radius:0 .85rem .85rem 0" placeholder="you@example.com" />
            </div>
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="mb-3">
            <x-input-label for="password" :value="__('Password')" />
            <div class="input-group">
                <span class="input-group-text" style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-right:0;color:rgba(255,255,255,0.4);border-radius:.85rem 0 0 .85rem">
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

        <div class="mb-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <div class="input-group">
                <span class="input-group-text" style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-right:0;color:rgba(255,255,255,0.4);border-radius:.85rem 0 0 .85rem">
                    <i class="bi bi-lock-fill"></i>
                </span>
                <x-text-input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                    style="border-left:0;border-radius:0 .85rem .85rem 0" placeholder="Repeat password" />
                <button type="button" class="input-group-text" id="togglePwd2"
                    style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-left:0;color:rgba(255,255,255,0.4);cursor:pointer;border-radius:0 .85rem .85rem 0">
                    <i class="bi bi-eye" id="togglePwdIcon2"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password_confirmation')" />
        </div>

        <button id="registerBtn" type="submit" class="btn w-100 justify-content-center py-2"
            style="background:linear-gradient(135deg,#f48fb1,#ce93d8);color:#fff;border:none;border-radius:.85rem;font-weight:600;font-size:1rem;transition:opacity .2s">
            {{-- Default state --}}
            <span id="registerBtnDefault">
                <i class="bi bi-hearts me-2"></i>{{ __('Create Free Account') }}
            </span>
            {{-- Loading state (hidden initially) --}}
            <span id="registerBtnLoading" class="d-none">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                {{ __('Creating your account�') }}
            </span>
        </button>

        <hr class="auth-divider my-4">
        <p class="text-center mb-0 small" style="color:rgba(255,255,255,0.45)">
            Already have an account? <a href="{{ route('login') }}" class="auth-link">Sign In</a>
        </p>
    </form>

    <script>
    function togglePassword(btnId, iconId, inputId) {
        document.getElementById(btnId)?.addEventListener('click', function () {
            const input = document.getElementById(inputId);
            const icon  = document.getElementById(iconId);
            if (input.type === 'password') { input.type = 'text'; icon.classList.replace('bi-eye','bi-eye-slash'); }
            else { input.type = 'password'; icon.classList.replace('bi-eye-slash','bi-eye'); }
        });
    }
    togglePassword('togglePwd',  'togglePwdIcon',  'password');
    togglePassword('togglePwd2', 'togglePwdIcon2', 'password_confirmation');

    // Show loading state on submit
    document.querySelector('form').addEventListener('submit', function () {
        const btn     = document.getElementById('registerBtn');
        const dflt    = document.getElementById('registerBtnDefault');
        const loading = document.getElementById('registerBtnLoading');

        btn.disabled = true;
        btn.style.opacity = '0.8';
        dflt.classList.add('d-none');
        loading.classList.remove('d-none');
    });
    </script>
</x-guest-layout>

