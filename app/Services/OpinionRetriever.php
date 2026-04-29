<?php

namespace App\Services;

use App\Models\LegalOpinionLibrary;
use App\Models\AiLearnedKnowledge;
use Illuminate\Support\Str;

class OpinionRetriever
{
    /**
     * Search in the AI's learned knowledge first.
     */
    public function findLearnedKnowledge(string $query): ?AiLearnedKnowledge
    {
        // Try exact match or very close full-text match
        return AiLearnedKnowledge::query()
            ->search($query)
            ->first();
    }

    public function retrieve(string $query, int $limit = 5): array
    {
        $query = trim($query);
        $expandedQueries = $this->buildExpandedQueries($query);

        $candidateIds = collect();
        $maxCandidates = max(80, $limit * 20);

        foreach ($expandedQueries as $idx => $q) {
            $q = trim($q);
            if ($q === '') {
                continue;
            }

            $perQueryLimit = $idx === 0 ? max(10, $limit * 3) : max(18, $limit * 6);

            $foundIds = collect();
            try {
                $foundIds = LegalOpinionLibrary::search($q)
                    ->limit($perQueryLimit)
                    ->get()
                    ->pluck('id');
            } catch (\Throwable $e) {
                $foundIds = collect();
            }

            if ($foundIds->isEmpty() && $idx < 6) {
                $foundIds = $this->fallbackLikeSearchIds($q, $perQueryLimit);
            }

            $candidateIds = $candidateIds->merge($foundIds);
        }

        $dedupedIds = $candidateIds
            ->filter(fn ($v) => (string) $v !== '')
            ->map(fn ($v) => (int) $v)
            ->filter(fn ($v) => $v > 0)
            ->unique()
            ->take($maxCandidates)
            ->values();

        $deduped = $dedupedIds->isEmpty()
            ? collect()
            : LegalOpinionLibrary::query()
                ->whereIn('id', $dedupedIds->all())
                ->get(['id', 'title', 'opinion_number', 'date', 'context', 'keywords']);

        $ranked = $deduped
            ->map(function (LegalOpinionLibrary $op) use ($query) {
                return [
                    'op' => $op,
                    'score' => $this->scoreOpinion($op, $query),
                ];
            })
            ->sortByDesc('score')
            ->take($limit)
            ->values();

        $items = [];

        foreach ($ranked as $row) {
            /** @var LegalOpinionLibrary $op */
            $op = $row['op'];
            $context = trim((string) $op->context);
            $snippet = Str::limit(preg_replace('/\\s+/', ' ', $this->firstParagraph($context)), 280, '…');
            $items[] = [
                'id' => $op->id,
                'title' => $op->title,
                'opinion_number' => $op->opinion_number,
                'date' => optional($op->date)->format('Y-m-d'),
                'snippet' => $snippet,
                'context' => $context,
                'url' => route('opinions.public.show', $op),
            ];
        }

        return $items;
    }

    protected function fallbackLikeSearchIds(string $query, int $limit): \Illuminate\Support\Collection
    {
        $terms = preg_split('/\s+/', trim($query)) ?: [];
        $terms = array_values(array_unique(array_filter(array_map(fn ($t) => trim((string) $t), $terms), fn ($t) => mb_strlen($t) > 2)));
        $terms = array_slice($terms, 0, 10);

        $stop = [
            'a', 'an', 'and', 'are', 'as', 'at', 'about', 'be', 'by', 'for', 'from', 'in', 'is', 'it', 'of', 'on', 'or', 'that', 'the', 'this', 'to', 'with',
            'legal', 'opinion', 'opinions', 'dilg',
            'ang', 'mga', 'ng', 'na', 'sa', 'si', 'kay', 'kayo', 'ko', 'ako', 'ito', 'yan', 'dito', 'doon', 'para', 'tungkol', 'patungkol',
        ];

        $filtered = array_values(array_filter($terms, function (string $t) use ($stop) {
            $lt = mb_strtolower($t);
            return !in_array($lt, $stop, true);
        }));

        $contextTerms = $filtered;
        usort($contextTerms, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));
        $contextTerms = array_slice($contextTerms, 0, 3);

        $likeQuery = LegalOpinionLibrary::query();

        $likeQuery->where(function ($q) use ($terms, $contextTerms) {
            foreach ($terms as $t) {
                $q->orWhere('title', 'like', '%'.$t.'%')
                    ->orWhere('opinion_number', 'like', '%'.$t.'%')
                    ->orWhere('keywords', 'like', '%'.$t.'%');
            }
            foreach ($contextTerms as $t) {
                $q->orWhere('context', 'like', '%'.$t.'%');
            }
        });

