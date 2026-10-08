<?php

namespace App\Livewire;

use App\Models\Setting;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Settings extends Component
{
    public string $app_name = '';
    public string $moz_access_id = '';
    public string $moz_api_key = '';
    public string $whoisfreaks_api_key = '';
    public string $godaddy_api_key = '';
    public string $godaddy_api_secret = '';
    public string $domain_fetch_source = 'whoisfreaks';
    public string $pool_droplist_url = '';
    public bool $saved = false;
    public string $fetchMessage = '';

    public function mount(): void
    {
        $this->app_name            = Setting::where('key', 'app_name')->value('value') ?? 'AI Domain Finder';
        $this->moz_access_id       = Setting::where('key', 'moz_access_id')->value('value') ?? '';
        $this->moz_api_key         = Setting::where('key', 'moz_api_key')->value('value') ?? '';
        $this->whoisfreaks_api_key = Setting::where('key', 'whoisfreaks_api_key')->value('value') ?? '';
        $this->godaddy_api_key     = Setting::where('key', 'godaddy_api_key')->value('value') ?? env('GODADDY_API_KEY', '');
        $this->godaddy_api_secret  = Setting::where('key', 'godaddy_api_secret')->value('value') ?? env('GODADDY_API_SECRET', '');
        $this->domain_fetch_source = Setting::where('key', 'domain_fetch_source')->value('value') ?? 'whoisfreaks';
        $this->pool_droplist_url   = Setting::where('key', 'pool_droplist_url')->value('value') ?? 'https://www.pool.com/Downloads/DropList.txt';
    }

    public function save(): void
    {
        $this->validate([
            'app_name'            => 'required|string|max:100',
            'moz_access_id'       => 'nullable|string|max:255',
            'moz_api_key'         => 'nullable|string|max:255',
            'whoisfreaks_api_key' => 'nullable|string|max:255',
            'godaddy_api_key'     => 'nullable|string|max:255',
            'godaddy_api_secret'  => 'nullable|string|max:255',
            'domain_fetch_source' => 'required|string|in:whoisfreaks,domaindataset,pool,godaddy,api,all',
            'pool_droplist_url'   => 'nullable|string|max:500',
        ]);

        Setting::updateOrCreate(['key' => 'app_name'],            ['value' => $this->app_name]);
        Setting::updateOrCreate(['key' => 'moz_access_id'],       ['value' => $this->moz_access_id]);
        Setting::updateOrCreate(['key' => 'moz_api_key'],         ['value' => $this->moz_api_key]);
        Setting::updateOrCreate(['key' => 'whoisfreaks_api_key'], ['value' => $this->whoisfreaks_api_key]);
        Setting::updateOrCreate(['key' => 'godaddy_api_key'],     ['value' => $this->godaddy_api_key]);
        Setting::updateOrCreate(['key' => 'godaddy_api_secret'],  ['value' => $this->godaddy_api_secret]);
        Setting::updateOrCreate(['key' => 'domain_fetch_source'], ['value' => $this->domain_fetch_source]);
        Setting::updateOrCreate(['key' => 'pool_droplist_url'],   ['value' => $this->pool_droplist_url]);

        $this->saved = true;
        $this->dispatch('settings-saved');
    }

    public function triggerFetchNow(): void
    {
        try {
            \Illuminate\Support\Facades\Artisan::call('domains:fetch', [
                '--source' => $this->domain_fetch_source,
                '--all'    => true,
            ]);
            $this->fetchMessage = 'Fetch completed! Latest domains have been updated in the database.';
        } catch (\Throwable $e) {
            $this->fetchMessage = 'Fetch error: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.settings')
            ->title('Settings – AI Domain Finder');
    }
}
