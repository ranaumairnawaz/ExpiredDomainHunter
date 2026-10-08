<div class="py-2" x-data="{ showSaved: false }"
     x-on:settings-saved.window="showSaved = true; setTimeout(() => showSaved = false, 3000)">

    {{-- Page Header --}}
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 mb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-600 to-indigo-600 flex items-center justify-center shadow-lg">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Application Settings</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">Manage your app configuration and API credentials</p>
            </div>
        </div>
    </div>

    {{-- Success Toast --}}
    <div x-show="showSaved"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="fixed bottom-6 right-6 z-50 flex items-center gap-3 bg-emerald-600 text-white px-5 py-3 rounded-xl shadow-2xl"
         style="display: none;">
        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <span class="text-sm font-medium">Settings saved successfully!</span>
    </div>

    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- General Settings Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    General
                </h2>
            </div>
            <div class="px-6 py-5">
                <div>
                    <label for="app_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Application Name
                    </label>
                    <input id="app_name"
                           type="text"
                           wire:model="app_name"
                           placeholder="e.g. AI Domain Finder"
                           class="w-full max-w-md rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2.5 text-sm text-gray-900 placeholder-gray-400
                                  focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-shadow shadow-sm" />
                    @error('app_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">This name appears in the navigation bar and browser tab.</p>
                </div>
            </div>
        </div>

        {{-- Moz API Settings Card --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                <h2 class="text-base font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Moz API Credentials
                </h2>
            </div>

            <div class="px-6 py-5 space-y-5">
                {{-- Moz Access ID --}}
                <div>
                    <label for="moz_access_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Moz Access ID
                    </label>
                    <div class="relative max-w-md">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <input id="moz_access_id"
                               type="text"
                               wire:model="moz_access_id"
                               placeholder="mozscape-xxxxxxxxxxxx"
                               autocomplete="off"
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm text-gray-900 placeholder-gray-400
                                      focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-shadow shadow-sm font-mono" />
                    </div>
                    @error('moz_access_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Moz API Secret Key --}}
                <div x-data="{ show: false }">
                    <label for="moz_api_key" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Moz API Secret Key
                    </label>
                    <div class="relative max-w-md">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input id="moz_api_key"
                               :type="show ? 'text' : 'password'"
                               wire:model="moz_api_key"
                               placeholder="Enter your Moz secret key"
                               autocomplete="new-password"
                               class="w-full pl-10 pr-12 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm text-gray-900 placeholder-gray-400
                                      focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-shadow shadow-sm font-mono" />
                        <button type="button"
                                @click="show = !show"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    @error('moz_api_key')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">
                        Used to fetch Domain Authority (DA) and Spam Score.
                        <a href="https://moz.com/products/api" target="_blank" class="text-violet-600 hover:underline">Get your key →</a>
                    </p>
                </div>
            </div>
        </div>

        {{-- Expired Domain Sources Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50 flex items-center justify-between">
                <h2 class="text-base font-semibold text-gray-800 dark:text-gray-100 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                    Internet Domain Sources
                </h2>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                    Live Pipeline
                </span>
            </div>

            <div class="px-6 py-5 space-y-5">
                {{-- Domain Fetch Source Selector --}}
                <div>
                    <label for="domain_fetch_source" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Primary Data Source
                    </label>
                    <select id="domain_fetch_source"
                            wire:model="domain_fetch_source"
                            class="w-full max-w-md rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white px-4 py-2.5 text-sm text-gray-900
                                   focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-shadow shadow-sm">
                        <option value="whoisfreaks">WhoisFreaks Public GitHub (Free, ~10k domains/day — Active & Automated)</option>
                        <option value="domaindataset">DomainDataset.com (Daily Removed Lists, ZIP/TXT — Active)</option>
                        <option value="godaddy">GoDaddy API (Aftermarket / Expired Listings — Requires API Key)</option>
                        <option value="api">WhoisFreaks Paid API (~400,000+ domains/day — Requires API Key)</option>
                        <option value="pool">Custom Drop List Feed URL (Plain-Text)</option>
                        <option value="all">All Automated Sources in Sequence</option>
                    </select>
                    @error('domain_fetch_source')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">
                        The automated daily cron scheduler and on-demand fetchers use this source to ingest fresh dropped domains.
                    </p>
                </div>

                {{-- GoDaddy API Credentials --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div x-data="{ show: false }">
                        <label for="godaddy_api_key" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            GoDaddy API Key <span class="text-xs font-normal text-gray-400">(Optional)</span>
                        </label>
                        <div class="relative">
                            <input id="godaddy_api_key"
                                   :type="show ? 'text' : 'password'"
                                   wire:model="godaddy_api_key"
                                   placeholder="GoDaddy API Key"
                                   autocomplete="off"
                                   class="w-full pr-10 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm text-gray-900 placeholder-gray-400
                                          focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-shadow shadow-sm font-mono text-xs" />
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors">
                                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                        @error('godaddy_api_key')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div x-data="{ show: false }">
                        <label for="godaddy_api_secret" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            GoDaddy API Secret <span class="text-xs font-normal text-gray-400">(Optional)</span>
                        </label>
                        <div class="relative">
                            <input id="godaddy_api_secret"
                                   :type="show ? 'text' : 'password'"
                                   wire:model="godaddy_api_secret"
                                   placeholder="GoDaddy API Secret"
                                   autocomplete="off"
                                   class="w-full pr-10 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm text-gray-900 placeholder-gray-400
                                          focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-shadow shadow-sm font-mono text-xs" />
                            <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors">
                                <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                            </button>
                        </div>
                        @error('godaddy_api_secret')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500 -mt-2">
                    Used for live domain availability lookups and GoDaddy aftermarket drops.
                    <a href="https://developer.godaddy.com/keys" target="_blank" class="text-violet-600 hover:underline">Get GoDaddy Developer Keys →</a>
                </p>

                {{-- WhoisFreaks API Key --}}
                <div x-data="{ show: false }">
                    <label for="whoisfreaks_api_key" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        WhoisFreaks API Key <span class="text-xs font-normal text-gray-400">(Optional for paid tier)</span>
                    </label>
                    <div class="relative max-w-md">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                            </svg>
                        </div>
                        <input id="whoisfreaks_api_key"
                               :type="show ? 'text' : 'password'"
                               wire:model="whoisfreaks_api_key"
                               placeholder="Enter API key for 400k+ daily domains"
                               autocomplete="off"
                               class="w-full pl-10 pr-12 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm text-gray-900 placeholder-gray-400
                                      focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-shadow shadow-sm font-mono" />
                        <button type="button"
                                @click="show = !show"
                                class="absolute inset-y-0 right-0 flex items-center pr-3 text-gray-400 hover:text-gray-600 transition-colors">
                            <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                            </svg>
                        </button>
                    </div>
                    <p class="mt-1.5 text-xs text-gray-400 dark:text-gray-500">
                        Leave blank to use the free WhoisFreaks GitHub feed (no API key required).
                    </p>
                </div>

                {{-- Custom Plain-Text Drop List Feed URL --}}
                <div>
                    <label for="pool_droplist_url" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Custom Plain-Text Drop List Feed URL <span class="text-xs font-normal text-gray-400">(Optional)</span>
                    </label>
                    <div class="relative max-w-xl">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        </div>
                        <input id="pool_droplist_url"
                               type="url"
                               wire:model="pool_droplist_url"
                               placeholder="https://example.com/droplist.txt"
                               class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm text-gray-900 placeholder-gray-400
                                      focus:ring-2 focus:ring-violet-500 focus:border-transparent transition-shadow shadow-sm font-mono text-xs" />
                    </div>
                    @error('pool_droplist_url')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1.5 text-xs text-gray-400">
                        Optional URL for custom plain-text domain drop list feeds or mirrors (one domain per line).
                    </p>
                </div>

                {{-- Web Cron Endpoint Card --}}
                <div class="p-4 rounded-xl bg-violet-50/60 border border-violet-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span class="text-xs font-bold uppercase tracking-wider text-violet-900">Automated Web Cron URL</span>
                        </div>
                        <p class="text-xs text-violet-700 mt-1">
                            Fetch from all three sources (WhoisFreaks, DomainDataset, and Pool) by hitting this URL:
                        </p>
                        <code class="text-xs font-mono font-semibold text-violet-950 block mt-1 select-all bg-white px-2 py-1 rounded border border-violet-200 inline-block">
                            {{ url('/cron/latest-domains') }}
                        </code>
                    </div>
                    <a href="{{ url('/cron/update-domains') }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-violet-600 text-white text-xs font-semibold hover:bg-violet-700 transition shrink-0">
                        <span>Open Cron URL</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                        </svg>
                    </a>
                </div>

                {{-- On-Demand Manual Fetch Action --}}
                <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100">Trigger Immediate Download</h3>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Pulls today's dropped domains right now using the selected source.</p>
                        @if($fetchMessage)
                            <p class="mt-2 text-xs font-semibold {{ str_contains($fetchMessage, 'error') ? 'text-red-600' : 'text-emerald-600' }}">
                                {{ $fetchMessage }}
                            </p>
                        @endif
                    </div>
                    <button type="button"
                            wire:click="triggerFetchNow"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border border-violet-200 bg-violet-50 text-violet-700 text-xs font-semibold
                                   hover:bg-violet-100 active:scale-95 transition-all disabled:opacity-60">
                        <svg wire:loading.remove wire:target="triggerFetchNow" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        <svg wire:loading wire:target="triggerFetchNow" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        <span wire:loading.remove wire:target="triggerFetchNow">Fetch Internet Domains Now</span>
                        <span wire:loading wire:target="triggerFetchNow">Fetching from Internet…</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- Save Button --}}
        <div class="flex items-center justify-between pb-6">
            <p class="text-xs text-gray-400 dark:text-gray-500">Settings are stored securely in the database.</p>
            <button id="save-settings-btn"
                    type="button"
                    wire:click="save"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-semibold
                           shadow-md hover:shadow-lg hover:from-violet-700 hover:to-indigo-700 active:scale-95 transition-all duration-150 disabled:opacity-60">
                <svg wire:loading.remove wire:target="save" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                <svg wire:loading wire:target="save" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
                <span wire:loading.remove wire:target="save">Save Settings</span>
                <span wire:loading wire:target="save">Saving…</span>
            </button>
        </div>

    </div>
</div>
