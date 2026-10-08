<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Domain;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class ImportDomains extends Component
{
    use WithFileUploads;

    public $file;
    public string $rawText = '';
    public string $categoryId = '';
    public bool $autoDetectCategory = true;
    public string $successMessage = '';
    public string $errorMessage = '';
    public int $importedCount = 0;

    public function importFile(): void
    {
        $this->validate([
            'file' => 'required|file|mimes:txt,csv|max:20480', // max 20MB
        ]);

        $path = $this->file->getRealPath();
        $this->processStream(fopen($path, 'r'));
    }

    public function importText(): void
    {
        $this->validate([
            'rawText' => 'required|string|min:3',
        ]);

        $stream = fopen('php://memory', 'r+');
        fwrite($stream, $this->rawText);
        rewind($stream);

        $this->processStream($stream);
    }

    protected function processStream($handle): void
    {
        if (!$handle) {
            $this->errorMessage = 'Could not read domain input stream.';
            return;
        }

        $categories = Category::where('active', true)->get();
        $selectedCatId = !empty($this->categoryId) ? (int)$this->categoryId : null;

        $buffer = [];
        $count = 0;
        $seen = [];

        while (!feof($handle)) {
            $line = trim(fgets($handle, 4096));

            if (empty($line) || str_starts_with($line, '#') || str_starts_with($line, ';')) {
                continue;
            }

            // If CSV or tab-delimited, grab first column
            if (str_contains($line, ',')) {
                $parts = str_getcsv($line);
                $raw = $parts[0] ?? '';
            } else {
                $parts = preg_split('/[\s\t]+/', $line, 2);
                $raw = $parts[0] ?? '';
            }

            $domain = strtolower(trim($raw, " \t\n\r\0\x0B."));

            // Skip header if line has "domain" and no dot
            if (stripos($domain, 'domain') !== false && !str_contains($domain, '.')) {
                continue;
            }

            if (!$this->isValidDomain($domain) || isset($seen[$domain])) {
                continue;
            }

            $seen[$domain] = true;

            // Determine category
            $catId = $selectedCatId;
            if ($catId === null && $this->autoDetectCategory && $categories->isNotEmpty()) {
                foreach ($categories as $cat) {
                    if ($cat->matchesDomain($domain)) {
                        $catId = $cat->id;
                        break;
                    }
                }
            }

            $dotPos = strpos($domain, '.');
            $sld    = $dotPos !== false ? substr($domain, 0, $dotPos) : $domain;
            $tld    = $dotPos !== false ? substr($domain, $dotPos + 1) : '';

            $buffer[] = [
                'name'           => $domain,
                'tld'            => $tld,
                'length'         => strlen($sld),
                'has_hyphen'     => str_contains($sld, '-'),
                'has_numbers'    => (bool) preg_match('/\d/', $sld),
                'category_id'    => $catId,
                'dropped_on'     => now()->toDateString(),
                'metrics_status' => 'pending',
                'created_at'     => now(),
                'updated_at'     => now(),
            ];

            $count++;

            if (count($buffer) >= 500) {
                Domain::upsert(
                    $buffer,
                    ['name'],
                    ['tld', 'length', 'has_hyphen', 'has_numbers', 'category_id', 'dropped_on', 'updated_at']
                );
                $buffer = [];
            }
        }

        if (!empty($buffer)) {
            Domain::upsert(
                $buffer,
                ['name'],
                ['tld', 'length', 'has_hyphen', 'has_numbers', 'category_id', 'dropped_on', 'updated_at']
            );
        }

        fclose($handle);

        $this->importedCount = $count;
        $this->successMessage = "Successfully imported {$count} domains!";
        $this->reset(['file', 'rawText']);
    }

    protected function isValidDomain(string $domain): bool
    {
        return (bool) preg_match('/^[a-z0-9][a-z0-9\-\.]{1,253}[a-z0-9]$/', $domain)
            && substr_count($domain, '.') >= 1;
    }

    public function render()
    {
        return view('livewire.import-domains', [
            'categories' => Category::where('active', true)->get(),
        ])->title('Import Expired Domains – AI Domain Finder');
    }
}
