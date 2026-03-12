<?php

namespace App\Services;

use App\Models\Law;
use Illuminate\Support\Str;

class LawRetriever
{
    public function retrieve(string $query, int $limit = 3): array
    {
        // Simple keyword-based search for now
        $keywords = explode(' ', $query);
        
        $laws = Law::query();

        foreach ($keywords as $keyword) {
            if (strlen($keyword) < 3) continue;
            $laws->where(function($q) use ($keyword) {
                $q->where('title', 'like', "%{$keyword}%")
                  ->orWhere('law_number', 'like', "%{$keyword}%")
                  ->orWhere('content_text', 'like', "%{$keyword}%");
            });
        }

        return $laws->latest()
            ->limit($limit)
            ->get()
            ->map(function ($law) use ($query) {
                return [
                    'title' => $law->title,
                    'number' => $law->law_number,
                    'year' => $law->year,
                    'snippet' => $this->getSnippet($law->content_text, $query),
                    'full_text' => $law->content_text,
                ];
            })
            ->toArray();
    }

    private function getSnippet(?string $text, string $query): string
    {
        if (!$text) return '';

        $query = strtolower($query);
        $textLower = strtolower($text);
        $pos = strpos($textLower, $query);

        if ($pos === false) {
            return Str::limit($text, 300);
        }

        $start = max(0, $pos - 150);
        $snippet = substr($text, $start, 300);
        
        return '...' . $snippet . '...';
    }
}