        return $likeQuery->limit($limit)->pluck('id');
    }

    protected function buildExpandedQueries(string $query): array
    {
        $q = trim(mb_strtolower($query));

        $withoutNumbers = preg_replace('/\b\d+\b/u', ' ', $q) ?? $q;
        $withoutPunct = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $withoutNumbers) ?? $withoutNumbers;
        $normalized = trim(preg_replace('/\s+/u', ' ', $withoutPunct) ?? $withoutPunct);

        $queries = [];

        $hasSk = str_contains($normalized, 'sk') || str_contains($normalized, 'sangguniang kabataan');
        $hasAge = str_contains($normalized, 'age') || str_contains($normalized, 'edad') || str_contains($normalized, 'gulang');
        $hasChair = str_contains($normalized, 'chair') || str_contains($normalized, 'chairperson') || str_contains($normalized, 'chairman');

        if ($hasSk || $hasChair) {
            $queries[] = 'SK Chairperson qualification';
            $queries[] = 'Sangguniang Kabataan Chairperson qualification';
            $queries[] = 'SK Chairperson age requirement';
            $queries[] = 'edad ng SK chairperson';
            $queries[] = 'RA 10742 SK Chairperson';
            $queries[] = 'Katipunan ng Kabataan age';
            $queries[] = 'Katipunan ng Kabataan chairperson age';
            $queries[] = 'Katipunan ng Kabataan Constitution and By-Laws age';
            $queries[] = 'KK age requirement';
        }

        if ($hasAge) {
            $queries[] = 'SK age requirement';
            $queries[] = 'kwalipikasyon edad SK';
            $queries[] = 'COMELEC Resolution No. 4779 Katipunan ng Kabataan age 15 18';
            $queries[] = 'DILG Memorandum Circular 2000-84 Katipunan ng Kabataan registration age';
        }

        $final = [
            $normalized,
            ...$queries,
            $query,
        ];

        return array_values(array_unique(array_filter($final, fn ($v) => trim((string) $v) !== '')));
    }

    protected function scoreOpinion(LegalOpinionLibrary $op, string $userQuery): int
    {
        $haystack = mb_strtolower(
            ($op->title ?? '')."\n".
            ($op->opinion_number ?? '')."\n".
            ($op->keywords ?? '')."\n".
            ($op->context ?? '')
        );

        $score = 0;

        $boost = function (bool $condition, int $points) use (&$score): void {
            if ($condition) {
                $score += $points;
            }
        };

        $boost(str_contains($haystack, 'sangguniang kabataan') || preg_match('/\bsk\b/u', $haystack) === 1, 40);
        $boost(str_contains($haystack, 'chairperson') || str_contains($haystack, 'chairman') || str_contains($haystack, 'punong'), 25);
        $boost(str_contains($haystack, 'qualification') || str_contains($haystack, 'kwalipikasyon') || str_contains($haystack, 'qualified'), 20);
        $boost(str_contains($haystack, 'age') || str_contains($haystack, 'edad') || str_contains($haystack, 'gulang'), 20);
        $boost(str_contains($haystack, 'ra 10742') || str_contains($haystack, '10742') || str_contains($haystack, 'sk reform'), 20);
        $boost(str_contains($haystack, 'katipunan ng kabataan') || preg_match('/\bkk\b/u', $haystack) === 1, 18);
        $boost(str_contains($haystack, 'constitution') || str_contains($haystack, 'by-laws') || str_contains($haystack, 'by laws'), 10);

        $uq = mb_strtolower($userQuery);

        $userAgeNums = [];
        if (preg_match_all('/\b\d{1,2}\b/u', $uq, $m2) === 1) {
            $userAgeNums = array_values(array_unique($m2[0] ?? []));
        }
        $userAsksAge = str_contains($uq, 'age') || str_contains($uq, 'edad') || str_contains($uq, 'gulang') || str_contains($uq, 'years old') || preg_match('/\byear old\b/u', $uq) === 1;

        if ($userAsksAge) {
            $ageRangeSignal =
                str_contains($haystack, '15 years old') ||
                str_contains($haystack, 'less than 18') ||
                str_contains($haystack, 'below 18') ||
                str_contains($haystack, 'not less than 15') ||
                (preg_match('/\b15\b.*\byears?\b/u', $haystack) === 1 && preg_match('/\b18\b.*\byears?\b/u', $haystack) === 1);

            if ($ageRangeSignal) {
                $score += 35;
            }

            if (str_contains($haystack, 'comelec') || str_contains($haystack, 'comelec resolution') || str_contains($haystack, '4779')) {
                $score += 18;
            }

            if (str_contains($haystack, 'katipunan ng kabataan') || preg_match('/\bkk\b/u', $haystack) === 1) {
                $score += 18;
            }
        }

        if (count($userAgeNums) > 0) {
            foreach ($userAgeNums as $num) {
                if ($num === '') {
                    continue;
                }
                if (str_contains($haystack, $num)) {
                    $score += 6;
                }
            }
        }

        $terms = preg_split('/\s+/u', trim($uq)) ?: [];
        foreach ($terms as $t) {
            $t = trim($t);
            if (mb_strlen($t) < 3) {
                continue;
            }
            if (str_contains($haystack, $t)) {
                $score += 2;
            }
        }

        return $score;
    }

    protected function firstParagraph(string $text): string
    {
        $text = trim($text);
        $parts = preg_split('/\\n\\s*\\n/', $text);

        return $parts[0] ?? $text;
    }
}
