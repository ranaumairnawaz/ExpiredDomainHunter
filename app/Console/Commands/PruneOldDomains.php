<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PruneOldDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'domains:prune {--dry-run : Only show how many would be deleted}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete domains older than 14 days unless they are saved by a user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $cutoffDate = now()->subDays(14)->toDateString();
        $dryRun = $this->option('dry-run');

        $query = \App\Models\Domain::where('dropped_on', '<', $cutoffDate)
            ->doesntHave('users');

        $count = $query->count();

        if ($dryRun) {
            $this->info("Dry run: {$count} domains older than {$cutoffDate} would be deleted.");
            return;
        }

        $query->delete();
        $this->info("Successfully deleted {$count} old domains.");
    }
}
