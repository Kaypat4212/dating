<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CheckDailyCoConfig extends Command
{
    protected $signature = 'dailyco:check';
    protected $description = 'Check Daily.co configuration and troubleshoot .env issues';

    public function handle(): int
    {
        $this->info('🔍 Checking Daily.co Configuration...');
        $this->newLine();

        // Read directly from env() - bypasses config cache
        $apiKey = env('DAILY_CO_API_KEY', '');
        $domain = env('DAILY_CO_DOMAIN', '');

        // Check .env file exists
        $envPath = base_path('.env');
        if (!file_exists($envPath)) {
            $this->error('❌ .env file not found at: ' . $envPath);
            $this->warn('💡 Copy .env.example to .env and add your keys');
            return self::FAILURE;
        }

        $this->line("📁 .env file exists: {$envPath}");
        $this->newLine();

        // Show what we found
        $this->line('📋 Current Values (via env()):');
        $this->line('  DAILY_CO_API_KEY: ' . ($apiKey ? '✅ Set (' . strlen($apiKey) . ' chars)' : '❌ Empty'));
        $this->line('  DAILY_CO_DOMAIN:  ' . ($domain ? "✅ Set ({$domain})" : '⚠️  Empty (optional)'));
        $this->newLine();

        // Search .env file directly
        $envContents = file_get_contents($envPath);
        $hasKeyLine = str_contains($envContents, 'DAILY_CO_API_KEY');
        $hasDomainLine = str_contains($envContents, 'DAILY_CO_DOMAIN');

        $this->line('📝 .env File Analysis:');
        $this->line('  DAILY_CO_API_KEY line exists: ' . ($hasKeyLine ? '✅ Yes' : '❌ No (add it!)'));
        $this->line('  DAILY_CO_DOMAIN line exists:  ' . ($hasDomainLine ? '✅ Yes' : '⚠️  No (optional)'));
        $this->newLine();

        // Extract the actual lines
        if ($hasKeyLine) {
            preg_match('/^DAILY_CO_API_KEY=(.*)$/m', $envContents, $matches);
            $lineValue = $matches[1] ?? '';
            $this->line('  Actual line in .env:');
            $this->line('    DAILY_CO_API_KEY=' . ($lineValue ? $lineValue : '(empty)'));
            
            // Check for common issues
            if (empty($lineValue)) {
                $this->warn('  ⚠️  Line exists but value is empty!');
            } elseif (str_starts_with($lineValue, '"') || str_starts_with($lineValue, "'")) {
                $this->warn('  ⚠️  Value has quotes - remove them! Just: DAILY_CO_API_KEY=abc123...');
            } elseif (str_contains($lineValue, ' ')) {
                $this->warn('  ⚠️  Value has spaces - remove them!');
            }
        }
        $this->newLine();

        // Test API connection
        if (empty($apiKey)) {
            $this->error('❌ DAILY_CO_API_KEY is empty - cannot test API');
            $this->newLine();
            $this->line('📝 To fix:');
            $this->line('  1. Open c:\xampp\htdocs\dating\.env in a text editor');
            $this->line('  2. Add this line (replace with your actual key):');
            $this->line('     DAILY_CO_API_KEY=abc123yourkeyhere');
            $this->line('  3. Add this line (replace with your domain):');
            $this->line('     DAILY_CO_DOMAIN=heartsconnect');
            $this->line('  4. Save the file');
            $this->line('  5. Run: php artisan config:clear');
            $this->line('  6. Restart Apache in XAMPP Control Panel');
            $this->line('  7. Run this command again: php artisan dailyco:check');
            return self::FAILURE;
        }

        $this->line('🌐 Testing Daily.co API connection...');
        try {
            $response = \Illuminate\Support\Facades\Http::timeout(10)
                ->withToken($apiKey)
                ->get('https://api.daily.co/v1/rooms', ['limit' => 1]);

            if ($response->successful()) {
                $data = $response->json();
                $totalRooms = $data['total_count'] ?? 0;
                $this->info('✅ API key is VALID!');
                $this->line("  Total rooms: {$totalRooms}");
                $this->newLine();
                $this->info('🎉 Daily.co is configured correctly!');
                $this->line('  Voice/video calls should work now.');
                return self::SUCCESS;
            } elseif ($response->status() === 401) {
                $this->error('❌ API key is INVALID (401 Unauthorized)');
                $this->line('  The key is set but Daily.co rejected it.');
                $this->line('  1. Go to https://dashboard.daily.co');
                $this->line('  2. Get a new API key');
                $this->line('  3. Update DAILY_CO_API_KEY in .env');
                $this->line('  4. Run: php artisan config:clear');
                return self::FAILURE;
            } else {
                $this->error("❌ API error (HTTP {$response->status()})");
                $this->line('  ' . $response->body());
                return self::FAILURE;
            }
        } catch (\Exception $e) {
            $this->error('❌ Connection failed: ' . $e->getMessage());
            $this->line('  Check your internet connection');
            return self::FAILURE;
        }
    }
}
