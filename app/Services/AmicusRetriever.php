<?php

namespace App\Services;

use App\Models\AmicusSection;
use Illuminate\Support\Str;

class AmicusRetriever
{
    public function retrieve(string $query, int $limit = 4): array
    {
        $tokens = $this->tokens($query);
        if ($tokens === []) {
            return [];
        }

        $sections = AmicusSection::query()
            ->latest('updated_at')
            ->limit(200)
            ->get(['id', 'section_title', 'category', 'section_content', 'updated_at']);

        $scored = $sections
            ->map(function (AmicusSection $section) use ($tokens) {
                $title = mb_strtolower((string) $section->section_title);
                $category = mb_strtolower((string) $section->category);
                $content = mb_strtolower((string) $section->section_content);
                $haystack = $title."\n".$category."\n".$content;

                $matches = 0;
                $score = 0;

                foreach ($tokens as $token) {
                    if (! str_contains($haystack, $token)) {
                        continue;
                    }

                    $matches++;
                    $score += 6;

                    if (str_contains($title, $token)) {
                        $score += 8;
                    }

                    if ($category !== '' && str_contains($category, $token)) {
                        $score += 4;
                    }
                }

                if ($matches === 0) {
                    return null;
                }

                $required = count($tokens) <= 2 ? 1 : 2;
                if ($matches < $required) {
                    return null;
                }

                return [
                    'id' => $section->id,
                    'title' => $section->section_title,
                    'category' => $section->category,
                    'content' => trim((string) $section->section_content),
                    'snippet' => Str::limit(preg_replace('/\s+/u', ' ', trim((string) $section->section_content)), 320, '...'),
                    'score' => $score,
                    'matches' => $matches,
                    'updated_sort' => optional($section->updated_at)->timestamp ?? 0,
                ];
            })
            ->filter()
            ->sort(function (array $a, array $b) {
                $scoreCompare = ((int) $b['score']) <=> ((int) $a['score']);
                if ($scoreCompare !== 0) {
                    return $scoreCompare;
                }

                return ((int) $b['updated_sort']) <=> ((int) $a['updated_sort']);
            })
            ->take($limit)
            ->values();

        return $scored->map(function (array $item) {
            unset($item['score'], $item['matches'], $item['updated_sort']);

            return $item;
        })->all();
    }

    private function tokens(string $query): array
    {
        $query = mb_strtolower(trim($query));
        $query = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $query) ?? $query;
        $query = trim(preg_replace('/\s+/u', ' ', $query) ?? $query);
        if ($query === '') {
            return [];
        }

        $stop = [
            'a', 'an', 'and', 'ang', 'are', 'as', 'at', 'ba', 'be', 'by', 'can', 'could', 'do', 'does',
            'for', 'from', 'how', 'i', 'in', 'is', 'it', 'me', 'my', 'ng', 'of', 'on', 'or', 'please',
            'po', 'sa', 'the', 'this', 'to', 'what', 'when', 'where', 'who', 'why', 'with', 'you', 'your',
            'ano', 'sino', 'paano', 'kailan', 'kelan', 'mga', 'legal', 'law', 'rules', 'rule',
        ];

        return collect(preg_split('/\s+/u', $query) ?: [])
            ->map(fn ($token) => trim((string) $token))
            ->filter(fn ($token) => mb_strlen($token) >= 3)
            ->reject(fn ($token) => in_array($token, $stop, true))
            ->unique()
            ->values()
            ->all();
    }
}
