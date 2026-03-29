<x-filament-panels::page>
    @php
        $grouped = \App\Filament\Pages\ArtisanRunner::groupedCommands();
        $filteredCommands = \App\Filament\Pages\ArtisanRunner::allowedCommands();
        
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
        .artisan-runner {
            --primary: theme('colors.blue.600');
            --primary-hover: theme('colors.blue.700');
            --danger: theme('colors.red.600');
            --danger-hover: theme('colors.red.700');
            --success: theme('colors.green.600');
            --warning: theme('colors.amber.600');
            --surface: theme('colors.white');
            --surface-hover: theme('colors.gray.50');
            --border: theme('colors.gray.200');
            --text: theme('colors.gray.900');
            --text-muted: theme('colors.gray.600');
            --text-subtle: theme('colors.gray.500');
        }
        
        .dark .artisan-runner {
            --surface: theme('colors.gray.900');
            --surface-hover: theme('colors.gray.800');
            --border: theme('colors.gray.700');
            --text: theme('colors.gray.100');
            --text-muted: theme('colors.gray.300');
            --text-subtle: theme('colors.gray.400');
        }

        .artisan-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            transition: all 0.2s ease;
        }

        .artisan-card:hover {
            background: var(--surface-hover);
            border-color: var(--primary);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .command-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1rem;
        }

        .command-card {
            position: relative;
            padding: 1.25rem;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 0.75rem;
            cursor: pointer;
            transition: all 0.15s ease;
        }

        .command-card:hover {
            border-color: var(--primary);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .command-card.selected {
            border-color: var(--primary);
            background: rgba(59, 130, 246, 0.05);
        }

        .command-card.dangerous {
            border-left: 4px solid var(--danger);
        }

        .spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .command-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            padding: 0.25rem 0.625rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .badge-safe {
            background: rgba(34, 197, 94, 0.1);
            color: theme('colors.green.700');
        }

        .badge-dangerous {
            background: rgba(239, 68, 68, 0.1);
            color: theme('colors.red.700');
        }

        .dark .badge-safe {
            color: theme('colors.green.400');
        }

        .dark .badge-dangerous {
            color: theme('colors.red.400');
        }
    </style>

    <div class="artisan-runner space-y-6">
        
        {{-- Header Stats --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="artisan-card p-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                        <x-heroicon-o-command-line class="w-6 h-6 text-blue-600 dark:text-blue-400"/>
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
                        <x-heroicon-o-check-circle class="w-6 h-6 text-green-600 dark:text-green-400"/>
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
                        <x-heroicon-o-clock class="w-6 h-6 text-purple-600 dark:text-purple-400"/>
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
                        <x-heroicon-o-magnifying-glass class="w-5 h-5 text-[var(--text-subtle)]"/>
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
                        <x-heroicon-o-magnifying-glass class="w-12 h-12 text-[var(--text-subtle)] mx-auto mb-3"/>
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
                                                <x-heroicon-o-arrow-path class="w-5 h-5 text-blue-500"/>
                                                @break
                                            @case('Database Operations')
                                                <x-heroicon-o-circle-stack class="w-5 h-5 text-green-500"/>
                                                @break
                                            @case('Queue Management')
                                                <x-heroicon-o-queue-list class="w-5 h-5 text-purple-500"/>
                                                @break
                                            @case('Application Maintenance')
                                                <x-heroicon-o-shield-exclamation class="w-5 h-5 text-red-500"/>
                                                @break
                                            @default
                                                <x-heroicon-o-squares-2x2 class="w-5 h-5 text-gray-500"/>
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
                                                            class="w-5 h-5 mt-0.5 text-[var(--primary)] flex-shrink-0"
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
                                                        <x-heroicon-s-check-circle class="w-5 h-5 text-[var(--primary)]"/>
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
                                <x-heroicon-o-play class="w-5 h-5 text-[var(--primary)]"/>
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
                                        <x-heroicon-o-exclamation-triangle class="w-5 h-5"/>
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
                                        <x-heroicon-o-arrow-path class="w-4 h-4 spinner"/>
                                        Executing...
                                    @else
                                        <x-heroicon-o-exclamation-triangle class="w-4 h-4"/>
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
                                        <x-heroicon-o-arrow-path class="w-4 h-4 spinner"/>
                                        Executing...
                                    @else
                                        <x-heroicon-o-play class="w-4 h-4"/>
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
                                <x-heroicon-o-clock class="w-5 h-5 text-gray-500"/>
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
                                    <x-heroicon-o-arrow-right class="w-4 h-4 text-gray-400"/>
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
                        <x-heroicon-o-terminal class="w-5 h-5 text-gray-500"/>
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
