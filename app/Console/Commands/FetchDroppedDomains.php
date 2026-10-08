<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Domain;
use App\Services\DomainSources\DomainDatasetSource;
use App\Services\DomainSources\DomainSourceInterface;
use App\Services\DomainSources\PoolSource;
use App\Services\DomainSources\WhoisFreaksApiSource;
use App\Services\DomainSources\WhoisFreaksSource;
use Illuminate\Console\Command;

/**
 * php artisan domains:fetch [options]
 *
 * Downloads the daily list of expired/dropped domains from one or more
 * internet sources and ingests them into the database.
 *
 * Sources (in priority order):
 *  1. whoisfreaks  – Free GitHub CSV, ~10k domains/day (default)
 *  2. domaindataset – Free TXT file,  ~50–100k domains/day
 *  3. pool          – pool.com DropList (legacy fallback)
 *  4. api           – WhoisFreaks paid API (~400k/day, requires API key)
 *  5. all           – Run all sources in sequence
 *
 * Examples:
 *   php artisan domains:fetch
 *   php artisan domains:fetch --source=domaindataset
 *   php artisan domains:fetch --source=all
 *   php artisan domains:fetch --date=2024-01-15
 *   php artisan domains:fetch --dry-run
 *   php artisan domains:fetch --all-domains
 */
class FetchDroppedDomains extends Command
{
    protected $signature = 'domains:fetch
                            {--date=          : The drop date (YYYY-MM-DD). Defaults to today.}
                            {--source= : Source to use: whoisfreaks|domaindataset|pool|api|all (defaults to setting or whoisfreaks)}
                            {--all            : Ingest ALL domains, skipping category filtering.}
                            {--all-domains    : Ingest ALL domains, skipping category filtering (alias for --all).}
                            {--dry-run        : Parse and count matches without writing to DB.}';

    protected $description = 'Fetch expired/dropped domains from the internet and ingest them into the database.';

    /** Chunk size for bulk inserts */
    protected const INSERT_CHUNK = 500;

    public function handle(): int
    {
        $date       = $this->option('date') ?? now()->toDateString();
        $dryRun     = (bool) $this->option('dry-run');
        $allDomains = (bool) $this->option('all') || (bool) $this->option('all-domains');
        $sourceKey  = $this->option('source')
            ?: (\App\Models\Setting::where('key', 'domain_fetch_source')->value('value') ?: 'whoisfreaks');

        $this->info("═══════════════════════════════════════════");
        $this->info("  AI Domain Finder — Expired Domain Fetch");
        $this->info("═══════════════════════════════════════════");
        $this->info("Date:     {$date}");
        $this->info("Source:   {$sourceKey}");
        $this->info("Mode:     " . ($allDomains ? 'Ingest ALL domains' : 'Category-matched only'));
        $this->info("Dry-run:  " . ($dryRun ? 'YES (no DB writes)' : 'NO'));
        $this->newLine();

        $sources = $this->resolveSources($sourceKey);

        if (empty($sources)) {
            $this->error("Unknown source: {$sourceKey}");
            $this->line("Valid values: whoisfreaks, domaindataset, pool, api, all");
            return self::FAILURE;
        }

        // Load active categories once (used for optional keyword matching)
        $categories = Category::where('active', true)->get();

        $totalMatched = 0;
        $totalScanned = 0;

        foreach ($sources as $source) {
            $this->info("▶ Source: " . $source->label());

            [$scanned, $matched] = $this->runSource($source, $date, $dryRun, $allDomains, $categories);

            $totalScanned += $scanned;
            $totalMatched += $matched;

            $this->newLine();
        }

        $this->info("═══════════════════════════════════════════");
        $this->info("  TOTAL: scanned {$totalScanned} | ingested {$totalMatched}");
        $this->info("═══════════════════════════════════════════");

        return self::SUCCESS;
    }

    // ─── Source Resolution ────────────────────────────────────────────────────

