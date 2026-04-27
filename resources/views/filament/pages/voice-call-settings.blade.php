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
    $agoraAppId = config('services.agora.app_id', '');
    $agoraCert = config('services.agora.app_certificate', '');
@endphp

<style>
/* Extra styles that Tailwind can't express inline */
.vc-stat-card { transition: all .2s cubic-bezier(0.4, 0, 0.2, 1); }
.vc-stat-card:hover { box-shadow: 0 10px 30px -5px rgba(0,0,0,.15); transform: translateY(-2px); }
.vc-danger-card { transition: all .2s; }
.vc-danger-card:hover { box-shadow: 0 6px 20px rgba(220,38,38,.2); transform: translateY(-1px); }
.vc-spin { animation: vc-spin .7s linear infinite; }
@keyframes vc-spin { to { transform: rotate(360deg); } }
.vc-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
@keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: .5; } }
</style>
<div class="space-y-6">

{{-- ═══════════════════════════════════════════════════
     AGORA CONNECTION STATUS
══════════════════════════════════════════════════════ --}}
@if(!empty($agoraAppId) && !empty($agoraCert))
<div class="flex items-center gap-3 px-6 py-4 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-950/30 dark:to-emerald-950/30 border-2 border-green-300 dark:border-green-700 shadow-sm">
    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-green-500 dark:bg-green-600">
        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <div class="flex-1">
        <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Agora Credentials Configured</h3>
        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">App ID and Certificate are set. Voice calls are ready to use.</p>
    </div>
    <div class="flex items-center gap-1 px-3 py-1.5 rounded-lg bg-green-100 dark:bg-green-900/40 border border-green-200 dark:border-green-800">
        <span class="w-2 h-2 bg-green-500 rounded-full vc-pulse"></span>
        <span class="text-xs font-semibold text-green-700 dark:text-green-300">Connected</span>
    </div>
</div>
@else
<div class="flex items-center gap-3 px-6 py-4 rounded-xl bg-gradient-to-r from-amber-50 to-yellow-50 dark:from-amber-950/30 dark:to-yellow-950/30 border-2 border-amber-300 dark:border-amber-700 shadow-sm">
    <div class="flex items-center justify-center w-8 h-8 rounded-full bg-amber-500 dark:bg-amber-600">
        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
        </svg>
    </div>
    <div class="flex-1">
        <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Agora Credentials Not Configured</h3>
        <p class="text-xs text-gray-600 dark:text-gray-400 mt-0.5">Add your Agora App ID and Certificate below to enable voice calling features.</p>
    </div>
    <a href="https://console.agora.io" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-100 dark:bg-amber-900/40 border border-amber-200 dark:border-amber-800 text-xs font-semibold text-amber-700 dark:text-amber-300 hover:bg-amber-200 dark:hover:bg-amber-900/60 transition-colors">
        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25"/>
        </svg>
        Get API Keys
    </a>
</div>
@endif

{{-- ═══════════════════════════════════════════════════
     AGORA TEST RESULT
══════════════════════════════════════════════════════ --}}
@if($agoraTestStatus === 'success')
<div class="flex items-start gap-3 px-5 py-4 rounded-xl bg-green-50 dark:bg-green-950/30 border-2 border-green-200 dark:border-green-800 shadow-sm animate-in slide-in-from-top duration-300">
    <div class="flex items-center justify-center w-7 h-7 rounded-full bg-green-100 dark:bg-green-900/60">
        <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
    </div>
    <div class="flex-1">
        <h4 class="text-sm font-bold text-green-800 dark:text-green-300">Connection Test Successful</h4>
        <p class="text-sm text-green-700 dark:text-green-400 mt-1 leading-relaxed">{{ $agoraTestResult }}</p>
    </div>
</div>
@elseif($agoraTestStatus === 'error')
<div class="flex items-start gap-3 px-5 py-4 rounded-xl bg-red-50 dark:bg-red-950/30 border-2 border-red-200 dark:border-red-800 shadow-sm animate-in slide-in-from-top duration-300">
    <div class="flex items-center justify-center w-7 h-7 rounded-full bg-red-100 dark:bg-red-900/60">
        <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
        </svg>
    </div>
    <div class="flex-1">
        <h4 class="text-sm font-bold text-red-800 dark:text-red-300">Connection Test Failed</h4>
        <p class="text-sm text-red-700 dark:text-red-400 mt-1 leading-relaxed">{{ $agoraTestResult }}</p>
    </div>
