<x-filament-panels::page>
    @php
        $backups = $this->getBackups();
        $backupCount = count($backups);
        // Calculate total size in MB
        $totalBytes = 0;
        foreach ($backups as $b) {
            preg_match('/[\d.]+/', $b['size'], $m);
            $val = floatval($m[0] ?? 0);
            if (str_contains($b['size'], 'GB')) $val *= 1024;
            $totalBytes += $val;
        }
        $totalSize = $totalBytes > 0 ? round($totalBytes, 1) . ' MB' : '—';
        $latestBackup = $backupCount > 0 ? $backups[0]['created_at'] : null;
    @endphp

    <div class="space-y-5">

        {{-- ── Stats row ─────────────────────────────────────────────── --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

            {{-- Total Backups --}}
            <div class="relative overflow-hidden rounded-2xl border border-rose-500/20 bg-gradient-to-br from-rose-950/60 to-rose-900/30 p-5 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-rose-400/70">Total Backups</p>
                        <p class="mt-1 text-3xl font-bold text-white">{{ $backupCount }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-rose-500/20">
                        <x-heroicon-o-archive-box class="h-6 w-6 text-rose-400" />
                    </div>
                </div>
                <div class="mt-3 h-1 rounded-full bg-rose-900/60">
                    <div class="h-1 rounded-full bg-gradient-to-r from-rose-500 to-pink-500" style="width:{{ min(100, $backupCount * 10) }}%"></div>
                </div>
            </div>

            {{-- Storage Used --}}
            <div class="relative overflow-hidden rounded-2xl border border-indigo-500/20 bg-gradient-to-br from-indigo-950/60 to-indigo-900/30 p-5 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-indigo-400/70">Storage Used</p>
                        <p class="mt-1 text-3xl font-bold text-white">{{ $totalSize }}</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-indigo-500/20">
                        <x-heroicon-o-circle-stack class="h-6 w-6 text-indigo-400" />
                    </div>
                </div>
                <p class="mt-3 text-xs text-indigo-400/50">Stored in <code class="text-indigo-300/70">storage/app/backups/</code></p>
            </div>

            {{-- Latest Backup --}}
            <div class="relative overflow-hidden rounded-2xl border border-emerald-500/20 bg-gradient-to-br from-emerald-950/60 to-emerald-900/30 p-5 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-widest text-emerald-400/70">Last Backup</p>
                        <p class="mt-1 text-lg font-bold leading-snug text-white">
                            {{ $latestBackup ?? 'No backups yet' }}
                        </p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500/20">
                        <x-heroicon-o-clock class="h-6 w-6 text-emerald-400" />
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    @if($latestBackup)
                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/15 px-2.5 py-0.5 text-xs font-medium text-emerald-400">
                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            Up to date
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1 rounded-full bg-amber-500/15 px-2.5 py-0.5 text-xs font-medium text-amber-400">
                            <span class="h-1.5 w-1.5 rounded-full bg-amber-400"></span>
                            No backups
                        </span>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── What gets backed up ────────────────────────────────────── --}}
        <div class="rounded-2xl border border-blue-700/30 bg-blue-950/30 p-4">
            <div class="flex items-start gap-3">
                <x-heroicon-o-information-circle class="mt-0.5 h-5 w-5 shrink-0 text-blue-400" />
                <div>
                    <p class="font-semibold text-blue-200">What gets backed up</p>
                    <div class="mt-2 flex flex-wrap gap-3">
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-800/40 px-3 py-1.5 text-xs text-blue-300">
                            <x-heroicon-o-table-cells class="h-3.5 w-3.5" />
                            Full database SQL dump
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-800/40 px-3 py-1.5 text-xs text-blue-300">
                            <x-heroicon-o-folder-open class="h-3.5 w-3.5" />
                            Code, config &amp; resources
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-lg bg-blue-800/40 px-3 py-1.5 text-xs text-blue-300">
                            <x-heroicon-o-photo class="h-3.5 w-3.5" />
                            User uploads (storage/app/)
                        </span>
                    </div>
                    <p class="mt-2 text-xs text-blue-400/60">
                        Excluded (regeneratable): <code>vendor/</code>, <code>node_modules/</code>, <code>storage/framework/</code>, <code>storage/logs/</code>
                    </p>
                </div>
            </div>
        </div>

        {{-- ── Backup list ────────────────────────────────────────────── --}}
        <div class="rounded-2xl border border-white/[0.06] bg-gray-900/80 shadow-xl overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-white/[0.06] bg-white/[0.02] px-6 py-4">
                <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-200">
                    <x-heroicon-o-archive-box class="h-4 w-4 text-rose-400" />
                    Backup Files
                    @if($backupCount > 0)
                    <span class="ml-1 rounded-full bg-rose-500/20 px-2.5 py-0.5 text-xs font-bold text-rose-300">
                        {{ $backupCount }}
                    </span>
                    @endif
                </h3>
                <span class="flex items-center gap-1.5 text-xs text-gray-500">
                    <x-heroicon-o-lock-closed class="h-3.5 w-3.5" />
                    Private storage
                </span>
            </div>

            @if(empty($backups))
                {{-- Empty state --}}
                <div class="flex flex-col items-center gap-4 px-6 py-16 text-center">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-800 ring-1 ring-white/[0.06]">
                        <x-heroicon-o-archive-box class="h-8 w-8 text-gray-600" />
                    </div>
                    <div>
                        <p class="font-semibold text-gray-400">No backups yet</p>
                        <p class="mt-1 text-sm text-gray-600">Click <strong class="text-gray-400">Create Backup</strong> at the top right to create your first snapshot.</p>
                    </div>
                </div>
            @else
                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-white/[0.05] text-left text-xs uppercase tracking-widest text-gray-600">
                                <th class="px-6 py-3.5">Filename</th>
                                <th class="px-6 py-3.5 hidden sm:table-cell">Size</th>
                                <th class="px-6 py-3.5 hidden md:table-cell">Created</th>
                                <th class="px-6 py-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-white/[0.04]">
                            @foreach($backups as $i => $backup)
                                <tr class="group transition-colors hover:bg-white/[0.025]">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-indigo-500/15">
                                                <x-heroicon-o-document-arrow-down class="h-4 w-4 text-indigo-400" />
                                            </div>
                                            <div>
                                                <span class="block font-mono text-xs font-medium text-gray-200 truncate max-w-[200px] lg:max-w-none">
                                                    {{ $backup['filename'] }}
                                                </span>
                                                <span class="mt-0.5 block text-xs text-gray-600 sm:hidden">{{ $backup['size'] }} · {{ $backup['created_at'] }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 hidden sm:table-cell">
                                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-800 px-2.5 py-1 text-xs text-gray-400 ring-1 ring-white/[0.05]">
                                            {{ $backup['size'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 hidden md:table-cell text-gray-500 text-xs">
                                        <div class="flex items-center gap-1.5">
                                            <x-heroicon-o-calendar class="h-3.5 w-3.5" />
                                            {{ $backup['created_at'] }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            {{-- Download --}}
                                            <a
                                                href="{{ route('admin.backup.download', ['filename' => $backup['filename']]) }}"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600/80 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-600 hover:shadow-indigo-500/25 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                title="Download {{ $backup['filename'] }}"
                                            >
                                                <x-heroicon-o-arrow-down-tray class="h-3.5 w-3.5" />
                                                <span class="hidden sm:inline">Download</span>
                                            </a>

                                            {{-- Delete --}}
                                            <button
                                                wire:click="deleteBackup('{{ $backup['filename'] }}')"
                                                wire:confirm="Delete {{ $backup['filename'] }}? This cannot be undone."
                                                wire:loading.attr="disabled"
                                                class="inline-flex items-center gap-1.5 rounded-xl bg-red-700/60 px-3.5 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-red-600 hover:shadow-red-500/25 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-40"
                                                title="Delete {{ $backup['filename'] }}"
                                            >
                                                <x-heroicon-o-trash class="h-3.5 w-3.5" />
                                                <span class="hidden sm:inline">Delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Footer --}}
                <div class="border-t border-white/[0.05] bg-white/[0.01] px-6 py-3 text-xs text-gray-600">
                    {{ $backupCount }} {{ Str::plural('backup', $backupCount) }} &middot; {{ $totalSize }} total
                </div>
            @endif
        </div>

        {{-- ── Restore instructions ───────────────────────────────────── --}}
        <details class="group rounded-2xl border border-yellow-700/30 bg-yellow-950/20 transition-all">
            <summary class="flex cursor-pointer items-center gap-3 px-5 py-4 text-sm font-semibold text-yellow-200 select-none list-none">
                <x-heroicon-o-wrench-screwdriver class="h-5 w-5 shrink-0 text-yellow-400" />
                How to restore a backup
                <x-heroicon-o-chevron-down class="ml-auto h-4 w-4 text-yellow-500/60 transition-transform group-open:rotate-180" />
            </summary>
            <div class="border-t border-yellow-700/20 px-5 pb-5 pt-4">
                <ol class="space-y-3 text-sm text-yellow-300/75">
                    <li class="flex items-start gap-3">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-yellow-700/40 text-xs font-bold text-yellow-300">1</span>
                        Extract the zip — it contains <code class="rounded bg-yellow-900/50 px-1.5 py-0.5 text-yellow-200">database.sql</code> and a <code class="rounded bg-yellow-900/50 px-1.5 py-0.5 text-yellow-200">files/</code> folder.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-yellow-700/40 text-xs font-bold text-yellow-300">2</span>
                        Import the database: <code class="rounded bg-yellow-900/50 px-1.5 py-0.5 text-yellow-200">mysql -u user -p dbname &lt; database.sql</code>
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-yellow-700/40 text-xs font-bold text-yellow-300">3</span>
                        Copy the contents of <code class="rounded bg-yellow-900/50 px-1.5 py-0.5 text-yellow-200">files/</code> over your application root.
                    </li>
                    <li class="flex items-start gap-3">
                        <span class="flex h-6 w-6 shrink-0 items-center justify-center rounded-full bg-yellow-700/40 text-xs font-bold text-yellow-300">4</span>
                        Run <code class="rounded bg-yellow-900/50 px-1.5 py-0.5 text-yellow-200">php artisan optimize:clear</code> and <code class="rounded bg-yellow-900/50 px-1.5 py-0.5 text-yellow-200">php artisan storage:link</code>.
                    </li>
                </ol>
            </div>
        </details>

    </div>
</x-filament-panels::page>
