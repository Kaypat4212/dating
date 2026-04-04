<x-filament-panels::page>

    {{-- ── Header actions ─────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Test that every external service key is live and reachable right now.
                Results are never cached — each click makes a real HTTP request.
            </p>
        </div>
        <button
            wire:click="testAll"
            wire:loading.attr="disabled"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg text-sm font-semibold bg-primary-600 hover:bg-primary-700 text-white shadow transition disabled:opacity-60"
        >
            <span wire:loading.remove wire:target="testAll">
                <x-heroicon-s-play class="w-4 h-4 inline" /> Test all services
            </span>
            <span wire:loading wire:target="testAll" class="flex items-center gap-2">
                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                </svg>
                Running tests…
            </span>
        </button>
    </div>

    {{-- ── Service grid ─────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">

        @php
        $services = [
            [
                'id'       => 'agora',
                'label'    => 'Agora (Video Calls)',
                'icon'     => 'heroicon-o-video-camera',
                'method'   => 'testAgora',
                'env_keys' => ['AGORA_APP_ID', 'AGORA_APP_CERTIFICATE'],
            ],
            [
                'id'       => 'groq',
                'label'    => 'Groq AI',
                'icon'     => 'heroicon-o-cpu-chip',
                'method'   => 'testGroq',
                'env_keys' => ['GROQ_API_KEY'],
            ],
            [
                'id'       => 'iphub',
                'label'    => 'IPHub (VPN Detection)',
                'icon'     => 'heroicon-o-shield-check',
                'method'   => 'testIpHub',
                'env_keys' => ['IPHUB_API_KEY'],
            ],
            [
                'id'       => 'proxycheck',
                'label'    => 'ProxyCheck (VPN Detection)',
                'icon'     => 'heroicon-o-shield-exclamation',
                'method'   => 'testProxyCheck',
                'env_keys' => ['PROXYCHECK_API_KEY'],
            ],
            [
                'id'       => 'telegram',
                'label'    => 'Telegram (Notifications)',
                'icon'     => 'heroicon-o-paper-airplane',
                'method'   => 'testTelegram',
                'env_keys' => ['TELEGRAM_BOT_TOKEN', 'TELEGRAM_CHAT_ID'],
            ],
            [
                'id'       => 'mail',
                'label'    => 'Mail / SMTP',
                'icon'     => 'heroicon-o-envelope',
                'method'   => 'testMailSmtp',
                'env_keys' => ['MAIL_MAILER', 'MAIL_HOST', 'MAIL_PORT'],
            ],
            [
                'id'       => 'reverb',
                'label'    => 'Reverb WebSocket',
                'icon'     => 'heroicon-o-signal',
                'method'   => 'testReverb',
                'env_keys' => ['REVERB_PORT', 'REVERB_APP_KEY'],
            ],
            [
                'id'       => 'database',
                'label'    => 'Database Connection',
                'icon'     => 'heroicon-o-circle-stack',
                'method'   => 'testDatabase',
                'env_keys' => ['DB_HOST', 'DB_DATABASE'],
            ],
        ];
        @endphp

        @foreach ($services as $svc)
        @php
            $result  = $this->results[$svc['id']] ?? null;
            $status  = $result['status'] ?? 'idle';

            $cardBg  = match($status) {
                'pass' => 'bg-green-50  dark:bg-green-900/20  border-green-200  dark:border-green-800',
                'fail' => 'bg-red-50    dark:bg-red-900/20    border-red-200    dark:border-red-800',
                'warn' => 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800',
                default => 'bg-white dark:bg-gray-900 border-gray-200 dark:border-gray-700',
            };

            $badgeClass = match($status) {
                'pass' => 'bg-green-100  text-green-800  dark:bg-green-800  dark:text-green-100',
                'fail' => 'bg-red-100    text-red-800    dark:bg-red-800    dark:text-red-100',
                'warn' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-800 dark:text-yellow-100',
                default => 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400',
            };

            $badgeLabel = match($status) {
                'pass' => '✓ PASS',
                'fail' => '✗ FAIL',
                'warn' => '⚠ WARN',
                default => '— not tested',
            };
        @endphp

        <div class="rounded-xl border p-4 flex flex-col gap-3 transition-colors {{ $cardBg }}">

            {{-- Card header --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <x-dynamic-component :component="$svc['icon']" class="w-5 h-5 text-gray-600 dark:text-gray-300 shrink-0" />
                    <span class="font-semibold text-sm text-gray-800 dark:text-gray-100">{{ $svc['label'] }}</span>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $badgeClass }}">
                    {{ $badgeLabel }}
                </span>
            </div>

            {{-- Env key hints --}}
            <div class="flex flex-wrap gap-1">
                @foreach ($svc['env_keys'] as $envKey)
                <code class="text-[10px] bg-gray-100 dark:bg-gray-800 text-gray-500 dark:text-gray-400 px-1.5 py-0.5 rounded font-mono">
                    {{ $envKey }}
                </code>
                @endforeach
            </div>

            {{-- Result details (only after test) --}}
            @if ($result)
            <div class="text-xs space-y-0.5">
                <p class="font-medium text-gray-800 dark:text-gray-100">{{ $result['message'] }}</p>
                @if (!empty($result['detail']))
                <p class="text-gray-500 dark:text-gray-400 break-all">{{ $result['detail'] }}</p>
                @endif
                @if (!is_null($result['ms']))
                <p class="text-gray-400 dark:text-gray-500">{{ $result['ms'] }} ms</p>
                @endif
            </div>
            @endif

            {{-- Individual test button --}}
            <button
                wire:click="{{ $svc['method'] }}"
                wire:loading.attr="disabled"
                wire:target="{{ $svc['method'] }},testAll"
                class="mt-auto w-full text-xs font-medium py-1.5 rounded-lg
                    border border-gray-300 dark:border-gray-600
                    bg-white dark:bg-gray-800
                    hover:bg-gray-50 dark:hover:bg-gray-700
                    text-gray-700 dark:text-gray-200
                    transition disabled:opacity-50"
            >
                <span wire:loading.remove wire:target="{{ $svc['method'] }}">Test</span>
                <span wire:loading wire:target="{{ $svc['method'] }}" class="flex items-center justify-center gap-1">
                    <svg class="animate-spin w-3 h-3" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Testing…
                </span>
            </button>

        </div>
        @endforeach

    </div>

    {{-- ── Summary row (after testAll) ─────────────────────────────────── --}}
    @if (count($this->results) > 0)
    @php
        $pass = collect($this->results)->where('status', 'pass')->count();
        $fail = collect($this->results)->where('status', 'fail')->count();
        $warn = collect($this->results)->where('status', 'warn')->count();
        $total = count($this->results);
    @endphp
    <div class="mt-6 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-4 flex items-center gap-6 flex-wrap text-sm">
        <span class="font-semibold text-gray-600 dark:text-gray-300">Summary ({{ $total }} tested)</span>
        <span class="flex items-center gap-1 text-green-600 dark:text-green-400 font-semibold">
            <x-heroicon-s-check-circle class="w-4 h-4" /> {{ $pass }} passed
        </span>
        <span class="flex items-center gap-1 text-yellow-600 dark:text-yellow-400 font-semibold">
            <x-heroicon-s-exclamation-triangle class="w-4 h-4" /> {{ $warn }} warnings
        </span>
        <span class="flex items-center gap-1 text-red-600 dark:text-red-400 font-semibold">
            <x-heroicon-s-x-circle class="w-4 h-4" /> {{ $fail }} failed
        </span>
    </div>
    @endif

</x-filament-panels::page>