</div>
@elseif($agoraTestStatus === 'testing')
<div class="flex items-center gap-3 px-5 py-4 rounded-xl bg-blue-50 dark:bg-blue-950/30 border-2 border-blue-200 dark:border-blue-800 shadow-sm">
    <div class="flex items-center justify-center w-8 h-8">
        <svg class="vc-spin w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
            <path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8v8z"/>
        </svg>
    </div>
    <p class="text-sm font-semibold text-blue-700 dark:text-blue-300">Testing Agora connection...</p>
</div>
@endif

{{-- ═══════════════════════════════════════════════════
     STATUS BANNER
══════════════════════════════════════════════════════ --}}
<div class="flex items-center gap-3 px-6 py-4 rounded-xl border-2 text-sm font-semibold shadow-sm
    {{ $enabled
        ? 'bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-950/40 dark:to-emerald-950/40 border-green-300 dark:border-green-700 text-green-800 dark:text-green-300'
        : 'bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-950/40 dark:to-rose-950/40 border-red-300 dark:border-red-700 text-red-800 dark:text-red-300' }}">
    @if($enabled)
        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-green-500 dark:bg-green-600 shadow-lg">
            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <span>Voice calls are <strong class="font-black">ENABLED</strong></span>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-100 dark:bg-green-900/50 border border-green-200 dark:border-green-800 text-xs">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full vc-pulse"></span>
                    Live
                </span>
            </div>
            <p class="text-xs text-green-600 dark:text-green-400 mt-1 font-normal">Users can make and receive calls right now.</p>
        </div>
    @else
        <div class="flex items-center justify-center w-8 h-8 rounded-full bg-red-500 dark:bg-red-600 shadow-lg">
            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                <path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd"/>
            </svg>
        </div>
        <div class="flex-1">
            <div class="flex items-center gap-2">
                <span>Voice calls are <strong class="font-black">DISABLED</strong></span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-red-100 dark:bg-red-900/50 border border-red-200 dark:border-red-800 text-xs">
                    Offline
                </span>
            </div>
            <p class="text-xs text-red-600 dark:text-red-400 mt-1 font-normal">The call button is hidden from all users.</p>
        </div>
    @endif
</div>

{{-- ═══════════════════════════════════════════════════
     TODAY'S LIVE STATS
══════════════════════════════════════════════════════ --}}
<div>
    <div class="flex items-center gap-2 mb-4">
        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
        </svg>
        <h2 class="text-base font-bold text-gray-900 dark:text-gray-100">Today's Live Statistics</h2>
        <span class="text-xs text-gray-400 dark:text-gray-500">(Real-time)</span>
    </div>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">

        {{-- Active Now --}}
        <div class="vc-stat-card rounded-xl border-2 border-gray-200 dark:border-gray-700
                    bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-green-500 to-emerald-600 shadow-md">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z"/>
                    </svg>
                </div>
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-green-100 dark:bg-green-900/40 border border-green-200 dark:border-green-800 text-xs font-semibold text-green-700 dark:text-green-300">
                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full vc-pulse"></span>
                    Live
                </span>
            </div>
            <div class="text-3xl font-black text-green-600 dark:text-green-400 leading-none mb-1">{{ $stats['active_now'] }}</div>
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Active Now</div>
            <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">Connected calls</div>
        </div>

        {{-- Ringing --}}
        <div class="vc-stat-card rounded-xl border-2 border-gray-200 dark:border-gray-700
                    bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-amber-500 to-orange-600 shadow-md">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                    </svg>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/40 border border-amber-200 dark:border-amber-800 text-xs font-semibold text-amber-700 dark:text-amber-300">
                    Ringing
                </span>
            </div>
            <div class="text-3xl font-black text-amber-600 dark:text-amber-400 leading-none mb-1">{{ $stats['ringing_now'] }}</div>
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Ringing Now</div>
            <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">Waiting for answer</div>
        </div>

        {{-- Calls Today --}}
        <div class="vc-stat-card rounded-xl border-2 border-gray-200 dark:border-gray-700
                    bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 shadow-md">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-black text-blue-600 dark:text-blue-400 leading-none mb-1">{{ $stats['total_today'] }}</div>
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Calls Today</div>
            <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">Calls initiated</div>
        </div>

        {{-- Missed Today --}}
        <div class="vc-stat-card rounded-xl border-2 border-gray-200 dark:border-gray-700
                    bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-red-500 to-rose-600 shadow-md">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 3.75L18 6m0 0l2.25 2.25M18 6l2.25-2.25M18 6l-2.25 2.25m1.5 13.5c-8.284 0-15-6.716-15-15V4.5A2.25 2.25 0 014.5 2.25h1.372c.516 0 .966.351 1.091.852l1.106 4.423c.11.44-.054.902-.417 1.173l-1.293.97a1.062 1.062 0 00-.38 1.21 12.035 12.035 0 007.143 7.143c.441.162.928-.004 1.21-.38l.97-1.293a1.125 1.125 0 011.173-.417l4.423 1.106c.5.125.852.575.852 1.091V19.5a2.25 2.25 0 01-2.25 2.25h-2.25z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-black text-red-600 dark:text-red-400 leading-none mb-1">{{ $stats['missed_today'] }}</div>
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Missed Today</div>
            <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">Unanswered calls</div>
        </div>

        {{-- Avg Duration --}}
        <div class="vc-stat-card rounded-xl border-2 border-gray-200 dark:border-gray-700
                    bg-gradient-to-br from-white to-gray-50 dark:from-gray-900 dark:to-gray-800 p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-purple-500 to-violet-600 shadow-md">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <div class="text-3xl font-black text-purple-600 dark:text-purple-400 leading-none mb-1">{{ $this->formatSeconds($stats['avg_duration_today']) }}</div>
            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Avg Duration</div>
            <div class="text-xs text-gray-400 dark:text-gray-500 mt-1">Completed today</div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     ALL-TIME STATISTICS
