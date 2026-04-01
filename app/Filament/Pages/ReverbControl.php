<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;

class ReverbControl extends Page
{
    protected string $view = 'filament.pages.reverb-control';
    
    public static function getNavigationIcon(): ?string 
    { 
        return 'heroicon-o-signal'; 
    }
    
    public static function getNavigationLabel(): string 
    { 
        return 'Reverb Server'; 
    }
    
    public static function getNavigationGroup(): ?string 
    { 
        return 'System'; 
    }
    
    public static function getNavigationSort(): ?int 
    { 
        return 99; 
    }

    public string $serverStatus = 'unknown';
    public string $output = '';
    public bool $isRunning = false;

    public function mount(): void
    {
        $this->checkServerStatus();
    }

    public function checkServerStatus(): void
    {
        // Try to check if Reverb process is running using proc_open (works even when exec is disabled)
        $descriptorspec = [
            0 => ['pipe', 'r'],  // stdin
            1 => ['pipe', 'w'],  // stdout
            2 => ['pipe', 'w'],  // stderr
        ];

        try {
            if (stripos(PHP_OS, 'WIN') === 0) {
                // Windows
                $cmd = 'tasklist /FI "IMAGENAME eq php.exe" /FO CSV 2>&1 | findstr /I "reverb"';
            } else {
                // Linux/Mac
                $cmd = 'ps aux | grep "reverb:start" | grep -v grep';
            }

            $process = proc_open($cmd, $descriptorspec, $pipes);

            if (is_resource($process)) {
                fclose($pipes[0]);
                $output = stream_get_contents($pipes[1]);
                fclose($pipes[1]);
                fclose($pipes[2]);
                proc_close($process);

                $this->isRunning = !empty(trim($output));
                $this->serverStatus = $this->isRunning ? 'running' : 'stopped';
            } else {
                $this->serverStatus = 'unknown';
            }
        } catch (\Exception $e) {
            $this->serverStatus = 'unknown';
            $this->output = "Status check error: " . $e->getMessage();
        }
    }

    public function startServer(): void
    {
        try {
            if ($this->isRunning) {
                $this->output = "⚠️ Reverb server is already running.\n";
                return;
            }

            $reverbHost = env('REVERB_HOST', '0.0.0.0');
            $reverbPort = env('REVERB_PORT', 8080);
            $artisanPath = base_path('artisan');
            $logPath = storage_path('logs/reverb.log');

            // Use proc_open to start Reverb in background
            $descriptorspec = [
                0 => ['pipe', 'r'],  // stdin
                1 => ['file', $logPath, 'a'],  // stdout to log file
                2 => ['file', $logPath, 'a'],  // stderr to log file
            ];

            if (stripos(PHP_OS, 'WIN') === 0) {
                // Windows - Start in background
                $cmd = 'start /B php "' . $artisanPath . '" reverb:start --host=' . $reverbHost . ' --port=' . $reverbPort;
                $process = proc_open($cmd, $descriptorspec, $pipes);
            } else {
                // Linux/Mac - Start in background with nohup
                $cmd = 'nohup php "' . $artisanPath . '" reverb:start --host=' . $reverbHost . ' --port=' . $reverbPort . ' > "' . $logPath . '" 2>&1 &';
                $process = proc_open($cmd, $descriptorspec, $pipes);
            }

            if (is_resource($process)) {
                fclose($pipes[0]);
                // Don't wait for the process - let it run in background
                proc_close($process);
                
                $this->output = "🚀 Starting Reverb server on {$reverbHost}:{$reverbPort}...\n";
                $this->output .= "📝 Logs: {$logPath}\n\n";
                
                // Wait a moment then check status
                sleep(2);
                $this->checkServerStatus();

                if ($this->isRunning) {
                    $this->output .= "✅ Reverb server started successfully!\n";
                    \Filament\Notifications\Notification::make()
                        ->title('Reverb Server Started')
                        ->success()
                        ->body("Server running on {$reverbHost}:{$reverbPort}")
                        ->send();
                } else {
                    $this->output .= "⏳ Server is starting... Refresh in a few seconds.\n";
                    \Filament\Notifications\Notification::make()
                        ->title('Server Starting')
                        ->warning()
                        ->body('Check status in a moment')
                        ->send();
                }
            } else {
                throw new \Exception('Failed to start Reverb process');
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
            if (!$this->isRunning) {
                $this->output = "⚠️ Reverb server is not running.\n";
                return;
            }

            $descriptorspec = [
                0 => ['pipe', 'r'],
                1 => ['pipe', 'w'],
                2 => ['pipe', 'w'],
            ];

            if (stripos(PHP_OS, 'WIN') === 0) {
                // Windows - Kill php processes running reverb
                $cmd = 'wmic process where "commandline like \'%reverb:start%\'" delete 2>&1';
            } else {
                // Linux/Mac
                $cmd = 'pkill -f "reverb:start"';
            }

            $process = proc_open($cmd, $descriptorspec, $pipes);

            if (is_resource($process)) {
                fclose($pipes[0]);
                $killOutput = stream_get_contents($pipes[1]);
                fclose($pipes[1]);
                fclose($pipes[2]);
                proc_close($process);

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
                    $this->output .= "⚠️ Server may still be stopping. Refresh to check.\n";
                }
            } else {
                throw new \Exception('Failed to stop Reverb process');
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
