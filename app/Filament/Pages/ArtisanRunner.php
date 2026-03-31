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

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-command-line';
    }

    public static function getNavigationLabel(): string
    {
        return 'Artisan Runner';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'System';
    }

    public static function getNavigationSort(): ?int
    {
        return 99;
    }

    public function getTitle(): string | Htmlable
    {
        return 'Laravel Artisan Command Runner';
    }

    /** Only the superadmin (ID 1) may use this page. */
    public static function canAccess(): bool
    {
        return Auth::check() && Auth::id() === 1;
    }

    // ── State ────────────────────────────────────────────────────────────

    public string $selectedCommand = '';
    public string $output = '';
    public int $exitCode = 0;
    public bool $ran = false;
    public bool $isRunning = false;
    public string $lastRunAt = '';
    public string $searchQuery = '';
    public array $recentCommands = [];
    public array $terminalHistory = [];

    public function mount(): void
    {
        $this->loadRecentCommands();
    }

    private function loadRecentCommands(): void
    {
        try {
            $this->recentCommands = Cache::get('artisan_recent_' . Auth::id(), []);
        } catch (\Exception $e) {
            $this->recentCommands = [];
        }
    }

    /**
     * Only commands listed here can ever be executed.
     */
    public static function allowedCommands(): array
    {
        return [
            'optimize:clear' => [
                'cmd' => 'optimize:clear',
                'args' => [],
                'dangerous' => false,
                'label' => 'Clear All Caches',
                'group' => 'Cache Management',
                'icon' => 'heroicon-o-arrow-path',
                'desc' => 'Clear all application caches (config, routes, views)',
            ],
            'cache:clear' => [
                'cmd' => 'cache:clear',
                'args' => [],
                'dangerous' => false,
                'label' => 'Clear Application Cache',
                'group' => 'Cache Management',
                'icon' => 'heroicon-o-trash',
                'desc' => 'Clear the application cache',
            ],
            'view:clear' => [
                'cmd' => 'view:clear',
                'args' => [],
                'dangerous' => false,
                'label' => 'Clear View Cache',
                'group' => 'Cache Management',
                'icon' => 'heroicon-o-eye',
                'desc' => 'Clear compiled Blade view cache',
            ],
            'config:clear' => [
                'cmd' => 'config:clear',
                'args' => [],
                'dangerous' => false,
                'label' => 'Clear Config Cache',
                'group' => 'Cache Management',
                'icon' => 'heroicon-o-cog-6-tooth',
                'desc' => 'Clear configuration cache',
            ],
            'route:clear' => [
                'cmd' => 'route:clear',
                'args' => [],
                'dangerous' => false,
                'label' => 'Clear Route Cache',
                'group' => 'Cache Management',
                'icon' => 'heroicon-o-map',
                'desc' => 'Clear route cache',
            ],
            'queue:restart' => [
                'cmd' => 'queue:restart',
                'args' => [],
                'dangerous' => false,
                'label' => 'Restart Queue Workers',
                'group' => 'Queue Management',
                'icon' => 'heroicon-o-arrow-path',
                'desc' => 'Restart all queue workers',
            ],
            'queue:failed' => [
                'cmd' => 'queue:failed',
                'args' => [],
                'dangerous' => false,
                'label' => 'List Failed Jobs',
                'group' => 'Queue Management',
                'icon' => 'heroicon-o-exclamation-triangle',
                'desc' => 'List all failed queue jobs',
            ],
            'migrate:status' => [
                'cmd' => 'migrate:status',
                'args' => [],
                'dangerous' => false,
                'label' => 'Migration Status',
                'group' => 'Database Operations',
                'icon' => 'heroicon-o-list-bullet',
                'desc' => 'Show migration status',
            ],
            'storage:link' => [
                'cmd' => 'storage:link',
                'args' => [],
                'dangerous' => false,
                'label' => 'Create Storage Link',
                'group' => 'Application',
                'icon' => 'heroicon-o-link',
                'desc' => 'Create symbolic link for storage',
            ],
            'seed:blog-posts' => [
                'cmd' => 'db:seed',
                'args' => ['--class' => 'Database\\Seeders\\BlogPostSeeder', '--force' => true],
                'dangerous' => false,
                'label' => 'Seed Blog Posts',
                'group' => 'Database Operations',
                'icon' => 'heroicon-o-document-text',
                'desc' => 'Insert the 5 starter blog posts (safe to re-run — uses updateOrCreate)',
            ],
        ];
    }

    public static function isDangerous(string $key): bool
    {
        return static::allowedCommands()[$key]['dangerous'] ?? false;
    }

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

    // ── Actions ─────────────────────────────────────────────────────────

    public function selectCommand(string $key): void
    {
        $this->selectedCommand = $key;
        $this->clearOutput();
    }

    public function runCommand(): void
    {
        $allowed = static::allowedCommands();

        if (!array_key_exists($this->selectedCommand, $allowed)) {
            Notification::make()
                ->title('Invalid command selected.')
                ->danger()
                ->send();
            return;
        }

        $def = $allowed[$this->selectedCommand];
        $this->isRunning = true;
        $this->ran = false;
        $this->output = '';

        try {
            $this->exitCode = Artisan::call($def['cmd'], $def['args']);
            $this->output = Artisan::output() ?: '✓ Command completed successfully';
            $this->ran = true;
            $this->lastRunAt = now()->format('M j, Y \a\t g:i A');
            $this->isRunning = false;

            $this->addToRecentCommands($this->selectedCommand);

            array_unshift($this->terminalHistory, [
                'cmd'     => "php artisan {$this->selectedCommand}",
                'output'  => $this->output,
                'exit'    => $this->exitCode,
                'at'      => now()->format('H:i:s'),
                'success' => $this->exitCode === 0,
            ]);
            $this->terminalHistory = array_slice($this->terminalHistory, 0, 8);

            Notification::make()
                ->title('Command completed!')
                ->body($def['label'])
                ->success()
                ->send();
        } catch (\Exception $e) {
            $this->output = 'Error: ' . $e->getMessage();
            $this->exitCode = 1;
            $this->ran = true;
            $this->isRunning = false;

            array_unshift($this->terminalHistory, [
                'cmd'     => "php artisan {$this->selectedCommand}",
                'output'  => $this->output,
                'exit'    => $this->exitCode,
                'at'      => now()->format('H:i:s'),
                'success' => false,
            ]);
            $this->terminalHistory = array_slice($this->terminalHistory, 0, 8);

            Notification::make()
                ->title('Command failed!')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    private function addToRecentCommands(string $commandKey): void
    {
        try {
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
        } catch (\Exception $e) {
            // Silently fail if cache is not working
        }
    }

    public function clearOutput(): void
    {
        $this->output = '';
        $this->exitCode = 0;
        $this->ran = false;
        $this->isRunning = false;
        $this->lastRunAt = '';
    }

    public function clearTerminal(): void
    {
        $this->terminalHistory = [];
        $this->clearOutput();
    }
}
