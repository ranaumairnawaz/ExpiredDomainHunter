<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SavedSearch extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'filters', 'email_alert', 'active', 'last_alerted_at'];

    protected $casts = [
        'filters'         => 'array',
        'email_alert'     => 'boolean',
        'active'          => 'boolean',
        'last_alerted_at' => 'datetime',
    ];

    /**
     * Apply the saved search filters to an Eloquent query builder.
     */
    public function applyFilters(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        $f = $this->filters;

        if (!empty($f['tld']))         $query->where('tld', $f['tld']);
        if (!empty($f['category_id'])) $query->where('category_id', $f['category_id']);
        if (!empty($f['keyword']))     $query->where('name', 'like', '%' . $f['keyword'] . '%');
        if (!empty($f['min_da']))      $query->where('da', '>=', $f['min_da']);
        if (!empty($f['max_spam']))    $query->where('spam_score', '<=', $f['max_spam']);
        if (!empty($f['max_length']))  $query->where('length', '<=', $f['max_length']);
        if (!empty($f['no_hyphen']))   $query->where('has_hyphen', false);
        if (!empty($f['no_numbers']))  $query->where('has_numbers', false);

        return $query;
    }
}
