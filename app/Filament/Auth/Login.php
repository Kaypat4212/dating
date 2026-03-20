<?php

namespace App\Filament\Auth;

use App\Services\TelegramService;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\Authenticatable;

class Login extends BaseLogin
{
    /**
     * Fast single-pass authentication:
     *  - Rate limit raised to 50 so normal use is never locked out.
     *  - One bcrypt check via attemptWhen (original code ran it twice → 2× slower).
     *  - Telegram alerts fired synchronously (no queue needed — tiny HTTP call).
     */
    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(50);
        } catch (\DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();
            TelegramService::notifyAdminLoginAttempt(
                email: 'unknown',
                success: false,
                reason: 'Rate-limited — too many attempts',
                ip: request()->ip(),
            );
            return null;
        }

        $data        = $this->form->getState();
        $email       = $data['email'] ?? '';
        $credentials = $this->getCredentialsFromFormData($data);

        /** @var \Illuminate\Auth\SessionGuard $authGuard */
        $authGuard = Filament::auth();

        $attempted = $authGuard->attemptWhen(
            $credentials,
            function (Authenticatable $u): bool {
                if (! ($u instanceof FilamentUser)) {
                    return true;
                }
                return $u->canAccessPanel(Filament::getCurrentOrDefaultPanel());
            },
            $data['remember'] ?? false,
        );

        if (! $attempted) {
            // Retrieve the user only to fire the failed event (no extra hash check)
            $user = $authGuard->getProvider()->retrieveByCredentials($credentials); /** @phpstan-ignore-line */
            $this->fireFailedEvent($authGuard, $user, $credentials);

            TelegramService::notifyAdminLoginAttempt(
                email: $email,
                success: false,
                reason: 'Invalid credentials or access denied',
                ip: request()->ip(),
            );

            $this->throwFailureValidationException();
        }

        session()->regenerate();

        TelegramService::notifyAdminLoginAttempt(
            email: $email,
            success: true,
            ip: request()->ip(),
        );

        return app(LoginResponse::class);
    }
}
