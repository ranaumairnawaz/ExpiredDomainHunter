<?php

namespace App\Services\DomainSources;

use Illuminate\Support\Facades\Log;

/**
 * WhoisFreaksSource
 *
 * Downloads the free daily expired/dropped domain list published by WhoisFreaks
 * on their public GitHub repository (no authentication required).
 *
 * Repository: https://github.com/WhoisFreaks/daily-expired-and-dropped-domains
 *
 * File format: CSV with a header row.
 * Columns: domain_name, (other columns may vary — only column 0 is used)
 *
 * Daily limit: ~10,000 domains (free tier).
 * Updated: 03:00 UTC daily.
 *
 * Raw URL pattern:
 *   https://raw.githubusercontent.com/WhoisFreaks/daily-expired-and-dropped-domains
 *          /main/expired-domains/YYYY-MM-DD.csv
 */
class WhoisFreaksSource implements DomainSourceInterface
{
    protected const RAW_BASE = 'https://raw.githubusercontent.com/WhoisFreaks/daily-expired-and-dropped-domains/main/';

    public function label(): string
    {
        return 'WhoisFreaks GitHub (free, ~10k/day)';
    }

    public function stream(string $date): \Generator
    {
        // Try date-specific first; fall back to the always-available "0-latest" feeds
        $candidates = [
            self::RAW_BASE . "{$date}-free-dropped-domains.csv",
            self::RAW_BASE . "{$date}-free-expired-domains.csv",
            self::RAW_BASE . '0-latest-free-dropped-domains.csv',
            self::RAW_BASE . '0-latest-free-expired-domains.csv',
        ];

        $context = stream_context_create([
            'http' => [
                'timeout'         => 60,
                'follow_location' => 1,
                'user_agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AIDomainFinder',
            ],
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ]);

        $streamOpened = false;
        $seen = [];

        foreach ($candidates as $url) {
            $stream = @fopen($url, 'r', false, $context);

            if ($stream === false) {
                continue;
            }

            $streamOpened = true;
            Log::info("WhoisFreaksSource: Streaming from {$url}");

            $firstLine = true;

            while (!feof($stream)) {
                $line = trim(fgets($stream, 4096));

                if (empty($line)) {
                    continue;
                }

                // Skip CSV header if present
                if ($firstLine) {
                    $firstLine = false;
                    if (stripos($line, 'domain') !== false && !str_contains($line, '.')) {
                        continue;
                    }
                }

                $parts  = str_getcsv($line);
                $domain = strtolower(trim($parts[0] ?? '', " \t\n\r\0\x0B."));

                if (!empty($domain) && !isset($seen[$domain])) {
                    $seen[$domain] = true;
                    yield $domain;
                }
            }

            fclose($stream);

            // If we successfully streamed a date-specific or latest file, we have good data
            // We can break after reading dropped + expired pair or if we got sufficient records
            if (count($seen) >= 5000) {
                break;
            }
        }

        if (!$streamOpened) {
            Log::warning("WhoisFreaksSource: Could not open any stream from candidates for date {$date}.");
        }
    }
}
