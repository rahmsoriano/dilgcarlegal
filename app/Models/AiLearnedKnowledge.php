<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiLearnedKnowledge extends Model
{
    use HasFactory;

    protected $table = 'ai_learned_knowledge';

    protected $fillable = [
        'query',
        'response',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'json',
    ];

    /**
     * Scope for full-text search.
     */
    public function scopeSearch($query, string $term)
    {
        return $query->whereRaw("MATCH(query) AGAINST(? IN NATURAL LANGUAGE MODE)", [$term]);
    }
}
