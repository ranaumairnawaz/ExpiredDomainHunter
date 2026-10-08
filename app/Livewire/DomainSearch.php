<?php

namespace App\Livewire;

use App\Jobs\FetchDomainMetrics;
use App\Models\Category;
use App\Models\Domain;
use App\Models\SavedSearch;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class DomainSearch extends Component
{
    use WithPagination;

    // ── Filters ────────────────────────────────────────────────────────────
    #[Url(as: 'q')]
    public string $keyword = '';

    #[Url]
    public string $tld = '';

    #[Url]
    public string $category_id = '';

    #[Url]
    public int $min_da = 0;

    #[Url]
    public int $max_spam = 17;

    #[Url]
    public int $max_length = 63;

    #[Url]
    public bool $no_hyphen = false;

    #[Url]
    public bool $no_numbers = false;

    // ── Save Search ────────────────────────────────────────────────────────
    public bool $showSaveModal = false;
    public string $saveName = '';
    public bool $saveEmailAlert = false;
    public bool $searchSaved = false;

    // ── UI ─────────────────────────────────────────────────────────────────
    public string $sortBy    = 'dropped_on';
    public string $sortDir   = 'desc';
    public int    $perPage   = 25;

    public function updatingKeyword(): void  { $this->resetPage(); }
    public function updatingTld(): void      { $this->resetPage(); }
    public function updatingCategoryId(): void { $this->resetPage(); }
    public function updatingMinDa(): void    { $this->resetPage(); }
    public function updatingMaxSpam(): void  { $this->resetPage(); }
    public function updatingMaxLength(): void{ $this->resetPage(); }
    public function updatingNoHyphen(): void { $this->resetPage(); }
    public function updatingNoNumbers(): void{ $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->reset(['keyword', 'tld', 'category_id', 'min_da', 'no_hyphen', 'no_numbers']);
        $this->max_spam   = 17;
        $this->max_length = 63;
        $this->resetPage();
    }

    public function sortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy  = $column;
            $this->sortDir = 'desc';
        }
        $this->resetPage();
    }

    // ── Save Search ─────────────────────────────────────────────────────────
    public function openSaveModal(): void
    {
        $this->showSaveModal = true;
        $this->saveName      = '';
        $this->saveEmailAlert = false;
        $this->searchSaved   = false;
    }

    public function saveSearch(): void
    {
        $this->validate([
            'saveName' => 'required|string|max:100',
        ]);

        SavedSearch::create([
            'name'        => $this->saveName,
            'email_alert' => $this->saveEmailAlert,
            'active'      => true,
            'filters'     => [
                'keyword'     => $this->keyword,
                'tld'         => $this->tld,
                'category_id' => $this->category_id,
                'min_da'      => $this->min_da,
                'max_spam'    => $this->max_spam,
                'max_length'  => $this->max_length,
                'no_hyphen'   => $this->no_hyphen,
                'no_numbers'  => $this->no_numbers,
            ],
        ]);

        $this->searchSaved   = true;
        $this->showSaveModal = false;
    }

    // ── Dispatch metrics fetch ──────────────────────────────────────────────
    public function dispatchMetricsFetch(): void
    {
        FetchDomainMetrics::dispatch();
        session()->flash('metrics-dispatched', true);
    }

    // ── Bulk Actions & Statuses ─────────────────────────────────────────────
    public array $selectedDomains = [];
    public array $availabilityStatuses = [];

    public function exportSelected()
    {
        if (empty($this->selectedDomains)) return;

        $domains = Domain::whereIn('id', $this->selectedDomains)->get();
        
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=domains_export_" . date('Y-m-d_H-i') . ".csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Name', 'TLD', 'Length', 'Category', 'DA', 'Spam Score', 'Dropped On'];

        $callback = function() use($domains, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($domains as $domain) {
                fputcsv($file, [
                    $domain->name,
                    $domain->tld,
                    $domain->length,
                    $domain->category ? $domain->category->name : '',
                    $domain->da,
                    $domain->spam_score,
                    $domain->dropped_on ? $domain->dropped_on->format('Y-m-d') : ''
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function checkAvailability(int $domainId)
    {
        $domain = Domain::find($domainId);
        if (!$domain) return;
        
        try {
            $service = new \App\Services\DomainSources\GoDaddySource();
            $isAvailable = $service->checkAvailability($domain->name);
            $this->availabilityStatuses[$domainId] = $isAvailable ? 'available' : 'taken';
        } catch (\Exception $e) {
            $this->availabilityStatuses[$domainId] = 'error';
        }
    }

    public function toggleSaveDomain(int $domainId)
    {
        $user = auth()->user();
        if (!$user) return;
        
        if ($user->savedDomains()->where('domain_id', $domainId)->exists()) {
            $user->savedDomains()->detach($domainId);
        } else {
            $user->savedDomains()->attach($domainId);
        }
    }

    // ── Render ─────────────────────────────────────────────────────────────
    public function render()
    {
        $query = Domain::query()
            ->with('category')
            ->when($this->keyword,     fn($q) => $q->where('name', 'like', "%{$this->keyword}%"))
            ->when($this->tld,         fn($q) => $q->where('tld', $this->tld))
            ->when($this->category_id, fn($q) => $q->where('category_id', $this->category_id))
            ->when($this->min_da > 0,  fn($q) => $q->where('da', '>=', $this->min_da))
            ->when($this->max_spam < 17, fn($q) => $q->where('spam_score', '<=', $this->max_spam))
            ->when($this->max_length < 63, fn($q) => $q->where('length', '<=', $this->max_length))
            ->when($this->no_hyphen,   fn($q) => $q->where('has_hyphen', false))
            ->when($this->no_numbers,  fn($q) => $q->where('has_numbers', false))
            ->orderBy($this->sortBy, $this->sortDir);

        $tlds       = Domain::select('tld')->distinct()->orderBy('tld')->pluck('tld');
        $categories = Category::where('active', true)->get();

        return view('livewire.domain-search', [
            'domains'    => $query->paginate($this->perPage),
            'tlds'       => $tlds,
            'categories' => $categories,
            'totalCount' => Domain::count(),
            'pendingCount' => Domain::where('metrics_status', 'pending')->count(),
        ])->title('Domain Search – AI Domain Finder');
    }
}
