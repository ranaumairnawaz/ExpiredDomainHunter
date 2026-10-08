<?php

namespace App\Models;

use Database\Factories\DomainFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Domain extends Model
{
    use HasFactory;

    protected static function newFactory(): DomainFactory
    {
        return DomainFactory::new();
    }

    protected $fillable = [
        'name',
        'tld',
        'length',
        'has_hyphen',
        'has_numbers',
        'category_id',
        'da',
        'spam_score',
        'backlinks',
        'dropped_on',
        'metrics_status',
    ];

    protected $casts = [
        'has_hyphen'  => 'boolean',
        'has_numbers' => 'boolean',
        'dropped_on'  => 'date',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function users(): \Illuminate\Database\Eloquent\Relations\BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Return the Wayback Machine preview URL for this domain.
     */
    public function waybackUrl(): string
    {
        return "https://web.archive.org/web/*/{$this->name}";
    }
}
