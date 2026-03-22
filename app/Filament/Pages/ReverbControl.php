<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;

class ReverbControl extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-signal';
    
    protected string $view = 'filament.pages.reverb-control';
    
    protected static ?string $navigationLabel = 'Reverb Server';
    
    protected static ?string $title = 'Reverb WebSocket Server';
    
    protected static \UnitEnum|string|null $navigationGroup = 'System';
    
    protected static ?int $navigationSort = 99;

    public string $serverStatus = 'unknown';
    public string $output = '';
    public bool $isRunning = false;

    public function mount(): void
    {
        $this->checkServerStatus();
    }

    public function checkServerStatus(): void
    {
        // Check if exec() is available
        if (!function_exists('exec') || in_array('exec', array_map('trim', explode(',', ini_get('disable_functions'))))) {
            $this->serverStatus = 'unavailable';
            $this->output = "⚠️ The exec() function is disabled on this server.\nPlease enable it in php.ini or contact your hosting provider.\n\nYou can still start Reverb manually from SSH:\nphp artisan reverb:start";
            return;
        }

        // Check if Reverb process is running
        if (stripos(PHP_OS, 'WIN') === 0) {
            // Windows
            @exec('tasklist /FI "IMAGENAME eq php.exe" /FO CSV 2>NUL | findstr /I "reverb"', $output, $returnCode);
        } else {
            // Linux/Mac
            @exec('ps aux | grep "reverb:start" | grep -v grep', $output, $returnCode);
        }

        $this->isRunning = !empty($output);
        $this->serverStatus = $this->isRunning ? 'running' : 'stopped';
    }

    public function startServer(): void
    {
        try {
            // Check if exec() is available
            if (!function_exists('exec') || in_array('exec', array_map('trim', explode(',', ini_get('disable_functions'))))) {
                $this->output = "❌ Cannot start server: exec() function is disabled.\nPlease enable it in php.ini or start Reverb manually via SSH:\nphp artisan reverb:start";
                \Filament\Notifications\Notification::make()
                    ->title('Server Control Unavailable')
                    ->danger()
                    ->body('exec() function is disabled on this server')
                    ->send();
                return;
            }

            if ($this->isRunning) {
                $this->output = "⚠️ Reverb server is already running.\n";
                return;
            }

            $reverbHost = env('REVERB_HOST', 'localhost');
            $reverbPort = env('REVERB_PORT', 8080);

            if (stripos(PHP_OS, 'WIN') === 0) {
                // Windows - Start in background
                @pclose(@popen('start /B php ' . base_path('artisan') . ' reverb:start --host=' . $reverbHost . ' --port=' . $reverbPort . ' 2>&1', 'r'));
                $this->output = "🚀 Starting Reverb server on {$reverbHost}:{$reverbPort}...\n";
            } else {
                // Linux/Mac - Start in background
                @exec('php ' . base_path('artisan') . ' reverb:start --host=' . $reverbHost . ' --port=' . $reverbPort . ' > /dev/null 2>&1 &');
                $this->output = "🚀 Starting Reverb server on {$reverbHost}:{$reverbPort}...\n";
            }

            sleep(2); // Wait for server to start
            $this->checkServerStatus();

            if ($this->isRunning) {
                $this->output .= "✅ Reverb server started successfully!\n";
                \Filament\Notifications\Notification::make()
                    ->title('Reverb Server Started')
                    ->success()
                    ->send();
            } else {
                $this->output .= "⚠️ Server may be starting. Check logs if not responding.\n";
            }
        } catch (\Exception $e) {
            $this->output = "❌ Error starting server: " . $e->getMessage() . "\n";
            \Filament\Notifications\Notification::make()
                ->title('Error Starting Server')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    public function stopServer(): void
    {
        try {
            // Check if exec() is available
            if (!function_exists('exec') || in_array('exec', array_map('trim', explode(',', ini_get('disable_functions'))))) {
                $this->output = "❌ Cannot stop server: exec() function is disabled.\nPlease enable it in php.ini or stop Reverb manually via SSH or task manager.";
                \Filament\Notifications\Notification::make()
                    ->title('Server Control Unavailable')
                    ->danger()
                    ->body('exec() function is disabled on this server')
                    ->send();
                return;
            }

            if (!$this->isRunning) {
                $this->output = "⚠️ Reverb server is not running.\n";
                return;
            }

            if (stripos(PHP_OS, 'WIN') === 0) {
                // Windows - Kill php processes running reverb
                @exec('taskkill /F /FI "WINDOWTITLE eq *reverb*" 2>NUL', $output, $returnCode);
                // Alternative: kill all php.exe with reverb in command line (more aggressive)
                @exec('wmic process where "commandline like \'%reverb:start%\'" delete 2>NUL', $output2);
            } else {
                // Linux/Mac
                @exec('pkill -f "reverb:start"');
            }

            $this->output = "🛑 Stopping Reverb server...\n";
            sleep(1);
            $this->checkServerStatus();

            if (!$this->isRunning) {
                $this->output .= "✅ Reverb server stopped successfully!\n";
                \Filament\Notifications\Notification::make()
                    ->title('Reverb Server Stopped')
                    ->success()
                    ->send();
            } else {
                $this->output .= "⚠️ Server may still be stopping. Check task manager.\n";
            }
        } catch (\Exception $e) {
            $this->output = "❌ Error stopping server: " . $e->getMessage() . "\n";
            \Filament\Notifications\Notification::make()
                ->title('Error Stopping Server')
                ->danger()
                ->body($e->getMessage())
                ->send();
        }
    }

    public function restartServer(): void
    {
        $this->stopServer();
        sleep(2);
        $this->startServer();
    }

    public function clearOutput(): void
    {
        $this->output = '';
    }
}
