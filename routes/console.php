<?php

use App\Jobs\FetchDomainMetrics;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
|
| domains:fetch  – runs at 07:00 every day, downloads expired domains from
|                  the internet (WhoisFreaks GitHub free list by default).
|                  The --all-domains flag ingests everything regardless of
|                  category, letting the search UI handle filtering.
|
|                  To change the source, update the 'domain_fetch_source'
|                  setting in your admin Settings page, or pass --source=:
|                    whoisfreaks  (default, ~10k/day, free)
|                    domaindataset (~50-100k/day, free)
|                    pool          (legacy fallback)
|                    api           (WhoisFreaks paid API, ~400k/day)
|                    all           (run all sources sequentially)
|
| FetchDomainMetrics::dispatch() – runs 30 minutes after ingestion to kick
|                  off the Moz metrics queue. The job re-dispatches itself
|                  until all pending domains have been processed.
|
| domains:alerts – runs at 08:00 to check saved searches and email the
|                  admin when new matches were found since last alert.
|
*/

Schedule::command('domains:fetch --source=whoisfreaks --all-domains')
    ->dailyAt('07:00')
    ->withoutOverlapping()
    ->runInBackground();

Schedule::command('domains:fetch --source=api --all-domains')
    ->dailyAt('07:15')
    ->withoutOverlapping()
    ->runInBackground()
    ->when(function () {
        return \Illuminate\Support\Facades\Schema::hasTable('settings')
            && !empty(\App\Models\Setting::where('key', 'whoisfreaks_api_key')->value('value'));
    });

Schedule::call(function () {
    FetchDomainMetrics::dispatch();
})->dailyAt('07:30')->name('dispatch-domain-metrics')->withoutOverlapping();

Schedule::command('domains:alerts')
    ->dailyAt('08:00')
    ->withoutOverlapping();

Schedule::command('domains:prune')
    ->dailyAt('09:00')
    ->withoutOverlapping()
    ->runInBackground();

