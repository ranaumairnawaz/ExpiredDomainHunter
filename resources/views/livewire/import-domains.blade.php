<div class="py-2" x-data="{ tab: 'file' }">

    {{-- Page Header --}}
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-600 flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Import Domains</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Upload CSV/TXT files or paste domain lists directly from ExpiredDomains.net</p>
                </div>
            </div>

            <a href="{{ route('domains.search') }}" wire:navigate
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                View Search UI
            </a>
        </div>
    </div>

    {{-- Alerts --}}
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 mb-6">
        @if($successMessage)
            <div class="flex items-center gap-3 bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 px-5 py-4 rounded-2xl shadow-sm">
                <svg class="w-5 h-5 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <span class="text-sm font-semibold">{{ $successMessage }}</span>
                    <a href="{{ route('domains.search') }}" wire:navigate class="ml-2 text-xs font-bold underline hover:text-emerald-900 dark:hover:text-emerald-200">
                        Open Domain Search →
                    </a>
                </div>
            </div>
        @endif

        @if($errorMessage)
            <div class="flex items-center gap-3 bg-red-50 dark:bg-red-950/40 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-300 px-5 py-4 rounded-2xl shadow-sm">
                <svg class="w-5 h-5 shrink-0 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="text-sm font-semibold">{{ $errorMessage }}</span>
            </div>
        @endif
    </div>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- Configuration Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6">
            <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 mb-4">Categorization Options</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">
                        Target Category
                    </label>
                    <select wire:model="categoryId"
                            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2.5 text-sm text-gray-900 focus:ring-2 focus:ring-violet-500">
                        <option value="">Auto-detect / Keep Unassigned</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Leave blank to auto-detect categories by keyword match.</p>
                </div>

                <div class="flex items-center">
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" wire:model="autoDetectCategory"
                               class="rounded border-gray-300 dark:border-gray-600 text-violet-600 focus:ring-violet-400 h-4 w-4" />
                        <div>
                            <span class="text-sm font-medium text-gray-800 dark:text-gray-200">Auto-match categories</span>
                            <p class="text-xs text-gray-400 dark:text-gray-500">Matches domain name against active category keywords</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- Import Mode Tabs --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex">
                <button type="button"
                        @click="tab = 'file'"
                        :class="tab === 'file' ? 'border-b-2 border-violet-600 text-violet-600 dark:text-violet-400 bg-white dark:bg-gray-800 font-semibold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                        class="px-6 py-3.5 text-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    Upload CSV / TXT File
                </button>
                <button type="button"
                        @click="tab = 'paste'"
                        :class="tab === 'paste' ? 'border-b-2 border-violet-600 text-violet-600 dark:text-violet-400 bg-white dark:bg-gray-800 font-semibold' : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200'"
                        class="px-6 py-3.5 text-sm transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Direct Paste / Clipboard
                </button>
            </div>

            {{-- File Upload Tab --}}
            <div x-show="tab === 'file'" class="p-6">
                <form wire:submit.prevent="importFile" class="space-y-4">
                    <div class="border-2 border-dashed border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/40 rounded-2xl p-8 text-center hover:border-violet-400 dark:hover:border-violet-500 transition-colors">
                        <div class="mx-auto w-12 h-12 rounded-full bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-400 flex items-center justify-center mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                        </div>
                        <label for="domain-file-input" class="cursor-pointer">
                            <span class="text-sm font-semibold text-violet-600 dark:text-violet-400 hover:text-violet-700 dark:hover:text-violet-300">Choose a file</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400"> or drag and drop here</span>
                            <input id="domain-file-input" type="file" wire:model="file" accept=".csv,.txt" class="sr-only" />
                        </label>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5">Supports CSV or plain TXT up to 20MB (one domain per line or first column)</p>

                        @if ($file)
                            <div class="mt-4 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/40 text-violet-700 dark:text-violet-300 text-xs font-semibold">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Selected: {{ $file->getClientOriginalName() }}
                            </div>
                        @endif
                    </div>

                    @error('file')
                        <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end pt-2">
                        <button type="submit"
                                wire:loading.attr="disabled"
                                :disabled="!@this.file"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-semibold
                                       shadow-md hover:shadow-lg hover:from-violet-700 hover:to-indigo-700 active:scale-95 transition-all disabled:opacity-50">
                            <span wire:loading.remove wire:target="importFile">Import Domains from File</span>
                            <span wire:loading wire:target="importFile">Processing File…</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Text Paste Tab --}}
            <div x-show="tab === 'paste'" class="p-6" style="display: none;">
                <form wire:submit.prevent="importText" class="space-y-4">
                    <div>
                        <label for="rawText" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">
                            Domain List (One per line)
                        </label>
                        <textarea id="rawText" wire:model="rawText" rows="10"
                                  placeholder="example-domain.com&#10;cleaningservices.co.uk&#10;removalvans.org"
                                  class="w-full rounded-xl border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white p-4 font-mono text-xs text-gray-800 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-violet-500"></textarea>
                    </div>

                    @error('rawText')
                        <p class="text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror

                    <div class="flex justify-end pt-2">
                        <button type="submit"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-semibold
                                       shadow-md hover:shadow-lg hover:from-violet-700 hover:to-indigo-700 active:scale-95 transition-all disabled:opacity-50">
                            <span wire:loading.remove wire:target="importText">Import Pasted Domains</span>
                            <span wire:loading wire:target="importText">Processing Domains…</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>

</div>
