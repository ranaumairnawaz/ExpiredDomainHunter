<?php

use App\Livewire\DomainSearch;
use App\Livewire\ImportDomains;
use App\Livewire\Settings;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])->group(function () {
    Route::get('domains', DomainSearch::class)->name('domains.search');
    Route::get('domains/import', ImportDomains::class)->name('domains.import');
    Route::get('settings', Settings::class)->name('settings');
});

// ── Web Cron Endpoints (Shared Hosting Friendly) ───────────────────────────
Route::get('cron/update-domains', [\App\Http\Controllers\CronController::class, 'fetchLatestDomains'])
    ->name('cron.update-domains');
Route::get('cron/update-domains/', [\App\Http\Controllers\CronController::class, 'fetchLatestDomains']);
Route::get('cron/schedule-run', [\App\Http\Controllers\CronController::class, 'runScheduler'])
    ->name('cron.schedule-run');
Route::get('cron/queue-work', [\App\Http\Controllers\CronController::class, 'runQueue'])
    ->name('cron.queue-work');

require __DIR__.'/auth.php';
