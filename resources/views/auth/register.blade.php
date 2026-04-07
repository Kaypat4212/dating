<x-guest-layout>
@php /** @var \Illuminate\Support\ViewErrorBag $errors */ @endphp
    <h4 class="fw-bold mb-1 text-center auth-heading">Create Your Account <i class="bi bi-hearts ms-1" style="color:#f48fb1"></i></h4>
    <p class="text-center mb-2 auth-subtitle"><i class="bi bi-infinity me-1" style="color:#ce93d8"></i>Free forever &mdash; find your match today</p>
    <p class="text-center mb-4 small" style="color:rgba(255,255,255,0.35)">
        <i class="bi bi-shield-check me-1" style="color:#80cbc4"></i>Your IP address: <span style="color:rgba(255,255,255,0.6);font-family:monospace">{{ request()->ip() }}</span>
    </p>

    <form method="POST" action="{{ route('register') }}">
        @csrf
        {{-- Preserve referral code through form submission --}}
        @if(session('referral_code'))
        <input type="hidden" name="ref" value="{{ session('referral_code') }}">
        @elseif(request()->filled('ref'))
        <input type="hidden" name="ref" value="{{ request('ref') }}">
        @endif

        {{-- Referral banner --}}
        @if(session('ref_note') || session('referral_code') || request()->filled('ref'))
        <div class="alert d-flex align-items-center gap-2 py-2 px-3 mb-3" style="background:rgba(244,143,177,.18);border:1px solid rgba(244,143,177,.4);border-radius:.85rem;color:#fff;font-size:.875rem">
            <i class="bi bi-gift-fill" style="color:#f48fb1"></i>
            <span>You were invited by a friend — welcome!</span>
        </div>
        @endif

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
                {{ __('Creating your account...') }}
            </span>
        </button>

        <div class="d-flex align-items-center gap-3 my-4">
            <hr class="flex-grow-1" style="border-color:rgba(255,255,255,.12);margin:0">
            <span style="color:rgba(255,255,255,.35);font-size:.8rem;white-space:nowrap">or sign up with</span>
            <hr class="flex-grow-1" style="border-color:rgba(255,255,255,.12);margin:0">
        </div>

        <a href="{{ route('auth.google') }}"
           class="btn w-100 d-flex align-items-center justify-content-center gap-2 fw-semibold"
           style="background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.15);
                  border-radius:.85rem;color:#fff;font-size:.95rem;padding:.6rem 1rem;
                  transition:background .2s;text-decoration:none"
           onmouseover="this.style.background='rgba(255,255,255,.12)'"
           onmouseout="this.style.background='rgba(255,255,255,.06)'">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 48 48">
                <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303c-1.649 4.657-6.08 8-11.303 8-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.65-.389-3.917z"/>
                <path fill="#FF3D00" d="m6.306 14.691 6.571 4.819C14.655 15.108 18.961 12 24 12c3.059 0 5.842 1.154 7.961 3.039l5.657-5.657C34.046 6.053 29.268 4 24 4 16.318 4 9.656 8.337 6.306 14.691z"/>
                <path fill="#4CAF50" d="M24 44c5.166 0 9.86-1.977 13.409-5.192l-6.19-5.238A11.91 11.91 0 0 1 24 36c-5.202 0-9.619-3.317-11.283-7.946l-6.522 5.025C9.505 39.556 16.227 44 24 44z"/>
                <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303a12.04 12.04 0 0 1-4.087 5.571l.003-.002 6.19 5.238C36.971 39.205 44 34 44 24c0-1.341-.138-2.65-.389-3.917z"/>
            </svg>
            Continue with Google
        </a>

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