    /**
     * @return DomainSourceInterface[]
     */
    protected function resolveSources(string $key): array
    {
        $map = [
            'whoisfreaks'   => [new WhoisFreaksSource()],
            'domaindataset' => [new DomainDatasetSource()],
            'pool'          => [new PoolSource()],
            'godaddy'       => [new \App\Services\DomainSources\GoDaddySource()],
            'api'           => [new WhoisFreaksApiSource()],
            'all'           => [
                new WhoisFreaksSource(),
                new DomainDatasetSource(),
                new PoolSource(),
                new \App\Services\DomainSources\GoDaddySource(),
            ],
        ];

        return $map[$key] ?? [];
    }

    // ─── Run a Single Source ──────────────────────────────────────────────────

    /**
     * @return array{int, int}  [scanned, matched]
     */
    protected function runSource(
        DomainSourceInterface $source,
        string $date,
        bool $dryRun,
        bool $allDomains,
        $categories
    ): array {
        $buffer  = [];
        $matched = 0;
        $scanned = 0;

        $bar = $this->output->createProgressBar();
        $bar->setFormat(' %current% scanned | %elapsed:6s% | ingested: %message%');
        $bar->setMessage('0');
        $bar->start();

        foreach ($source->stream($date) as $rawDomain) {
            $scanned++;

            if (!$this->isValidDomain($rawDomain)) {
                $bar->advance();
                continue;
            }

            // ── Category matching ──────────────────────────────────────────
            $categoryId = null;

            if (!$allDomains && $categories->isNotEmpty()) {
                // Only ingest domains that match a category keyword
                foreach ($categories as $cat) {
                    if ($cat->matchesDomain($rawDomain)) {
                        $categoryId = $cat->id;
                        break;
                    }
                }

                if ($categoryId === null) {
                    // No match — skip
                    $bar->advance();
                    continue;
                }
            } elseif ($categories->isNotEmpty()) {
                // Ingest all, but still auto-assign category if a keyword matches
                foreach ($categories as $cat) {
                    if ($cat->matchesDomain($rawDomain)) {
                        $categoryId = $cat->id;
                        break;
                    }
                }
            }
            // If no categories in DB and --all-domains: $categoryId stays null → OK

            // ── Parse & buffer ─────────────────────────────────────────────
            $parsed   = $this->parseDomain($rawDomain);
            $buffer[] = [
                'name'           => $rawDomain,
                'tld'            => $parsed['tld'],
                'length'         => $parsed['length'],
                'has_hyphen'     => $parsed['has_hyphen'],
                'has_numbers'    => $parsed['has_numbers'],
                'category_id'    => $categoryId,
                'dropped_on'     => $date,
                'metrics_status' => 'pending',
                'created_at'     => now(),
                'updated_at'     => now(),
            ];

            $matched++;
            $bar->setMessage((string) $matched);

            if (!$dryRun && count($buffer) >= self::INSERT_CHUNK) {
                Domain::upsert(
                    $buffer,
                    ['name'],
                    ['tld', 'length', 'has_hyphen', 'has_numbers', 'category_id', 'dropped_on', 'updated_at']
                );
                $buffer = [];
            }

            $bar->advance();
        }

        // Flush remaining buffer
        if (!$dryRun && !empty($buffer)) {
            Domain::upsert(
                $buffer,
                ['name'],
                ['tld', 'length', 'has_hyphen', 'has_numbers', 'category_id', 'dropped_on', 'updated_at']
            );
        }

        $bar->finish();
        $this->newLine();
        $this->info("  ✓ Scanned {$scanned} | Ingested {$matched}" . ($dryRun ? ' (dry-run, not saved)' : ''));

        return [$scanned, $matched];
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    protected function isValidDomain(string $domain): bool
    {
        return (bool) preg_match('/^[a-z0-9][a-z0-9\-\.]{1,253}[a-z0-9]$/', $domain)
            && substr_count($domain, '.') >= 1;
    }

    protected function parseDomain(string $domain): array
    {
        $dotPos = strpos($domain, '.');
        $sld    = $dotPos !== false ? substr($domain, 0, $dotPos) : $domain;
        $tld    = $dotPos !== false ? substr($domain, $dotPos + 1) : '';

        return [
            'tld'         => $tld,
            'length'      => strlen($sld),
            'has_hyphen'  => str_contains($sld, '-'),
            'has_numbers' => (bool) preg_match('/\d/', $sld),
        ];
    }
}
