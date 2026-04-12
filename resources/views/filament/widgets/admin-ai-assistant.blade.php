<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <div class="flex items-center justify-between w-full">
                <div class="flex items-center gap-3">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500/10 to-fuchsia-500/10 border border-violet-200 dark:border-violet-800">
                        <x-heroicon-o-sparkles class="h-5 w-5 text-violet-600 dark:text-violet-400" />
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-gray-100">AI Admin Assistant</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5 mt-0.5">
                            <span class="inline-flex items-center gap-1">
                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/>
                                </svg>
                                Powered by
                            </span>
                            <span class="font-semibold text-violet-600 dark:text-violet-400">Groq</span>
                            <span class="text-gray-400">·</span>
                            <span class="font-medium">Llama 3.1</span>
                        </p>
                    </div>
                </div>
            </div>
        </x-slot>

        <div class="space-y-5">

            {{-- Quick suggestion chips --}}
            <div>
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wider">
                    Quick Prompts
                </label>
                <div class="flex flex-wrap gap-2">
                    @foreach($suggestions as $s)
                        <button
                            wire:click="useSuggestion('{{ addslashes($s) }}')"
                            type="button"
                            class="group inline-flex items-center rounded-lg border-2 border-gray-200 dark:border-gray-700
                                   bg-white dark:bg-gray-800 px-3.5 py-2 text-xs font-medium text-gray-700 dark:text-gray-300
                                   hover:border-violet-400 dark:hover:border-violet-500 hover:bg-violet-50 dark:hover:bg-violet-950/30
                                   hover:text-violet-700 dark:hover:text-violet-300 hover:shadow-sm
                                   transition-all duration-200 cursor-pointer active:scale-95"
                        >
                            <x-heroicon-m-light-bulb class="h-3.5 w-3.5 mr-1.5 opacity-60 group-hover:opacity-100 transition-opacity" />
                            {{ $s }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Input section --}}
            <div class="relative">
                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-3 uppercase tracking-wider">
                    Your Request
                </label>
                <div class="flex gap-3">
                    <div class="flex-1 relative">
                        <textarea
                            wire:model="prompt"
                            wire:keydown.ctrl.enter="ask"
                            rows="4"
                            placeholder="Ask anything — write email copy, draft announcements, get moderation advice, create FAQ answers, plan campaigns…&#10;&#10;💡 Tip: Press Ctrl+Enter to send"
                            class="w-full rounded-xl border-2 border-gray-300 dark:border-gray-600
                                   bg-white dark:bg-gray-900 text-sm text-gray-900 dark:text-gray-100
                                   placeholder-gray-400 dark:placeholder-gray-500
                                   px-4 py-3 resize-none
                                   focus:outline-none focus:ring-4 focus:ring-violet-500/20 focus:border-violet-500
                                   transition-all duration-200 shadow-sm"
                        ></textarea>
                        <div class="absolute bottom-3 right-3 flex items-center gap-2">
                            <span class="text-xs text-gray-400 dark:text-gray-500 font-medium">
                                <kbd class="px-1.5 py-0.5 text-xs bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded">Ctrl</kbd>
                                +
                                <kbd class="px-1.5 py-0.5 text-xs bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded">Enter</kbd>
                            </span>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <button
                            wire:click="ask"
                            wire:loading.attr="disabled"
                            type="button"
                            class="group relative inline-flex items-center justify-center rounded-xl
                                   bg-gradient-to-br from-pink-500 via-violet-500 to-indigo-500
                                   px-6 py-3 text-white text-sm font-bold shadow-lg shadow-violet-500/30
                                   hover:shadow-xl hover:shadow-violet-500/40 hover:scale-105
                                   disabled:opacity-60 disabled:cursor-not-allowed disabled:hover:scale-100
                                   transition-all duration-200 min-w-[100px]"
                        >
                            <span wire:loading.remove wire:target="ask" class="flex items-center gap-2">
                                <x-heroicon-m-paper-airplane class="h-4 w-4 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform" />
                                Ask AI
                            </span>
                            <span wire:loading wire:target="ask" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                </svg>
                                Thinking...
                            </span>
                        </button>

                        <button
                            wire:click="clearAll"
                            type="button"
                            class="inline-flex items-center justify-center rounded-xl border-2
                                   border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800
                                   px-6 py-3 text-xs font-semibold
                                   text-gray-600 dark:text-gray-400
                                   hover:bg-gray-50 dark:hover:bg-gray-700
                                   hover:border-gray-400 dark:hover:border-gray-500
                                   hover:text-gray-800 dark:hover:text-gray-200
                                   transition-all duration-200 active:scale-95"
                        >
                            <x-heroicon-m-x-circle class="h-4 w-4 mr-1.5" />
                            Clear
                        </button>
                    </div>
                </div>
            </div>

            {{-- Error Alert --}}
            @if($error)
            <div class="animate-shake rounded-xl bg-gradient-to-r from-red-50 to-rose-50 dark:from-red-950/40 dark:to-rose-950/40 border-2 border-red-200 dark:border-red-800 p-4 shadow-sm">
                <div class="flex items-start gap-3">
                    <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-red-100 dark:bg-red-900/60">
                        <x-heroicon-o-exclamation-circle class="h-5 w-5 text-red-600 dark:text-red-400" />
                    </div>
                    <div class="flex-1">
                        <h4 class="text-sm font-bold text-red-800 dark:text-red-300 mb-1">Error</h4>
                        <p class="text-sm text-red-700 dark:text-red-400 leading-relaxed">{{ $error }}</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- AI Response --}}
            @if($response)
            <div class="rounded-xl bg-gradient-to-br from-violet-50/80 via-purple-50/50 to-fuchsia-50/80 dark:from-violet-950/20 dark:via-purple-950/20 dark:to-fuchsia-950/20 border-2 border-violet-200 dark:border-violet-800 overflow-hidden shadow-lg">
                <div class="flex items-center justify-between px-5 py-3 bg-white/60 dark:bg-gray-900/60 backdrop-blur-sm border-b-2 border-violet-200 dark:border-violet-800">
                    <div class="flex items-center gap-2">
                        <div class="flex items-center justify-center w-7 h-7 rounded-lg bg-gradient-to-br from-violet-500 to-fuchsia-500">
                            <x-heroicon-m-sparkles class="h-4 w-4 text-white" />
                        </div>
                        <span class="text-sm font-bold text-gray-900 dark:text-gray-100">AI Response</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900/40 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-800">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5 animate-pulse"></span>
                            Generated
                        </span>
                    </div>
                    <button
                        onclick="navigator.clipboard.writeText(this.closest('.rounded-xl').querySelector('pre').innerText).then(()=>{ 
                            const original = this.innerHTML;
                            this.innerHTML = '<svg class=&quot;w-3.5 h-3.5 mr-1.5&quot; fill=&quot;currentColor&quot; viewBox=&quot;0 0 20 20&quot;><path fill-rule=&quot;evenodd&quot; d=&quot;M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z&quot; clip-rule=&quot;evenodd&quot;/></svg>Copied!';
                            setTimeout(()=>{ this.innerHTML = original; }, 2000);
                        })"
                        type="button"
                        class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold
                               bg-white dark:bg-gray-800 text-violet-600 dark:text-violet-400
                               border-2 border-violet-200 dark:border-violet-700
                               hover:bg-violet-50 dark:hover:bg-violet-950/50
                               hover:border-violet-300 dark:hover:border-violet-600
                               transition-all duration-200 active:scale-95"
                    >
                        <x-heroicon-m-clipboard-document class="h-3.5 w-3.5 mr-1.5" />
                        Copy
                    </button>
                </div>
                <div class="p-5">
                    <div class="prose prose-sm dark:prose-invert max-w-none">
                        <pre class="whitespace-pre-wrap text-sm text-gray-800 dark:text-gray-200 leading-relaxed font-sans bg-transparent border-0 p-0 m-0">{{ $response }}</pre>
                    </div>
                </div>
            </div>
            @endif

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
