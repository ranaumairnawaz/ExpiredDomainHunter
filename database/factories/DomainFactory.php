<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Domain factory for generating realistic fake expired domains in development.
 *
 * Usage:
 *   Domain::factory()->count(200)->create();
 *   Domain::factory()->withMetrics()->count(50)->create();
 *   Domain::factory()->pending()->count(50)->create();
 */
class DomainFactory extends Factory
{
    /** Domain name components for realistic fake domains */
    protected array $niches = [
        'removal', 'removals', 'vanremoval', 'clearout', 'clears',
        'cleaning', 'cleanpro', 'maidservice', 'sparkle', 'shiny',
        'plumber', 'plumbing', 'drain', 'boilerpro', 'heating',
        'electrician', 'electricpro', 'sparks', 'rewire', 'fuse',
        'roofer', 'roofing', 'tilemaster', 'roofpro', 'covering',
        'garden', 'gardenpro', 'lawn', 'hedge', 'landscaper',
        'skiphire', 'wastepro', 'rubbish', 'junk', 'recycler',
        'builder', 'buildco', 'handyman', 'fixpro', 'repairpro',
    ];

    protected array $tlds = [
        'com', 'co.uk', 'net', 'org', 'io',
        'co', 'uk', 'co.uk', 'com', 'co.uk',
    ];

    public function definition(): array
    {
        $niche = $this->faker->randomElement($this->niches);
        $city  = strtolower($this->faker->city());
        $city  = preg_replace('/[^a-z0-9]/', '', $city);
        $sld   = $niche . $city;
        $tld   = $this->faker->randomElement($this->tlds);
        $name  = $sld . '.' . $tld;

        return [
            'name'           => $name,
            'tld'            => $tld,
            'length'         => strlen($sld),
            'has_hyphen'     => str_contains($sld, '-'),
            'has_numbers'    => (bool) preg_match('/\d/', $sld),
            'category_id'    => null,
            'dropped_on'     => $this->faker->dateTimeBetween('-90 days', 'now')->format('Y-m-d'),
            'metrics_status' => 'pending',
            'da'             => null,
            'spam_score'     => null,
            'backlinks'      => null,
        ];
    }

    /**
     * Domain with fully fetched Moz metrics.
     */
    public function withMetrics(): static
    {
        return $this->state(fn () => [
            'metrics_status' => 'fetched',
            'da'             => $this->faker->numberBetween(1, 75),
            'spam_score'     => $this->faker->numberBetween(0, 17),
            'backlinks'      => $this->faker->numberBetween(0, 50000),
        ]);
    }

    /**
     * Domain still awaiting metrics.
     */
    public function pending(): static
    {
        return $this->state(fn () => [
            'metrics_status' => 'pending',
            'da'             => null,
            'spam_score'     => null,
        ]);
    }

    /**
     * Domain with a high Domain Authority (useful for filter testing).
     */
    public function highDa(): static
    {
        return $this->state(fn () => [
            'metrics_status' => 'fetched',
            'da'             => $this->faker->numberBetween(40, 90),
            'spam_score'     => $this->faker->numberBetween(0, 5),
        ]);
    }
}
