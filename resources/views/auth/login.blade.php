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


