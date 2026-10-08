<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Domain;
use Illuminate\Database\Seeder;

/**
 * DomainSeeder
 *
 * Seeds the database with realistic fake expired domains for local development
 * and testing. Runs after CategorySeeder so categories exist to be assigned.
 *
 * Run with:
 *   php artisan db:seed --class=DomainSeeder
 *
 * Or as part of the full seed:
 *   php artisan db:seed
 */
class DomainSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('Seeding fake domains for development...');

        $categories = Category::where('active', true)->get();

        // 150 domains with metrics fetched (variety of DAs)
        Domain::factory()
            ->count(100)
            ->withMetrics()
            ->create()
            ->each(function ($domain) use ($categories) {
                if ($categories->isNotEmpty() && rand(0, 1)) {
                    $cat = $categories->random();
                    $domain->update(['category_id' => $cat->id]);
                }
            });

        // 50 high-DA domains (great for filter testing)
        Domain::factory()
            ->count(50)
            ->highDa()
            ->create()
            ->each(function ($domain) use ($categories) {
                if ($categories->isNotEmpty()) {
                    $cat = $categories->random();
                    $domain->update(['category_id' => $cat->id]);
                }
            });

        // 50 pending domains (awaiting metrics)
        Domain::factory()
            ->count(50)
            ->pending()
            ->create();

        $total = Domain::count();
        $this->command->info("✓ Done! {$total} domains in the database.");
    }
}
