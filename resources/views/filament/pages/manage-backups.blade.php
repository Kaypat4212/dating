<x-filament-panels::page>
    <div class="space-y-6">

        {{-- Info / warning banner --}}
        <div class="rounded-xl border border-blue-700/50 bg-blue-900/20 p-4 text-sm text-blue-300">
            <div class="flex items-start gap-3">
                <x-heroicon-o-information-circle class="mt-0.5 h-5 w-5 shrink-0 text-blue-400" />
                <div>
                    <p class="font-semibold text-blue-200">What gets backed up</p>
                    <ul class="mt-1 list-disc space-y-0.5 pl-4 text-blue-300/80">
                        <li><strong class="text-blue-200">Database</strong> — full SQL dump of all tables and data</li>
                        <li><strong class="text-blue-200">Application files</strong> — all code, config, resources, public assets, and user uploads in <code>storage/app/</code></li>
                    </ul>
                    <p class="mt-2 text-blue-300/70">
                        Excluded (can be regenerated): <code>vendor/</code>, <code>node_modules/</code>,
                        <code>storage/framework/</code>, <code>storage/logs/</code>.
                        Backups are stored privately in <code>storage/app/backups/</code>.
                    </p>
                </div>
            </div>
        </div>

        {{-- Backup list --}}
        @php
            $backups = $this->getBackups();
        @endphp

        <div class="rounded-xl border border-gray-700 bg-gray-900 shadow">

            {{-- Header --}}
            <div class="flex items-center justify-between border-b border-gray-700 px-5 py-4">
                <h3 class="flex items-center gap-2 text-sm font-semibold text-gray-200">
                    <x-heroicon-o-archive-box class="h-4 w-4 text-rose-400" />
                    Existing Backups
                    <span class="ml-1 rounded-full bg-gray-700 px-2 py-0.5 text-xs text-gray-400">
                        {{ count($backups) }}
                    </span>
                </h3>
                <span class="text-xs text-gray-500">Stored in storage/app/backups/</span>
            </div>

            @if(empty($backups))
                {{-- Empty state --}}
                <div class="flex flex-col items-center gap-3 py-12 text-center">
                    <x-heroicon-o-archive-box class="h-10 w-10 text-gray-600" />
                    <p class="text-sm text-gray-500">No backups yet.</p>
                    <p class="text-xs text-gray-600">Click <strong class="text-gray-400">Create Backup</strong> at the top right to create your first one.</p>
                </div>
            @else
                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-gray-800 text-left text-xs uppercase tracking-widest text-gray-500">
                                <th class="px-5 py-3">Filename</th>
                                <th class="px-5 py-3">Size</th>
                                <th class="px-5 py-3">Created</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-800">
                            @foreach($backups as $backup)
                                <tr class="group hover:bg-gray-800/50">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-2">
                                            <x-heroicon-o-document-arrow-down class="h-4 w-4 shrink-0 text-gray-500" />
                                            <span class="font-mono text-xs text-gray-300">{{ $backup['filename'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-3 text-gray-400">
                                        {{ $backup['size'] }}
                                    </td>
                                    <td class="px-5 py-3 text-gray-400">
                                        {{ $backup['created_at'] }}
                                    </td>
                                    <td class="px-5 py-3 text-right">
                                        <div class="inline-flex items-center gap-2">
                                            {{-- Download --}}
                                            <a
                                                href="{{ route('admin.backup.download', ['filename' => $backup['filename']]) }}"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-indigo-600/80 px-3 py-1.5 text-xs font-medium text-white hover:bg-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                                title="Download {{ $backup['filename'] }}"
                                            >
                                                <x-heroicon-o-arrow-down-tray class="h-3.5 w-3.5" />
                                                Download
                                            </a>

                                            {{-- Delete --}}
                                            <button
                                                wire:click="deleteBackup('{{ $backup['filename'] }}')"
                                                wire:confirm="Delete {{ $backup['filename'] }}? This cannot be undone."
                                                wire:loading.attr="disabled"
                                                class="inline-flex items-center gap-1.5 rounded-lg bg-red-700/70 px-3 py-1.5 text-xs font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 disabled:opacity-50"
                                                title="Delete {{ $backup['filename'] }}"
                                            >
                                                <x-heroicon-o-trash class="h-3.5 w-3.5" />
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Restore instructions --}}
        <div class="rounded-xl border border-yellow-700/40 bg-yellow-900/10 p-4 text-sm text-yellow-300/80">
            <div class="flex items-start gap-3">
                <x-heroicon-o-wrench-screwdriver class="mt-0.5 h-5 w-5 shrink-0 text-yellow-400" />
                <div>
                    <p class="font-semibold text-yellow-200">Restoring a backup</p>
                    <ol class="mt-1 list-decimal space-y-1 pl-4 text-yellow-300/70">
                        <li>Extract the zip — it contains <code>database.sql</code> and a <code>files/</code> folder.</li>
                        <li>Import <code>database.sql</code> into MySQL: <code>mysql -u user -p dbname &lt; database.sql</code></li>
                        <li>Copy the contents of <code>files/</code> over your application root.</li>
                        <li>Run <code>php artisan optimize:clear</code> and <code>php artisan storage:link</code>.</li>
                    </ol>
                </div>
            </div>
        </div>

    </div>
</x-filament-panels::page>
