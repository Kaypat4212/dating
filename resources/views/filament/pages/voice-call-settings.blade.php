{{-- voice-call-settings.blade.php --}}
<x-filament-panels::page>
@php
    $stats    = $this->getStatsProperty();
    $enabled  = filter_var(\App\Models\SiteSetting::get('voice_calls_enabled', '1'),      FILTER_VALIDATE_BOOLEAN);
    $timeout  = (int) \App\Models\SiteSetting::get('voice_call_timeout', 30);
    $maxDur   = (int) \App\Models\SiteSetting::get('voice_call_max_duration', 0);
    $daily    = (int) \App\Models\SiteSetting::get('voice_call_daily_limit', 0);
    $expire   = (int) \App\Models\SiteSetting::get('voice_call_token_expire', 3600);
    $reqMatch = filter_var(\App\Models\SiteSetting::get('voice_call_require_match', '1'), FILTER_VALIDATE_BOOLEAN);
@endphp

<style>
/* Extra styles that Tailwind can't express inline */
.vc-stat-card { transition: box-shadow .15s, transform .15s; }
.vc-stat-card:hover { box-shadow: 0 6px 20px rgba(0,0,0,.10); transform: translateY(-1px); }
.vc-danger-card { transition: box-shadow .15s; }
.vc-danger-card:hover { box-shadow: 0 4px 14px rgba(220,38,38,.15); }
.vc-spin { animation: vc-spin .7s linear infinite; }
@keyframes vc-spin { to { transform: rotate(360deg); } }
</style>
<div class="space-y-5">

{{-- ═══════════════════════════════════════════════════
     STATUS BANNER
══════════════════════════════════════════════════════ --}}
<div class="flex items-center gap-3 px-5 py-4 rounded-xl border-2 text-sm font-semibold
    {{ $enabled
        ? 'bg-green-50 dark:bg-green-950/40 border-green-200 dark:border-green-800 text-green-700 dark:text-green-400'
        : 'bg-red-50   dark:bg-red-950/40   border-red-200   dark:border-red-800   text-red-700   dark:text-red-400' }}">
    @if($enabled)
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/>
        </svg>
        Voice calls are <strong class="font-black mx-1">ENABLED</strong> — users can make and receive calls right now.
    @else
        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
            <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd"/>
        </svg>
        Voice calls are <strong class="font-black mx-1">DISABLED</strong> — the call button is hidden from all users.
    @endif
</div>

{{-- ═══════════════════════════════════════════════════
     TODAY'S LIVE STATS
══════════════════════════════════════════════════════ --}}
<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">

    {{-- Active Now --}}
    <div class="vc-stat-card rounded-xl border border-gray-200 dark:border-gray-700
                bg-white dark:bg-gray-900 p-4 border-l-4 border-l-green-500">
        <div class="flex items-center gap-1.5 mb-3">
            <x-heroicon-o-phone class="w-4 h-4 text-green-500 dark:text-green-400 shrink-0" />
            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Active Now</span>
        </div>
        <div class="text-2xl font-black text-green-600 dark:text-green-400 leading-none">{{ $stats['active_now'] }}</div>
        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">live &amp; connected</div>
    </div>

    {{-- Ringing --}}
    <div class="vc-stat-card rounded-xl border border-gray-200 dark:border-gray-700
                bg-white dark:bg-gray-900 p-4 border-l-4 border-l-amber-500">
        <div class="flex items-center gap-1.5 mb-3">
            <x-heroicon-o-bell class="w-4 h-4 text-amber-500 dark:text-amber-400 shrink-0" />
            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ringing Now</span>
        </div>
        <div class="text-2xl font-black text-amber-600 dark:text-amber-400 leading-none">{{ $stats['ringing_now'] }}</div>
        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">waiting for answer</div>
    </div>

    {{-- Calls Today --}}
    <div class="vc-stat-card rounded-xl border border-gray-200 dark:border-gray-700
                bg-white dark:bg-gray-900 p-4 border-l-4 border-l-blue-500">
        <div class="flex items-center gap-1.5 mb-3">
            <x-heroicon-o-chart-bar class="w-4 h-4 text-blue-500 dark:text-blue-400 shrink-0" />
            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Calls Today</span>
        </div>
        <div class="text-2xl font-black text-blue-600 dark:text-blue-400 leading-none">{{ $stats['total_today'] }}</div>
        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">initiated today</div>
    </div>

    {{-- Missed Today --}}
    <div class="vc-stat-card rounded-xl border border-gray-200 dark:border-gray-700
                bg-white dark:bg-gray-900 p-4 border-l-4 border-l-red-500">
        <div class="flex items-center gap-1.5 mb-3">
            <x-heroicon-o-x-circle class="w-4 h-4 text-red-500 dark:text-red-400 shrink-0" />
            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Missed Today</span>
        </div>
        <div class="text-2xl font-black text-red-600 dark:text-red-400 leading-none">{{ $stats['missed_today'] }}</div>
        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">unanswered calls</div>
    </div>

    {{-- Avg Duration --}}
    <div class="vc-stat-card rounded-xl border border-gray-200 dark:border-gray-700
                bg-white dark:bg-gray-900 p-4 border-l-4 border-l-purple-500">
        <div class="flex items-center gap-1.5 mb-3">
            <x-heroicon-o-clock class="w-4 h-4 text-purple-500 dark:text-purple-400 shrink-0" />
            <span class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Avg Duration</span>
        </div>
        <div class="text-2xl font-black text-purple-600 dark:text-purple-400 leading-none">{{ $this->formatSeconds($stats['avg_duration_today']) }}</div>
        <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">completed calls today</div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     ALL-TIME STATISTICS
