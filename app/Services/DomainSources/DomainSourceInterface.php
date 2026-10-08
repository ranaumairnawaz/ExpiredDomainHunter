<?php

namespace App\Services\DomainSources;

/**
 * Contract for all expired-domain data sources.
 *
 * Each source must implement a single method that returns a Generator
 * yielding one raw domain string at a time. Using a generator keeps
 * memory usage flat even when processing millions of rows.
 */
interface DomainSourceInterface
{
    /**
     * Stream expired/dropped domain names one-by-one.
     *
     * @param  string  $date  ISO date string (YYYY-MM-DD) for the drop list
     * @return \Generator<string>  Yields raw domain strings (e.g. "vanremoval.co.uk")
     */
    public function stream(string $date): \Generator;

    /**
     * A human-readable label for this source (used in logs and CLI output).
     */
    public function label(): string;
}
