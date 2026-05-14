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

        $promptTokens = $this->tokens($normalized);
        if ($promptTokens->isEmpty()) {
            return null;
        }

        $faqs = FaqResponse::query()
            ->select(['id', 'inquiry', 'inquiry_normalized', 'aliases', 'response'])
            ->get();

        $best = null;
        $bestScore = 0.0;

        foreach ($faqs as $faq) {
            foreach ($faq->comparableQuestions() as $candidateQuestion) {
                $candidateNormalized = FaqResponse::normalizeInquiry((string) $candidateQuestion);
                if ($candidateNormalized === '') {
                    continue;
                }

                $score = $this->score($normalized, $promptTokens, $candidateNormalized);
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $best = $faq;
                }

                if ($score >= 0.995) {
                    return $faq;
                }
            }
        }

        if ($best === null) {
            return null;
        }

        if ($bestScore >= 0.72) {
            return $best;
        }

        return null;
    }

    private function score(string $promptNorm, Collection $promptTokens, string $candidateNorm): float
    {
        if ($promptNorm === $candidateNorm) {
            return 1.0;
        }

        $candidateTokens = $this->tokens($candidateNorm);
        if ($candidateTokens->isEmpty()) {
            return 0.0;
        }

        $intersection = $promptTokens->intersect($candidateTokens)->count();
        $promptCount = max(1, $promptTokens->count());
        $candidateCount = max(1, $candidateTokens->count());
        $coveragePrompt = $intersection / $promptCount;
        $coverageCandidate = $intersection / $candidateCount;
        $tokenScore = (($coveragePrompt * 0.6) + ($coverageCandidate * 0.4));

        $containsScore = 0.0;
        if (str_contains($promptNorm, $candidateNorm) || str_contains($candidateNorm, $promptNorm)) {
            $short = min(mb_strlen($promptNorm), mb_strlen($candidateNorm));
            $long = max(1, mb_strlen($promptNorm), mb_strlen($candidateNorm));
            $containsScore = 0.84 + (($short / $long) * 0.12);
        }

        similar_text($promptNorm, $candidateNorm, $percent);
        $similarityScore = $percent / 100;

        $orderedBonus = $this->orderedTokenBonus($promptTokens, $candidateTokens);

        return max($containsScore, min(1.0, ($tokenScore * 0.55) + ($similarityScore * 0.35) + ($orderedBonus * 0.10)));
    }

    private function orderedTokenBonus(Collection $promptTokens, Collection $candidateTokens): float
    {
        $promptValues = $promptTokens->values()->all();
        $candidateValues = $candidateTokens->values()->all();
        $matched = 0;
        $candidateIndex = 0;

        foreach ($promptValues as $token) {
            while ($candidateIndex < count($candidateValues)) {
                if ($candidateValues[$candidateIndex] === $token) {
                    $matched++;
                    $candidateIndex++;
                    continue 2;
                }

                $candidateIndex++;
            }
        }

        return count($promptValues) > 0 ? ($matched / count($promptValues)) : 0.0;
    }

    private function tokens(string $normalized): Collection
    {
        $raw = preg_split('/\s+/u', trim($normalized)) ?: [];
        $stop = [
            'a', 'an', 'and', 'ang', 'are', 'as', 'at', 'ba', 'be', 'but', 'by', 'can', 'could', 'current',
            'do', 'does', 'for', 'from', 'how', 'i', 'in', 'is', 'it', 'me', 'my', 'ng', 'of', 'on', 'or',
            'please', 'po', 'sa', 'the', 'this', 'to', 'what', 'when', 'where', 'who', 'why', 'with', 'you',
            'your', 'yung',
        ];

        return collect($raw)
            ->map(fn ($token) => trim((string) $token))
            ->filter(fn ($token) => $token !== '')
            ->map(fn ($token) => str_replace('_', ' ', $token))
            ->filter(fn ($token) => mb_strlen($token) >= 3)
            ->reject(fn ($token) => in_array($token, $stop, true))
            ->unique()
            ->values();
    }
}
