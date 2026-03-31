<x-filament-panels::page>
    @php
        $grouped = \App\Filament\Pages\ArtisanRunner::groupedCommands();
        $allCommands = \App\Filament\Pages\ArtisanRunner::allowedCommands();

        // Apply search filter if needed
        $filteredCommands = $allCommands;
        if (!empty($this->searchQuery)) {
            $query = strtolower($this->searchQuery);
            $filteredCommands = array_filter($allCommands, function ($def, $key) use ($query) {
                return str_contains(strtolower($key), $query) || 
                       str_contains(strtolower($def['label']), $query) || 
                       str_contains(strtolower($def['desc']), $query) ||
                       str_contains(strtolower($def['group']), $query);
            }, ARRAY_FILTER_USE_BOTH);
        }
    @endphp

    <style>
        /* ── Light mode variables (hardcoded – theme() only works in compiled CSS) ── */
        .artisan-runner {
            --primary:       #2563eb; /* blue-600  */
            --primary-hover: #1d4ed8; /* blue-700  */
            --danger:        #dc2626; /* red-600   */
            --danger-hover:  #b91c1c; /* red-700   */
            --success:       #16a34a; /* green-600 */
            --warning:       #d97706; /* amber-600 */
            --surface:       #ffffff;
            --surface-hover: #f9fafb; /* gray-50   */
            --border:        #e5e7eb; /* gray-200  */
            --text:          #111827; /* gray-900  */
            --text-muted:    #4b5563; /* gray-600  */
            --text-subtle:   #6b7280; /* gray-500  */
        }

        /* ── Dark mode overrides ───────────────────────────────────────────── */
        .dark .artisan-runner {
            --surface:       #111827; /* gray-900  */
            --surface-hover: #1f2937; /* gray-800  */
            --border:        #374151; /* gray-700  */
            --text:          #f3f4f6; /* gray-100  */
            --text-muted:    #d1d5db; /* gray-300  */
            --text-subtle:   #9ca3af; /* gray-400  */
        }

        /* ── Cards ─────────────────────────────────────────────────────────── */
        .artisan-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            transition: background 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .artisan-card:hover {
            background: var(--surface-hover);
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(0,0,0,.08);
        }

        /* ── Command grid + cards ───────────────────────────────────────────── */
        .command-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 0.875rem;
        }

        .command-card {
            position: relative;
            padding: 1rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 0.625rem;
            cursor: pointer;
            transition: border-color 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
        }

        .command-card:hover {
            border-color: var(--primary);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,.08);
        }

        .command-card.selected {
            border-color: var(--primary);
            background: rgba(37,99,235,.05);
        }

        .command-card.dangerous {
            border-left: 3px solid var(--danger);
        }

        /* ── Icons: force consistent sizing ────────────────────────────────── */
        .artisan-runner svg {
            display: inline-block;
            flex-shrink: 0;
        }

        /* stat-card icon wrappers */
        .stat-icon { width: 1.5rem; height: 1.5rem; }   /* w-6 h-6  */
        .cmd-icon  { width: 1.25rem; height: 1.25rem; }  /* w-5 h-5  */
        .sm-icon   { width: 1rem; height: 1rem; }         /* w-4 h-4  */

        /* ── Spinner ────────────────────────────────────────────────────────── */
        .spinner { animation: ar-spin 1s linear infinite; }

        @keyframes ar-spin {
            from { transform: rotate(0deg); }
            to   { transform: rotate(360deg); }
        }

        /* ── Badges ─────────────────────────────────────────────────────────── */
        .command-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.2rem 0.5rem;
            border-radius: 9999px;
            font-size: 0.7rem;
            font-weight: 600;
            letter-spacing: .03em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .badge-safe {
            background: rgba(34,197,94,.12);
            color: #15803d; /* green-700 */
        }

        .badge-dangerous {
            background: rgba(239,68,68,.12);
            color: #b91c1c; /* red-700   */
        }

        .dark .badge-safe      { color: #4ade80; /* green-400 */ }
        .dark .badge-dangerous { color: #f87171; /* red-400   */ }

        /* ── Misc ───────────────────────────────────────────────────────────── */
        .artisan-runner code {
            font-family: 'Cascadia Code', 'Fira Code', ui-monospace, monospace;
        }
    </style>

    <div class="artisan-runner space-y-6">
        
        {{-- Header Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="artisan-card p-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <x-heroicon-o-command-line class="stat-icon text-blue-600 dark:text-blue-400"/>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-[var(--text)]">{{ count(\App\Filament\Pages\ArtisanRunner::allowedCommands()) }}</p>
                        <p class="text-sm text-[var(--text-muted)]">Available Commands</p>
                    </div>
                </div>
            </div>

            <div class="artisan-card p-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-green-100 dark:bg-green-900/20 rounded-lg">
                        <x-heroicon-o-check-circle class="stat-icon text-green-600 dark:text-green-400"/>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-[var(--text)]">{{ $exitCode === 0 && $ran ? '✓' : '—' }}</p>
                        <p class="text-sm text-[var(--text-muted)]">Last Status</p>
                    </div>
                </div>
            </div>

            <div class="artisan-card p-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                        <x-heroicon-o-clock class="stat-icon text-purple-600 dark:text-purple-400"/>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-[var(--text)]">{{ $lastRunAt ?: 'Never' }}</p>
                        <p class="text-sm text-[var(--text-muted)]">Last Execution</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left Column: Commands --}}
            <div class="lg:col-span-2 space-y-4">
                
                {{-- Search Bar --}}
                <div class="artisan-card p-4">
                    <div class="flex items-center gap-3 mb-4">
                        <x-heroicon-o-magnifying-glass class="cmd-icon text-[var(--text-subtle)]"/>
                        <h3 class="text-lg font-semibold text-[var(--text)]">Search Commands</h3>
                    </div>
                    <input
                        type="text"
                        wire:model.live.debounce.300ms="searchQuery"
                        placeholder="Search by command name, description, or group..."
                        class="w-full px-4 py-2 border border-[var(--border)] rounded-lg bg-[var(--surface)] text-[var(--text)] placeholder:text-[var(--text-subtle)] focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                </div>

                {{-- Commands by Group --}}
                @if(empty($filteredCommands))
                    <div class="artisan-card p-8 text-center">
                        <x-heroicon-o-magnifying-glass class="cmd-icon text-[var(--text-subtle)] mx-auto mb-3" style="width:2.5rem;height:2.5rem"/>
                        <h3 class="text-lg font-medium text-[var(--text)]">No commands found</h3>
                        <p class="text-[var(--text-muted)]">Try adjusting your search query.</p>
                    </div>
                @else
                    @foreach($grouped as $groupName => $commands)
                        @php
                            $groupCommands = array_intersect_key($commands, $filteredCommands);
                        @endphp
                        
                        @if(!empty($groupCommands))
                            <div class="artisan-card">
                                <div class="p-4 border-b border-[var(--border)]">
                                    <h3 class="text-lg font-semibold text-[var(--text)] flex items-center gap-2">
                                        @switch($groupName)
                                            @case('Cache Management')
                                                <x-heroicon-o-arrow-path class="cmd-icon text-blue-500"/>
                                                @break
                                            @case('Database Operations')
                                                <x-heroicon-o-circle-stack class="cmd-icon text-green-500"/>
                                                @break
                                            @case('Queue Management')
                                                <x-heroicon-o-queue-list class="cmd-icon text-purple-500"/>
                                                @break
                                            @case('Application Maintenance')
                                                <x-heroicon-o-shield-exclamation class="cmd-icon text-red-500"/>
                                                @break
                                            @default
                                                <x-heroicon-o-squares-2x2 class="cmd-icon text-gray-500"/>
                                        @endswitch
                                        {{ $groupName }}
                                        <span class="text-xs bg-gray-100 dark:bg-gray-800 px-2 py-1 rounded-full text-[var(--text-subtle)]">
                                            {{ count($groupCommands) }}
                                        </span>
                                    </h3>
                                </div>
                                <div class="p-4">
                                    <div class="command-grid">
                                        @foreach($groupCommands as $key => $def)
                                            <div 
                                                wire:click="selectCommand('{{ $key }}')"
                                                class="command-card {{ $selectedCommand === $key ? 'selected' : '' }} {{ $def['dangerous'] ? 'dangerous' : '' }}"
                                            >
                                                <div class="flex items-start gap-3">
                                                    @if($def['icon'] ?? null)
                                                        <x-dynamic-component 
                                                            :component="$def['icon']" 
                                                            class="cmd-icon mt-0.5 text-blue-600 dark:text-blue-400 flex-shrink-0"
                                                        />
                                                    @endif
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center gap-2 mb-2">
                                                            <h4 class="font-medium text-[var(--text)] truncate">{{ $def['label'] }}</h4>
                                                            <span class="command-badge {{ $def['dangerous'] ? 'badge-dangerous' : 'badge-safe' }}">
                                                                {{ $def['dangerous'] ? 'Dangerous' : 'Safe' }}
                                                            </span>
                                                        </div>
                                                        <p class="text-sm text-[var(--text-muted)] leading-relaxed">{{ $def['desc'] }}</p>
                                                        <code class="inline-block mt-2 px-2 py-1 bg-gray-100 dark:bg-gray-800 rounded text-xs text-[var(--text-subtle)] font-mono">
                                                            php artisan {{ $key }}
                                                        </code>
                                                    </div>
                                                </div>
                                                
                                                @if($selectedCommand === $key)
                                                    <div class="absolute top-3 right-3">
                                                        <x-heroicon-s-check-circle class="cmd-icon text-blue-600 dark:text-blue-400"/>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endif
            </div>

            {{-- Right Column: Selected Command & Recent Commands --}}
            <div class="space-y-4">
                
                {{-- Selected Command Action --}}
                @if($selectedCommand)
                    @php
                        $def = \App\Filament\Pages\ArtisanRunner::allowedCommands()[$selectedCommand];
                    @endphp
                    
                    <div class="artisan-card">
                        <div class="p-4 border-b border-[var(--border)]">
                            <h3 class="text-lg font-semibold text-[var(--text)] flex items-center gap-2">
                                <x-heroicon-o-play class="cmd-icon text-blue-600 dark:text-blue-400"/>
                                Execute Command
                            </h3>
                        </div>
                        <div class="p-4 space-y-4">
                            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                                <h4 class="font-medium text-[var(--text)] mb-2">{{ $def['label'] }}</h4>
                                <p class="text-sm text-[var(--text-muted)] mb-3">{{ $def['desc'] }}</p>
                                <code class="block bg-gray-900 text-green-400 p-3 rounded text-sm font-mono">
                                    php artisan {{ $selectedCommand }}
                                    @foreach($def['args'] as $flag => $val)
                                        {{ is_bool($val) ? $flag : "$flag=$val" }}
                                    @endforeach
                                </code>
                            </div>
                            
                            @if($def['dangerous'])
                                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-700 rounded-lg p-4">
                                    <div class="flex items-center gap-2 text-red-700 dark:text-red-400">
                                        <x-heroicon-o-exclamation-triangle class="cmd-icon"/>
                                        <span class="font-medium">Dangerous Command</span>
                                    </div>
                                    <p class="text-sm text-red-600 dark:text-red-400 mt-1">
                                        This command can affect your application. Please confirm before proceeding.
                                    </p>
                                </div>
                            @endif

                            @if($def['dangerous'])
                                <button
                                    wire:click="runCommand"
                                    wire:confirm="⚠️ This is a DANGEROUS command that could affect your application. Are you absolutely sure you want to run it?"
                                    wire:loading.attr="disabled"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors disabled:opacity-50"
                                >
                                    @if($isRunning)
                                        <x-heroicon-o-arrow-path class="sm-icon spinner"/>
                                        Executing...
                                    @else
                                        <x-heroicon-o-exclamation-triangle class="sm-icon"/>
                                        Run Dangerous Command
                                    @endif
                                </button>
                            @else
                                <button
                                    wire:click="runCommand"
                                    wire:loading.attr="disabled"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-[var(--primary)] hover:bg-[var(--primary-hover)] text-white font-medium rounded-lg transition-colors disabled:opacity-50"
                                >
                                    @if($isRunning)
                                        <x-heroicon-o-arrow-path class="sm-icon spinner"/>
                                        Executing...
                                    @else
                                        <x-heroicon-o-play class="sm-icon"/>
                                        Execute Command
                                    @endif
                                </button>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Recent Commands --}}
                @if(!empty($recentCommands))
                    <div class="artisan-card">
                        <div class="p-4 border-b border-[var(--border)]">
                            <h3 class="text-lg font-semibold text-[var(--text)] flex items-center gap-2">
                                <x-heroicon-o-clock class="cmd-icon text-gray-500"/>
                                Recent Commands
                            </h3>
                        </div>
                        <div class="p-4 space-y-2">
                            @foreach($recentCommands as $recent)
                                <div 
                                    wire:click="selectCommand('{{ $recent['key'] }}')"
                                    class="flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 cursor-pointer transition-colors"
                                >
                                    <div class="flex-1 min-w-0">
                                        <p class="font-medium text-[var(--text)] truncate">{{ $recent['label'] }}</p>
                                        <p class="text-xs text-[var(--text-subtle)]">{{ $recent['ran_at'] }}</p>
                                    </div>
                                    <x-heroicon-o-arrow-right class="sm-icon text-gray-400"/>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Output Section --}}
        @if($ran)
            <div class="artisan-card">
                <div class="flex items-center justify-between p-4 border-b border-[var(--border)]">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-command-line class="cmd-icon text-gray-500"/>
                        <h3 class="text-lg font-semibold text-[var(--text)]">Command Output</h3>
                        <span class="command-badge {{ $exitCode === 0 ? 'badge-safe' : 'badge-dangerous' }}">
                            {{ $exitCode === 0 ? 'Success' : 'Failed' }} (exit {{ $exitCode }})
                        </span>
                    </div>
                    <button
                        wire:click="clearOutput"
                        class="text-sm px-3 py-1 text-[var(--text-subtle)] hover:text-[var(--text)] transition-colors"
                    >
                        Clear Output
                    </button>
                </div>
                <div class="p-4">
                    <pre class="bg-gray-900 text-green-400 p-4 rounded-lg overflow-x-auto text-sm font-mono whitespace-pre-wrap max-h-96 overflow-y-auto border">{{ $output }}</pre>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