══════════════════════════════════════════════════════ --}}
<div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden">
    <div class="flex items-center gap-2 px-5 py-3 border-b border-gray-100 dark:border-gray-800
                bg-gray-50 dark:bg-gray-800/50 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
        <x-heroicon-o-chart-bar class="w-4 h-4 shrink-0" />
        All-time Statistics
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-y divide-gray-100 dark:divide-gray-800">
        <div class="p-5 text-center">
            <div class="text-2xl font-black text-gray-900 dark:text-gray-100">{{ number_format($stats['total_all']) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Total Calls</div>
        </div>
        <div class="p-5 text-center">
            <div class="text-2xl font-black text-green-600 dark:text-green-400">{{ number_format($stats['total_ended']) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Completed</div>
        </div>
        <div class="p-5 text-center">
            <div class="text-2xl font-black text-red-600 dark:text-red-400">{{ number_format($stats['total_missed']) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Missed</div>
        </div>
        <div class="p-5 text-center">
            <div class="text-2xl font-black text-purple-600 dark:text-purple-400">{{ $this->formatSeconds($stats['avg_duration_all']) }}</div>
            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">Avg Duration (all‑time)</div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     SETTINGS FORM
══════════════════════════════════════════════════════ --}}
<div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden">
    <div class="flex items-center gap-2 px-5 py-3 border-b border-gray-100 dark:border-gray-800
                bg-gray-50 dark:bg-gray-800/50 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
        <x-heroicon-o-cog-6-tooth class="w-4 h-4 shrink-0" />
        Call Configuration
    </div>
    <div class="p-5">
        {{ $this->form }}

        <div class="flex justify-end pt-4 mt-4 border-t border-gray-100 dark:border-gray-800">
            <button type="button"
                wire:click="save"
                wire:loading.attr="disabled"
                wire:target="save"
                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-lg text-sm font-semibold
                       bg-primary-600 hover:bg-primary-700 text-white shadow-sm transition disabled:opacity-60">
                <span wire:loading.remove wire:target="save" class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    Save Settings
                </span>
                <span wire:loading wire:target="save" class="flex items-center gap-2">
                    <svg class="vc-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
                        <path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Saving…
                </span>
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     ACTIVE SETTINGS SNAPSHOT
══════════════════════════════════════════════════════ --}}
<div class="rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden">
    <div class="flex items-center gap-2 px-5 py-3 border-b border-gray-100 dark:border-gray-800
                bg-gray-50 dark:bg-gray-800/50 text-xs font-bold uppercase tracking-wider text-gray-500 dark:text-gray-400">
        <x-heroicon-o-information-circle class="w-4 h-4 shrink-0" />
        Currently Active Settings <span class="ml-1 font-normal normal-case">(reflects last save)</span>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 divide-x divide-y divide-gray-100 dark:divide-gray-800">
        <div class="p-4">
            <div class="text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1.5">Feature Status</div>
            <div class="text-sm font-bold {{ $enabled ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                {{ $enabled ? '✓ Enabled' : '✗ Disabled' }}
            </div>
        </div>
        <div class="p-4">
            <div class="text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1.5">Ring Timeout</div>
            <div class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $timeout }}s</div>
        </div>
        <div class="p-4">
            <div class="text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1.5">Max Duration</div>
            <div class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $maxDur > 0 ? $maxDur . ' min' : 'Unlimited' }}</div>
        </div>
        <div class="p-4">
            <div class="text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1.5">Daily Limit</div>
            <div class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $daily > 0 ? $daily . ' / user / day' : 'Unlimited' }}</div>
        </div>
        <div class="p-4">
            <div class="text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1.5">Token Expiry</div>
            <div class="text-sm font-bold text-gray-900 dark:text-gray-100">
                {{ $expire >= 3600 ? ($expire / 3600) . 'h' : ($expire / 60) . ' min' }}
            </div>
        </div>
        <div class="p-4">
            <div class="text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-1.5">Require Match</div>
            <div class="text-sm font-bold {{ $reqMatch ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                {{ $reqMatch ? 'Yes' : 'No' }}
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     DANGER ZONE
══════════════════════════════════════════════════════ --}}
<div class="rounded-xl border-2 border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-950/30 overflow-hidden">
    <div class="flex items-center gap-2 px-5 py-3 border-b border-red-200 dark:border-red-800
                text-xs font-bold uppercase tracking-wider text-red-600 dark:text-red-400">
        <x-heroicon-o-exclamation-triangle class="w-4 h-4 shrink-0" />
        Danger Zone
    </div>
    <div class="p-4 grid grid-cols-1 sm:grid-cols-2 gap-4">

        {{-- End All Active Calls --}}
        <div class="vc-danger-card rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 flex flex-col gap-3">
            <div>
                <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center gap-1.5">
                    <x-heroicon-o-bolt class="w-4 h-4 text-red-500 shrink-0" /> End All Active Calls
                </h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                    Immediately force-terminates every call that is currently ringing or connected
                    <strong class="text-gray-700 dark:text-gray-300">({{ $stats['active_now'] + $stats['ringing_now'] }} right now)</strong>.
                    Use after deploying breaking changes or in an emergency.
                </p>
            </div>
            <button
                wire:click="endAllActiveCalls"
                wire:loading.attr="disabled"
                wire:confirm="Are you sure? This will immediately end ALL active and ringing calls."
                class="self-start inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold
                       bg-red-600 hover:bg-red-700 text-white transition disabled:opacity-60">
                <span wire:loading.remove wire:target="endAllActiveCalls" class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    End All Calls
                </span>
                <span wire:loading wire:target="endAllActiveCalls" class="flex items-center gap-1.5">
                    <svg class="vc-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
                        <path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Ending…
                </span>
            </button>
        </div>

        {{-- Clear Call History --}}
        <div class="vc-danger-card rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 flex flex-col gap-3">
            <div>
                <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 flex items-center gap-1.5">
                    <x-heroicon-o-trash class="w-4 h-4 text-gray-500 shrink-0" /> Clear Call History
                </h4>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">
                    Permanently deletes all
                    <strong class="text-gray-700 dark:text-gray-300">{{ number_format($stats['total_all']) }} call records</strong>
                    from the database. This action is <strong class="text-red-600 dark:text-red-400">irreversible</strong> — only use on dev / staging environments.
                </p>
            </div>
            <button
                wire:click="clearCallHistory"
                wire:loading.attr="disabled"
                wire:confirm="WARNING: This permanently deletes ALL call history. This cannot be undone. Are you absolutely sure?"
                class="self-start inline-flex items-center gap-2 px-4 py-2 rounded-lg text-xs font-semibold
                       bg-gray-800 hover:bg-gray-900 dark:bg-gray-700 dark:hover:bg-gray-600 text-white transition disabled:opacity-60">
                <span wire:loading.remove wire:target="clearCallHistory" class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                    </svg>
                    Clear History
                </span>
                <span wire:loading wire:target="clearCallHistory" class="flex items-center gap-1.5">
                    <svg class="vc-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
                        <path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Deleting…
                </span>
            </button>
        </div>

    </div>
</div>

</div>{{-- space-y-5 --}}

</x-filament-panels::page>
