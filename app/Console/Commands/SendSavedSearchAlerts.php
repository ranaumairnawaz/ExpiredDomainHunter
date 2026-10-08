<?php

namespace App\Console\Commands;

use App\Models\Domain;
use App\Models\SavedSearch;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendSavedSearchAlerts extends Command
{
    protected $signature   = 'domains:alerts';
    protected $description = 'Check saved searches and email the admin when new matching domains are found.';

    public function handle(): int
    {
        $adminEmail = config('mail.from.address');

        if (empty($adminEmail) || $adminEmail === 'hello@example.com') {
            $this->warn('No admin email configured. Set MAIL_FROM_ADDRESS in .env');
            return self::SUCCESS;
        }

        $searches = SavedSearch::where('active', true)
            ->where('email_alert', true)
            ->get();

        if ($searches->isEmpty()) {
            $this->info('No active email-alert searches found.');
            return self::SUCCESS;
        }

        $sent = 0;

        foreach ($searches as $search) {
            // Only consider domains dropped since the last alert
            $since = $search->last_alerted_at ?? now()->subDay();

            $query = Domain::query()->where('dropped_on', '>=', $since->toDateString());
            $query = $search->applyFilters($query);

            $matches = $query->with('category')->limit(50)->get();

            if ($matches->isEmpty()) {
                continue;
            }

            // Build a simple plain-text email body
            $lines   = ["New expired domain matches for saved search: \"{$search->name}\"\n"];
            $lines[] = str_repeat('-', 60);
            foreach ($matches as $d) {
                $da    = $d->da    !== null ? "DA:{$d->da}"   : 'DA:—';
                $spam  = $d->spam_score !== null ? "Spam:{$d->spam_score}" : 'Spam:—';
                $lines[] = "  {$d->name}  [{$da}] [{$spam}]  {$d->waybackUrl()}";
            }
            $lines[] = "\nTotal matches: " . $matches->count();
            $body    = implode("\n", $lines);

            Mail::raw($body, function ($msg) use ($adminEmail, $search) {
                $msg->to($adminEmail)
                    ->subject("[AI Domain Finder] New matches for \"{$search->name}\"");
            });

            $search->update(['last_alerted_at' => now()]);
            $sent++;
            $this->info("Alert sent for: {$search->name} ({$matches->count()} matches)");
        }

        $this->info("Done. {$sent} alert(s) sent.");
        return self::SUCCESS;
    }
}
