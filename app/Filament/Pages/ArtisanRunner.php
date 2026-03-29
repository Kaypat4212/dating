<?php

namespace App\Filament\Pages;

use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ArtisanRunner extends Page
{
    protected string $view = 'filament.pages.artisan-runner';

    public static function getNavigationIcon(): ?string  { return 'heroicon-o-command-line'; }
    public static function getNavigationLabel(): string  { return 'Artisan Runner'; }
    public static function getNavigationGroup(): ?string { return 'System'; }
    public static function getNavigationSort(): ?int     { return 99; }

    public function getTitle(): string | Htmlable { return 'Laravel Artisan Command Runner'; }

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
    public bool   $isRunning       = false;
    public string $lastRunAt       = '';
    public string $searchQuery     = '';
    public array  $recentCommands  = [];

    public function mount(): void
    {
        $this->loadRecentCommands();
    }

    private function loadRecentCommands(): void
    {
        // Load recent commands from cache for this user
        $this->recentCommands = Cache::get('artisan_recent_' . Auth::id(), []);
    }

    /**
     * Only commands listed here can ever be executed.
     * Keys are what the user selects; values are [artisan_command, arguments, label, group, dangerous, icon, description].
     */
    public static function allowedCommands(): array
    {
        return [
            // Cache Management
            'optimize:clear'            => [
                'cmd' => 'optimize:clear', 'args' => [], 'dangerous' => false,
                'label' => 'Clear All Optimization Caches', 
                'group' => 'Cache Management', 
                'icon' => 'heroicon-o-arrow-path',
                'desc' => 'Clears config, route, view, and event caches in one command'
            ],
            'cache:clear'               => [
                'cmd' => 'cache:clear', 'args' => [], 'dangerous' => false,
                'label' => 'Flush Application Cache', 
                'group' => 'Cache Management', 
                'icon' => 'heroicon-o-trash',
                'desc' => 'Remove all items from the application cache'
            ],
            'config:cache'              => [
                'cmd' => 'config:cache', 'args' => [], 'dangerous' => false,
                'label' => 'Cache Configuration', 
                'group' => 'Cache Management', 
                'icon' => 'heroicon-o-cog-6-tooth',
                'desc' => 'Create a cache file for faster configuration loading'
            ],
            'config:clear'              => [
                'cmd' => 'config:clear', 'args' => [], 'dangerous' => false,
                'label' => 'Clear Configuration Cache', 
                'group' => 'Cache Management', 
                'icon' => 'heroicon-o-x-mark',
                'desc' => 'Remove the configuration cache file'
            ],
            'route:cache'               => [
                'cmd' => 'route:cache', 'args' => [], 'dangerous' => false,
                'label' => 'Cache Route Information', 
                'group' => 'Cache Management', 
                'icon' => 'heroicon-o-map',
                'desc' => 'Create a route cache file for faster route registration'
            ],
            'route:clear'               => [
                'cmd' => 'route:clear', 'args' => [], 'dangerous' => false,
                'label' => 'Clear Route Cache', 
                'group' => 'Cache Management', 
                'icon' => 'heroicon-o-x-mark',
                'desc' => 'Remove the route cache file'
            ],
            'view:cache'                => [
                'cmd' => 'view:cache', 'args' => [], 'dangerous' => false,
                'label' => 'Compile Blade Views', 
                'group' => 'Cache Management', 
                'icon' => 'heroicon-o-eye',
                'desc' => 'Compile all Blade view templates'
            ],
            'view:clear'                => [
                'cmd' => 'view:clear', 'args' => [], 'dangerous' => false,
                'label' => 'Clear Compiled Views', 
                'group' => 'Cache Management', 
                'icon' => 'heroicon-o-x-mark',
                'desc' => 'Clear all compiled view files'
            ],
            'event:cache'               => [
                'cmd' => 'event:cache', 'args' => [], 'dangerous' => false,
                'label' => 'Cache Event Discovery', 
                'group' => 'Cache Management', 
                'icon' => 'heroicon-o-bolt',
                'desc' => 'Discover and cache the application events and listeners'
            ],
            'event:clear'               => [
                'cmd' => 'event:clear', 'args' => [], 'dangerous' => false,
                'label' => 'Clear Event Cache', 
                'group' => 'Cache Management', 
                'icon' => 'heroicon-o-x-mark',
                'desc' => 'Clear cached events and listeners'
            ],

            // Filament Management
            'filament:cache-components'         => [
                'cmd' => 'filament:cache-components', 'args' => [], 'dangerous' => false,
                'label' => 'Cache Filament Components', 
                'group' => 'Filament', 
                'icon' => 'heroicon-o-squares-2x2',
                'desc' => 'Cache Filament component discovery for better performance'
            ],
            'filament:clear-cached-components'  => [
                'cmd' => 'filament:clear-cached-components', 'args' => [], 'dangerous' => false,
                'label' => 'Clear Filament Component Cache', 
                'group' => 'Filament', 
                'icon' => 'heroicon-o-x-mark',
                'desc' => 'Clear the cached Filament components'
            ],

            // Storage & Files
            'storage:link'              => [
                'cmd' => 'storage:link', 'args' => [], 'dangerous' => false,
                'label' => 'Create Storage Symlink', 
                'group' => 'Storage & Files', 
                'icon' => 'heroicon-o-link',
                'desc' => 'Create the symbolic link from public/storage to storage/app/public'
            ],

            // Queue Management
            'queue:restart'             => [
                'cmd' => 'queue:restart', 'args' => [], 'dangerous' => false,
                'label' => 'Restart Queue Workers', 
                'group' => 'Queue Management', 
                'icon' => 'heroicon-o-arrow-path',
                'desc' => 'Restart all queue worker daemons after the next job'
            ],
            'queue:failed'              => [
                'cmd' => 'queue:failed', 'args' => [], 'dangerous' => false,
                'label' => 'List Failed Jobs', 
                'group' => 'Queue Management', 
                'icon' => 'heroicon-o-exclamation-triangle',
                'desc' => 'Show a list of all failed queue jobs'
            ],
            'queue:flush'               => [
                'cmd' => 'queue:flush', 'args' => [], 'dangerous' => true,
                'label' => 'Flush All Failed Jobs', 
                'group' => 'Queue Management', 
                'icon' => 'heroicon-o-trash',
                'desc' => 'Delete all failed queue jobs'
            ],

            // Task Scheduling
            'schedule:run'              => [
                'cmd' => 'schedule:run', 'args' => [], 'dangerous' => false,
                'label' => 'Run Due Scheduled Tasks', 
                'group' => 'Task Scheduling', 
                'icon' => 'heroicon-o-clock',
                'desc' => 'Run the scheduled commands that are due now'
            ],

            // Application Maintenance
            'down'                      => [
                'cmd' => 'down', 'args' => [], 'dangerous' => true,
                'label' => 'Enable Maintenance Mode', 
                'group' => 'Application Maintenance', 
                'icon' => 'heroicon-o-shield-exclamation',
                'desc' => 'Put the application into maintenance mode'
            ],
            'up'                        => [
                'cmd' => 'up', 'args' => [], 'dangerous' => false,
                'label' => 'Disable Maintenance Mode', 
                'group' => 'Application Maintenance', 
                'icon' => 'heroicon-o-check-circle',
                'desc' => 'Bring the application out of maintenance mode'
            ],

            // Database Operations
            'migrate'                   => [
                'cmd' => 'migrate', 'args' => ['--force' => true], 'dangerous' => true,
                'label' => 'Run Database Migrations', 
                'group' => 'Database Operations', 
                'icon' => 'heroicon-o-circle-stack',
                'desc' => 'Run all pending database migrations (FORCED)'
            ],
            'migrate:status'            => [
                'cmd' => 'migrate:status', 'args' => [], 'dangerous' => false,
                'label' => 'Migration Status', 
                'group' => 'Database Operations', 
                'icon' => 'heroicon-o-list-bullet',
                'desc' => 'Show the status of each migration'
            ],

            // Custom Application Commands
            'app:expire-premium'        => [
                'cmd' => 'app:expire-premium', 'args' => [], 'dangerous' => false,
                'label' => 'Process Premium Expiries', 
                'group' => 'Application Commands', 
                'icon' => 'heroicon-o-star',
                'desc' => 'Process and expire overdue premium account subscriptions'
            ],
        ];
    }

    public static function isDangerous(string $key): bool
    {
        return static::allowedCommands()[$key]['dangerous'] ?? false;
    }

    /** Returns grouped [ 'Group' => ['key' => 'def', ...] ] for the UI. */
    public static function groupedCommands(): array
    {
        $groups = [];
        foreach (static::allowedCommands() as $key => $def) {
            $groups[$def['group']][$key] = $def;
        }
        return $groups;
    }

    public function getFilteredCommands(): array
    {
        $commands = static::allowedCommands();
        if (empty($this->searchQuery)) {
            return $commands;
        }
        
        $query = strtolower($this->searchQuery);
        return array_filter($commands, function ($def, $key) use ($query) {
            return str_contains(strtolower($key), $query) || 
                   str_contains(strtolower($def['label']), $query) || 
                   str_contains(strtolower($def['desc']), $query) ||
                   str_contains(strtolower($def['group']), $query);
        }, ARRAY_FILTER_USE_BOTH);
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function selectCommand(string $key): void
    {
        $this->selectedCommand = $key;
        $this->clearOutput();
    }

    public function runCommand(): void
    {
        $allowed = static::allowedCommands();

        if (! array_key_exists($this->selectedCommand, $allowed)) {
            Notification::make()->title('Invalid command selected.')->danger()->send();
            return;
        }

        $def = $allowed[$this->selectedCommand];
        $this->isRunning = true;
        $this->ran = false;
        $this->output = '';

        try {
            Artisan::call($def['cmd'], $def['args']);

            $this->output = Artisan::output() ?: '✓ Command completed successfully with no output';
            $this->exitCode = 0; // Artisan::call() throws on failure; 0 = success
            $this->ran = true;
            $this->lastRunAt = now()->format('M j, Y \a\t g:i A');
            $this->isRunning = false;

            // Add to recent commands
            $this->addToRecentCommands($this->selectedCommand);

            Notification::make()
                ->title('Command completed successfully!')
                ->body($def['label'])
                ->success()
                ->send();
        } catch (\Exception $e) {
            $this->output = '❌ Error: ' . $e->getMessage();
            $this->exitCode = 1;
            $this->ran = true;
            $this->isRunning = false;

            Notification::make()
                ->title('Command failed!')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function addToRecentCommands(string $commandKey): void
    {
        $recent = $this->recentCommands;
        
        // Remove if already exists
        $recent = array_filter($recent, fn($item) => $item['key'] !== $commandKey);
        
        // Add to beginning
        array_unshift($recent, [
            'key' => $commandKey,
            'label' => static::allowedCommands()[$commandKey]['label'],
            'ran_at' => now()->format('M j, g:i A')
        ]);
        
        // Keep only 5 most recent
        $this->recentCommands = array_slice($recent, 0, 5);
        
        // Cache for future sessions
        Cache::put('artisan_recent_' . Auth::id(), $this->recentCommands, now()->addDays(30));
    }

    public function clearOutput(): void
    {
        $this->output = '';
        $this->exitCode = 0;
        $this->ran = false;
        $this->isRunning = false;
        $this->lastRunAt = '';
    }
}
