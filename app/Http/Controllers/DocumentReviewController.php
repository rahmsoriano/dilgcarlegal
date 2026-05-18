<?php

namespace App\Http\Controllers;

use App\Exceptions\AiRequestException;
use App\Models\LegalOpinionLibrary;
use App\Services\DocumentTextExtractor;
use App\Services\GeminiChatClient;
use App\Services\GroqChatClient;
use App\Services\OpenAiChatClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DocumentReviewController extends Controller
{
    public function storePublic(Request $request, DocumentTextExtractor $extractor, OpenAiChatClient $openai, GeminiChatClient $gemini, GroqChatClient $groq): JsonResponse
    {
        return $this->storeReview($request, $extractor, $openai, $gemini, $groq);
    }

    public function store(Request $request, DocumentTextExtractor $extractor, OpenAiChatClient $openai, GeminiChatClient $gemini, GroqChatClient $groq): JsonResponse
    {
        return $this->storeReview($request, $extractor, $openai, $gemini, $groq);
    }

    private function storeReview(Request $request, DocumentTextExtractor $extractor, OpenAiChatClient $openai, GeminiChatClient $gemini, GroqChatClient $groq): JsonResponse
    {
        $validated = $request->validate([
            'document' => ['nullable', 'file', 'max:20480'],
            'opinion_id' => ['nullable', 'integer', 'exists:legal_opinions_library,id'],
            'focus' => ['nullable', 'string', 'max:500'],
        ]);

        if (! $request->hasFile('document') && empty($validated['opinion_id'])) {
            return response()->json([
                'message' => 'Please upload a document or choose an existing legal opinion to review.',
            ], 422);
        }

        try {
            $sourceTitle = '';
            $text = '';

            if ($request->hasFile('document')) {
                $document = $request->file('document');
                $extracted = $extractor->extractFromUpload($document);
                $sourceTitle = (string) $document->getClientOriginalName();
                $text = $extracted['text'];
            } else {
                $opinion = LegalOpinionLibrary::query()->findOrFail((int) $validated['opinion_id']);
                $sourceTitle = trim($opinion->title.' '.$opinion->opinion_number);
                $text = $extractor->normalizeText((string) $opinion->context);
            }

            if ($text === '') {
                return response()->json([
                    'message' => 'The selected document did not contain readable text for review.',
                ], 422);
            }

            $review = $this->buildReview($text, (string) ($validated['focus'] ?? ''), $openai, $gemini, $groq);

            return response()->json([
                'title' => $sourceTitle !== '' ? $sourceTitle : 'Document review',
                'source_excerpt' => Str::limit($text, 700, '...'),
                'review' => $review,
            ]);
        } catch (\RuntimeException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 422);
        } catch (\Throwable $e) {
            Log::error('Document review failed.', [
                'message' => $e->getMessage(),
                'exception' => get_class($e),
            ]);

            return response()->json([
                'message' => 'Unable to review the selected document right now.',
            ], 500);
        }
    }

    private function buildReview(string $text, string $focus, OpenAiChatClient $openai, GeminiChatClient $gemini, GroqChatClient $groq): string
    {
        $prompt = "Review the following document and return a clean, readable summary.\n".
            "Required format:\n".
            "Overview:\n".
            "- 2 to 4 short bullet points\n\n".
            "Key Details:\n".
            "- Important names, offices, dates, obligations, and legal points when present\n\n".
            "Practical Notes:\n".
            "- Any missing information, ambiguity, or follow-up needed\n\n".
            "Keep the wording clear and neutral. Preserve line breaks. Do not use markdown tables.\n";

        if ($focus !== '') {
            $prompt .= "Focus area: {$focus}\n\n";
        }

        $messages = [
            ['role' => 'system', 'content' => $prompt],
            ['role' => 'user', 'content' => "Document text:\n".$text],
        ];

        try {
            $resp = $this->chatWithFallback($messages, $openai, $gemini, $groq);

            return $this->formatReviewText((string) ($resp['content'] ?? ''));
        } catch (\Throwable $e) {
            return $this->fallbackReview($text, $focus);
        }
    }

    private function formatReviewText(string $text): string
    {
        $text = str_replace(["\r\n", "\r"], "\n", trim($text));
        $text = preg_replace('/^\s*\*\s+/m', '- ', $text) ?? $text;
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;

        return trim($text);
    }

    private function fallbackReview(string $text, string $focus): string
    {
        $paragraphs = preg_split("/\n{2,}/", $text) ?: [];
        $paragraphs = array_values(array_filter(array_map(static fn ($item) => trim((string) $item), $paragraphs), static fn ($item) => $item !== ''));
        $overview = array_slice($paragraphs, 0, 2);
        $lines = preg_split("/\n/u", $text) ?: [];
        $keyLines = [];

        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }

            if (preg_match('/\b(section|article|dated|office|director|memorandum|opinion|resolution|ordinance|barangay|municipal|city|province)\b/i', $line) === 1) {
                $keyLines[] = $line;
            }

            if (count($keyLines) >= 4) {
                break;
            }
        }

        $review = "Overview:\n";
        foreach ($overview !== [] ? $overview : [Str::limit($text, 280, '...')] as $item) {
            $review .= '- '.Str::limit($item, 240, '...')."\n";
        }

        $review .= "\nKey Details:\n";
        foreach ($keyLines !== [] ? $keyLines : [Str::limit($text, 320, '...')] as $item) {
            $review .= '- '.Str::limit($item, 220, '...')."\n";
        }

        $review .= "\nPractical Notes:\n";
        $review .= '- This is an automated document review summary. Verify important legal conclusions against the full text before relying on it.'."\n";
        if ($focus !== '') {
            $review .= '- Requested focus: '.$focus.".\n";
        }

        return trim($review);
    }

    private function chatWithFallback(array $messages, OpenAiChatClient $openai, GeminiChatClient $gemini, GroqChatClient $groq): array
    {
        $openAiKey = (string) config('services.openai.api_key', '');
        $geminiKey = (string) config('services.gemini.api_key', '');
        $groqKey = (string) config('services.groq.api_key', '');
        $providers = [];

        if ($groqKey !== '' && $groqKey !== 'your_groq_api_key_here') {
            $providers[] = 'groq';
        }
        if ($geminiKey !== '') {
            $providers[] = 'gemini';
        }
        if ($openAiKey !== '') {
            $providers[] = 'openai';
        }

        if ($providers === []) {
            throw new AiRequestException('No AI provider is configured.');
        }

        $lastError = null;
        foreach ($providers as $provider) {
            try {
                if ($provider === 'groq') {
                    return $groq->chat($messages);
                }

                if ($provider === 'gemini') {
                    return $gemini->chat($messages);
                }

                return $openai->chat($messages);
            } catch (\Throwable $e) {
                $lastError = $e;
            }
        }

        throw $lastError instanceof \Throwable ? $lastError : new AiRequestException('AI provider error.');
    }
}
