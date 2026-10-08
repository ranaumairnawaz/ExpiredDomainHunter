<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-2xl text-gray-900 dark:text-white leading-tight">
            {{ __('Overview') }}
        </h2>
    </x-slot>

    <div class="space-y-8">
        {{-- Stats Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            @php
                $totalDomains  = \App\Models\Domain::count();
                $pendingMetrics = \App\Models\Domain::where('metrics_status','pending')->count();
                $fetchedMetrics = \App\Models\Domain::where('metrics_status','fetched')->count();
                $savedSearches = \App\Models\SavedSearch::count();

                $cards = [
                    ['label' => 'Total Domains',      'value' => number_format($totalDomains),   'icon' => 'M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064', 'color' => 'from-violet-500 to-indigo-600'],
                    ['label' => 'Metrics Pending',   'value' => number_format($pendingMetrics),  'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                                                                                           'color' => 'from-amber-400 to-orange-500'],
                    ['label' => 'Metrics Fetched',   'value' => number_format($fetchedMetrics),  'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',                                                                                                                                                                                          'color' => 'from-emerald-400 to-teal-500'],
                    ['label' => 'Saved Searches',    'value' => number_format($savedSearches),   'icon' => 'M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z',                                                                                                                                                                                       'color' => 'from-fuchsia-400 to-pink-500'],
                ];
            @endphp

            @foreach($cards as $card)
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 flex items-center gap-5 hover:shadow-md transition-shadow">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br {{ $card['color'] }} flex items-center justify-center shadow-lg shrink-0 transform -rotate-3 group-hover:rotate-0 transition-transform">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">{{ $card['value'] }}</p>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400 mt-1">{{ $card['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Quick actions --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden">
            <div class="border-b border-gray-100 dark:border-gray-700 px-6 py-5 bg-gray-50/50 dark:bg-gray-800/50">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Quick Actions</h3>
            </div>
            <div class="p-6 flex flex-wrap gap-4">
                <a href="{{ route('domains.search') }}" wire:navigate
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600
                          text-white text-sm font-semibold shadow-md hover:shadow-lg hover:from-violet-700 hover:to-indigo-700 transition-all active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M17 11A6 6 0 115 11a6 6 0 0112 0z"/>
                    </svg>
                    Search Domains
                </a>
                <a href="{{ route('settings') }}" wire:navigate
                   class="inline-flex items-center gap-2 px-6 py-3 rounded-xl border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-700
                          text-gray-700 dark:text-gray-200 text-sm font-semibold shadow-sm hover:bg-gray-50 dark:hover:bg-gray-600 hover:text-violet-600 dark:hover:text-violet-400 transition-all active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    Configure Settings
                </a>
            </div>
        </div>

        {{-- Getting started tip if no domains --}}
        @if($totalDomains === 0)
            <div class="bg-violet-50/50 dark:bg-violet-900/20 border border-violet-100 dark:border-violet-800/50 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-violet-100 dark:bg-violet-800 rounded-lg">
                        <svg class="w-6 h-6 text-violet-600 dark:text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-violet-800 dark:text-violet-300">Getting Started</h3>
                </div>
                <ol class="text-sm text-violet-700 dark:text-violet-200 space-y-3 list-decimal list-inside ml-2">
                    <li class="pl-2">Go to <strong class="font-semibold text-violet-900 dark:text-violet-100">Settings</strong> and enter your Moz API credentials.</li>
                    <li class="pl-2">Add categories and keywords via <code class="bg-violet-100 dark:bg-violet-800/60 text-violet-800 dark:text-violet-300 px-2 py-0.5 rounded-md font-mono text-xs shadow-sm">php artisan tinker</code>.</li>
                    <li class="pl-2">Run <code class="bg-violet-100 dark:bg-violet-800/60 text-violet-800 dark:text-violet-300 px-2 py-0.5 rounded-md font-mono text-xs shadow-sm">php artisan domains:fetch</code> to ingest today's dropped domains.</li>
                    <li class="pl-2">Visit <strong class="font-semibold text-violet-900 dark:text-violet-100">Domains</strong> to search and filter results.</li>
                </ol>
            </div>
        @endif

    </div>
</x-app-layout>
