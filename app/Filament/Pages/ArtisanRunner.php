<?php

namespace App\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class ArtisanRunner extends Page
{
    protected string $view = 'filament.pages.artisan-runner';

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-command-line'; }
    public static function getNavigationLabel(): string  { return 'Artisan Runner'; }
    public static function getNavigationGroup(): ?string { return 'System'; }
    public static function getNavigationSort(): ?int     { return 99; }

    public function getTitle(): string | Htmlable { return 'Artisan Command Runner'; }

    /** Only the superadmin (ID 1) may use this page. */
    public static function canAccess(): bool
    {
        return Auth::check() && Auth::id() === 1;
    }

    // ── State ──────────────────────────────────────────────────────────────────

    public string $selectedCommand = '';
    public string $output          = '';
    public int    $exitCode        = 0;
    public bool   $ran             = false;

    // ── Whitelist of allowed commands ─────────────────────────────────────────

    /**
     * Only commands listed here can ever be executed.
     * Keys are what the user selects; values are [artisan_command, arguments, label, group, dangerous].
     */
    public static function allowedCommands(): array
    {
        return [
            // Cache
            'optimize:clear'            => ['cmd' => 'optimize:clear',             'args' => [],              'label' => 'optimize:clear — Clear all caches (config, route, view, events)',  'group' => 'Cache',        'dangerous' => false],
            'cache:clear'               => ['cmd' => 'cache:clear',                'args' => [],              'label' => 'cache:clear — Flush the application cache',                        'group' => 'Cache',        'dangerous' => false],
            'config:cache'              => ['cmd' => 'config:cache',               'args' => [],              'label' => 'config:cache — Cache configuration files',                         'group' => 'Cache',        'dangerous' => false],
            'config:clear'              => ['cmd' => 'config:clear',               'args' => [],              'label' => 'config:clear — Remove cached config',                              'group' => 'Cache',        'dangerous' => false],
            'route:cache'               => ['cmd' => 'route:cache',                'args' => [],              'label' => 'route:cache — Cache route information',                            'group' => 'Cache',        'dangerous' => false],
            'route:clear'               => ['cmd' => 'route:clear',                'args' => [],              'label' => 'route:clear — Remove cached routes',                               'group' => 'Cache',        'dangerous' => false],
            'view:cache'                => ['cmd' => 'view:cache',                 'args' => [],              'label' => 'view:cache — Compile all Blade views',                             'group' => 'Cache',        'dangerous' => false],
            'view:clear'                => ['cmd' => 'view:clear',                 'args' => [],              'label' => 'view:clear — Clear compiled views',                                'group' => 'Cache',        'dangerous' => false],
            'event:cache'               => ['cmd' => 'event:cache',                'args' => [],              'label' => 'event:cache — Discover and cache events',                          'group' => 'Cache',        'dangerous' => false],
            'event:clear'               => ['cmd' => 'event:clear',                'args' => [],              'label' => 'event:clear — Clear cached events',                                'group' => 'Cache',        'dangerous' => false],

            // Filament
            'filament:cache-components'         => ['cmd' => 'filament:cache-components',         'args' => [], 'label' => 'filament:cache-components — Cache Filament component discovery',   'group' => 'Filament',     'dangerous' => false],
            'filament:clear-cached-components'  => ['cmd' => 'filament:clear-cached-components',  'args' => [], 'label' => 'filament:clear-cached-components — Clear Filament component cache', 'group' => 'Filament',     'dangerous' => false],

            // Storage
            'storage:link'              => ['cmd' => 'storage:link',               'args' => [],              'label' => 'storage:link — Create the public storage symlink',                 'group' => 'Storage',      'dangerous' => false],

            // Queue
            'queue:restart'             => ['cmd' => 'queue:restart',              'args' => [],              'label' => 'queue:restart — Restart queue worker daemons',                     'group' => 'Queue',        'dangerous' => false],

            // Scheduler
            'schedule:run'              => ['cmd' => 'schedule:run',               'args' => [],              'label' => 'schedule:run — Run scheduled commands due now',                    'group' => 'Scheduler',    'dangerous' => false],

            // Maintenance
            'down'                      => ['cmd' => 'down',                       'args' => [],              'label' => 'down — Put the app in maintenance mode',                           'group' => 'Maintenance',  'dangerous' => true],
            'up'                        => ['cmd' => 'up',                         'args' => [],              'label' => 'up — Bring the app out of maintenance mode',                       'group' => 'Maintenance',  'dangerous' => false],

            // Database (dangerous — confirmation required)
            'migrate'                   => ['cmd' => 'migrate',                    'args' => ['--force' => true], 'label' => 'migrate --force — Run pending migrations',                    'group' => 'Database',     'dangerous' => true],
            'migrate:status'            => ['cmd' => 'migrate:status',             'args' => [],              'label' => 'migrate:status — Show migration status',                           'group' => 'Database',     'dangerous' => false],

            // Jobs
            'queue:failed'              => ['cmd' => 'queue:failed',               'args' => [],              'label' => 'queue:failed — List all failed queue jobs',                        'group' => 'Queue',        'dangerous' => false],
            'queue:flush'               => ['cmd' => 'queue:flush',                'args' => [],              'label' => 'queue:flush — Flush all failed queue jobs',                        'group' => 'Queue',        'dangerous' => true],

            // Premium expiry job
            'app:expire-premium'        => ['cmd' => 'app:expire-premium',         'args' => [],              'label' => 'app:expire-premium — Expire overdue premium accounts',             'group' => 'App Jobs',     'dangerous' => false],

            // Broadcasting
            'reverb:start'              => ['cmd' => 'reverb:start',               'args' => [],              'label' => 'reverb:start — Start the Reverb WebSocket server',                 'group' => 'Broadcasting', 'dangerous' => false],
        ];
    }

    public static function isDangerous(string $key): bool
    {
        return static::allowedCommands()[$key]['dangerous'] ?? false;
    }

    /** Returns grouped [ 'Group' => ['key' => 'label', ...] ] for the select. */
    public static function groupedOptions(): array
    {
        $groups = [];
        foreach (static::allowedCommands() as $key => $def) {
            $groups[$def['group']][$key] = $def['label'];
        }
        return $groups;
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function runCommand(): void
    {
        $allowed = static::allowedCommands();

        if (! array_key_exists($this->selectedCommand, $allowed)) {
            Notification::make()->title('Invalid command selected.')->danger()->send();
            return;
        }

        $def = $allowed[$this->selectedCommand];

        Artisan::call($def['cmd'], $def['args']);

        $this->output  = Artisan::output() ?: '(command completed with no output)';
        $this->exitCode = 0; // Artisan::call() throws on failure; 0 = success
        $this->ran      = true;

        Notification::make()
            ->title('Command completed: ' . $def['cmd'])
            ->success()
            ->send();
    }

    public function clearOutput(): void
    {
        $this->output  = '';
        $this->exitCode = 0;
        $this->ran      = false;
    }
}