══════════════════════════════════════════════════════ --}}
<div class="rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden shadow-sm">
    <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b-2 border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-2">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-violet-500 to-purple-600 shadow-md">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                </svg>
            </div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">All-Time Statistics</h3>
        </div>
        <span class="inline-flex items-center px-2.5 py-1 rounded-full bg-violet-100 dark:bg-violet-900/40 border border-violet-200 dark:border-violet-800 text-xs font-semibold text-violet-700 dark:text-violet-300">
            Lifetime Data
        </span>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-4 divide-x divide-y divide-gray-100 dark:divide-gray-800">
        <div class="p-6 text-center hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
            <div class="text-3xl font-black text-gray-900 dark:text-gray-100 mb-1">{{ number_format($stats['total_all']) }}</div>
            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Calls</div>
        </div>
        <div class="p-6 text-center hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
            <div class="text-3xl font-black text-green-600 dark:text-green-400 mb-1">{{ number_format($stats['total_ended']) }}</div>
            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Completed</div>
        </div>
        <div class="p-6 text-center hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
            <div class="text-3xl font-black text-red-600 dark:text-red-400 mb-1">{{ number_format($stats['total_missed']) }}</div>
            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Missed</div>
        </div>
        <div class="p-6 text-center hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
            <div class="text-3xl font-black text-purple-600 dark:text-purple-400 mb-1">{{ $this->formatSeconds($stats['avg_duration_all']) }}</div>
            <div class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Avg Duration</div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     SETTINGS FORM
══════════════════════════════════════════════════════ --}}
<div class="rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden shadow-sm">
    <div class="flex items-center gap-2 px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b-2 border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 shadow-md">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 11-3 0m3 0a1.5 1.5 0 10-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m-9.75 0h9.75"/>
            </svg>
        </div>
        <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Call Configuration</h3>
    </div>
    <div class="p-6">
        {{ $this->form }}
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     ACTIVE SETTINGS SNAPSHOT
══════════════════════════════════════════════════════ --}}
<div class="rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 overflow-hidden shadow-sm">
    <div class="flex items-center justify-between px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 border-b-2 border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-2">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gradient-to-br from-cyan-500 to-sky-600 shadow-md">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                </svg>
            </div>
            <h3 class="text-sm font-bold text-gray-900 dark:text-gray-100">Active Configuration</h3>
        </div>
        <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">(Currently applied settings)</span>
    </div>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 divide-x divide-y divide-gray-100 dark:divide-gray-800">
        <div class="p-5 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
            <div class="text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2 font-semibold">Feature Status</div>
            <div class="text-sm font-bold {{ $enabled ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                {{ $enabled ? '✓ Enabled' : '✗ Disabled' }}
            </div>
        </div>
        <div class="p-5 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
            <div class="text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2 font-semibold">Ring Timeout</div>
            <div class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $timeout }}s</div>
        </div>
        <div class="p-5 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
            <div class="text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2 font-semibold">Max Duration</div>
            <div class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $maxDur > 0 ? $maxDur . ' min' : 'Unlimited' }}</div>
        </div>
        <div class="p-5 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
            <div class="text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2 font-semibold">Daily Limit</div>
            <div class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $daily > 0 ? $daily . ' / day' : 'Unlimited' }}</div>
        </div>
        <div class="p-5 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
            <div class="text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2 font-semibold">Token Expiry</div>
            <div class="text-sm font-bold text-gray-900 dark:text-gray-100">
                {{ $expire >= 3600 ? ($expire / 3600) . 'h' : ($expire / 60) . ' min' }}
            </div>
        </div>
        <div class="p-5 hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
            <div class="text-xs uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2 font-semibold">Require Match</div>
            <div class="text-sm font-bold {{ $reqMatch ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                {{ $reqMatch ? 'Yes' : 'No' }}
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════
     DANGER ZONE
