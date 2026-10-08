<?php

namespace App\Services\DomainSources;

use Illuminate\Support\Facades\Log;
use ZipArchive;

/**
 * DomainDatasetSource
 *
 * Scrapes the latest daily removed domain ZIP archives directly from
 * DomainDataset.com/removed-domains, downloads the archives, extracts the
 * plain-text domain lists on the fly, and yields each domain.
 *
 * No authentication required. Covers top TLDs (.com, .shop, .xyz, .online, etc.).
 */
class DomainDatasetSource implements DomainSourceInterface
{
    protected const OVERVIEW_URL = 'https://domaindataset.com/removed-domains';
    protected const BASE_DOMAIN  = 'https://domaindataset.com';

    /**
     * Max domains to yield per execution from DomainDataset to keep runtimes fast.
     */
    protected int $maxDomains;

    public function __construct(int $maxDomains = 20000)
    {
        $this->maxDomains = $maxDomains;
    }

    public function label(): string
    {
        return 'DomainDataset.com (Daily Removed Lists, ZIP/TXT)';
    }

    public function stream(string $date): \Generator
    {
        Log::info('DomainDatasetSource: Fetching removed-domains overview from ' . self::OVERVIEW_URL);

        $context = stream_context_create([
            'http' => [
                'timeout'         => 20,
                'follow_location' => 1,
                'user_agent'      => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36 AIDomainFinder',
            ],
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false,
            ],
        ]);

        $html = @file_get_contents(self::OVERVIEW_URL, false, $context);

        if ($html === false) {
            Log::warning('DomainDatasetSource: Failed to fetch overview page.');
            return;
        }

        // Match /downloads/{hash}/dailyremove/{tld}
        preg_match_all('#/downloads/[a-f0-9]+/dailyremove/([a-z0-9]+)#i', $html, $matches, PREG_SET_ORDER);

        if (empty($matches)) {
            Log::warning('DomainDatasetSource: No download links found on overview page.');
            return;
        }

        Log::info('DomainDatasetSource: Found ' . count($matches) . ' TLD download links on DomainDataset.');

        $yieldedCount = 0;
        $seen = [];

        foreach ($matches as $match) {
            $relativeUrl = $match[0];
            $tld         = $match[1];
            $downloadUrl = self::BASE_DOMAIN . $relativeUrl;

            Log::info("DomainDatasetSource: Downloading ZIP for .{$tld} from {$downloadUrl}");

            $tempZip = tempnam(sys_get_temp_dir(), 'dd_') . '.zip';
            $zipData = @file_get_contents($downloadUrl, false, $context);

            if ($zipData === false) {
                @unlink($tempZip);
                continue;
            }

            file_put_contents($tempZip, $zipData);
            unset($zipData);

            $zip = new ZipArchive();
            if ($zip->open($tempZip) === true) {
                for ($i = 0; $i < $zip->numFiles; $i++) {
                    $entryName = $zip->getNameIndex($i);
                    $fp = $zip->getStream($entryName);

                    if (!$fp) {
                        continue;
                    }

                    while (!feof($fp)) {
                        $line = trim(fgets($fp, 4096));

                        if (empty($line) || str_starts_with($line, '#') || str_starts_with($line, ';')) {
                            continue;
                        }

                        $parts  = preg_split('/[\s\t,]+/', $line, 2);
                        $domain = strtolower(trim($parts[0], " \t\n\r\0\x0B."));

                        if (!empty($domain) && !isset($seen[$domain])) {
                            $seen[$domain] = true;
                            $yieldedCount++;
                            yield $domain;

                            if ($yieldedCount >= $this->maxDomains) {
                                fclose($fp);
                                $zip->close();
                                @unlink($tempZip);
                                return;
                            }
                        }
                    }

                    fclose($fp);
                }

                $zip->close();
            }

            @unlink($tempZip);

            if ($yieldedCount >= $this->maxDomains) {
                break;
            }
        }
    }
}
