<?php

namespace Tests\Feature;

use App\Livewire\DomainSearch;
use App\Livewire\ImportDomains;
use App\Models\Domain;
use App\Models\User;
use App\Services\DomainSources\WhoisFreaksSource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DomainFetchAndImportTest extends TestCase
{
    use RefreshDatabase;

    public function test_whoisfreaks_source_can_be_instantiated_and_labeled(): void
    {
        $source = new WhoisFreaksSource();
        $this->assertNotEmpty($source->label());
        $this->assertStringContainsString('WhoisFreaks', $source->label());
    }

    public function test_domains_fetch_dry_run_command(): void
    {
        $this->artisan('domains:fetch', [
            '--source'  => 'whoisfreaks',
            '--all'     => true,
            '--dry-run' => true,
        ])->assertExitCode(0);
    }

    public function test_import_domains_via_text(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(ImportDomains::class)
            ->set('rawText', "alpha-test-import-domain.com\nbeta-test-import-domain.org")
            ->call('importText')
            ->assertHasNoErrors()
            ->assertSet('importedCount', 2);

        $this->assertDatabaseHas('domains', [
            'name' => 'alpha-test-import-domain.com',
            'tld'  => 'com',
        ]);
        $this->assertDatabaseHas('domains', [
            'name' => 'beta-test-import-domain.org',
            'tld'  => 'org',
        ]);
    }

    public function test_domain_search_filters(): void
    {
        Domain::firstOrCreate(
            ['name' => 'unique-search-sample.co.uk'],
            [
                'tld'            => 'co.uk',
                'length'         => 20,
                'has_hyphen'     => true,
                'has_numbers'    => false,
                'metrics_status' => 'pending',
                'dropped_on'     => now()->toDateString(),
            ]
        );

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(DomainSearch::class)
            ->set('keyword', 'unique-search-sample')
            ->assertSee('unique-search-sample.co.uk');
    }

    public function test_cron_latest_domains_web_route(): void
    {
        // Test JSON response
        $response = $this->get('/cron/latest-domains?format=json');
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'date',
            'duration_secs',
            'total_scanned',
            'total_ingested',
            'total_in_db',
            'sources' => [
                'whoisfreaks',
                'domaindataset',
                'pool',
            ],
            'timestamp',
        ]);

        // Test HTML response
        $htmlResponse = $this->get('/cron/latest-domains/');
        $htmlResponse->assertStatus(200);
        $htmlResponse->assertSee('Expired Domains Cron Job');
    }
}
