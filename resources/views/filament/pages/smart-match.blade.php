<x-filament-panels::page>

{{-- ── Page-level styles ─────────────────────────────────────────────────── --}}
<style>
/* Gradient glow for active user card */
.sm-user-active {
    background: linear-gradient(135deg, rgba(244,63,94,.15) 0%, rgba(168,85,247,.1) 100%) !important;
    border-left: 3px solid #f43f5e !important;
}
/* Score arc ring */
@keyframes ring-fill { from { stroke-dashoffset: 220; } }
/* Card hover lift */
.sm-candidate-card { transition: transform .18s ease, box-shadow .18s ease; }
.sm-candidate-card:hover { transform: translateY(-3px); box-shadow: 0 16px 40px rgba(0,0,0,.5); }
/* Pulse dot */
@keyframes pulse-dot { 0%,100%{opacity:1} 50%{opacity:.35} }
.sm-pulse { animation: pulse-dot 1.8s infinite; }
/* Shimmer skeleton */
@keyframes shimmer { 0%{background-position:-400px 0} 100%{background-position:400px 0} }
.sm-skeleton {
    background: linear-gradient(90deg, rgba(255,255,255,.04) 25%, rgba(255,255,255,.09) 50%, rgba(255,255,255,.04) 75%);
    background-size: 400px 100%;
    animation: shimmer 1.4s infinite;
    border-radius: 6px;
}
</style>

