<x-filament-panels::page>
<div class="flex flex-col gap-6 lg:flex-row lg:items-start">

    {{-- ===== LEFT PANEL: New Users List ===== --}}
    <div class="w-full shrink-0 lg:w-72">
        <div class="rounded-xl border border-gray-700 bg-gray-900 shadow">
            <div class="border-b border-gray-700 px-4 py-3">
                <h3 class="text-xs font-semibold uppercase tracking-widest text-gray-400">
                    New Members (last 7 days)
                </h3>
            </div>

            @php $newUsers = $this->getNewUsers(); @endphp

            @if($newUsers->isEmpty())
                <p class="px-4 py-6 text-center text-sm text-gray-500">
                    No new users in the past 7 days.
                </p>
            @else
                <ul class="divide-y divide-gray-800">
                    @foreach($newUsers as $nu)
                        @php
                            $photo    = $nu->primaryPhoto;
                            $photoUrl = $photo ? $photo->thumbnail_url : null;
                            $active   = $focusUserId === $nu->id;
                        @endphp
                        <li>
                            <button
                                wire:click="selectUser({{ $nu->id }})"
                                class="flex w-full items-center gap-3 px-4 py-3 text-left transition hover:bg-gray-800
                                       {{ $active ? 'bg-rose-900/30 ring-l-2 ring-rose-500' : '' }}"
                            >
                                <div class="relative h-10 w-10 shrink-0">
                                    @if($photoUrl)
                                        <img src="{{ $photoUrl }}"
                                             alt="{{ $nu->name }}"
                                             class="h-10 w-10 rounded-full object-cover ring-2 {{ $active ? 'ring-rose-500' : 'ring-gray-700' }}" />
                                    @else
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-gray-700 text-lg font-bold text-gray-300 ring-2 {{ $active ? 'ring-rose-500' : 'ring-gray-700' }}">
                                            {{ strtoupper(substr($nu->name, 0, 1)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-medium {{ $active ? 'text-rose-400' : 'text-white' }}">
                                        {{ $nu->name }}
                                        @if($nu->is_premium)
                                            <span class="ml-1 inline-block rounded bg-amber-500 px-1 text-[10px] font-bold text-black">PRO</span>
                                        @endif
                                    </p>
                                    <p class="truncate text-xs text-gray-500">
                                        {{ $nu->gender ?? '—' }} · {{ $nu->age ?? '?' }} yrs
                                    </p>
                                    <p class="text-[10px] text-gray-600">{{ $nu->created_at->diffForHumans() }}</p>
                                </div>
                            </button>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    {{-- ===== RIGHT PANEL: Match Suggestions ===== --}}
    <div class="flex-1">
        @if(! $focusUserId)
            <div class="flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-700 bg-gray-900 py-24 text-center">
                <x-heroicon-o-sparkles class="mb-3 h-10 w-10 text-gray-600" />
                <p class="text-sm text-gray-400">Select a new member on the left to view their best match suggestions.</p>
            </div>
        @else
            @php
                $focusUser = \App\Models\User::with(['profile.interests', 'primaryPhoto'])->find($focusUserId);
                $focusPhoto = $focusUser?->primaryPhoto;
            @endphp

            @if(! $focusUser)
                <div class="rounded-xl border border-red-700 bg-red-900/30 px-4 py-6 text-center text-sm text-red-400">
                    User not found.
                </div>
            @else
                {{-- Focus user header --}}
                <div class="mb-4 flex items-center gap-4 rounded-xl border border-rose-700/50 bg-rose-900/20 px-5 py-4 shadow">
                    <div class="shrink-0">
                        @if($focusPhoto)
                            <img src="{{ $focusPhoto->thumbnail_url }}" alt="{{ $focusUser->name }}"
                                 class="h-14 w-14 rounded-full object-cover ring-2 ring-rose-500" />
                        @else
                            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-rose-800 text-2xl font-bold text-white ring-2 ring-rose-500">
                                {{ strtoupper(substr($focusUser->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-base font-bold text-white">{{ $focusUser->name }}</h2>
                            @if($focusUser->is_premium)
                                <span class="rounded bg-amber-500 px-1.5 py-0.5 text-[10px] font-bold text-black">PREMIUM</span>
                            @endif
                            @if($focusUser->is_verified)
                                <span class="rounded bg-sky-600 px-1.5 py-0.5 text-[10px] font-bold text-white">VERIFIED</span>
                            @endif
                        </div>
                        <p class="mt-0.5 text-xs text-gray-400">
                            {{ ucfirst($focusUser->gender ?? '?') }}
                            · {{ $focusUser->age ?? '?' }} yrs
                            · Seeking: {{ ucfirst($focusUser->seeking ?? 'everyone') }}
                            @if($focusUser->profile?->city)
                                · {{ $focusUser->profile->city }}{{ $focusUser->profile->country ? ', '.$focusUser->profile->country : '' }}
                            @endif
                        </p>
                        @if($focusUser->profile?->headline)
                            <p class="mt-1 truncate text-xs italic text-gray-500">"{{ $focusUser->profile->headline }}"</p>
                        @endif
                    </div>
                    <div class="shrink-0 text-right text-xs text-gray-500">
                        Joined {{ $focusUser->created_at->format('d M Y') }}
                    </div>
                </div>

                {{-- Suggestions --}}
                @if($suggestions->isEmpty())
                    <div class="rounded-xl border border-dashed border-gray-700 bg-gray-900 py-16 text-center">
                        <x-heroicon-o-face-frown class="mx-auto mb-3 h-8 w-8 text-gray-600" />
                        <p class="text-sm text-gray-400">No compatible candidates found (all may be already matched).</p>
                    </div>
                @else
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                        @foreach($suggestions as $item)
                            @php
                                /** @var \App\Models\User $candidate */
                                $candidate     = $item['user'];
                                $score         = $item['score'];
                                $candPhoto     = $candidate->primaryPhoto;
                                $interests     = $candidate->profile?->interests ?? collect();
                                $scoreColor    = $score >= 70 ? 'text-emerald-400' : ($score >= 40 ? 'text-amber-400' : 'text-gray-400');
                                $scoreBg       = $score >= 70 ? 'bg-emerald-900/40 border-emerald-700/50' : ($score >= 40 ? 'bg-amber-900/30 border-amber-700/40' : 'bg-gray-800 border-gray-700');
                            @endphp
                            <div class="flex flex-col rounded-xl border {{ $scoreBg }} shadow transition hover:shadow-lg">

                                {{-- Card header: photo + basic info --}}
                                <div class="flex items-center gap-3 p-4 pb-3">
                                    <div class="relative shrink-0">
                                        @if($candPhoto)
                                            <img src="{{ $candPhoto->thumbnail_url }}"
                                                 alt="{{ $candidate->name }}"
                                                 class="h-12 w-12 rounded-full object-cover ring-2 ring-gray-600" />
                                        @else
                                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-gray-700 text-xl font-bold text-gray-300 ring-2 ring-gray-600">
                                                {{ strtoupper(substr($candidate->name, 0, 1)) }}
                                            </div>
                                        @endif
                                        {{-- Score badge --}}
                                        <span class="absolute -bottom-1 -right-1 rounded-full bg-gray-900 px-1.5 py-0.5 text-[10px] font-bold {{ $scoreColor }} ring-1 ring-gray-700">
                                            {{ $score }}%
                                        </span>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate text-sm font-semibold text-white">
                                            {{ $candidate->name }}
                                            @if($candidate->is_premium)
                                                <span class="ml-1 rounded bg-amber-500 px-1 text-[10px] font-bold text-black">PRO</span>
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-400">
                                            {{ ucfirst($candidate->gender ?? '?') }}
                                            · {{ $candidate->age ?? '?' }} yrs
                                        </p>
                                        @if($candidate->profile?->city)
                                            <p class="truncate text-[11px] text-gray-500">
                                                📍 {{ $candidate->profile->city }}{{ $candidate->profile->country ? ', '.$candidate->profile->country : '' }}
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                {{-- Compatibility bar --}}
                                <div class="mx-4 mb-3">
                                    <div class="mb-1 flex items-center justify-between text-[10px] text-gray-500">
                                        <span>Compatibility</span>
                                        <span class="{{ $scoreColor }} font-semibold">{{ $score }}%</span>
                                    </div>
                                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-700">
                                        <div class="h-full rounded-full {{ $score >= 70 ? 'bg-emerald-400' : ($score >= 40 ? 'bg-amber-400' : 'bg-gray-500') }}"
                                             style="width: {{ $score }}%"></div>
                                    </div>
                                </div>

                                {{-- Profile snippet --}}
                                @if($candidate->profile?->headline)
                                    <p class="mx-4 mb-2 truncate text-xs italic text-gray-500">
                                        "{{ $candidate->profile->headline }}"
                                    </p>
                                @endif

                                {{-- Shared details --}}
                                <div class="mx-4 mb-3 flex flex-wrap gap-1 text-[10px]">
                                    @if($candidate->profile?->relationship_goal)
                                        <span class="rounded-full bg-rose-900/40 px-2 py-0.5 text-rose-300">{{ $candidate->profile->relationship_goal }}</span>
                                    @endif
                                    @if($candidate->profile?->education)
                                        <span class="rounded-full bg-blue-900/40 px-2 py-0.5 text-blue-300">{{ $candidate->profile->education }}</span>
                                    @endif
                                    @if($candidate->profile?->religion)
                                        <span class="rounded-full bg-purple-900/40 px-2 py-0.5 text-purple-300">{{ $candidate->profile->religion }}</span>
                                    @endif
                                </div>

                                {{-- Shared interests --}}
                                @php
                                    $focusInterestNames = $focusUser->profile?->interests->pluck('name')->toArray() ?? [];
                                    $candInterestNames  = $interests->pluck('name')->toArray();
                                    $sharedInterests    = array_intersect($focusInterestNames, $candInterestNames);
                                @endphp
                                @if(count($sharedInterests) > 0)
                                    <div class="mx-4 mb-3">
                                        <p class="mb-1 text-[10px] text-gray-600">Shared interests:</p>
                                        <div class="flex flex-wrap gap-1">
                                            @foreach(array_slice($sharedInterests, 0, 5) as $int)
                                                <span class="rounded-full bg-teal-900/50 px-2 py-0.5 text-[10px] text-teal-300">{{ $int }}</span>
                                            @endforeach
                                            @if(count($sharedInterests) > 5)
                                                <span class="rounded-full bg-gray-700 px-2 py-0.5 text-[10px] text-gray-400">+{{ count($sharedInterests) - 5 }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <div class="mt-auto border-t border-gray-700/60 p-3">
                                    <button
                                        wire:click="forceMatch({{ $focusUserId }}, {{ $candidate->id }})"
                                        wire:confirm="Force match {{ $focusUser->name }} with {{ $candidate->name }}? This will create a mutual match and open a conversation for both."
                                        wire:loading.attr="disabled"
                                        class="w-full rounded-lg bg-rose-600 px-4 py-2 text-xs font-semibold text-white shadow transition hover:bg-rose-500 active:bg-rose-700 disabled:opacity-50"
                                    >
                                        <span wire:loading.remove wire:target="forceMatch({{ $focusUserId }}, {{ $candidate->id }})">
                                            ❤️ Force Match
                                        </span>
                                        <span wire:loading wire:target="forceMatch({{ $focusUserId }}, {{ $candidate->id }})">
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
