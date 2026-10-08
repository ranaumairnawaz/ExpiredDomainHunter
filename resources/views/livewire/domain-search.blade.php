<div class="space-y-6" x-data>

    {{-- ── Header ─────────────────────────────────────────────────────────── --}}
    <div>
        <div class="flex flex-wrap items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-600 flex items-center justify-center shadow-lg">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Domain Search</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ number_format($totalCount) }} domains indexed
                        @if($pendingCount > 0)
                            · <span class="text-amber-600 dark:text-amber-400">{{ number_format($pendingCount) }} awaiting metrics</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                {{-- Dispatch Metrics Job --}}
                @if($pendingCount > 0)
                    <button wire:click="dispatchMetricsFetch"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-300 text-sm font-medium
                                   hover:bg-amber-200 dark:hover:bg-amber-900/60 transition-colors border border-amber-200 dark:border-amber-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Fetch Metrics
                    </button>
                @endif

                {{-- Export Selected --}}
                @if(count($selectedDomains) > 0)
                    <button wire:click="exportSelected"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300
                                   text-sm font-medium shadow-sm hover:bg-emerald-200 dark:hover:bg-emerald-900/60 transition-colors border border-emerald-200 dark:border-emerald-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export ({{ count($selectedDomains) }})
                    </button>
                @endif

                {{-- Save Search --}}
                <button wire:click="openSaveModal"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-gradient-to-r from-violet-600 to-indigo-600
                               text-white text-sm font-medium shadow-md hover:shadow-lg hover:from-violet-700 hover:to-indigo-700
                               active:scale-95 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                    </svg>
                    Save Search
                </button>
            </div>
        </div>
    </div>

    {{-- ── Filters ─────────────────────────────────────────────────────────── --}}
    <div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-5">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                {{-- Keyword --}}
                <div class="lg:col-span-2">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Keyword</label>
                    <div class="relative">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                        </svg>
                        <input id="filter-keyword" type="text" wire:model.live.debounce.400ms="keyword"
                               placeholder="e.g. removal, van…"
                               class="w-full pl-9 pr-4 py-2.5 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 text-sm focus:ring-2 focus:ring-violet-400 focus:border-transparent transition" />
                    </div>
                </div>

                {{-- TLD --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">TLD</label>
                    <select id="filter-tld" wire:model.live="tld"
                            class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-violet-400 focus:border-transparent transition">
                        <option value="">All TLDs</option>
                        @foreach($tlds as $t)
                            <option value="{{ $t }}">{{ $t }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Category --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">Category</label>
                    <select id="filter-category" wire:model.live="category_id"
                            class="w-full py-2.5 px-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100 text-sm focus:ring-2 focus:ring-violet-400 focus:border-transparent transition">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Min DA --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">
                        Min DA: <span class="text-violet-600 dark:text-violet-400">{{ $min_da }}</span>
                    </label>
                    <input id="filter-min-da" type="range" wire:model.live="min_da" min="0" max="100"
                           class="w-full accent-violet-600" />
                </div>

                {{-- Max Spam --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">
                        Max Spam: <span class="text-violet-600 dark:text-violet-400">{{ $max_spam }}</span>
                    </label>
                    <input id="filter-max-spam" type="range" wire:model.live="max_spam" min="0" max="17"
                           class="w-full accent-violet-600" />
                </div>

                {{-- Max Length --}}
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1 uppercase tracking-wide">
                        Max Length: <span class="text-violet-600 dark:text-violet-400">{{ $max_length }}</span>
                    </label>
                    <input id="filter-max-length" type="range" wire:model.live="max_length" min="3" max="63"
                           class="w-full accent-violet-600" />
                </div>

                {{-- Checkboxes --}}
                <div class="flex flex-col gap-2 justify-center">
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input id="filter-no-hyphen" type="checkbox" wire:model.live="no_hyphen"
                               class="rounded border-gray-300 dark:border-gray-600 text-violet-600 focus:ring-violet-400" />
                        <span class="text-sm text-gray-700 dark:text-gray-300">No hyphens</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer select-none">
                        <input id="filter-no-numbers" type="checkbox" wire:model.live="no_numbers"
                               class="rounded border-gray-300 dark:border-gray-600 text-violet-600 focus:ring-violet-400" />
                        <span class="text-sm text-gray-700 dark:text-gray-300">No numbers</span>
                    </label>
                </div>

            </div>

            <div class="mt-3 flex justify-end">
                <button wire:click="clearFilters"
                        class="text-xs text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300 transition-colors flex items-center gap-1">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Clear filters
                </button>
            </div>
        </div>
    </div>

    {{-- ── Results Table ───────────────────────────────────────────────────── --}}
    <div>
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden relative">

            {{-- Loading overlay --}}
            <div wire:loading.delay.shorter class="absolute inset-0 bg-white/60 dark:bg-gray-800/60 z-10 flex items-center justify-center rounded-2xl">
                <svg class="animate-spin h-7 w-7 text-violet-600" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-gray-900/50 border-b border-gray-100 dark:border-gray-700">
                            <th class="px-4 py-3 text-left w-10"></th>
                            @foreach([
                                ['name', 'Domain'],
                                ['tld', 'TLD'],
                                ['length', 'Length'],
                                ['category_id', 'Category'],
                                ['da', 'DA'],
                                ['spam_score', 'Spam'],
                                ['dropped_on', 'Dropped'],
                            ] as [$col, $label])
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide cursor-pointer hover:text-violet-600 dark:hover:text-violet-400 transition-colors"
                                wire:click="sortBy('{{ $col }}')">
                                <span class="flex items-center gap-1">
                                    {{ $label }}
                                    @if($sortBy === $col)
                                        <svg class="w-3 h-3 {{ $sortDir === 'asc' ? 'rotate-180' : '' }} transition-transform" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </span>
                            </th>
                            @endforeach
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Links</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-700/50">
                        @forelse($domains as $domain)
                            <tr class="hover:bg-violet-50/40 dark:hover:bg-violet-900/10 transition-colors group">

                                {{-- Checkbox --}}
                                <td class="px-4 py-3">
                                    <input type="checkbox" wire:model.live="selectedDomains" value="{{ $domain->id }}"
                                           class="rounded border-gray-300 dark:border-gray-600 text-violet-600 focus:ring-violet-400" />
                                </td>

                                {{-- Domain name --}}
                                <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100 whitespace-nowrap">
                                    {{ $domain->name }}
                                </td>

                                {{-- TLD --}}
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 font-mono text-xs">
                                    .{{ $domain->tld }}
                                </td>

                                {{-- Length --}}
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-center">{{ $domain->length }}</td>

                                {{-- Category --}}
                                <td class="px-4 py-3">
                                    @if($domain->category)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                              style="background: {{ $domain->category->color }}22; color: {{ $domain->category->color }}">
                                            {{ $domain->category->name }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600">—</span>
                                    @endif
                                </td>

                                {{-- DA --}}
                                <td class="px-4 py-3 text-center">
                                    @if($domain->da !== null)
                                        <span class="inline-block w-8 h-8 rounded-full text-xs font-bold leading-8 text-center
                                            {{ $domain->da >= 40 ? 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400' : ($domain->da >= 20 ? 'bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400') }}">
                                            {{ $domain->da }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600 text-xs">—</span>
                                    @endif
                                </td>

                                {{-- Spam Score --}}
                                <td class="px-4 py-3 text-center">
                                    @if($domain->spam_score !== null)
                                        <span class="text-xs font-medium {{ $domain->spam_score <= 4 ? 'text-emerald-600 dark:text-emerald-400' : ($domain->spam_score <= 8 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400') }}">
                                            {{ $domain->spam_score }}/17
                                        </span>
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600 text-xs">—</span>
                                    @endif
                                </td>

                                {{-- Dropped on --}}
                                <td class="px-4 py-3 text-gray-500 dark:text-gray-400 text-xs whitespace-nowrap">
                                    {{ $domain->dropped_on?->format('d M Y') ?? '—' }}
                                </td>

                                {{-- Links --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        {{-- Check Live Availability --}}
                                        <button wire:click="checkAvailability({{ $domain->id }})"
                                                title="Check Live Availability"
                                                class="p-1 rounded hover:bg-violet-100 dark:hover:bg-violet-900/40 text-gray-400 hover:text-violet-600 dark:hover:text-violet-400 transition-colors">
                                            @if(isset($availabilityStatuses[$domain->id]))
                                                @if($availabilityStatuses[$domain->id] === 'available')
                                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                @elseif($availabilityStatuses[$domain->id] === 'taken')
                                                    <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                @else
                                                    <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                @endif
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4" />
                                                </svg>
                                            @endif
                                        </button>

                                        {{-- Save Domain --}}
                                        @php
                                            $isSaved = auth()->user() && auth()->user()->savedDomains()->where('domain_id', $domain->id)->exists();
                                        @endphp
                                        <button wire:click="toggleSaveDomain({{ $domain->id }})"
                                                title="{{ $isSaved ? 'Unsave Domain' : 'Save Domain' }}"
                                                class="p-1 rounded hover:bg-violet-100 dark:hover:bg-violet-900/40 {{ $isSaved ? 'text-amber-500' : 'text-gray-400 hover:text-amber-500' }} transition-colors">
                                            <svg class="w-4 h-4" fill="{{ $isSaved ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                            </svg>
                                        </button>

                                        {{-- Wayback Machine --}}
                                        <a href="{{ $domain->waybackUrl() }}" target="_blank"
                                           title="View on Wayback Machine"
                                           class="p-1 rounded hover:bg-violet-100 dark:hover:bg-violet-900/40 text-gray-400 hover:text-violet-600 dark:hover:text-violet-400 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </a>

                                        {{-- Visit domain --}}
                                        <a href="https://{{ $domain->name }}" target="_blank"
                                           title="Visit domain"
                                           class="p-1 rounded hover:bg-violet-100 dark:hover:bg-violet-900/40 text-gray-400 hover:text-violet-600 dark:hover:text-violet-400 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="px-4 py-16 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <svg class="w-12 h-12 text-gray-200 dark:text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                  d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <p class="text-gray-400 dark:text-gray-500 text-sm">No domains found matching your filters.</p>
                                        <button wire:click="clearFilters" class="text-violet-600 dark:text-violet-400 text-sm hover:underline">Clear filters</button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($domains->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 dark:border-gray-700">
                    {{ $domains->links() }}
                </div>
            @endif

        </div>
    </div>

    {{-- ── Save Search Modal ───────────────────────────────────────────────── --}}
    @if($showSaveModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
             x-transition>
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4 border border-gray-100 dark:border-gray-700"
                 @click.outside="$wire.showSaveModal = false">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Save Search</h2>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search Name</label>
                        <input id="save-search-name" type="text" wire:model="saveName"
                               placeholder="e.g. High DA Removal .co.uk"
                               class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2.5 text-sm
                                      focus:ring-2 focus:ring-violet-500 focus:border-transparent placeholder-gray-400 dark:placeholder-gray-500" />
                        @error('saveName')
                            <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <label class="flex items-center gap-3 cursor-pointer">
                        <input id="save-email-alert" type="checkbox" wire:model="saveEmailAlert"
                               class="rounded border-gray-300 dark:border-gray-600 text-violet-600 focus:ring-violet-400" />
                        <span class="text-sm text-gray-700 dark:text-gray-300">Send daily email alert when new matches are found</span>
                    </label>
                </div>

                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('showSaveModal', false)"
                            class="px-4 py-2 rounded-lg text-sm text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        Cancel
                    </button>
                    <button id="confirm-save-search" wire:click="saveSearch"
                            class="px-5 py-2 rounded-lg bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-medium
                                   hover:from-violet-700 hover:to-indigo-700 active:scale-95 transition-all">
                        Save
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ── Saved confirmation toast ────────────────────────────────────────── --}}
    @if($searchSaved)
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             class="fixed bottom-6 right-6 z-50 flex items-center gap-3 bg-emerald-600 text-white px-5 py-3 rounded-xl shadow-2xl">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Search saved!
        </div>
    @endif

</div>
