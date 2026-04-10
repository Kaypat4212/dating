<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center gap-2">
                <x-heroicon-o-sparkles class="h-5 w-5 text-violet-500" />
                <span>AI Admin Assistant</span>
                <span class="text-xs font-normal text-gray-400 dark:text-gray-500 ml-1">powered by Groq · Llama 3</span>
            </div>
        </x-slot>

        <div class="space-y-4">

            {{-- Quick suggestion chips --}}
            <div class="flex flex-wrap gap-2">
                @foreach($suggestions as $s)
                    <button
                        wire:click="useSuggestion('{{ addslashes($s) }}')"
                        type="button"
                        class="inline-flex items-center rounded-full border border-gray-200 dark:border-gray-700
                               bg-gray-50 dark:bg-gray-800 px-3 py-1 text-xs text-gray-600 dark:text-gray-300
                               hover:border-violet-400 hover:text-violet-600 dark:hover:text-violet-400
                               transition-colors cursor-pointer"
                    >
                        {{ $s }}
                    </button>
                @endforeach
            </div>

            {{-- Input row --}}
            <div class="flex gap-2">
                <textarea
                    wire:model="prompt"
                    wire:keydown.ctrl.enter="ask"
                    rows="3"
                    placeholder="Ask anything — write email copy, draft announcements, get moderation advice… (Ctrl+Enter to send)"
                    class="flex-1 rounded-lg border border-gray-300 dark:border-gray-600
                           bg-white dark:bg-gray-900 text-sm text-gray-900 dark:text-gray-100
                           placeholder-gray-400 dark:placeholder-gray-500
                           p-3 resize-none focus:outline-none focus:ring-2 focus:ring-violet-500"
                ></textarea>

                <div class="flex flex-col gap-2">
                    <button
                        wire:click="ask"
                        wire:loading.attr="disabled"
                        type="button"
                        class="inline-flex items-center justify-center rounded-lg
                               bg-gradient-to-br from-pink-500 via-violet-500 to-indigo-500
                               px-4 py-2 text-white text-sm font-semibold shadow
                               hover:opacity-90 disabled:opacity-50 transition-opacity"
                    >
                        <span wire:loading.remove wire:target="ask">
                            <x-heroicon-m-paper-airplane class="h-4 w-4 mr-1 inline" />
                            Ask
                        </span>
                        <span wire:loading wire:target="ask" class="flex items-center gap-1">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                            </svg>
                            …
                        </span>
                    </button>

                    <button
                        wire:click="clearAll"
                        type="button"
                        class="inline-flex items-center justify-center rounded-lg border
                               border-gray-300 dark:border-gray-600 px-4 py-2 text-xs
                               text-gray-500 dark:text-gray-400 hover:text-gray-700
                               dark:hover:text-gray-200 transition-colors"
                    >
                        Clear
                    </button>
                </div>
            </div>

            {{-- Error --}}
            @if($error)
            <div class="flex items-start gap-2 rounded-lg bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-3 text-sm text-red-700 dark:text-red-400">
                <x-heroicon-o-exclamation-circle class="h-4 w-4 mt-0.5 flex-shrink-0" />
                {{ $error }}
            </div>
            @endif

            {{-- Response --}}
            @if($response)
            <div class="rounded-lg bg-gray-50 dark:bg-gray-800/60 border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-semibold text-violet-500 uppercase tracking-wide flex items-center gap-1">
                        <x-heroicon-m-sparkles class="h-3 w-3" /> AI Response
                    </span>
                    <button
                        onclick="navigator.clipboard.writeText(this.closest('.rounded-lg').querySelector('pre').innerText).then(()=>{ this.textContent='Copied!'; setTimeout(()=>this.textContent='Copy',1500); })"
                        type="button"
                        class="text-xs text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors"
                    >Copy</button>
                </div>
                <pre class="whitespace-pre-wrap text-sm text-gray-800 dark:text-gray-200 leading-relaxed font-sans">{{ $response }}</pre>
            </div>
            @endif

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
