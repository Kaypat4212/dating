<x-filament-panels::page>

    @php
        $stats    = $this->getStatsProperty();
        $enabled  = filter_var(\App\Models\SiteSetting::get('voice_calls_enabled', '1'), FILTER_VALIDATE_BOOLEAN);
        $timeout  = (int) \App\Models\SiteSetting::get('voice_call_timeout', 30);
        $maxDur   = (int) \App\Models\SiteSetting::get('voice_call_max_duration', 0);
        $daily    = (int) \App\Models\SiteSetting::get('voice_call_daily_limit', 0);

        function fmtSec(float $sec): string {
            if ($sec < 60) return number_format($sec, 0) . 's';
            return number_format($sec / 60, 1) . ' min';
        }
    @endphp

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- Status Banner                                             --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div @class([
        'rounded-xl border px-5 py-3 flex items-center gap-3 mb-4 text-sm font-semibold',
        'bg-green-50 border-green-200 text-green-800 dark:bg-green-900/20 dark:border-green-800 dark:text-green-300' => $enabled,
        'bg-red-50 border-red-200 text-red-800 dark:bg-red-900/20 dark:border-red-800 dark:text-red-300'           => !$enabled,
    ])>
        @if ($enabled)
            <x-heroicon-s-check-circle class="w-5 h-5 shrink-0" />
            Voice calls are <strong>ENABLED</strong> — users can make and receive calls right now.
        @else
            <x-heroicon-s-x-circle class="w-5 h-5 shrink-0" />
            Voice calls are <strong>DISABLED</strong> — the call button is hidden for all users.
        @endif
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- Live Stats Grid                                           --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-3 mb-6">

        @php
        $statCards = [
            ['label' => 'Active now',      'value' => $stats['active_now'],       'sub' => 'live connected',      'color' => 'text-green-600 dark:text-green-400',  'bg' => 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800'],
            ['label' => 'Ringing now',     'value' => $stats['ringing_now'],      'sub' => 'waiting for answer',  'color' => 'text-yellow-600 dark:text-yellow-400', 'bg' => 'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800'],
            ['label' => 'Calls today',     'value' => $stats['total_today'],      'sub' => 'initiated today',     'color' => 'text-blue-600 dark:text-blue-400',    'bg' => 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800'],
            ['label' => 'Missed today',    'value' => $stats['missed_today'],     'sub' => 'unanswered today',    'color' => 'text-red-600 dark:text-red-400',      'bg' => 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800'],
            ['label' => 'Avg duration',    'value' => fmtSec($stats['avg_duration_today']), 'sub' => 'completed calls today', 'color' => 'text-purple-600 dark:text-purple-400', 'bg' => 'bg-purple-50 dark:bg-purple-900/20 border-purple-200 dark:border-purple-800'],
        ];
        @endphp

        @foreach ($statCards as $card)
        <div class="rounded-xl border p-4 flex flex-col gap-1 {{ $card['bg'] }}">
            <span class="text-2xl font-bold {{ $card['color'] }}">{{ $card['value'] }}</span>
            <span class="text-xs font-semibold text-gray-700 dark:text-gray-200">{{ $card['label'] }}</span>
            <span class="text-[10px] text-gray-400 dark:text-gray-500">{{ $card['sub'] }}</span>
        </div>
        @endforeach

    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- All-time totals                                           --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 mb-6 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-800 flex items-center gap-2">
            <x-heroicon-o-chart-bar class="w-4 h-4 text-gray-500" />
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">All-time Statistics</span>
        </div>
        <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-gray-100 dark:divide-gray-800 text-center">
            <div class="px-4 py-5">
                <div class="text-xl font-bold text-gray-800 dark:text-gray-100">{{ number_format($stats['total_all']) }}</div>
                <div class="text-xs text-gray-500 mt-1">Total calls</div>
            </div>
            <div class="px-4 py-5">
                <div class="text-xl font-bold text-green-600 dark:text-green-400">{{ number_format($stats['total_ended']) }}</div>
                <div class="text-xs text-gray-500 mt-1">Completed</div>
            </div>
            <div class="px-4 py-5">
                <div class="text-xl font-bold text-red-500 dark:text-red-400">{{ number_format($stats['total_missed']) }}</div>
                <div class="text-xs text-gray-500 mt-1">Missed</div>
            </div>
            <div class="px-4 py-5">
                <div class="text-xl font-bold text-purple-600 dark:text-purple-400">{{ fmtSec($stats['avg_duration_all']) }}</div>
                <div class="text-xs text-gray-500 mt-1">Avg duration (all time)</div>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- Settings form                                             --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <x-filament-panels::form wire:submit="save">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getCachedHeaderActions()"
            :full-width="false"
        />
    </x-filament-panels::form>

    {{-- ══════════════════════════════════════════════════════════ --}}
    {{-- Danger Zone                                               --}}
    {{-- ══════════════════════════════════════════════════════════ --}}
    <div class="rounded-xl border border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/10 mt-6 overflow-hidden">
        <div class="px-5 py-3 border-b border-red-100 dark:border-red-800 flex items-center gap-2">
            <x-heroicon-o-exclamation-triangle class="w-4 h-4 text-red-600 dark:text-red-400" />
            <span class="text-sm font-semibold text-red-700 dark:text-red-300">Danger Zone</span>
        </div>
        <div class="p-5 flex flex-col sm:flex-row gap-4">

            {{-- End all active calls --}}
            <div class="flex-1 rounded-lg border border-red-200 dark:border-red-700 bg-white dark:bg-gray-900 p-4 flex flex-col gap-3">
                <div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">End All Active Calls</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Immediately terminates every call that is currently ringing or active ({{ $stats['active_now'] + $stats['ringing_now'] }} right now).
                        Use in emergencies or after deploying a breaking change.
                    </p>
                </div>
                <button
                    wire:click="endAllActiveCalls"
                    wire:loading.attr="disabled"
                    wire:confirm="Are you sure you want to forcibly end all active and ringing calls?"
                    class="self-start inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold
                           bg-red-600 hover:bg-red-700 text-white transition disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="endAllActiveCalls">
                        <x-heroicon-o-phone-x-mark class="w-4 h-4 inline" /> End All Calls
                    </span>
                    <span wire:loading wire:target="endAllActiveCalls">Ending…</span>
                </button>
            </div>

            {{-- Clear call history --}}
            <div class="flex-1 rounded-lg border border-red-200 dark:border-red-700 bg-white dark:bg-gray-900 p-4 flex flex-col gap-3">
                <div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Clear Call History</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        Permanently deletes all {{ number_format($stats['total_all']) }} call records.
                        This is irreversible — only use on dev / staging.
                    </p>
                </div>
                <button
                    wire:click="clearCallHistory"
                    wire:loading.attr="disabled"
                    wire:confirm="WARNING: This deletes ALL call history permanently. Are you absolutely sure?"
                    class="self-start inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold
                           bg-gray-700 hover:bg-gray-900 text-white transition disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="clearCallHistory">
                        <x-heroicon-o-trash class="w-4 h-4 inline" /> Clear History
                    </span>
                    <span wire:loading wire:target="clearCallHistory">Deleting…</span>
                </button>
            </div>

        </div>
    </div>

    {{-- ── Quick reference panel ────────────────────────────────────────── --}}
    <div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 mt-6 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-800 flex items-center gap-2">
            <x-heroicon-o-information-circle class="w-4 h-4 text-gray-400" />
            <span class="text-sm font-semibold text-gray-700 dark:text-gray-200">Current Effective Settings</span>
        </div>
        <div class="p-5 grid grid-cols-2 sm:grid-cols-3 gap-4 text-sm">
            <div>
                <span class="text-gray-500 dark:text-gray-400 text-xs">Status</span>
                <p class="font-semibold {{ $enabled ? 'text-green-600' : 'text-red-600' }}">
                    {{ $enabled ? 'Enabled' : 'Disabled' }}
                </p>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400 text-xs">Ring timeout</span>
                <p class="font-semibold text-gray-800 dark:text-gray-100">{{ $timeout }}s</p>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400 text-xs">Max duration</span>
                <p class="font-semibold text-gray-800 dark:text-gray-100">
                    {{ $maxDur > 0 ? $maxDur . ' min' : 'Unlimited' }}
                </p>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400 text-xs">Daily call limit</span>
                <p class="font-semibold text-gray-800 dark:text-gray-100">
                    {{ $daily > 0 ? $daily . ' calls/user/day' : 'Unlimited' }}
                </p>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400 text-xs">Token expiry</span>
                <p class="font-semibold text-gray-800 dark:text-gray-100">
                    {{ (int)\App\Models\SiteSetting::get('voice_call_token_expire', 3600) / 60 }} min
                </p>
            </div>
            <div>
                <span class="text-gray-500 dark:text-gray-400 text-xs">Require match</span>
                <p class="font-semibold text-gray-800 dark:text-gray-100">
                    {{ filter_var(\App\Models\SiteSetting::get('voice_call_require_match', '1'), FILTER_VALIDATE_BOOLEAN) ? 'Yes' : 'No' }}
                </p>
            </div>
        </div>
    </div>

</x-filament-panels::page>
