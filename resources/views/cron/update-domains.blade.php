<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cron Job — AI Domain Finder</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        code, pre { font-family: 'JetBrains Mono', monospace; }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-3xl mx-auto space-y-6">

        {{-- Header Card --}}
        <div class="bg-white rounded-3xl p-8 border border-gray-100 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-violet-600 to-indigo-600 flex items-center justify-center text-white shadow-lg shadow-violet-500/30 shrink-0">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 flex-wrap">
                            <h1 class="text-2xl font-extrabold text-gray-900">Domain Ingestion Cron</h1>
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Job Dispatched
                            </span>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">
                            Triggered for date
                            <code class="text-xs bg-gray-100 px-2 py-0.5 rounded text-violet-700 font-semibold">{{ $date }}</code>
                            at <span class="text-gray-700">{{ \Carbon\Carbon::parse($timestamp)->format('H:i:s T') }}</span>
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('domains.search') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-violet-600 to-indigo-600 text-white text-sm font-semibold hover:from-violet-700 hover:to-indigo-700 transition shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        Search Domains
                    </a>
                    <a href="{{ url()->current() }}"
                       class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 text-sm font-medium hover:bg-gray-50 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                        Run Again
                    </a>
                </div>
            </div>
        </div>

        {{-- Status Banner --}}
        <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-6 flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 flex items-center justify-center shrink-0">
                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div>
                <h2 class="text-base font-bold text-emerald-800">Background Job Dispatched Successfully</h2>
                <p class="mt-1 text-sm text-emerald-700">{{ $message }}</p>
                <p class="mt-2 text-xs text-emerald-600">
                    The heavy-lifting (downloading, parsing ~150k domains, and inserting to database) is now running in the background queue.
                    Check your queue worker logs for progress. The web request returned instantly.
                </p>
            </div>
        </div>

        {{-- Info Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-violet-50 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Target Date</p>
                <p class="text-xl font-extrabold text-gray-900 mt-1">{{ $date }}</p>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-indigo-50 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Active Sources</p>
                <p class="text-xl font-extrabold text-gray-900 mt-1">4 Feeds</p>
                <p class="text-xs text-gray-400 mt-0.5">WhoisFreaks · DomainDataset · Pool · GoDaddy</p>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Response Time</p>
                <p class="text-xl font-extrabold text-gray-900 mt-1">&lt; 1s</p>
                <p class="text-xs text-gray-400 mt-0.5">Job dispatched instantly</p>
            </div>
        </div>

        {{-- Queue Worker Info --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center gap-2">
                <svg class="w-4 h-4 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="text-sm font-bold text-gray-900">Setup & Automation Notes</h3>
            </div>
            <div class="p-6 space-y-5 text-sm text-gray-600">

                <div class="flex gap-3">
                    <span class="w-6 h-6 rounded-full bg-violet-100 text-violet-700 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">1</span>
                    <div>
                        <p class="font-semibold text-gray-800">Ensure the queue worker is running</p>
                        <p class="text-xs text-gray-500 mt-1">Jobs are processed by the Laravel queue. Start the worker with:</p>
                        <div class="bg-gray-900 text-gray-200 rounded-lg p-3 text-xs font-mono mt-2 overflow-x-auto">
                            php artisan queue:work --sleep=3 --tries=3
                        </div>
                    </div>
                </div>

                <div class="flex gap-3">
                    <span class="w-6 h-6 rounded-full bg-violet-100 text-violet-700 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">2</span>
                    <div>
                        <p class="font-semibold text-gray-800">Automate with external cron or cPanel</p>
                        <p class="text-xs text-gray-500 mt-1">Hit this URL once daily to dispatch the ingestion job:</p>
                        <div class="bg-gray-900 text-gray-200 rounded-lg p-3 text-xs font-mono mt-2 overflow-x-auto">
                            curl -s "{{ url('/cron/update-domains') }}?format=json" > /dev/null
                        </div>
                        <p class="text-xs text-gray-400 mt-1.5">
                            The <code class="bg-gray-100 px-1 py-0.5 rounded text-gray-600">?format=json</code> flag returns a clean JSON response for monitoring.
                        </p>
                    </div>
                </div>

                <div class="flex gap-3">
                    <span class="w-6 h-6 rounded-full bg-violet-100 text-violet-700 flex items-center justify-center text-xs font-bold shrink-0 mt-0.5">3</span>
                    <div>
                        <p class="font-semibold text-gray-800">Database auto-pruning</p>
                        <p class="text-xs text-gray-500 mt-1">
                            The <code class="bg-gray-100 px-1 py-0.5 rounded text-gray-600">domains:prune</code> command runs daily at 09:00 via the Laravel scheduler,
                            automatically deleting domains older than 14 days (unless saved by a user).
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </div>
</body>
</html>
