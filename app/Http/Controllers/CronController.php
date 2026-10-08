<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CronController extends Controller
{
    /**
     * Triggered via web route: GET /cron/update-domains/
     *
     * Fetches expired domains from all three sources:
     *  1. WhoisFreaks GitHub
     *  2. DomainDataset.com
     *  3. Pool.com / Configured Drop List Feed
     */
    public function fetchLatestDomains(Request $request)
    {
        $secret = env('CRON_SECRET');
        if (!empty($secret) && $request->query('token') !== $secret) {
            abort(403, 'Unauthorized cron access.');
        }

        $date = $request->query('date', now()->toDateString());
        
        if ($request->boolean('sync')) {
            @set_time_limit(300);
            \App\Jobs\IngestDomainsJob::dispatchSync($date);
            $message = 'Domain ingestion job executed synchronously.';
        } else {
            \App\Jobs\IngestDomainsJob::dispatch($date);
            $message = 'Domain ingestion job has been dispatched to the background.';
        }

        $data = [
            'status'    => 'success',
            'message'   => $message,
            'date'      => $date,
            'timestamp' => now()->toIso8601String(),
        ];

        if ($request->wantsJson() || $request->query('format') === 'json') {
            return response()->json($data);
        }

        return view('cron.update-domains', $data);
    }

    /**
     * Web trigger for the Laravel scheduler.
     * Useful on shared hosting where CLI cron is unavailable.
     */
    public function runScheduler(Request $request)
    {
        $secret = env('CRON_SECRET');
        if (!empty($secret) && $request->query('token') !== $secret) {
            abort(403, 'Unauthorized cron access.');
        }

        \Illuminate\Support\Facades\Artisan::call('schedule:run');
        $output = \Illuminate\Support\Facades\Artisan::output();

        return response()->json([
            'status'    => 'success',
            'message'   => 'Laravel scheduler executed successfully.',
            'output'    => trim($output),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Web trigger to process background jobs.
     * Useful on shared hosting without background daemons.
     */
    public function runQueue(Request $request)
    {
        $secret = env('CRON_SECRET');
        if (!empty($secret) && $request->query('token') !== $secret) {
            abort(403, 'Unauthorized cron access.');
        }

        @set_time_limit(60);
        \Illuminate\Support\Facades\Artisan::call('queue:work', [
            '--stop-when-empty' => true,
            '--max-time'        => 45,
            '--tries'           => 3,
        ]);
        $output = \Illuminate\Support\Facades\Artisan::output();

        return response()->json([
            'status'    => 'success',
            'message'   => 'Queue processed successfully.',
            'output'    => trim($output),
            'timestamp' => now()->toIso8601String(),
        ]);
    }

}