══════════════════════════════════════════════════════ --}}
<div class="rounded-xl border-2 border-red-300 dark:border-red-800 bg-gradient-to-br from-red-50 to-rose-50 dark:from-red-950/40 dark:to-rose-950/40 overflow-hidden shadow-sm">
    <div class="flex items-center gap-2 px-6 py-4 bg-red-100 dark:bg-red-900/40 border-b-2 border-red-300 dark:border-red-800">
        <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-red-600 dark:bg-red-700 shadow-md">
            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
            </svg>
        </div>
        <h3 class="text-sm font-bold text-red-900 dark:text-red-200">Danger Zone</h3>
        <span class="ml-auto text-xs font-semibold text-red-700 dark:text-red-400">⚠️ Destructive Actions</span>
    </div>
    <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-5">

        {{-- End All Active Calls --}}
        <div class="vc-danger-card rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 flex flex-col gap-4 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-red-100 dark:bg-red-900/40 flex-shrink-0">
                    <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-1.5">End All Active Calls</h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                        Force-terminate every call that is currently ringing or connected
                        <strong class="text-gray-800 dark:text-gray-200">({{ $stats['active_now'] + $stats['ringing_now'] }} active)</strong>.
                        Use after deploying breaking changes or in emergencies.
                    </p>
                </div>
            </div>
            <button
                wire:click="endAllActiveCalls"
                wire:loading.attr="disabled"
                wire:confirm="Are you sure? This will immediately end ALL active and ringing calls."
                class="self-start inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-bold
                       bg-red-600 hover:bg-red-700 text-white shadow-md hover:shadow-lg transition-all disabled:opacity-60 active:scale-95">
                <span wire:loading.remove wire:target="endAllActiveCalls" class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    End All Calls
                </span>
                <span wire:loading wire:target="endAllActiveCalls" class="flex items-center gap-2">
                    <svg class="vc-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
                        <path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Ending…
                </span>
            </button>
        </div>

        {{-- Clear Call History --}}
        <div class="vc-danger-card rounded-xl border-2 border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 p-5 flex flex-col gap-4 shadow-sm">
            <div class="flex items-start gap-3">
                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-gray-100 dark:bg-gray-800 flex-shrink-0">
                    <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-gray-900 dark:text-gray-100 mb-1.5">Clear Call History</h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400 leading-relaxed">
                        Permanently delete all
                        <strong class="text-gray-800 dark:text-gray-200">{{ number_format($stats['total_all']) }} call records</strong>
                        from the database. This action is <strong class="text-red-600 dark:text-red-400">irreversible</strong> — only use on dev/staging.
                    </p>
                </div>
            </div>
            <button
                wire:click="clearCallHistory"
                wire:loading.attr="disabled"
                wire:confirm="WARNING: This permanently deletes ALL call history. This cannot be undone. Are you absolutely sure?"
                class="self-start inline-flex items-center gap-2 px-4 py-2.5 rounded-lg text-xs font-bold
                       bg-gray-800 hover:bg-gray-900 dark:bg-gray-700 dark:hover:bg-gray-600 text-white shadow-md hover:shadow-lg transition-all disabled:opacity-60 active:scale-95">
                <span wire:loading.remove wire:target="clearCallHistory" class="flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                    </svg>
                    Clear All History
                </span>
                <span wire:loading wire:target="clearCallHistory" class="flex items-center gap-2">
                    <svg class="vc-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"/>
                        <path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8v8z"/>
                    </svg>
                    Deleting…
                </span>
            </button>
        </div>

    </div>
</div>

</div>{{-- space-y-6 --}}

</x-filament-panels::page>
