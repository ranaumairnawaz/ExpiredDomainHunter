<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * MozService
 *
 * Fetches Domain Authority (DA) and Spam Score for a batch of domains
 * using the Moz Links API v2.
 *
 * Credentials are read from the `settings` table so they can be updated
 * through the Settings UI without touching .env or redeploying.
 *
 * Moz API docs: https://moz.com/help/links-api
 */
class MozService
{
    protected string $accessId  = '';
    protected string $secretKey = '';

    /** Moz Links API v2 batch endpoint */
    protected const ENDPOINT = 'https://lsapi.seomoz.com/v2/url_metrics';

    public function __construct()
    {
        $this->accessId  = Setting::where('key', 'moz_access_id')->value('value') ?? '';
        $this->secretKey = Setting::where('key', 'moz_api_key')->value('value') ?? '';
    }

    /**
     * Returns true if credentials are configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->accessId) && !empty($this->secretKey);
    }

    /**
     * Fetch metrics for up to 25 domains at once.
     *
     * @param  string[]  $domains  e.g. ['vanremoval.co.uk', 'junkgone.com']
     * @return array<string, array{da: int|null, spam_score: int|null}>
     */
    public function fetchBatch(array $domains): array
    {
        if (!$this->isConfigured()) {
            Log::warning('MozService: API credentials not configured.');
            return [];
        }

        // Moz accepts a max of 25 targets per request
        $domains = array_slice($domains, 0, 25);

        $payload = [
            'targets' => $domains,
            'metrics' => ['domain_authority', 'spam_score'],
        ];

        $response = Http::withBasicAuth($this->accessId, $this->secretKey)
            ->timeout(30)
            ->post(self::ENDPOINT, $payload);

        if ($response->failed()) {
            Log::error('MozService: API request failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return [];
        }

        $results = [];
        foreach ($response->json('results', []) as $item) {
            $domain = $item['target'] ?? null;
            if ($domain) {
                $results[$domain] = [
                    'da'         => isset($item['domain_authority']) ? (int) $item['domain_authority'] : null,
                    'spam_score' => isset($item['spam_score'])       ? (int) $item['spam_score']       : null,
                ];
            }
        }

        return $results;
    }
}
