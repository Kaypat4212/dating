<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Auth\Login as CustomLogin;
use Filament\Auth\MultiFactor\App\AppAuthentication;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\HtmlString;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function boot(): void
    {
        if (app()->environment('local')) {
            FilamentView::registerRenderHook(
                PanelsRenderHook::AUTH_LOGIN_FORM_AFTER,
                fn (): HtmlString => new HtmlString('
                    <div style="margin-top:1.25rem;padding:1rem;border-radius:.75rem;border:1px dashed rgba(255,255,255,0.15);background:rgba(0,0,0,0.25)">
                        <p style="margin:0 0 .6rem;font-size:.68rem;font-weight:600;letter-spacing:.08em;text-transform:uppercase;color:rgba(255,255,255,0.4);text-align:center">
                            &#128187; Dev Quick Login
                        </p>
                        <div style="display:flex;flex-direction:column;gap:.5rem">
                            <button type="button"
                                onclick="devQuickLogin(\'admin@heartsconnect.com\',\'Admin@2026\')"
                                style="width:100%;padding:.45rem .75rem;border-radius:.65rem;border:1px solid rgba(255,193,7,0.35);background:rgba(255,193,7,0.12);color:#ffd54f;font-size:.8rem;font-weight:600;cursor:pointer;text-align:left">
                                &#128737; Admin &nbsp;<span style="opacity:.65;font-weight:400">admin@heartsconnect.com &nbsp;/&nbsp; Admin@2026</span>
                            </button>
                            <button type="button"
                                onclick="devQuickLogin(\'demo@heartsconnect.com\',\'password\')"
                                style="width:100%;padding:.45rem .75rem;border-radius:.65rem;border:1px solid rgba(100,200,255,0.30);background:rgba(100,200,255,0.10);color:#81d4fa;font-size:.8rem;font-weight:600;cursor:pointer;text-align:left">
                                &#128100; Demo User &nbsp;<span style="opacity:.65;font-weight:400">demo@heartsconnect.com &nbsp;/&nbsp; password</span>
                            </button>
                        </div>
                    </div>
                    <script>
                    function devQuickLogin(email, pass) {
                        var emailInput = document.querySelector(\'input[type=email], input[name=email]\');
                        var passInput  = document.querySelector(\'input[type=password], input[name=password]\');
                        if (!emailInput || !passInput) { alert(\'Cannot find login fields — try refreshing the page.\'); return; }

                        function setVal(el, val) {
                            var setter = Object.getOwnPropertyDescriptor(window.HTMLInputElement.prototype, \'value\').set;
                            setter.call(el, val);
                            el.dispatchEvent(new Event(\'input\',  { bubbles: true }));
                            el.dispatchEvent(new Event(\'change\', { bubbles: true }));
                        }

                        setVal(emailInput, email);
                        setVal(passInput,  pass);

                        setTimeout(function() {
                            var btn = document.querySelector(\'button[type=submit]\') ||
                                      document.querySelector(\'[wire\\:click*=authenticate]\');
                            if (btn) { btn.click(); }
                        }, 400);
                    }
                    </script>
                '),
            );
        }
    }

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login(CustomLogin::class)
            ->passwordReset()
            ->multiFactorAuthentication([
                AppAuthentication::make()->recoverable(),
            ])
            ->brandName('HeartsConnect Admin')
            ->darkMode()
            ->colors([
                'primary' => Color::Rose,
            ])
            ->globalSearch(true)
            ->globalSearchKeyBindings(['mod+k', '/'])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\Filament\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\Filament\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\Filament\Widgets')
            ->widgets([
                AccountWidget::class,
                \App\Filament\Widgets\AdminAiAssistantWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