<div class="flex flex-col gap-5 lg:flex-row lg:items-start" style="min-height:70vh">

    {{-- ══════════════════════════════════════════════════════════
         LEFT SIDEBAR — New Members
    ══════════════════════════════════════════════════════════ --}}
    <div class="w-full shrink-0 lg:w-[280px]">
        {{-- Sidebar header --}}
        <div class="mb-3 overflow-hidden rounded-2xl"
             style="background:linear-gradient(135deg,#1e0a2e 0%,#2d1050 50%,#1a0a2e 100%);border:1px solid rgba(244,63,94,.25)">
            <div class="px-4 py-3" style="border-bottom:1px solid rgba(255,255,255,.07)">
                <div class="flex items-center gap-2">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg"
                         style="background:linear-gradient(135deg,#f43f5e,#a855f7)">
                        <svg class="h-4 w-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M17 20h5v-2a3 3 0 00-3-3H5a3 3 0 00-3 3v2h5M9 11a4 4 0 110-8 4 4 0 010 8zm6 0a4 4 0 110-8 4 4 0 010 8z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-[11px] font-bold uppercase tracking-widest" style="color:rgba(255,255,255,.5)">New Members</p>
                        <p class="text-[10px]" style="color:rgba(255,255,255,.3)">Last 7 days</p>
                    </div>
                    @php $newUsers = $this->getNewUsers(); @endphp
                    <span class="ml-auto inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full px-1 text-[10px] font-bold"
                          style="background:linear-gradient(135deg,#f43f5e,#a855f7);color:#fff">
                        {{ $newUsers->count() }}
                    </span>
                </div>
            </div>

            {{-- Member list --}}
            @if($newUsers->isEmpty())
                <div class="px-4 py-8 text-center">
                    <div class="mb-2 text-3xl">👥</div>
                    <p class="text-xs" style="color:rgba(255,255,255,.35)">No new users in the past 7 days.</p>
                </div>
            @else
                <ul class="max-h-[calc(100vh-220px)] overflow-y-auto" style="scrollbar-width:thin;scrollbar-color:rgba(244,63,94,.3) transparent">
                    @foreach($newUsers as $nu)
                        @php
                            $photo    = $nu->primaryPhoto;
                            $photoUrl = $photo ? $photo->thumbnail_url : null;
                            $active   = $focusUserId === $nu->id;
                        @endphp
                        <li style="border-bottom:1px solid rgba(255,255,255,.05)">
                            <button
                                wire:click="selectUser({{ $nu->id }})"
                                wire:loading.class="opacity-60"
                                wire:target="selectUser({{ $nu->id }})"
                                class="group flex w-full items-center gap-3 px-4 py-2.5 text-left transition-all duration-150
                                       {{ $active ? 'sm-user-active' : 'hover:bg-white/5' }}"
                            >
                                {{-- Avatar --}}
                                <div class="relative shrink-0">
                                    @if($photoUrl)
                                        <img src="{{ $photoUrl }}" alt="{{ $nu->name }}"
                                             class="h-9 w-9 rounded-full object-cover"
                                             style="box-shadow:0 0 0 2px {{ $active ? '#f43f5e' : 'rgba(255,255,255,.12)' }}" />
                                    @else
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full text-sm font-bold"
                                             style="background:linear-gradient(135deg,#2d1050,#4a0e6e);color:#fff;box-shadow:0 0 0 2px {{ $active ? '#f43f5e' : 'rgba(255,255,255,.12)' }}">
                                            {{ strtoupper(substr($nu->name, 0, 1)) }}
                                        </div>
                                    @endif
                                    {{-- Online indicator --}}
                                    @if(isset($nu->last_active_at) && \Carbon\Carbon::parse($nu->last_active_at)->diffInMinutes() < 30)
                                        <span class="absolute -bottom-0.5 -right-0.5 h-2.5 w-2.5 rounded-full border-2 sm-pulse"
                                              style="background:#22c55e;border-color:#1a0a2e"></span>
                                    @endif
                                </div>

                                {{-- Info --}}
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-1">
                                        <p class="truncate text-xs font-semibold {{ $active ? '' : 'text-white/80 group-hover:text-white' }}"
                                           style="{{ $active ? 'color:#fb7185' : '' }}">
                                            {{ $nu->name }}
                                        </p>
                                        @if($nu->is_premium)
                                            <span class="shrink-0 rounded px-1 text-[9px] font-black"
                                                  style="background:#f59e0b;color:#000">★</span>
                                        @endif
                                        @if($nu->is_verified)
                                            <span class="shrink-0 rounded px-1 text-[9px] font-black"
                                                  style="background:#0ea5e9;color:#fff">✓</span>
                                        @endif
                                    </div>
                                    <p class="text-[10px]" style="color:rgba(255,255,255,.35)">
                                        {{ ucfirst($nu->gender ?? '—') }}{{ $nu->age ? ' · '.$nu->age.' yrs' : '' }}
                                    </p>
                                    <p class="text-[10px]" style="color:rgba(255,255,255,.2)">{{ $nu->created_at->diffForHumans() }}</p>
                                </div>

                                {{-- Chevron when active --}}
                                @if($active)
                                    <svg class="h-4 w-4 shrink-0" style="color:#f43f5e" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                @endif
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════
         RIGHT PANEL — Suggestions
    ══════════════════════════════════════════════════════════ --}}
    <div class="flex-1 min-w-0">

        @if(! $focusUserId)
            {{-- Empty state --}}
            <div class="flex flex-col items-center justify-center rounded-2xl py-20 text-center"
                 style="background:linear-gradient(135deg,rgba(30,10,46,.6),rgba(45,16,80,.4));border:1.5px dashed rgba(244,63,94,.2)">
                <div class="mb-5 flex h-20 w-20 items-center justify-center rounded-full"
                     style="background:linear-gradient(135deg,rgba(244,63,94,.12),rgba(168,85,247,.12));border:1.5px solid rgba(244,63,94,.2)">
                    <svg class="h-10 w-10" style="color:rgba(244,63,94,.5)" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                              d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                    </svg>
                </div>
                <h3 class="mb-1 text-base font-bold text-white/70">Select a member to begin</h3>
                <p class="text-sm" style="color:rgba(255,255,255,.3);max-width:300px">
                    Pick any new member from the sidebar to see their AI-powered compatibility suggestions.
                </p>
                <div class="mt-6 flex items-center gap-6 text-[11px]" style="color:rgba(255,255,255,.25)">
                    <span>🧠 AI scoring</span>
                    <span>💞 Auto-match</span>
                    <span>⚡ Instant notifications</span>
                </div>
            </div>

        @else
            @php
                $focusUser  = \App\Models\User::with(['profile.interests', 'primaryPhoto'])->find($focusUserId);
                $focusPhoto = $focusUser?->primaryPhoto;
            @endphp

            @if(! $focusUser)
                <div class="rounded-xl py-6 text-center text-sm"
                     style="background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#fca5a5">
                    User not found.
                </div>
            @else

                {{-- ── Focus user hero banner ─────────────────────────────── --}}
                <div class="mb-5 overflow-hidden rounded-2xl"
                     style="background:linear-gradient(135deg,#1e0a2e 0%,#2d1050 50%,#1a0a2e 100%);border:1px solid rgba(244,63,94,.3);box-shadow:0 8px 32px rgba(244,63,94,.12)">
                    {{-- Top gradient line --}}
                    <div class="h-0.5 w-full" style="background:linear-gradient(90deg,#f43f5e,#a855f7,#3b82f6)"></div>

                    <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center">
                        {{-- Avatar --}}
                        <div class="relative shrink-0 self-start sm:self-center">
                            @if($focusPhoto)
                                <img src="{{ $focusPhoto->thumbnail_url }}" alt="{{ $focusUser->name }}"
                                     class="h-16 w-16 rounded-2xl object-cover"
                                     style="box-shadow:0 0 0 3px #f43f5e,0 8px 24px rgba(244,63,94,.35)" />
                            @else
                                <div class="flex h-16 w-16 items-center justify-center rounded-2xl text-2xl font-black text-white"
                                     style="background:linear-gradient(135deg,#f43f5e,#a855f7);box-shadow:0 8px 24px rgba(244,63,94,.35)">
                                    {{ strtoupper(substr($focusUser->name, 0, 1)) }}
                                </div>
                            @endif
                            {{-- "Analysing" pulse ring --}}
                            <span class="absolute -bottom-1 -right-1 flex h-5 w-5 items-center justify-center rounded-full text-[10px]"
                                  style="background:linear-gradient(135deg,#f43f5e,#a855f7);box-shadow:0 2px 8px rgba(244,63,94,.5)">
                                ✦
                            </span>
                        </div>

                        {{-- Details --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2 mb-1">
                                <h2 class="text-lg font-black text-white leading-none">{{ $focusUser->name }}</h2>
                                @if($focusUser->is_premium)
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-black"
                                          style="background:linear-gradient(90deg,#f59e0b,#f97316);color:#fff">
                                        ★ PREMIUM
                                    </span>
                                @endif
                                @if($focusUser->is_verified)
                                    <span class="rounded-full px-2 py-0.5 text-[10px] font-bold"
                                          style="background:rgba(14,165,233,.2);border:1px solid rgba(14,165,233,.4);color:#7dd3fc">
                                        ✓ VERIFIED
                                    </span>
                                @endif
                            </div>
                            <div class="flex flex-wrap gap-x-3 gap-y-1 text-xs" style="color:rgba(255,255,255,.5)">
                                <span>{{ ucfirst($focusUser->gender ?? 'Unknown') }}</span>
                                @if($focusUser->age)<span>· {{ $focusUser->age }} yrs</span>@endif
                                <span>· Seeking <strong class="text-white/70">{{ ucfirst($focusUser->seeking ?? 'everyone') }}</strong></span>
                                @if($focusUser->profile?->city)
                                    <span>· 📍 {{ $focusUser->profile->city }}{{ $focusUser->profile->country ? ', '.$focusUser->profile->country : '' }}</span>
                                @endif
                            </div>
                            @if($focusUser->profile?->headline)
                                <p class="mt-1.5 text-xs italic" style="color:rgba(255,255,255,.35)">"{{ Str::limit($focusUser->profile->headline, 80) }}"</p>
                            @endif
                        </div>

                        {{-- Joined meta --}}
                        <div class="shrink-0 text-right">
                            <p class="text-[10px] font-semibold uppercase tracking-widest" style="color:rgba(255,255,255,.25)">Joined</p>
                            <p class="text-sm font-bold text-white/60">{{ $focusUser->created_at->format('d M Y') }}</p>
                            <p class="text-[10px]" style="color:rgba(255,255,255,.25)">{{ $focusUser->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>

                {{-- ── Suggestions grid ──────────────────────────────────── --}}
                @if($suggestions->isEmpty())
                    <div class="flex flex-col items-center justify-center rounded-2xl py-14 text-center"
                         style="background:rgba(255,255,255,.025);border:1.5px dashed rgba(255,255,255,.1)">
                        <div class="mb-3 text-4xl">🤷</div>
                        <p class="text-sm text-white/50">No compatible candidates found.</p>
                        <p class="mt-1 text-xs text-white/25">All suitable users may already be matched.</p>
                    </div>
                @else
                    {{-- Count header --}}
                    <div class="mb-3 flex items-center gap-3">
                        <p class="text-sm font-semibold text-white/60">
                            <span class="font-black text-white">{{ $suggestions->count() }}</span> compatibility matches found
                        </p>
                        <div class="h-px flex-1" style="background:linear-gradient(90deg,rgba(244,63,94,.3),transparent)"></div>
                        <div class="flex items-center gap-2 text-[10px]" style="color:rgba(255,255,255,.3)">
                            <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-emerald-400"></span>≥70%</span>
                            <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-amber-400"></span>≥40%</span>
                            <span class="flex items-center gap-1"><span class="h-2 w-2 rounded-full bg-gray-500"></span>&lt;40%</span>
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach($suggestions as $rank => $item)
                            @php
                                $candidate  = $item['user'];
                                $score      = $item['score'];
                                $candPhoto  = $candidate->primaryPhoto;
                                $interests  = $candidate->profile?->interests ?? collect();

                                // Tiers
                                if ($score >= 70) {
                                    $tier      = 'emerald';
                                    $cardBg    = 'background:linear-gradient(160deg,rgba(16,185,129,.06),rgba(5,150,105,.03));border:1px solid rgba(16,185,129,.25)';
                                    $barColor  = '#10b981';
                                    $scoreText = 'color:#34d399';
                                } elseif ($score >= 40) {
                                    $tier      = 'amber';
                                    $cardBg    = 'background:linear-gradient(160deg,rgba(245,158,11,.06),rgba(217,119,6,.03));border:1px solid rgba(245,158,11,.2)';
                                    $barColor  = '#f59e0b';
                                    $scoreText = 'color:#fbbf24';
                                } else {
                                    $tier      = 'gray';
                                    $cardBg    = 'background:rgba(255,255,255,.025);border:1px solid rgba(255,255,255,.07)';
                                    $barColor  = '#6b7280';
                                    $scoreText = 'color:#9ca3af';
                                }

                                $focusInterestNames = $focusUser->profile?->interests->pluck('name')->toArray() ?? [];
                                $sharedInterests    = array_intersect($focusInterestNames, $interests->pluck('name')->toArray());
                            @endphp

                            <div class="sm-candidate-card flex flex-col rounded-2xl overflow-hidden"
                                 style="{{ $cardBg }};box-shadow:0 4px 20px rgba(0,0,0,.3)">

                                {{-- Card photo banner (if has photo) --}}
                                @if($candPhoto)
                                    <div class="relative h-28 w-full overflow-hidden">
                                        <img src="{{ $candPhoto->thumbnail_url }}"
                                             alt="{{ $candidate->name }}"
                                             class="h-full w-full object-cover"
                                             style="filter:brightness(.7)" />
                                        <div class="absolute inset-0"
                                             style="background:linear-gradient(to top,rgba(0,0,0,.85) 0%,transparent 60%)"></div>

                                        {{-- Rank badge --}}
                                        <span class="absolute left-2.5 top-2.5 flex h-6 w-6 items-center justify-center rounded-full text-[10px] font-black"
                                              style="background:rgba(0,0,0,.6);backdrop-filter:blur(6px);color:#fff;border:1px solid rgba(255,255,255,.2)">
                                            #{{ $rank + 1 }}
                                        </span>

                                        {{-- Score ring --}}
                                        <div class="absolute right-2.5 top-2" style="line-height:0">
                                            <svg width="44" height="44" viewBox="0 0 44 44">
                                                <circle cx="22" cy="22" r="18" fill="none" stroke="rgba(255,255,255,.1)" stroke-width="3"/>
                                                <circle cx="22" cy="22" r="18" fill="none" stroke="{{ $barColor }}" stroke-width="3"
                                                        stroke-dasharray="113"
                                                        stroke-dashoffset="{{ 113 - ($score / 100 * 113) }}"
                                                        stroke-linecap="round"
                                                        transform="rotate(-90 22 22)"/>
                                                <text x="22" y="26" text-anchor="middle"
                                                      font-size="9" font-weight="900" fill="{{ $barColor }}">{{ $score }}%</text>
                                            </svg>
                                        </div>

                                        {{-- Name over photo --}}
                                        <div class="absolute bottom-2 left-3 right-3">
                                            <p class="truncate text-sm font-bold text-white leading-tight">{{ $candidate->name }}</p>
                                        </div>
                                    </div>
                                @else
                                    {{-- No photo — gradient banner --}}
                                    <div class="relative flex h-20 items-center justify-center overflow-hidden"
                                         style="background:linear-gradient(135deg,#2d1050,#4a0e6e)">
                                        <span class="text-3xl font-black text-white/20 select-none">{{ strtoupper(substr($candidate->name,0,1)) }}</span>
                                        <span class="absolute left-2.5 top-2.5 flex h-6 w-6 items-center justify-center rounded-full text-[10px] font-black"
                                              style="background:rgba(0,0,0,.5);color:#fff;border:1px solid rgba(255,255,255,.2)">#{{ $rank + 1 }}</span>
                                        <div class="absolute right-2.5 top-2" style="line-height:0">
                                            <svg width="44" height="44" viewBox="0 0 44 44">
                                                <circle cx="22" cy="22" r="18" fill="none" stroke="rgba(255,255,255,.1)" stroke-width="3"/>
                                                <circle cx="22" cy="22" r="18" fill="none" stroke="{{ $barColor }}" stroke-width="3"
                                                        stroke-dasharray="113" stroke-dashoffset="{{ 113 - ($score / 100 * 113) }}"
                                                        stroke-linecap="round" transform="rotate(-90 22 22)"/>
                                                <text x="22" y="26" text-anchor="middle" font-size="9" font-weight="900" fill="{{ $barColor }}">{{ $score }}%</text>
                                            </svg>
                                        </div>
                                    </div>
                                @endif

                                {{-- Card body --}}
                                <div class="flex flex-1 flex-col gap-2 p-3">
                                    {{-- Basic info --}}
                                    <div>
                                        @if(!$candPhoto)
                                            <p class="mb-0.5 text-sm font-bold text-white">{{ $candidate->name }}</p>
                                        @endif
                                        <div class="flex flex-wrap items-center gap-1.5 text-[10px]" style="color:rgba(255,255,255,.45)">
                                            {{ ucfirst($candidate->gender ?? '?') }}
                                            @if($candidate->age)<span>· {{ $candidate->age }} yrs</span>@endif
                                            @if($candidate->is_premium)
                                                <span class="rounded px-1 text-[9px] font-black" style="background:#f59e0b;color:#000">★</span>
                                            @endif
                                        </div>
                                        @if($candidate->profile?->city)
                                            <p class="mt-0.5 truncate text-[10px]" style="color:rgba(255,255,255,.3)">
                                                📍 {{ $candidate->profile->city }}{{ $candidate->profile->country ? ', '.$candidate->profile->country : '' }}
                                            </p>
                                        @endif
                                    </div>

                                    {{-- Headline --}}
                                    @if($candidate->profile?->headline)
                                        <p class="text-[10px] italic" style="color:rgba(255,255,255,.3)">
                                            "{{ Str::limit($candidate->profile->headline, 55) }}"
                                        </p>
                                    @endif

                                    {{-- Tags --}}
                                    @php $tags = array_filter([$candidate->profile?->relationship_goal ?? null, $candidate->profile?->religion ?? null, $candidate->profile?->education ?? null]); @endphp
                                    @if(count($tags))
                                        <div class="flex flex-wrap gap-1">
                                            @foreach(array_slice($tags, 0, 3) as $tag)
                                                <span class="rounded-full px-1.5 py-0.5 text-[9px] font-medium capitalize"
                                                      style="background:rgba(168,85,247,.15);border:1px solid rgba(168,85,247,.25);color:#c4b5fd">
                                                    {{ $tag }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Shared interests --}}
                                    @if(count($sharedInterests) > 0)
                                        <div>
                                            <p class="mb-1 text-[9px] uppercase tracking-wider" style="color:rgba(255,255,255,.2)">Shared</p>
                                            <div class="flex flex-wrap gap-1">
                                                @foreach(array_slice($sharedInterests, 0, 4) as $int)
                                                    <span class="rounded-full px-1.5 py-0.5 text-[9px]"
                                                          style="background:rgba(20,184,166,.15);border:1px solid rgba(20,184,166,.25);color:#5eead4">
                                                        {{ $int }}
                                                    </span>
                                                @endforeach
                                                @if(count($sharedInterests) > 4)
                                                    <span class="rounded-full px-1.5 py-0.5 text-[9px]"
                                                          style="background:rgba(255,255,255,.06);color:rgba(255,255,255,.3)">
                                                        +{{ count($sharedInterests) - 4 }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                {{-- Compat bar + Action --}}
                                <div class="px-3 pb-3 pt-1">
                                    <div class="mb-2">
                                        <div class="mb-1 flex items-center justify-between text-[10px]" style="color:rgba(255,255,255,.3)">
                                            <span>Compatibility</span>
                                            <span style="{{ $scoreText }}" class="font-bold">{{ $score }}%</span>
                                        </div>
                                        <div class="h-1 w-full overflow-hidden rounded-full" style="background:rgba(255,255,255,.07)">
                                            <div class="h-full rounded-full transition-all duration-700"
                                                 style="width:{{ $score }}%;background:{{ $barColor }}"></div>
                                        </div>
                                    </div>

                                    <button
                                        wire:click="forceMatch({{ $focusUserId }}, {{ $candidate->id }})"
                                        wire:confirm="Force match {{ addslashes($focusUser->name) }} ↔ {{ addslashes($candidate->name) }}?\n\nThis creates a mutual match, ensures cross-likes exist, and opens a shared conversation. Both users will be notified."
                                        wire:loading.attr="disabled"
                                        class="group w-full overflow-hidden rounded-xl px-3 py-2 text-xs font-bold text-white transition-all duration-150"
                                        style="background:linear-gradient(135deg,#f43f5e,#a855f7);box-shadow:0 4px 14px rgba(244,63,94,.3)"
                                        onmouseover="this.style.boxShadow='0 6px 20px rgba(244,63,94,.55)'"
                                        onmouseout="this.style.boxShadow='0 4px 14px rgba(244,63,94,.3)'"
                                    >
                                        <span wire:loading.remove wire:target="forceMatch({{ $focusUserId }}, {{ $candidate->id }})">
                                            💞 Force Match
                                        </span>
                                        <span wire:loading wire:target="forceMatch({{ $focusUserId }}, {{ $candidate->id }})" class="flex items-center justify-center gap-1.5">
                                            <svg class="h-3 w-3 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                                            </svg>
                                            Matching…
                                        </span>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            @endif
        @endif
    </div>
</div>
</x-filament-panels::page>

