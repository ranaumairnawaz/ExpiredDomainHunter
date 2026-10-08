<?php

namespace App\Jobs;

use App\Models\Domain;
use App\Services\MozService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

/**
 * FetchDomainMetrics
 *
 * Picks up to 25 domains with metrics_status = 'pending',
 * sends them to the Moz API in a single batch call,
 * and saves the returned DA / Spam Score back to the DB.
 *
 * Dispatch this job from a scheduled command or after ingestion.
 *
 *   FetchDomainMetrics::dispatch();
 */
class FetchDomainMetrics implements ShouldQueue
{
    use Queueable;

    /** Number of times the job may be attempted before it is marked failed */
    public int $tries = 3;

    /** Seconds to wait between retries */
    public int $backoff = 60;

    public function __construct()
    {
        $this->onQueue('metrics');
    }

    public function handle(MozService $moz): void
    {
        if (!$moz->isConfigured()) {
            Log::info('FetchDomainMetrics: Moz credentials not set — skipping.');
            return;
        }

        // Fetch a batch of up to 25 pending domains
        $pending = Domain::where('metrics_status', 'pending')
            ->whereNull('da')
            ->limit(25)
            ->pluck('name')
            ->toArray();

        if (empty($pending)) {
            return;
        }

        Log::info('FetchDomainMetrics: Fetching metrics for ' . count($pending) . ' domains.');

        $results = $moz->fetchBatch($pending);

        foreach ($results as $domainName => $metrics) {
            Domain::where('name', $domainName)->update([
                'da'             => $metrics['da'],
                'spam_score'     => $metrics['spam_score'],
                'metrics_status' => 'fetched',
            ]);
        }

        // Mark any domains not returned by Moz as failed so they don't block the queue
        $returned = array_keys($results);
        $failed   = array_diff($pending, $returned);
        if (!empty($failed)) {
            Domain::whereIn('name', $failed)->update(['metrics_status' => 'failed']);
        }

        Log::info('FetchDomainMetrics: Done. Updated: ' . count($results) . ', Failed: ' . count($failed));

        // Dispatch another batch if there are more pending domains
        if (Domain::where('metrics_status', 'pending')->whereNull('da')->exists()) {
            static::dispatch()->delay(now()->addSeconds(2));
        }
    }
}
