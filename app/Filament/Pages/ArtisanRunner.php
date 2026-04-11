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
    protected static ?string $slug = 'artisan-runner';
    
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
    public string $customCommand = '';
    
    // Shell Terminal State
    public string $shellCommand = '';
    public string $shellOutput = '';
    public int $shellExitCode = 0;
    public bool $shellRan = false;
    public bool $shellIsRunning = false;
    public string $shellLastRunAt = '';
    public array $shellHistory = [];

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
            'queue:work' => [
                'cmd' => 'queue:work',
                'args' => ['--once' => true],
                'dangerous' => false,
                'label' => 'Process One Queue Job',
                'group' => 'Queue Management',
                'icon' => 'heroicon-o-play',
                'desc' => 'Process a single job from the queue (useful for testing broadcasts)',
            ],
            'queue:flush' => [
                'cmd' => 'queue:flush',
                'args' => [],
                'dangerous' => true,
                'label' => 'Clear All Queue Jobs',
                'group' => 'Queue Management',
                'icon' => 'heroicon-o-trash',
                'desc' => 'Remove all jobs from the queue. DESTRUCTIVE - pending jobs will be lost.',
            ],
            'queue:retry' => [
                'cmd' => 'queue:retry',
                'args' => ['--queue' => 'default'],
                'dangerous' => false,
                'label' => 'Retry All Failed Jobs',
                'group' => 'Queue Management',
                'icon' => 'heroicon-o-arrow-path-rounded-square',
                'desc' => 'Retry all failed queue jobs',
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
            'migrate' => [
                'cmd' => 'migrate',
                'args' => ['--force' => true],
                'dangerous' => false,
                'label' => 'Run Migrations',
                'group' => 'Database Operations',
                'icon' => 'heroicon-o-arrow-up-on-square',
                'desc' => 'Run all pending database migrations (--force for production)',
            ],
            'migrate:rollback' => [
                'cmd' => 'migrate:rollback',
                'args' => ['--force' => true],
                'dangerous' => true,
                'label' => 'Rollback Last Migration',
                'group' => 'Database Operations',
                'icon' => 'heroicon-o-arrow-uturn-left',
                'desc' => 'Roll back the last batch of migrations. DESTRUCTIVE — data may be lost.',
            ],
            'migrate:fresh' => [
                'cmd' => 'migrate:fresh',
                'args' => ['--force' => true],
                'dangerous' => true,
                'label' => 'Fresh Migration (Drop All)',
                'group' => 'Database Operations',
                'icon' => 'heroicon-o-fire',
                'desc' => 'Drop ALL tables and re-run all migrations. DESTROYS ALL DATA.',
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
            'reverb:start' => [
                'cmd' => 'reverb:start',
                'args' => ['--host' => env('REVERB_HOST', '0.0.0.0'), '--port' => env('REVERB_PORT', 8080)],
                'dangerous' => false,
                'label' => '🚀 Start Reverb Server',
                'group' => 'Reverb WebSocket',
                'icon' => 'heroicon-o-signal',
                'desc' => 'Start the WebSocket server for real-time features (runs in background)',
                'background' => true, // Special flag to run in background
            ],
            'reverb:restart' => [
                'cmd' => 'reverb:restart',
                'args' => [],
                'dangerous' => false,
                'label' => '🔄 Restart Reverb Server',
                'group' => 'Reverb WebSocket',
                'icon' => 'heroicon-o-arrow-path',
                'desc' => 'Restart the WebSocket server',
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
        
        // Check if this is a background command (like reverb:start)
        if (isset($def['background']) && $def['background'] === true) {
            $this->runBackgroundCommand($def);
            return;
        }
        
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
    
    /**
     * Run background commands like reverb:start using proc_open
     */
    private function runBackgroundCommand(array $def): void
    {
        try {
            $artisanPath = base_path('artisan');
            $logPath = storage_path('logs/' . str_replace(':', '-', $def['cmd']) . '.log');
            
            // Build command string
            $cmdParts = [$def['cmd']];
            foreach ($def['args'] as $key => $value) {
                if (is_numeric($key)) {
                    $cmdParts[] = $value;
                } else {
                    $cmdParts[] = "--{$key}=" . escapeshellarg($value);
                }
            }
            $artisanCommand = implode(' ', $cmdParts);
            
            // Prepare descriptors for background process
            $descriptorspec = [
                0 => ['pipe', 'r'],  // stdin
                1 => ['file', $logPath, 'a'],  // stdout to log file
                2 => ['file', $logPath, 'a'],  // stderr to log file
            ];
            
            if (stripos(PHP_OS, 'WIN') === 0) {
                // Windows - Start in background
                $cmd = 'start /B php "' . $artisanPath . '" ' . $artisanCommand;
            } else {
                // Linux/Mac - Start in background with nohup
                $cmd = 'nohup php "' . $artisanPath . '" ' . $artisanCommand . ' >> "' . $logPath . '" 2>&1 &';
            }
            
            $process = proc_open($cmd, $descriptorspec, $pipes);
            
            if (is_resource($process)) {
                fclose($pipes[0]);
                proc_close($process);
                
                $this->output = "🚀 Background command started: {$artisanCommand}\n";
                $this->output .= "📝 Logs: {$logPath}\n";
                $this->output .= "\n⏳ Process is running in the background.\n";
                $this->output .= "Check the Reverb Control page for server status.\n";
                
                $this->ran = true;
                $this->exitCode = 0;
                $this->lastRunAt = now()->format('M j, Y \a\t g:i A');
                
                $this->addToRecentCommands($this->selectedCommand);
                
                array_unshift($this->terminalHistory, [
                    'cmd'     => "php artisan {$artisanCommand}",
                    'output'  => $this->output,
                    'exit'    => 0,
                    'at'      => now()->format('H:i:s'),
                    'success' => true,
                ]);
                $this->terminalHistory = array_slice($this->terminalHistory, 0, 8);
                
                Notification::make()
                    ->title('Background Process Started')
                    ->body($def['label'])
                    ->success()
                    ->send();
            } else {
                throw new \Exception('Failed to start background process');
            }
        } catch (\Exception $e) {
            $this->output = "❌ Error starting background process: " . $e->getMessage();
            $this->exitCode = 1;
            $this->ran = true;
            
            Notification::make()
                ->title('Background Process Failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function runCustomCommand(): void
    {
        $raw = trim($this->customCommand);

        if (empty($raw)) {
            Notification::make()->title('Please enter a command.')->warning()->send();
            return;
        }

        // Block shell-injection characters — only artisan-safe chars allowed
        if (preg_match('/[;&|`><\\\\]/', $raw)) {
            Notification::make()
                ->title('Invalid characters in command.')
                ->body('Shell metacharacters are not allowed.')
                ->danger()
                ->send();
            return;
        }

        $this->isRunning = true;
        $this->ran = false;
        $this->output = '';

        try {
            $this->exitCode = Artisan::call($raw);
            $this->output = Artisan::output() ?: 'Command completed successfully';
            $this->ran = true;
            $this->lastRunAt = now()->format('M j, Y \\a\\t g:i A');
            $this->isRunning = false;

            array_unshift($this->terminalHistory, [
                'cmd'     => 'php artisan ' . $raw,
                'output'  => $this->output,
                'exit'    => $this->exitCode,
                'at'      => now()->format('H:i:s'),
                'success' => $this->exitCode === 0,
            ]);
            $this->terminalHistory = array_slice($this->terminalHistory, 0, 8);

            Notification::make()->title('Command completed!')->success()->send();
        } catch (\Throwable $e) {
            $this->output = 'Error: ' . $e->getMessage();
            $this->exitCode = 1;
            $this->ran = true;
            $this->isRunning = false;

            array_unshift($this->terminalHistory, [
                'cmd'     => 'php artisan ' . $raw,
                'output'  => $this->output,
                'exit'    => 1,
                'at'      => now()->format('H:i:s'),
                'success' => false,
            ]);
            $this->terminalHistory = array_slice($this->terminalHistory, 0, 8);

            Notification::make()->title('Command failed!')->body($e->getMessage())->danger()->send();
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

    // ── Shell Command Methods ───────────────────────────────────────────

    public function runShellCommand(): void
    {
        $raw = trim($this->shellCommand);

        if (empty($raw)) {
            Notification::make()->title('Please enter a command.')->warning()->send();
            return;
        }

        // Blocklist of dangerous commands
        $dangerousCommands = ['rm -rf /', 'mkfs', 'dd if=', ':(){:|:&};:', 'chmod -R 777 /', 'chown -R'];
        foreach ($dangerousCommands as $dangerous) {
            if (str_contains(strtolower($raw), strtolower($dangerous))) {
                Notification::make()
                    ->title('Blocked: Dangerous command detected')
                    ->body('This command has been blocked for safety reasons.')
                    ->danger()
                    ->send();
                return;
            }
        }

        $this->shellIsRunning = true;
        $this->shellRan = false;
        $this->shellOutput = '';

        try {
            // Change to the project root directory
            $projectRoot = base_path();
            
            // Execute the command
            $descriptorspec = [
                0 => ['pipe', 'r'],  // stdin
                1 => ['pipe', 'w'],  // stdout
                2 => ['pipe', 'w'],  // stderr
            ];

            $process = proc_open("cd {$projectRoot} && {$raw} 2>&1", $descriptorspec, $pipes);

            if (is_resource($process)) {
                // Close stdin
                fclose($pipes[0]);

                // Read stdout
                $output = stream_get_contents($pipes[1]);
                fclose($pipes[1]);

                // Read stderr (already combined with stdout via 2>&1)
                fclose($pipes[2]);

                // Get exit code
                $this->shellExitCode = proc_close($process);
                $this->shellOutput = $output ?: 'Command completed (no output)';
            } else {
                throw new \Exception('Failed to execute command');
            }

            $this->shellRan = true;
            $this->shellLastRunAt = now()->format('M j, Y \a\t g:i A');
            $this->shellIsRunning = false;

            // Add to history
            array_unshift($this->shellHistory, [
                'cmd'     => $raw,
                'output'  => $this->shellOutput,
                'exit'    => $this->shellExitCode,
                'at'      => now()->format('H:i:s'),
                'success' => $this->shellExitCode === 0,
            ]);
            $this->shellHistory = array_slice($this->shellHistory, 0, 10);

            if ($this->shellExitCode === 0) {
                Notification::make()->title('Command executed successfully!')->success()->send();
            } else {
                Notification::make()->title('Command finished with errors')->warning()->send();
            }
        } catch (\Throwable $e) {
            $this->shellOutput = 'Error: ' . $e->getMessage();
            $this->shellExitCode = 1;
            $this->shellRan = true;
            $this->shellIsRunning = false;

            array_unshift($this->shellHistory, [
                'cmd'     => $raw,
                'output'  => $this->shellOutput,
                'exit'    => 1,
                'at'      => now()->format('H:i:s'),
                'success' => false,
            ]);
            $this->shellHistory = array_slice($this->shellHistory, 0, 10);

            Notification::make()->title('Command failed!')->body($e->getMessage())->danger()->send();
        }
    }

    public function clearShellOutput(): void
    {
        $this->shellOutput = '';
        $this->shellExitCode = 0;
        $this->shellRan = false;
        $this->shellIsRunning = false;
        $this->shellLastRunAt = '';
    }

    public function clearShellTerminal(): void
    {
        $this->shellHistory = [];
        $this->clearShellOutput();
    }
}
