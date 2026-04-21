<?php

namespace App\Http\Controllers;

use App\Exceptions\AiRequestException;
use App\Models\Conversation;
use App\Models\Message;
use App\Services\FaqResponseMatcher;
use App\Services\GeminiChatClient;
use App\Services\OpenAiChatClient;
use App\Services\OpinionRetriever;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function storePublic(Request $request, int $conversationId, OpinionRetriever $retriever, GeminiChatClient $gemini, OpenAiChatClient $openai, FaqResponseMatcher $faqMatcher)
    {
        $validated = $request->validate([
            'prompt' => ['required', 'string', 'max:8000'],
        ]);

        $prompt = trim($validated['prompt']);

        $rows = (array) $request->session()->get('public_conversations', []);
        $idx = null;
        foreach ($rows as $i => $row) {
            if ((int) ($row['id'] ?? 0) === $conversationId) {
                $idx = $i;
                break;
            }
        }

        abort_unless($idx !== null, 404);

        $nowIso = now()->toIso8601String();

        $conversation = $rows[$idx];
        $conversationMessages = (array) ($conversation['messages'] ?? []);

        $userMessageId = count($conversationMessages) + 1;
        $conversationMessages[] = [
            'id' => $userMessageId,
            'role' => 'user',
            'content' => $prompt,
            'created_at' => $nowIso,
        ];

        if (($conversation['title'] ?? null) === null) {
            $titleSeed = preg_replace('/\\s+/', ' ', $prompt);
            $conversation['title'] = Str::limit(is_string($titleSeed) ? $titleSeed : $prompt, 60, '');
        }

        $conversation['last_message_at'] = $nowIso;

        $history = array_slice($conversationMessages, -10);
        $resp = $this->generateChatbotResponse($prompt, $history, $retriever, $gemini, $openai, $faqMatcher);

        $assistantContent = $resp['content'];
        $assistantModel = $resp['model'];
        $assistantProvider = $resp['provider'];

        $assistantMessageId = count($conversationMessages) + 1;
        $conversationMessages[] = [
            'id' => $assistantMessageId,
            'role' => 'assistant',
            'content' => $assistantContent,
            'created_at' => now()->toIso8601String(),
            'model' => $assistantModel,
            'response_meta' => [
                'provider' => $assistantProvider,
            ],
        ];

        $conversation['messages'] = $conversationMessages;
        $conversation['last_message_at'] = now()->toIso8601String();

        $rows[$idx] = $conversation;
        $request->session()->put('public_conversations', $rows);

        return response()->json([
            'user_message' => [
                'id' => $userMessageId,
                'role' => 'user',
                'content' => $prompt,
                'created_at' => $nowIso,
            ],
            'assistant_message' => [
                'id' => $assistantMessageId,
                'role' => 'assistant',
                'content' => $assistantContent,
                'created_at' => $conversationMessages[array_key_last($conversationMessages)]['created_at'] ?? null,
                'model' => $assistantModel,
                'usage' => [
                    'prompt_tokens' => null,
                    'completion_tokens' => null,
                    'total_tokens' => null,
                ],
            ],
        ]);
    }

    public function store(Request $request, Conversation $conversation, OpinionRetriever $retriever, GeminiChatClient $gemini, OpenAiChatClient $openai, FaqResponseMatcher $faqMatcher)
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

        $historyModels = $conversation->messages()
            ->orderByDesc('id')
            ->limit(10)
            ->get()
            ->reverse()
            ->values();

        $history = [];
        foreach ($historyModels as $m) {
            $history[] = [
                'role' => $m->role,
                'content' => $m->content,
            ];
        }

        $resp = $this->generateChatbotResponse($prompt, $history, $retriever, $gemini, $openai, $faqMatcher);

        $assistantContent = $resp['content'];
        $assistantModel = $resp['model'];
        $assistantProvider = $resp['provider'];

        $assistantMessage = Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $assistantContent,
            'model' => $assistantModel,
            'response_meta' => [
                'provider' => $assistantProvider,
            ],
        ]);

        $conversation->update(['last_message_at' => now()]);

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
                'content' => $assistantContent,
                'created_at' => $assistantMessage->created_at?->toIso8601String(),
                'model' => $assistantModel,
                'usage' => [
                    'prompt_tokens' => null,
                    'completion_tokens' => null,
                    'total_tokens' => null,
                ],
            ],
        ]);
    }

    private function generateChatbotResponse(string $prompt, array $history, OpinionRetriever $retriever, GeminiChatClient $gemini, OpenAiChatClient $openai, FaqResponseMatcher $faqMatcher): array
    {
        $faqMatch = $faqMatcher->findBestMatch($prompt);

        if ($faqMatch) {
            return [
                'content' => (string) $faqMatch->response,
                'model' => 'faq',
                'provider' => 'faq_response_manager',
            ];
        }

        $systemPrompt = trim((string) config('services.chat.system_prompt', ''));
        $isSmallTalk = $this->isSmallTalk($prompt);

        try {
            $opinions = $isSmallTalk ? [] : $retriever->retrieve($prompt, 3);
        } catch (\Throwable $e) {
            $opinions = [];
        }

        if (count($opinions) === 0) {
            $instruction = $systemPrompt !== ''
                ? $systemPrompt
                : 'You are an AI assistant that responds conversationally, like ChatGPT, making interactions friendly and engaging. For general questions not related to a legal opinions library, respond naturally with polite small talk, greetings, or general conversation without mentioning any library. Always be helpful, approachable, and human-like.';

            $messages = [['role' => 'system', 'content' => $instruction]];
            foreach ($history as $m) {
                $messages[] = [
                    'role' => ($m['role'] ?? '') === 'assistant' ? 'assistant' : 'user',
                    'content' => (string) ($m['content'] ?? ''),
                ];
            }

            try {
                $resp = $this->chatWithFallback($messages, $openai, $gemini);
                return [
                    'content' => (string) ($resp['content'] ?? ''),
                    'model' => (string) ($resp['model'] ?? $resp['provider']),
                    'provider' => $resp['provider'],
                ];
            } catch (AiRequestException $e) {
                return [
                    'content' => $this->formatAiErrorReply($e),
                    'model' => 'error',
                    'provider' => 'ai_error',
                ];
            } catch (\Throwable $e) {
                return [
                    'content' => $this->fallbackChatReply($prompt),
                    'model' => 'fallback',
                    'provider' => 'fallback',
                ];
            }
        }

        // Library Logic
        $maxChars = 18000;
        $libraryText = '';
        foreach ($opinions as $op) {
            $block = ($op['title'] ?? '').' — '.($op['opinion_number'] ?? '');
            if (!empty($op['date'])) {
                $block .= ' ('.$op['date'].')';
            }
            $block .= "\n\n".($op['context'] ?? '')."\n\n";

            if (mb_strlen($libraryText.$block) > $maxChars) {
                $remaining = max(0, $maxChars - mb_strlen($libraryText));
                if ($remaining > 0) {
                    $libraryText .= mb_substr($block, 0, $remaining);
                }
                break;
            }
            $libraryText .= $block;
        }

        $libraryInstruction = 'You are an AI assistant for the DILG CAR Legal Opinions Library. 
            STRICT RULE: You must ONLY use the provided LEGAL OPINIONS LIBRARY CONTENT to answer. 
            CRITICAL: If the user asks about a person, event, or legal matter (like "Robredo") and it is NOT in the provided content, you MUST say "I do not have any legal opinions regarding that matter in my library." 
            DO NOT use your internal training data about politics, history, or celebrities. 
            DO NOT invent facts. If the content only has data from 2009, only talk about 2009.
            Keep your response professional and strictly tied to the DILG CAR context.';

        $instruction = $systemPrompt !== '' ? $systemPrompt : $libraryInstruction;

        $userText = "USER MESSAGE:\n".$prompt."\n\nLEGAL OPINIONS LIBRARY CONTENT (SEARCH RESULTS):\n".$libraryText;

        $messages = [['role' => 'system', 'content' => $instruction]];
        // Add history for context even in library mode!
        $historyLimit = 5;
        $recentHistory = array_slice($history, -$historyLimit);
        foreach ($recentHistory as $m) {
            // Avoid duplication of current prompt
            if (($m['role'] ?? '') === 'user' && trim((string) ($m['content'] ?? '')) === trim($prompt)) {
                continue;
            }
            $messages[] = [
                'role' => ($m['role'] ?? '') === 'assistant' ? 'assistant' : 'user',
                'content' => (string) ($m['content'] ?? ''),
            ];
        }
        $messages[] = ['role' => 'user', 'content' => $userText];

        try {
            $resp = $this->chatWithFallback($messages, $openai, $gemini);
            return [
                'content' => (string) ($resp['content'] ?? ''),
                'model' => (string) ($resp['model'] ?? $resp['provider']),
                'provider' => $resp['provider'],
            ];
        } catch (\Throwable $e) {
            $lines = [];
            foreach ($opinions as $op) {
                $line = $op['title'].' — '.$op['opinion_number'];
                if ($op['date']) {
                    $line .= ' ('.$op['date'].')';
                }
                $line .= "\n\n".$op['context'];
                $lines[] = $line;
            }
            return [
                'content' => "Here are the most relevant legal opinions from the system database:\n\n".implode("\n\n", $lines),
                'model' => 'library',
                'provider' => 'library',
            ];
        }
    }

    private function chatWithFallback(array $messages, OpenAiChatClient $openai, GeminiChatClient $gemini, ?string $model = null): array
    {
        $openAiKey = (string) config('services.openai.api_key', '');
        $geminiKey = (string) config('services.gemini.api_key', '');

        $providers = [];
        if ($geminiKey !== '') {
            $providers[] = 'gemini';
        }
        if ($openAiKey !== '') {
            $providers[] = 'openai';
        }

        if (count($providers) === 0) {
            throw new AiRequestException('No AI provider is configured.');
        }

        $lastError = null;

        foreach ($providers as $provider) {
            try {
                $resp = $provider === 'openai'
                    ? $openai->chat($messages, $model)
                    : $gemini->chat($messages, $model);

                $resp['provider'] = $provider;

                return $resp;
            } catch (\Throwable $e) {
                $lastError = $e;
            }
        }

        throw $lastError instanceof \Throwable ? $lastError : new AiRequestException('AI provider error.');
    }

    private function formatAiErrorReply(AiRequestException $e): string
    {
        $message = trim($e->getMessage());
        $status = $e->httpStatus;
        $type = strtolower((string) ($e->errorType ?? ''));
        $lower = strtolower($message);

        if ($status === 429 || str_contains($lower, 'exceeded your current quota') || str_contains($type, 'resource_exhausted')) {
            return "I can’t reply right now because the Gemini API quota is exceeded. Please check your Google AI Studio usage/billing (or wait for the quota to reset), then try again.";
        }

        if ($status === 401 || $status === 403 || str_contains($lower, 'api key') || str_contains($lower, 'permission')) {
            return "I can’t reply right now because the Gemini API key is invalid, restricted, or missing permission. Please re-check the key and its API restrictions, then try again.";
        }

        if ($message !== '') {
            return "I can’t reply right now because the AI provider returned an error: ".$message;
        }

        return "I can’t connect to the AI provider right now. Please try again in a moment.";
    }

    private function isSmallTalk(string $text): bool
    {
        $t = trim($text);
        if ($t === '') {
            return true;
        }

        if (mb_strlen($t) > 120) {
            return false;
        }

        return (bool) preg_match(
            '/^(hi|hello|hey|yo|sup|good\\s*(morning|afternoon|evening|day)|kamusta|kumusta|how\\s+are\\s+you|whats\\s+up|what\\s+is\\s+up|thank\\s*you|thanks|ty|love\\s+you|i\\s+love\\s+you|haha|lol|who\\s+are\\s+you|what\\s+can\\s+you\\s+do)[!.\\s]*$/i',
            $t
        );
    }

    private function fallbackChatReply(string $prompt): string
    {
        $t = mb_strtolower(trim($prompt));

        if ($t === '') {
            return "Hi! What can I help you with?";
        }

        if (preg_match('/\\b(i\\s+love\\s+you|love\\s+you)\\b/i', $prompt)) {
            return "Aww, thank you! I’m here for you—what do you want to talk about?";
        }

        if (preg_match('/\\b(thanks|thank\\s*you|ty)\\b/i', $prompt)) {
            return "You’re welcome! What else can I help with?";
        }

        if ($this->isSmallTalk($prompt)) {
            return "Hi! How can I help you today?";
        }

        return "I’m having trouble connecting right now. Please try again in a moment.";
    }
}
