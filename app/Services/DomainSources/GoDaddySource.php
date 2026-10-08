<?php

namespace App\Services\DomainSources;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GoDaddySource implements DomainSourceInterface
{
    protected const API_URL = 'https://api.godaddy.com/v1';
    
    protected string $apiKey = '';
    protected string $apiSecret = '';

    public function __construct()
    {
        $this->apiKey = Setting::where('key', 'godaddy_api_key')->value('value') ?? env('GODADDY_API_KEY', '');
        $this->apiSecret = Setting::where('key', 'godaddy_api_secret')->value('value') ?? env('GODADDY_API_SECRET', '');
    }

    public function label(): string
    {
        return 'GoDaddy API (Aftermarket/Expired)';
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey) && !empty($this->apiSecret);
    }

    /**
     * Check if a specific domain is available for registration
     * Uses GoDaddy API if configured, otherwise falls back to ICANN RDAP.
     */
    public function checkAvailability(string $domain): bool
    {
        if ($this->isConfigured()) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => "sso-key {$this->apiKey}:{$this->apiSecret}"
                ])->timeout(10)->get(self::API_URL . "/domains/available", [
                    'domain' => $domain
                ]);

                if ($response->successful()) {
                    return $response->json('available') === true;
                }
                
                Log::warning("GoDaddy Check Availability Failed: " . $response->body());
            } catch (\Exception $e) {
                Log::error("GoDaddy Check Availability Exception: " . $e->getMessage());
            }
        }

        // Open RDAP fallback (free, no API key required)
        try {
            $rdapResponse = Http::timeout(6)->withHeaders([
                'User-Agent' => 'AIDomainFinder/1.0',
            ])->get("https://rdap.org/domain/" . urlencode($domain));

            if ($rdapResponse->status() === 404) {
                return true; // Not found in registry = Available
            } elseif ($rdapResponse->status() === 200) {
                return false; // Found in registry = Registered / Taken
            }
        } catch (\Exception $e) {
            Log::info("RDAP Check exception for {$domain}: " . $e->getMessage());
        }

        return false;
    }

    /**
     * Stream domains from GoDaddy Aftermarket / Auctions
     */
    public function stream(string $date): \Generator
    {
        if (!$this->isConfigured()) {
            Log::warning('GoDaddySource: API keys not configured. Skipping.');
            return;
        }

        // Note: GoDaddy's aftermarket API is highly restricted and paginated.
        // This is a reference implementation for fetching expiring/auction domains.
        $page = 1;

        while (true) {
            $response = Http::withHeaders([
                'Authorization' => "sso-key {$this->apiKey}:{$this->apiSecret}",
                'Accept' => 'application/json'
            ])->timeout(30)->get(self::API_URL . '/aftermarket/listings', [
                'limit' => 100,
                'offset' => ($page - 1) * 100,
                'status' => 'ACTIVE'
            ]);

            if ($response->failed()) {
                Log::error('GoDaddySource: API request failed', [
                    'status' => $response->status(),
                    'body'   => substr($response->body(), 0, 500),
                ]);
                break;
            }

            $data = $response->json();
            $listings = $data['listings'] ?? $data ?? [];

            if (empty($listings)) {
                break;
            }

            foreach ($listings as $item) {
                $domain = strtolower(trim($item['domainName'] ?? $item['domain'] ?? '', '.'));
                
                if (!empty($domain)) {
                    yield $domain;
                }
            }

            // If we received fewer items than the limit, we're at the end
            if (count($listings) < 100 || $page >= 10) { // Limit to 10 pages for safety
                break;
            }

            $page++;
        }
    }
}
