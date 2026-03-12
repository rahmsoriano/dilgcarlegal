<?php

namespace App\Services;

use App\Models\DilgOpinion;
use Illuminate\Support\Str;

class OpinionRetriever
{
    public function retrieve(string $query, int $limit = 5): array
    {
        $results = DilgOpinion::query()
            ->search($query)
            ->limit($limit)
            ->get(['id', 'title', 'reference_no', 'opinion_date', 'slug', 'body']);

        $items = [];

        foreach ($results as $op) {
            $snippet = Str::limit(preg_replace('/\\s+/', ' ', $this->firstParagraph((string) $op->body)), 280, '…');
            $items[] = [
                'id' => $op->id,
                'title' => $op->title,
                'reference' => $op->reference_no,
                'date' => optional($op->opinion_date)->format('Y-m-d'),
                'slug' => $op->slug,
                'snippet' => $snippet,
                'url' => route('admin.opinions.index').'#opinion-'.$op->id,
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
