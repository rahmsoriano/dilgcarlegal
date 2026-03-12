<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DilgOpinion extends Model
{
    protected $fillable = [
        'title',
        'reference_no',
        'opinion_date',
        'tags',
        'slug',
        'body',
    ];

    protected $casts = [
        'opinion_date' => 'date',
    ];

    public static function booted(): void
    {
        static::creating(function (self $model) {
            if (! $model->slug) {
                $model->slug = Str::slug(Str::limit($model->title, 60, ''));
            }
        });
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $driver = $this->getConnection()->getDriverName();
        $term = trim($term);

        if ($driver === 'mysql') {
            return $query
                ->select('*')
                ->selectRaw('MATCH(title, body) AGAINST (? IN NATURAL LANGUAGE MODE) AS score', [$term])
                ->whereRaw('MATCH(title, body) AGAINST (? IN NATURAL LANGUAGE MODE)', [$term])
                ->orderByDesc('score');
        }

        return $query
            ->where(function (Builder $q) use ($term) {
                $q->where('title', 'like', '%'.$term.'%')
                    ->orWhere('body', 'like', '%'.$term.'%');
            })
            ->orderByDesc('updated_at');
    }
}
