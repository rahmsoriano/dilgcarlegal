<?php

namespace App\Http\Controllers;

use App\Exceptions\AiRequestException;
use App\Models\AiRequest;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\GeminiChatClient;
use App\Services\LawRetriever;
use App\Services\OpenAiChatClient;
use App\Services\OpinionRetriever;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function store(Request $request, Conversation $conversation, OpenAiChatClient $client, OpinionRetriever $retriever, LawRetriever $lawRetriever)
    {
        abort_unless($conversation->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'prompt' => ['required', 'string', 'max:8000'],
        ]);

        $prompt = trim($validated['prompt']);

        $userMessage = Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $prompt,
        ]);

        if ($conversation->title === null) {
            $titleSeed = preg_replace('/\\s+/', ' ', $prompt);
            $conversation->update([
                'title' => Str::limit(is_string($titleSeed) ? $titleSeed : $prompt, 60, ''),
            ]);
        }

        $conversation->update(['last_message_at' => now()]);

        $systemPrompt = (string) env('CHAT_SYSTEM_PROMPT', '');

        $history = $conversation->messages()
            ->orderByDesc('id')
            ->limit(20)
            ->get()
            ->reverse()
            ->values();

        $aiMessages = [];
        $preface = $systemPrompt;

        $opinions = $retriever->retrieve($prompt, 5);
        if (count($opinions) > 0) {
            $lines = [];
            foreach ($opinions as $op) {
                $line = '- '.$op['title'];
                if ($op['reference']) {
                    $line .= ' ('.$op['reference'].')';
                }
                if ($op['date']) {
                    $line .= ' ['.$op['date'].']';
                }
                $line .= ': '.$op['snippet'];
                $lines[] = $line;
            }
            $context = "Use the following DILG Opinions as primary references. Prefer direct citations with titles and reference numbers. If unsure, say so.\n\n".implode("\n", $lines);
            $preface = trim($preface) !== '' ? ($preface."\n\n".$context) : $context;
        }

        $laws = $lawRetriever->retrieve($prompt, 3);
        if (count($laws) > 0) {
            $lawLines = [];
            foreach ($laws as $law) {
                $lawLines[] = "- {$law['title']} ({$law['number']}, {$law['year']}): " . Str::limit($law['full_text'], 2000);
            }
            $lawContext = "Also consider the following Law documents from our library:\n\n" . implode("\n", $lawLines);
            $preface = trim($preface) !== '' ? ($preface . "\n\n" . $lawContext) : $lawContext;
        }

        if ($preface !== '') {
            $aiMessages[] = ['role' => 'system', 'content' => $preface];
        }

        foreach ($history as $message) {
            $aiMessages[] = [
                'role' => $message->role,
                'content' => $message->content,
            ];
        }

        $requestLog = AiRequest::create([
            'user_id' => $request->user()->id,
            'conversation_id' => $conversation->id,
            'provider' => 'openai',
            'model' => config('services.openai.model'),
            'status' => 'pending',
        ]);

        $startedAt = hrtime(true);

        try {
            $useGemini = (string) config('services.gemini.api_key') !== '';
            if ($useGemini) {
                $gemini = app(GeminiChatClient::class);
                $result = $gemini->chat($aiMessages);
            } else {
                $result = $client->chat($aiMessages);
            }
        } catch (AiRequestException $e) {
            $durationMs = (int) ((hrtime(true) - $startedAt) / 1_000_000);
            $requestLog->update([
                'status' => 'error',
                'http_status' => $e->httpStatus,
                'error_type' => $e->errorType,
                'error_code' => $e->errorCode,
                'duration_ms' => $durationMs,
            ]);

            return response()->json([
                'message' => $e->getMessage(),
            ], $e->httpStatus && $e->httpStatus >= 400 ? $e->httpStatus : 500);
        }

        $durationMs = (int) ((hrtime(true) - $startedAt) / 1_000_000);
        $usage = is_array($result['usage'] ?? null) ? $result['usage'] : [];

        $assistantContent = trim((string) $result['content']);

        $assistantMessage = Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $assistantContent,
            'model' => is_string($result['model'] ?? null) ? $result['model'] : (string) config('services.openai.model'),
            'prompt_tokens' => is_numeric($usage['prompt_tokens'] ?? null) ? (int) $usage['prompt_tokens'] : null,
            'completion_tokens' => is_numeric($usage['completion_tokens'] ?? null) ? (int) $usage['completion_tokens'] : null,
            'total_tokens' => is_numeric($usage['total_tokens'] ?? null) ? (int) $usage['total_tokens'] : null,
            'response_meta' => [
                'provider' => 'openai',
            ],
        ]);

        $conversation->update(['last_message_at' => now()]);

        $requestLog->update([
            'status' => 'ok',
            'http_status' => 200,
            'model' => $assistantMessage->model,
            'prompt_tokens' => $assistantMessage->prompt_tokens,
            'completion_tokens' => $assistantMessage->completion_tokens,
            'total_tokens' => $assistantMessage->total_tokens,
            'duration_ms' => $durationMs,
        ]);

        return response()->json([
            'user_message' => [
                'id' => $userMessage->id,
                'role' => $userMessage->role,
                'content' => $userMessage->content,
                'created_at' => $userMessage->created_at?->toIso8601String(),
            ],
            'assistant_message' => [
                'id' => $assistantMessage->id,
                'role' => $assistantMessage->role,
                'content' => $assistantMessage->content,
                'created_at' => $assistantMessage->created_at?->toIso8601String(),
                'model' => $assistantMessage->model,
                'usage' => [
                    'prompt_tokens' => $assistantMessage->prompt_tokens,
                    'completion_tokens' => $assistantMessage->completion_tokens,
                    'total_tokens' => $assistantMessage->total_tokens,
                ],
            ],
        ]);
    }
}
