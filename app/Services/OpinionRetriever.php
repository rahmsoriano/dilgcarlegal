<?php

namespace App\Services;

use App\Models\LegalOpinionLibrary;
use Illuminate\Support\Str;

class OpinionRetriever
{
    public function retrieve(string $query, int $limit = 5): array
    {
        $results = LegalOpinionLibrary::query()
            ->search($query)
            ->limit($limit)
            ->get(['id', 'title', 'opinion_number', 'date', 'context']);

        $items = [];

        foreach ($results as $op) {
            $snippet = Str::limit(preg_replace('/\\s+/', ' ', $this->firstParagraph((string) $op->context)), 280, '…');
            $items[] = [
                'id' => $op->id,
                'title' => $op->title,
                'opinion_number' => $op->opinion_number,
                'date' => optional($op->date)->format('Y-m-d'),
                'snippet' => $snippet,
                'url' => route('admin.opinions.show', $op),
            ];
        }

        return $items;
    }

    protected function firstParagraph(string $text): string
    {
        $text = trim($text);
        $parts = preg_split('/\\n\\s*\\n/', $text);

        return $parts[0] ?? $text;
    }
}
