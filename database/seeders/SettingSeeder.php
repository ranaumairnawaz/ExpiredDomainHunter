<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            ['key' => 'app_name',             'value' => 'AI Domain Finder'],
            ['key' => 'moz_api_key',           'value' => ''],
            ['key' => 'moz_access_id',         'value' => ''],
            // WhoisFreaks paid API (optional - for ~400k domains/day)
            ['key' => 'whoisfreaks_api_key',   'value' => ''],
            // Domain ingestion source: whoisfreaks | domaindataset | pool | api | all
            ['key' => 'domain_fetch_source',   'value' => 'whoisfreaks'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], ['value' => $setting['value']]);
        }
    }
}