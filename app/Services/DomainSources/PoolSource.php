<?php

namespace App\Services\DomainSources;

use App\Models\Setting;
use Illuminate\Support\Facades\Log;

/**
 * PoolSource
 *
 * Streams a domain drop list from pool.com or a configured mirror drop list URL.
 *
 * The URL can be configured in Settings (key: pool_droplist_url).
 * Defaults to https://www.pool.com/Downloads/DropList.txt.
 */
class PoolSource implements DomainSourceInterface
{
    protected const DEFAULT_URL = 'https://www.pool.com/Downloads/DropList.txt';

    protected ?string $customUrl;

    public function __construct(?string $customUrl = null)
    {
        $this->customUrl = $customUrl;
    }

    public function label(): string
    {
        return 'Pool.com / Custom Drop List Feed';
    }

    public function stream(string $date): \Generator
    {
        $url = $this->customUrl;

        if (empty($url) && class_exists(Setting::class)) {
            try {
                $url = Setting::where('key', 'pool_droplist_url')->value('value');
            } catch (\Throwable $e) {
                $url = null;
            }
        }

        $url = !empty($url) ? trim($url) : self::DEFAULT_URL;

        Log::info("PoolSource: Streaming from {$url}");

        $context = stream_context_create([
            'http' => [
                'timeout'         => 30,
                'follow_location' => 1,
                'user_agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AIDomainFinder',
            ],
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ]);

        $stream = @fopen($url, 'r', false, $context);

        if ($stream === false) {
            Log::warning("PoolSource: Could not open stream from {$url}.");
            return;
        }

        // Verify HTTP response status
        if (isset($http_response_header) && is_array($http_response_header)) {
            $statusLine = $http_response_header[0] ?? '';
            if (preg_match('#HTTP/\S+\s+([0-9]{3})#i', $statusLine, $m)) {
                $code = (int) $m[1];
                if ($code >= 400) {
                    fclose($stream);
                    Log::warning("PoolSource: {$url} returned HTTP {$code}. If using Pool.com legacy URL, please update to a custom active drop list mirror in Settings.");
                    return;
                }
            }
        }

        $seen = [];

        while (!feof($stream)) {
            $line = trim(fgets($stream, 4096));

            // Skip comments and empty lines
            if (empty($line) || str_starts_with($line, '#') || str_starts_with($line, ';')) {
                continue;
            }

            // Normalise: some lists include NS records or tab-separated columns
            $parts  = preg_split('/[\s\t,]+/', $line, 2);
            $domain = strtolower(trim($parts[0], " \t\n\r\0\x0B."));

            if (!empty($domain) && !isset($seen[$domain])) {
                $seen[$domain] = true;
                yield $domain;
            }
        }

        fclose($stream);
    }
}
