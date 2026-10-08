<?php

namespace App\Jobs;

use App\Models\Category;
use App\Models\Domain;
use App\Services\DomainSources\DomainDatasetSource;
use App\Services\DomainSources\GoDaddySource;
use App\Services\DomainSources\PoolSource;
use App\Services\DomainSources\WhoisFreaksSource;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class IngestDomainsJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 3600; // 1 hour timeout for massive data processing
    protected string $date;

    /**
     * Create a new job instance.
     */
    public function __construct(string $date)
    {
        $this->date = $date;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $categories = Category::where('active', true)->get();

        $sources = [
            'whoisfreaks'   => new WhoisFreaksSource(),
            'domaindataset' => new DomainDatasetSource(10000), // Ingest up to 10k per hit
            'pool'          => new PoolSource(),
            'godaddy'       => new \App\Services\DomainSources\GoDaddySource(),
        ];

        foreach ($sources as $key => $source) {
            $sourceScanned  = 0;
            $sourceIngested = 0;
            $buffer = [];

            try {
                foreach ($source->stream($this->date) as $rawDomain) {
                    $sourceScanned++;

                    if (!$this->isValidDomain($rawDomain)) {
                        continue;
                    }

                    // Auto-categorize if matches category keyword
                    $categoryId = null;
                    if ($categories->isNotEmpty()) {
                        foreach ($categories as $cat) {
                            if ($cat->matchesDomain($rawDomain)) {
                                $categoryId = $cat->id;
                                break;
                            }
                        }
                    }

                    $parsed = $this->parseDomain($rawDomain);
                    $buffer[] = [
                        'name'           => $rawDomain,
                        'tld'            => $parsed['tld'],
                        'length'         => $parsed['length'],
                        'has_hyphen'     => $parsed['has_hyphen'],
                        'has_numbers'    => $parsed['has_numbers'],
                        'category_id'    => $categoryId,
                        'dropped_on'     => $this->date,
                        'metrics_status' => 'pending',
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];

                    $sourceIngested++;

                    if (count($buffer) >= 500) {
                        Domain::upsert(
                            $buffer,
                            ['name'],
                            ['tld', 'length', 'has_hyphen', 'has_numbers', 'category_id', 'dropped_on', 'updated_at']
                        );
                        $buffer = [];
                    }
                }

                if (!empty($buffer)) {
                    Domain::upsert(
                        $buffer,
                        ['name'],
                        ['tld', 'length', 'has_hyphen', 'has_numbers', 'category_id', 'dropped_on', 'updated_at']
                    );
                }

                Log::info("IngestDomainsJob: Successfully ingested {$sourceIngested} out of {$sourceScanned} from {$key}.");
            } catch (\Throwable $e) {
                Log::error("IngestDomainsJob: Source {$key} failed: " . $e->getMessage());
            }
        }
    }

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
