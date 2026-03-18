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
        'context',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
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
                ->selectRaw('MATCH(title, opinion_number, context) AGAINST (? IN NATURAL LANGUAGE MODE) AS score', [$term])
                ->whereRaw('MATCH(title, opinion_number, context) AGAINST (? IN NATURAL LANGUAGE MODE)', [$term])
                ->orderByDesc('score');
        }

        return $query
            ->where(function (Builder $q) use ($term) {
                $q->where('title', 'like', '%'.$term.'%')
                    ->orWhere('opinion_number', 'like', '%'.$term.'%')
                    ->orWhere('context', 'like', '%'.$term.'%');
            })
            ->orderByDesc('updated_at');
    }
}
