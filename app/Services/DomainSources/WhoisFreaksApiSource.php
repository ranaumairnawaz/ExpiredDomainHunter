<?php

namespace App\Services\DomainSources;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * WhoisFreaksApiSource
 *
 * Uses the paid WhoisFreaks "Expiring and Dropped Domains" API.
 * Provides ~400,000+ domains per day with WHOIS metadata.
 *
 * Requires a WhoisFreaks API key stored in the settings table
 * under the key: whoisfreaks_api_key
 *
 * API docs: https://whoisfreaks.com/products/dropped-domains.html
 * Pricing: from $59/month
 *
 * This source is only active when an API key is configured.
 */
class WhoisFreaksApiSource implements DomainSourceInterface
{
    protected const ENDPOINT = 'https://api.whoisfreaks.com/v1.0/domain/dropped';
    protected const PAGE_SIZE = 100;

    protected string $apiKey = '';

    public function __construct()
    {
        $this->apiKey = Setting::where('key', 'whoisfreaks_api_key')->value('value') ?? '';
    }

    public function label(): string
    {
        return 'WhoisFreaks API (paid, ~400k/day)';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function stream(string $date): \Generator
    {
        if (!$this->isConfigured()) {
            Log::warning('WhoisFreaksApiSource: No API key configured. Skipping.');
            return;
        }

        $page = 1;

        while (true) {
            $response = Http::timeout(30)->get(self::ENDPOINT, [
                'apiKey'   => $this->apiKey,
                'date'     => $date,
                'pageNo'   => $page,
                'pageSize' => self::PAGE_SIZE,
            ]);

            if ($response->failed()) {
                Log::error('WhoisFreaksApiSource: API request failed', [
                    'status' => $response->status(),
                    'page'   => $page,
                    'body'   => substr($response->body(), 0, 500),
                ]);
                break;
            }

            $data    = $response->json();
            $domains = $data['dropped_domains'] ?? $data['domains'] ?? [];

            if (empty($domains)) {
                break; // No more pages
            }

            foreach ($domains as $item) {
                $domain = is_array($item)
                    ? strtolower(trim($item['domain_name'] ?? $item['domain'] ?? '', '.'))
                    : strtolower(trim($item, '.'));

                if (!empty($domain)) {
                    yield $domain;
                }
            }

            // Check if there are more pages
            $totalPages = $data['total_pages'] ?? $data['totalPages'] ?? 1;
            if ($page >= $totalPages) {
                break;
            }

            $page++;
        }
    }
}
