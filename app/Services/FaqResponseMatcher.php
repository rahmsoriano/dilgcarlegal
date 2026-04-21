<?php

namespace App\Services;

use App\Models\FaqResponse;
use Illuminate\Support\Collection;

class FaqResponseMatcher
{
    public function findBestMatch(string $prompt): ?FaqResponse
    {
        $normalized = FaqResponse::normalizeInquiry($prompt);
        if ($normalized === '') {
            return null;
        }

        $tokens = $this->tokens($normalized)
            ->sortByDesc(fn ($t) => mb_strlen($t))
            ->values()
            ->take(6)
            ->values();

        $query = FaqResponse::query()
            ->select(['id', 'inquiry', 'inquiry_normalized', 'response']);

        $query->where('inquiry_normalized', $normalized)
            ->orWhere('inquiry_normalized', 'like', '%'.$normalized.'%');

        if ($tokens->isNotEmpty()) {
            $query->orWhere(function ($q) use ($tokens) {
                foreach ($tokens as $t) {
                    $q->orWhere('inquiry_normalized', 'like', '%'.$t.'%');
                }
            });
        }

        $candidates = $query->limit(60)->get();
        if ($candidates->isEmpty()) {
            return null;
        }

        $best = null;
        $bestScore = 0.0;

        foreach ($candidates as $candidate) {
            $score = $this->score($normalized, $tokens, (string) $candidate->inquiry_normalized);
            if ($score > $bestScore) {
                $bestScore = $score;
                $best = $candidate;
            }
        }

        if ($best === null) {
            return null;
        }

        if ($bestScore >= 0.70) {
            return $best;
        }

        $bestTokens = $this->tokens((string) $best->inquiry_normalized);
        $intersection = $tokens->intersect($bestTokens)->count();
        if ($intersection >= 2 && $bestScore >= 0.50) {
            return $best;
        }

        if ($bestScore >= 0.58) {
            return $best;
        }

        return null;
    }

    private function score(string $promptNorm, Collection $promptTokens, string $candidateNorm): float
    {
        $candidateNorm = FaqResponse::normalizeInquiry($candidateNorm);
        if ($candidateNorm === '') {
            return 0.0;
        }

        if ($promptNorm === $candidateNorm) {
            return 1.0;
        }

        $containsScore = 0.0;
        if (str_contains($promptNorm, $candidateNorm) || str_contains($candidateNorm, $promptNorm)) {
            $short = min(mb_strlen($promptNorm), mb_strlen($candidateNorm));
            $long = max(mb_strlen($promptNorm), mb_strlen($candidateNorm));
            $ratio = $long > 0 ? ($short / $long) : 0;
            $containsScore = 0.82 + (0.18 * $ratio);
        }

        $candidateTokens = $this->tokens($candidateNorm);
        if ($promptTokens->isEmpty() || $candidateTokens->isEmpty()) {
            return $containsScore;
        }

        $intersection = $promptTokens->intersect($candidateTokens)->count();
        $union = $promptTokens->merge($candidateTokens)->unique()->count();
        $jaccard = $union > 0 ? ($intersection / $union) : 0.0;

        $weightedOverlap = min(1.0, $jaccard * 1.15);

        return max($containsScore, $weightedOverlap);
    }

    private function tokens(string $normalized): Collection
    {
        $raw = preg_split('/\s+/u', trim($normalized)) ?: [];
        $stop = [
            'a', 'an', 'and', 'are', 'as', 'at', 'be', 'but', 'by', 'can', 'could', 'do', 'does', 'for', 'from',
            'how', 'i', 'in', 'is', 'it', 'me', 'my', 'of', 'on', 'or', 'please', 'the', 'this', 'to', 'what',
            'when', 'where', 'who', 'why', 'with', 'you', 'your', 'yung', 'ng', 'sa', 'po', 'paano', 'ano', 'saan',
            'kailan', 'bakit', 'sino',
        ];

        return collect($raw)
            ->map(fn ($t) => trim((string) $t))
            ->filter(fn ($t) => $t !== '' && mb_strlen($t) >= 3)
            ->reject(fn ($t) => in_array($t, $stop, true))
            ->unique()
            ->values();
    }
}

