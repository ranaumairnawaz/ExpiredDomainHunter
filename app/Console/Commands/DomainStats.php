<?php

namespace App\Console\Commands;

use App\Models\Domain;
use Illuminate\Console\Command;

class DomainStats extends Command
{
    protected $signature = 'domains:stats';
    protected $description = 'Show summary of domains in the database';

    public function handle(): int
    {
        $total = Domain::count();
        $this->info("Total Domains: {$total}");

        $this->table(
            ['ID', 'Name', 'TLD', 'Length', 'Category', 'DA', 'Spam Score', 'Status'],
            Domain::with('category')->latest()->take(10)->get()->map(fn($d) => [
                $d->id,
                $d->name,
                $d->tld,
                $d->length,
                $d->category?->name ?? 'Unassigned',
                $d->da ?? '—',
                $d->spam_score ?? '—',
                $d->metrics_status,
            ])
        );

        return self::SUCCESS;
    }
}
