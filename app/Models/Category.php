<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'keywords', 'color', 'active'];

    protected $casts = [
        'keywords' => 'array',
        'active'   => 'boolean',
    ];

    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    /**
     * Check if a domain name string matches any keyword in this category.
     */
    public function matchesDomain(string $domainName): bool
    {
        $domainLower = strtolower($domainName);
        foreach ($this->keywords as $keyword) {
            if (str_contains($domainLower, strtolower($keyword))) {
                return true;
            }
        }
        return false;
    }
}
