<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class LegalOpinionLibrary extends Model
{
    protected $table = 'legal_opinions_library';

    protected $fillable = [
        'title',
        'opinion_number',
        'opinion_no',
        'opinion_year',
        'keywords',
        'context',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
        'opinion_no' => 'integer',
        'opinion_year' => 'integer',
    ];

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $driver = $this->getConnection()->getDriverName();
        $term = trim($term);

        if ($term === '') {
            return $query;
        }

        if ($driver === 'mysql') {
            return $query
                ->select('*')
                ->selectRaw('MATCH(title, opinion_number, context, keywords) AGAINST (? IN NATURAL LANGUAGE MODE) AS score', [$term])
                ->whereRaw('MATCH(title, opinion_number, context, keywords) AGAINST (? IN NATURAL LANGUAGE MODE)', [$term])
                ->orderByDesc('score');
        }

        return $query
            ->where(function (Builder $q) use ($term) {
                $q->where('title', 'like', '%'.$term.'%')
                    ->orWhere('opinion_number', 'like', '%'.$term.'%')
                    ->orWhere('context', 'like', '%'.$term.'%')
                    ->orWhere('keywords', 'like', '%'.$term.'%');
            })
            ->orderByDesc('updated_at');
    }

    public function scopeSearchAdmin(Builder $query, string $term): Builder
    {
        $term = trim($term);

        if ($term === '') {
            return $query;
        }

        $any = '%' . $term . '%';
        $start = $term . '%';

        // Use a more robust relevance scoring that works even for short terms
        return $query->where(function (Builder $q) use ($any) {
            $q->where('title', 'like', $any)
              ->orWhere('opinion_number', 'like', $any)
              ->orWhere('context', 'like', $any);
        })
        ->select('*')
        ->selectRaw("
            (CASE 
                WHEN title LIKE ? THEN 100
                WHEN title LIKE ? THEN 50
                WHEN opinion_number LIKE ? THEN 30
                ELSE 10
            END) AS relevance_score
        ", [$term, $start, $any]);
    }
}
