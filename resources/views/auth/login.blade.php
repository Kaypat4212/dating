<x-guest-layout>
    @php
        /** @var \Illuminate\Support\ViewErrorBag $errors */
        $devAdminEmail = \App\Models\SiteSetting::get('dev_quick_admin_email') ?: 'admin@heartsconnect.com';
        $devDemoEmail  = \App\Models\SiteSetting::get('dev_quick_demo_email')  ?: 'demo@heartsconnect.com';
    @endphp

    <h4 class="fw-bold mb-1 text-center auth-heading">Welcome Back <i class="bi bi-hearts text-pink ms-1" style="color:#f48fb1"></i></h4>
    <p class="text-center mb-4 auth-subtitle">Sign in to continue finding your match</p>

    <x-auth-session-status :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <x-input-label for="email" :value="__('Email Address')" />
            <div class="input-group">
                <span class="input-group-text" style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-right:0;color:rgba(255,255,255,0.4);border-radius:.85rem 0 0 .85rem">
                    <i class="bi bi-envelope"></i>
                </span>
                <x-text-input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
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
                <x-text-input id="password" type="password" name="password" required autocomplete="current-password"
                    style="border-left:0;border-radius:0 .85rem .85rem 0" placeholder="Enter your password" />
                <button type="button" class="input-group-text" id="togglePwd"
                    style="background:rgba(255,255,255,0.07);border:1px solid rgba(255,255,255,0.12);border-left:0;color:rgba(255,255,255,0.4);cursor:pointer;border-radius:0 .85rem .85rem 0">
                    <i class="bi bi-eye" id="togglePwdIcon"></i>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
            <div class="d-flex flex-column align-items-end gap-1">
                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="auth-link small">Forgot password?</a>
                @endif
                <a href="{{ route('password.secret') }}" class="small" style="color:rgba(255,255,255,0.35);text-decoration:none" onmouseover="this.style.color='#f48fb1'" onmouseout="this.style.color='rgba(255,255,255,0.35)'">
                    <i class="bi bi-key me-1"></i>Use secret word
                </a>
            </div>
        </div>

        <x-primary-button class="w-100 justify-content-center py-2">
            <i class="bi bi-box-arrow-in-right me-2"></i>{{ __('Sign In') }}
        </x-primary-button>

        <div class="d-flex align-items-center gap-3 my-4">
            <hr class="flex-grow-1" style="border-color:rgba(255,255,255,.12);margin:0">
            <span style="color:rgba(255,255,255,.35);font-size:.8rem;white-space:nowrap">or continue with</span>
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
            Don't have an account? <a href="{{ route('register') }}" class="auth-link">Join Free</a>
        </p>
    </form>

    @if(app()->environment('local'))
    {{-- -- Dev Credentials Helper (local only) -------------------------------- --}}
    <div class="mt-4 rounded-3 p-3" style="background:rgba(0,0,0,0.25);border:1px dashed rgba(255,255,255,0.15)">
        <p class="mb-2 text-center fw-semibold" style="font-size:.72rem;letter-spacing:.08em;color:rgba(255,255,255,0.4);text-transform:uppercase">
            <i class="bi bi-code-slash me-1"></i>Dev Quick Login
        </p>
        <div class="d-flex flex-column gap-2">
            <button type="button"
                onclick="fillLogin(@js($devAdminEmail),'admin123')"
                class="btn btn-sm w-100 fw-semibold"
                style="background:rgba(255,193,7,0.15);border:1px solid rgba(255,193,7,0.35);color:#ffd54f;border-radius:.75rem">
                <i class="bi bi-shield-fill me-1"></i>Admin &mdash; <span class="opacity-75 fw-normal">{{ $devAdminEmail }} / admin123</span>
            </button>
            <button type="button"
                onclick="fillLogin(@js($devDemoEmail),'password')"
                class="btn btn-sm w-100 fw-semibold"
                style="background:rgba(100,200,255,0.12);border:1px solid rgba(100,200,255,0.3);color:#81d4fa;border-radius:.75rem">
                <i class="bi bi-person-fill me-1"></i>Demo User &mdash; <span class="opacity-75 fw-normal">{{ $devDemoEmail }} / password</span>
            </button>
        </div>
    </div>
    <script>
    function fillLogin(email, pwd) {
        document.getElementById('email').value    = email;
        document.getElementById('password').value = pwd;
        document.getElementById('email').closest('form').submit();
    }
    </script>
    @endif

    <script>
    document.getElementById('togglePwd')?.addEventListener('click', function () {
        const pwd  = document.getElementById('password');
        const icon = document.getElementById('togglePwdIcon');
        if (pwd.type === 'password') { pwd.type = 'text'; icon.classList.replace('bi-eye','bi-eye-slash'); }
        else { pwd.type = 'password'; icon.classList.replace('bi-eye-slash','bi-eye'); }
    });
    </script>
</x-guest-layout>


