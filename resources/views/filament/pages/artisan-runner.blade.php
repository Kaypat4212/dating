<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Command selector card --}}
        <div class="rounded-xl border border-gray-700 bg-gray-900 p-6 shadow">
            <h3 class="mb-4 text-sm font-semibold uppercase tracking-widest text-gray-400">
                Select a command to run
            </h3>

            <div class="flex flex-col gap-4 sm:flex-row sm:items-end">
                <div class="flex-1">
                    <label for="artisan-cmd" class="mb-1 block text-xs font-medium text-gray-400">
                        Command
                    </label>
                    <select
                        id="artisan-cmd"
                        wire:model.live="selectedCommand"
                        class="w-full rounded-lg border border-gray-600 bg-gray-800 px-3 py-2 text-sm text-white focus:border-rose-500 focus:outline-none focus:ring-1 focus:ring-rose-500"
                    >
                        <option value="">— choose a command —</option>
                        @foreach(\App\Filament\Pages\ArtisanRunner::groupedOptions() as $group => $options)
                            <optgroup label="{{ $group }}">
                                @foreach($options as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                @if($selectedCommand)
                    @php
                        $isDangerous = \App\Filament\Pages\ArtisanRunner::isDangerous($selectedCommand);
                    @endphp

                    @if($isDangerous)
                        <button
                            wire:click="runCommand"
                            wire:confirm="⚠️ This is a DANGEROUS command. Are you sure you want to run it?"
                            class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500"
                        >
                            <x-heroicon-o-exclamation-triangle class="h-4 w-4" />
                            Run (dangerous)
                        </button>
                    @else
                        <button
                            wire:click="runCommand"
                            class="inline-flex items-center gap-2 rounded-lg bg-rose-600 px-5 py-2 text-sm font-semibold text-white shadow hover:bg-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500"
                        >
                            <x-heroicon-o-play class="h-4 w-4" />
                            Run Command
                        </button>
                    @endif
                @endif
            </div>

            @if($selectedCommand)
                <p class="mt-3 font-mono text-xs text-gray-500">
                    php artisan {{ $selectedCommand }}
                    @php
                        $allCmds = \App\Filament\Pages\ArtisanRunner::allowedCommands();
                        $cmdKey  = (string) $selectedCommand;
                        $cmdDef  = $allCmds[$cmdKey] ?? [];
                        $args    = $cmdDef['args'] ?? [];
                    @endphp
                    @foreach($args as $flag => $val)
                        {{ is_bool($val) ? $flag : "$flag=$val" }}
                    @endforeach
                </p>
            @endif
        </div>

        {{-- Output card --}}
        @if($ran)
            <div class="rounded-xl border border-gray-700 bg-gray-900 shadow">
                <div class="flex items-center justify-between border-b border-gray-700 px-5 py-3">
                    <span class="flex items-center gap-2 text-sm font-semibold text-gray-300">
                        <x-heroicon-o-command-line class="h-4 w-4 text-rose-400" />
                        Output
                        <span class="rounded-full bg-green-700/30 px-2 py-0.5 text-xs text-green-400">
                            exit 0
                        </span>
                    </span>
                    <button
                        wire:click="clearOutput"
                        class="text-xs text-gray-500 hover:text-gray-300"
                    >
                        Clear
                    </button>
                </div>
                <pre class="overflow-x-auto p-5 font-mono text-xs leading-relaxed text-green-300 whitespace-pre-wrap">{{ $output }}</pre>
            </div>
        @endif

    </div>
</x-filament-panels::page>
