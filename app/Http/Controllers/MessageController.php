<?php

namespace App\Http\Controllers;

use App\Exceptions\AiRequestException;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\AiLearnedKnowledge;
use App\Models\LegalOpinionLibrary;
use App\Services\FaqResponseMatcher;
use App\Services\GeminiChatClient;
use App\Services\OpenAiChatClient;
use App\Services\GroqChatClient;
use App\Services\OpinionRetriever;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function storePublic(Request $request, int $conversationId, OpinionRetriever $retriever, GeminiChatClient $gemini, OpenAiChatClient $openai, GroqChatClient $groq, FaqResponseMatcher $faqMatcher)
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(120);
        }

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
            $rows[$idx]['title'] = Str::limit(is_string($titleSeed) ? $titleSeed : $prompt, 60, '');
        }

        $rows[$idx]['last_message_at'] = $nowIso;

        $history = [];
        foreach (array_slice($conversationMessages, -10) as $m) {
            $history[] = [
                'role' => $m['role'],
                'content' => $m['content'],
            ];
        }

        $resp = $this->generateChatbotResponse($prompt, $history, $retriever, $gemini, $openai, $groq, $faqMatcher);

        $assistantContent = $resp['content'];
        $assistantModel = $resp['model'];
        $assistantProvider = $resp['provider'];

        // Learning Process: Store the response if it's from an AI provider
        if (in_array($assistantProvider, ['groq', 'gemini', 'openai'])) {
            try {
                AiLearnedKnowledge::create([
                    'query' => $prompt,
                    'response' => $assistantContent,
                    'metadata' => [
                        'provider' => $assistantProvider,
                        'model' => $assistantModel,
                        'learned_at' => now()->toIso8601String(),
                    ]
                ]);
            } catch (\Throwable $e) {
                // Silently fail learning to not block the user
            }
        }

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

    public function store(Request $request, Conversation $conversation, OpinionRetriever $retriever, GeminiChatClient $gemini, OpenAiChatClient $openai, GroqChatClient $groq, FaqResponseMatcher $faqMatcher)
    {
        if (function_exists('set_time_limit')) {
            @set_time_limit(120);
        }

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

        $resp = $this->generateChatbotResponse($prompt, $history, $retriever, $gemini, $openai, $groq, $faqMatcher);

        $assistantContent = $resp['content'];
        $assistantModel = $resp['model'];
        $assistantProvider = $resp['provider'];

        // Learning Process: Store the response if it's from an AI provider
        if (in_array($assistantProvider, ['groq', 'gemini', 'openai'])) {
            try {
                AiLearnedKnowledge::create([
                    'query' => $prompt,
                    'response' => $assistantContent,
                    'metadata' => [
                        'provider' => $assistantProvider,
                        'model' => $assistantModel,
                        'learned_at' => now()->toIso8601String(),
                    ]
                ]);
            } catch (\Throwable $e) {
                // Silently fail learning to not block the user
            }
        }

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

    private function linkifyOpinions(string $content, array $opinions): string
    {
        if (empty($opinions)) {
            return $content;
        }

        // 0. Strip ALL existing anchor tags AND the duplicated titles/numbers generated by AI
        $content = preg_replace('/<a[^>]*>(.*?)<\/a>/i', '$1', $content);

        $content = preg_replace(
            '/\R?\s*The following are related legal opinions that may also be helpful:\s*\R\s*These opinions[^\r\n]*\R?/i',
            "\n",
            $content
        );
        
        // Remove patterns like "Title - Title" or "Num - Num" or "Title (Op. No. X) \n Op. No. X"
        // This is a more aggressive cleanup to handle AI duplication before we linkify
        foreach ($opinions as $op) {
            $title = preg_quote(trim((string)($op['title'] ?? '')), '/');
            $num = preg_quote(trim((string)($op['opinion_number'] ?? '')), '/');
            $cleanNum = preg_replace('/^(DILG\s+)?Opinion\s+No\.\s+/i', '', $num);
            $quotedCleanNum = preg_quote($cleanNum, '/');

            if ($title !== '') {
                $content = preg_replace('/' . $title . '\s*—\s*' . $title . '/i', $title, $content);
                $content = preg_replace('/' . $title . '\s*-\s*' . $title . '/i', $title, $content);
            }
            if ($num !== '') {
                $content = preg_replace('/' . $num . '\s*—\s*' . $num . '/i', $num, $content);
                $content = preg_replace('/' . $num . '\s*-\s*' . $num . '/i', $num, $content);
            }
        }

        $lines = preg_split("/\R/u", (string) $content);
        $lineCount = count($lines);
        for ($i = 0; $i < $lineCount; $i++) {
            $lineTrim = trim((string) $lines[$i]);
            if ($lineTrim === '') {
                continue;
            }

            foreach ($opinions as $op) {
                $title = trim((string)($op['title'] ?? ''));
                $num = trim((string)($op['opinion_number'] ?? ''));
                if ($title === '' || $num === '') {
                    continue;
                }

                $cleanNum = preg_replace('/^(DILG\s+)?Opinion\s+No\.\s+/i', '', $num);
                $isTitleLine =
                    mb_strtolower($lineTrim) === mb_strtolower($title) ||
                    mb_strtolower($lineTrim) === mb_strtolower($title.' — '.$num) ||
                    mb_strtolower($lineTrim) === mb_strtolower($title.' - '.$num) ||
                    mb_strtolower($lineTrim) === mb_strtolower($title.' — '.$cleanNum) ||
                    mb_strtolower($lineTrim) === mb_strtolower($title.' - '.$cleanNum);

                if (!$isTitleLine) {
                    continue;
                }

                $next1 = $i + 1 < $lineCount ? (string) $lines[$i + 1] : '';
                $next2 = $i + 2 < $lineCount ? (string) $lines[$i + 2] : '';
                $lookahead = $next1."\n".$next2;

                if (preg_match('/Opinion\s+No\./i', $lookahead) && (str_contains($lookahead, $num) || ($cleanNum !== '' && str_contains($lookahead, $cleanNum)))) {
                    $lines[$i] = '';
                }

                break;
            }
        }

        $content = implode("\n", $lines);
        $content = preg_replace("/\n{3,}/", "\n\n", (string) $content);

        // 1. Pre-process opinions
        $opinionsByTitle = [];
        $opinionsByNum = [];
        $allPatterns = [];

        foreach ($opinions as $op) {
            $id = $op['id'];
            $url = (string) ($op['url'] ?? '');
            $title = trim((string) ($op['title'] ?? ''));
            $num = trim((string) ($op['opinion_number'] ?? ''));
            $date = trim((string) ($op['date'] ?? ''));

            $opinionData = [
                'id' => $id, 
                'url' => $url, 
                'title' => $title, 
                'num' => $num,
                'date' => $date
            ];

            if ($title !== '') {
                $quotedTitle = preg_quote($title, '/');
                if (!isset($opinionsByTitle[$quotedTitle])) {
                    $opinionsByTitle[$quotedTitle] = [];
                }
                $opinionsByTitle[$quotedTitle][] = $opinionData;
                $allPatterns[$quotedTitle] = 'title';
            }

            if ($num !== '') {
                // Support matching the number with or without "Opinion No." prefix
                // Also support partial matches like "65, s. 2009" matching "Opinion No. 65, s. 2009"
                $cleanNum = preg_replace('/^(DILG\s+)?Opinion\s+No\.\s+/i', '', $num);
                $quotedNum = preg_quote($num, '/');
                $quotedCleanNum = preg_quote($cleanNum, '/');
                
                if (!isset($opinionsByNum[$quotedNum])) {
                    $opinionsByNum[$quotedNum] = [];
                }
                $opinionsByNum[$quotedNum][] = $opinionData;
                $allPatterns[$quotedNum] = 'num';

                if ($cleanNum !== $num && $cleanNum !== '') {
                    if (!isset($opinionsByNum[$quotedCleanNum])) {
                        $opinionsByNum[$quotedCleanNum] = [];
                    }
                    $opinionsByNum[$quotedCleanNum][] = $opinionData;
                    $allPatterns[$quotedCleanNum] = 'num';
                }
            }
        }

        // Sort patterns by length descending to match longest first
        uksort($allPatterns, fn($a, $b) => strlen($b) <=> strlen($a));

        if (empty($allPatterns)) {
            return $content;
        }

        $combinedPattern = '(' . implode('|', array_keys($allPatterns)) . ')';
        // Match existing links (though we stripped them, we might add them during processing)
        // and match titles/numbers
        $fullRegex = '/<a[^>]*>.*?<\/a>|' . $combinedPattern . '/i';

        // 2. Find all matches with offsets
        if (!preg_match_all($fullRegex, $content, $matches, PREG_OFFSET_CAPTURE)) {
            return $content;
        }

        $result = '';
        $lastOffset = 0;

        foreach ($matches[0] as $matchData) {
            $matchedText = $matchData[0];
            $offset = $matchData[1];

            // Add text before the match
            $result .= substr($content, $lastOffset, $offset - $lastOffset);
            
            // Skip existing links (added in this pass)
            if (str_starts_with(strtolower($matchedText), '<a')) {
                $result .= $matchedText;
                $lastOffset = $offset + strlen($matchedText);
                continue;
            }

            $replacement = $matchedText;
            $found = false;

            // Check if it's a number match first (to replace with the title link)
            foreach ($opinionsByNum as $pattern => $options) {
                // Use a more flexible match for numbers (allow partial matches within the line)
                if (preg_match('/' . $pattern . '/i', trim($matchedText))) {
                    if (count($options) === 1) {
                        $data = $options[0];
                    } else {
                        $start = max(0, $offset - 200);
                        $end = min(strlen($content), $offset + strlen($matchedText) + 300);
                        $surroundingText = substr($content, $start, $end - $start);
                        $data = $this->findBestOpinionByPriority($surroundingText, $options);
                    }
                    
                    // The user wants it "below of this" but also wants to avoid duplication
                    // We will replace the current line with just the title link (bold, blue, underlined)
                    $titleLink = '<a href="'.$data['url'].'" data-opinion-id="'.$data['id'].'" class="opinion-link text-blue-600 underline font-bold" style="color: blue; text-decoration: underline;">'.$data['title'].'</a>';
                    
                    // Instead of appending, we replace the matched text with the link to avoid duplication
                    $replacement = $titleLink;
                    $found = true;
                    break;
                }
            }

            // If not found in numbers, check title matches
            if (!$found) {
                foreach ($opinionsByTitle as $pattern => $options) {
                    if (preg_match('/^' . $pattern . '$/i', trim($matchedText))) {
                        if (count($options) === 1) {
                            $data = $options[0];
                        } else {
                            $start = max(0, $offset - 200);
                            $end = min(strlen($content), $offset + strlen($matchedText) + 300);
                            $surroundingText = substr($content, $start, $end - $start);
                            $data = $this->findBestOpinionByPriority($surroundingText, $options);
                        }
                        
                        $replacement = '<a href="'.$data['url'].'" data-opinion-id="'.$data['id'].'" class="opinion-link text-blue-600 underline font-bold" style="color: blue; text-decoration: underline;">'.$matchedText.'</a>';
                        $found = true;
                        break;
                    }
                }
            }

            $result .= $replacement;
            $lastOffset = $offset + strlen($matchedText);
        }

        // Add remaining text
        $result .= substr($content, $lastOffset);

        return $result;
    }

    /**
     * Finds the best opinion among options using priority: Title > Number > Date.
     */
    private function findBestOpinionByPriority(string $text, array $options): array
    {
        $bestMatch = null;
        $maxScore = -1;

        foreach ($options as $opt) {
            $score = 0;
            $title = $opt['title'];
            $num = $opt['num'];
            $date = $opt['date'];

            // 1. Title Match (Already matched by the caller, but checking context for emphasis)
            if ($title !== '') {
                $quotedTitle = preg_quote($title, '/');
                if (preg_match('/' . $quotedTitle . '/i', $text)) {
                    $score += 1000; // Highest priority
                }
            }

            // 2. Opinion Number Match
            if ($num !== '') {
                // Extract pure number and year if possible for better matching
                $n = ''; $y = '';
                if (preg_match('/(\d+).+?(\d{4})/', $num, $m)) {
                    $n = $m[1]; $y = $m[2];
                }

                $quotedNum = preg_quote($num, '/');
                if (preg_match('/' . $quotedNum . '/i', $text)) {
                    $score += 500;
                } elseif ($n !== '' && $y !== '' && preg_match('/\b' . $n . '\b.+?\b' . $y . '\b/i', $text)) {
                    $score += 400;
                }
            }

            // 3. Date Match
            if ($date !== '') {
                $quotedDate = preg_quote($date, '/');
                if (preg_match('/' . $quotedDate . '/i', $text)) {
                    $score += 100; // Lowest priority
                }
            }

            if ($score > $maxScore) {
                $maxScore = $score;
                $bestMatch = $opt;
            }
        }

        return $bestMatch ?? $options[0];
    }

    private function generateChatbotResponse(string $prompt, array $history, OpinionRetriever $retriever, GeminiChatClient $gemini, OpenAiChatClient $openai, GroqChatClient $groq, FaqResponseMatcher $faqMatcher): array
    {
        $systemPrompt = trim((string) config('services.chat.system_prompt', ''));
        $isSmallTalk = $this->isSmallTalk($prompt);
        $isListRequest = $this->isOpinionListRequest($prompt);
        $isSearchMode = ! $isSmallTalk && $isListRequest;

        if ($isSearchMode) {
            $topic = $isListRequest ? $this->extractOpinionListTopic($prompt) : trim($prompt);
            if ($topic === '') {
                $topic = trim($prompt);
            }

            $opinions = $this->fallbackOpinionListByTopic($topic, 25);

            if (count($opinions) === 0) {
                $generalInfo = $this->generalInfoFallbackForTopic($topic, $prompt, $openai, $gemini, $groq);

                return [
                    'content' => $generalInfo,
                    'model' => 'general_info_fallback',
                    'provider' => 'ai_fallback_general_info',
                ];
            }

            return [
                'content' => $this->buildOpinionListHtml($topic, $opinions),
                'model' => 'opinion_search_list',
                'provider' => 'opinion_retriever',
            ];
        }

        // Priority: FAQ Response Manager (pre-defined answers)
        if (! $isSmallTalk) {
            $faqMatch = $faqMatcher->findBestMatch($prompt);

            if ($faqMatch) {
                return [
                    'content' => $this->sanitizeAssistantText((string) $faqMatch->response),
                    'model' => 'faq',
                    'provider' => 'faq_response_manager',
                ];
            }
        }

        try {
            // 1. Priority: Opinion Library
            $opinions = $isSmallTalk ? [] : $retriever->retrieve($prompt, 12);
        } catch (\Throwable $e) {
            $opinions = [];
        }

        if (count($opinions) === 0) {
            // 2. Priority: FAQ Match (Pre-defined specific answers)
            $faqMatch = $faqMatcher->findBestMatch($prompt);

            if ($faqMatch) {
                return [
                    'content' => $this->sanitizeAssistantText((string) $faqMatch->response),
                    'model' => 'faq',
                    'provider' => 'faq_response_manager',
                ];
            }

            $conversationalPrompt = 'You are Lex, the GABAY-Lex AI. Respond conversationally, but with strict legal accuracy.

IMPORTANT: 
- NEVER invent dates, years, or specific names (e.g., "Leni Robredo", "2016-2022") unless they are explicitly mentioned in the USER MESSAGE.
- If the user asks about a person or event not in your database, state clearly that you do not have that specific information in your DILG legal library.

STRICT FIDELITY RULES:
- Use ONLY facts explicitly provided in the USER MESSAGE.
- NEVER assume the user\'s age, position (e.g., "Kagawad"), or status.
- DO NOT use personal pronouns like "Since you are..." or "As a [position]..." unless the user explicitly stated those facts.
- If the user did not provide a specific fact, provide the information in a general, third-person perspective (e.g., "The law states that a person must be...").
- NEVER invent or substitute values.

RESPONSE FORMAT (FOLLOW THIS EXACT ORDER, NO MARKDOWN SYMBOLS):
Direct Answer:
[Provide a clear, immediate answer in 1–4 sentences.]

Legal Basis / Supporting Reference:
- If a stored DILG legal opinion directly answers the question, cite it in this format: "[Title] (op. no. [Number]) ([Date])".
- If no stored DILG legal opinion answers the question, SKIP listing "None" and proceed to general information below.

Explanation:
[Briefly explain how the law/opinion applies.]

For general information (not based on stored DILG legal opinions):
Only include this section if the stored DILG opinion library has no direct answer for the user\'s question. This is a last resort.
Reminder: The following answer is based on general legal information outside the stored DILG opinion library because no directly relevant DILG legal opinion was found.
[General information here.]

Conclusion:
[Summarize the final answer in 1–2 sentences.]

STRICT RULES:
- Clearly state when using information outside the DILG library.
- Keep the tone professional, factual, and helpful.
- Do NOT use asterisks (*) or bold markers (**). Use plain text labels and hyphen bullets only.';

            $pureSmallTalkPrompt = 'You are a friendly and helpful AI assistant. Respond naturally to the user\'s greeting or small talk. Keep it brief, polite, and engaging. No need to mention any legal library or disclaimers for simple greetings.';

            // If it's small talk, ALWAYS use the pure small talk prompt.
            // If it's NOT small talk but no opinions found, use systemPrompt if available, else conversational.
            if ($isSmallTalk) {
                $instruction = $pureSmallTalkPrompt;
            } else {
                $instruction = $systemPrompt !== '' ? $systemPrompt : $conversationalPrompt;
            }

            $messages = [['role' => 'system', 'content' => $instruction]];
            foreach ($history as $m) {
                $messages[] = [
                    'role' => ($m['role'] ?? '') === 'assistant' ? 'assistant' : 'user',
                    'content' => (string) ($m['content'] ?? ''),
                ];
            }

            try {
            $resp = $this->chatWithFallback($messages, $openai, $gemini, $groq);
            $content = $this->sanitizeAssistantText((string) ($resp['content'] ?? ''));
            
            if ($this->shouldValidateResponse($prompt, $content)) {
                $content = $this->validateAndCorrectResponse($prompt, $content, $openai, $gemini, $groq);
            }

            // Linkify if any opinions were found (even if not used in primary logic)
            $content = $this->linkifyOpinions($content, $opinions);
            $content = $this->ensureMainReferenceLink($content, $opinions);

            return [
                'content' => $content,
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
        $maxChars = 9000;
        $libraryText = '';
        $ageNumbers = [];
        if (preg_match_all('/\b\d{1,2}\b/u', $prompt, $m) === 1) {
            $ageNumbers = array_values(array_unique($m[0] ?? []));
        }

        foreach ($opinions as $op) {
            $block = ($op['title'] ?? '').' — '.($op['opinion_number'] ?? '');
            if (!empty($op['date'])) {
                $block .= ' ('.$op['date'].')';
            }
            $context = (string) ($op['context'] ?? '');
            $contextForScan = mb_substr($context, 0, 12000);

            $highlights = [];
            $needles = [
                'years old',
                'year old',
                'age',
                'edad',
                'gulang',
                'qualification',
                'qualified',
                'disqualified',
                'not qualified',
                'shall be',
                'must be',
                'at least',
                'not more than',
                'not less than',
                'between',
                'katipunan ng kabataan',
                'sangguniang kabataan',
                'sk reform',
                'ra 10742',
            ];
            foreach ($ageNumbers as $n) {
                $needles[] = (string) $n;
            }

            $parts = preg_split('/(?<=[\.\?\!])\s+|\n/u', $contextForScan) ?: [];
            foreach ($parts as $p) {
                $p = trim((string) $p);
                if ($p === '') {
                    continue;
                }
                $lp = mb_strtolower($p);
                foreach ($needles as $needle) {
                    $needle = mb_strtolower(trim((string) $needle));
                    if ($needle === '') {
                        continue;
                    }
                    if (str_contains($lp, $needle)) {
                        $highlights[] = $p;
                        break;
                    }
                }
                if (count($highlights) >= 4) {
                    break;
                }
            }

            $highlights = array_values(array_unique($highlights));
            if (count($highlights) > 0) {
                $block .= "\n\nKey excerpts:\n- ".implode("\n- ", array_map(fn ($h) => mb_substr($h, 0, 260), $highlights))."\n";
            }

            $compactContext = trim((string) preg_replace('/\s+/u', ' ', $contextForScan));
            if ($compactContext !== '') {
                $block .= "\n\nContext excerpt:\n".mb_substr($compactContext, 0, 1600).(mb_strlen($compactContext) > 1600 ? '…' : '')."\n\n";
            } else {
                $block .= "\n\n";
            }

            if (mb_strlen($libraryText.$block) > $maxChars) {
                $remaining = max(0, $maxChars - mb_strlen($libraryText));
                if ($remaining > 0) {
                    $libraryText .= mb_substr($block, 0, $remaining);
                }
                break;
            }
            $libraryText .= $block;
        }

        $libraryInstruction = 'You are Lex, the GABAY-Lex AI. Your primary task is to answer user questions using the provided legal opinion context.

STRICT FIDELITY RULES:
- Use ONLY facts explicitly provided in the USER MESSAGE or the LEGAL OPINIONS LIBRARY CONTENT.
- NEVER assume the user\'s age, position (e.g., "Kagawad"), or status.
- DO NOT use personal pronouns like "Since you are..." or "As a [position]..." unless the user explicitly stated those facts.
- If the user did not provide a specific fact, provide the information in a general, third-person perspective (e.g., "The law states that a person must be...").
- NEVER invent or substitute values.

RESPONSE FORMAT (FOLLOW THIS EXACT ORDER, NO MARKDOWN SYMBOLS):
Direct Answer:
[Provide a clear, immediate answer in 1–4 sentences.]

Legal Basis / Supporting Reference:
- Cite ONE main DILG legal opinion from the provided library content that most directly answers the question, using: "[Title] (op. no. [Number]) ([Date])".
- If there are other relevant DILG legal opinions, list them as supporting references (do NOT write "None" if there are no other opinions).

Explanation:
[Briefly explain how the cited DILG opinion(s) support the answer.]

For general information (not based on stored DILG legal opinions):
Only include this section if (and only if) the provided DILG library content does NOT contain enough information to answer the user\'s question. This is a last resort.
If you can answer based on the DILG library content, DO NOT include this section at all.
Reminder: This section is general information outside the stored DILG opinion library.
[General information here.]

Conclusion:
[Summarize the final answer in 1–2 sentences. If you used general information, clearly separate what is based on DILG opinions vs what is general information.]
If the user asks about "this year/ngayong taon" but did not provide a specific year, DO NOT write a numeric year. Use "this year/ngayong taon" wording instead.

STRICT RULES:
- ALWAYS prioritize the provided DILG Legal Opinions.
- Ensure opinion titles and numbers match the context exactly for hyperlinking.
- Maintain a professional and factual tone.
- Do NOT use asterisks (*) or bold markers (**). Use plain text labels and hyphen bullets only.';

        $instruction = $libraryInstruction;

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
            $resp = $this->chatWithFallback($messages, $openai, $gemini, $groq);
            $content = (string) ($resp['content'] ?? '');

        // Linkify opinions found in the library content
        $content = $this->linkifyOpinions($content, $opinions);

        // Ensure proper bolding of opinion numbers for the regex to match better if needed
        // but linkifyOpinions should handle it if the AI followed the format.

        return [
            'content' => $this->sanitizeAssistantText($content),
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
                $lines[] = $line."\n".$op['snippet'];
            }

            $content = "I found these relevant legal opinions in the library:\n\n".implode("\n\n", $lines)."\n\n(Note: Detailed AI analysis is currently unavailable, but you can check these references.)";
            
            // Linkify even in fallback mode
            $content = $this->linkifyOpinions($content, $opinions);
            $content = $this->ensureMainReferenceLink($content, $opinions);

            return [
                'content' => $this->sanitizeAssistantText($content),
                'model' => 'fallback-list',
                'provider' => 'opinion_retriever',
            ];
        }
    }

    private function ensureMainReferenceLink(string $content, array $opinions): string
    {
        if (empty($opinions)) {
            return $content;
        }

        if (str_contains($content, 'class="opinion-link"') || str_contains($content, "class='opinion-link'")) {
            return $content;
        }

        $first = $opinions[0] ?? null;
        if (!is_array($first)) {
            return $content;
        }

        $id = (int) ($first['id'] ?? 0);
        $url = (string) ($first['url'] ?? '#');
        $title = trim((string) ($first['title'] ?? ''));
        $num = trim((string) ($first['opinion_number'] ?? ''));
        $date = trim((string) ($first['date'] ?? ''));

        if ($id <= 0 || $title === '') {
            return $content;
        }

        $escape = static fn (string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $meta = implode(' • ', array_values(array_filter([$num, $date], fn ($v) => trim((string) $v) !== '')));
        $link = '<a href="'.$escape($url).'" data-opinion-id="'.$id.'" class="opinion-link text-blue-600 underline font-bold" style="color: blue; text-decoration: underline;">'.$escape($title).'</a>';

        $line = 'Main Reference: '.$link.($meta !== '' ? ' <span style="opacity:0.85; font-size: 12px;">— '.$escape($meta).'</span>' : '');

        return rtrim($content)."\n\n".$line;
    }

    private function generalInfoFallbackForTopic(string $topic, string $originalPrompt, OpenAiChatClient $openai, GeminiChatClient $gemini, GroqChatClient $groq): string
    {
        $topic = trim($topic);
        $title = $topic !== '' ? $topic : $originalPrompt;

        $instruction = 'You are Lex, the GABAY-Lex AI.

The user asked for DILG legal opinions about a topic, but no matching DILG legal opinions were found in the stored DILG opinion library for this query.

Task: Provide helpful GENERAL legal information related to the topic in the Philippines.

Strict rules:
- Do not claim you found or cited any DILG legal opinion.
- Do not invent specific cases, dates, or circular/opinion numbers.
- Use plain text only. Do NOT use asterisks (*) or markdown bold (**).
- Keep it practical and concise.

Response format (exact labels, no extra sections):
General Information:
[short explanation]

Practical Notes:
- [2–5 bullets]

Reminder:
This answer is based on general legal information outside the stored DILG opinion library because no directly relevant DILG legal opinion was found.';

        try {
            $messages = [
                ['role' => 'system', 'content' => $instruction],
                ['role' => 'user', 'content' => $originalPrompt],
            ];

            $resp = $this->chatWithFallback($messages, $openai, $gemini, $groq);
            $content = $this->sanitizeAssistantText((string) ($resp['content'] ?? ''));
        } catch (\Throwable $e) {
            $content = 'General Information:'."\n".'I can provide general information, but I could not reach the AI provider at the moment.'."\n\n".'Practical Notes:'."\n".'- Please try again.'."\n\n".'Reminder:'."\n".'This answer is based on general legal information outside the stored DILG opinion library because no directly relevant DILG legal opinion was found.';
        }

        return 'No matching DILG legal opinions found for: '.$title."\n\n".$content;
    }

    private function sanitizeAssistantText(string $text): string
    {
        $t = (string) $text;
        if ($t === '') {
            return $t;
        }

        $t = str_replace(["\r\n", "\r"], "\n", $t);

        // Remove markdown bold markers
        $t = preg_replace('/\*\*(.*?)\*\*/s', '$1', $t) ?? $t;

        // Convert leading '*' bullets to '-'
        $t = preg_replace('/^\s*\*\s+/m', '- ', $t) ?? $t;

        // Remove standalone "None"/"None." lines
        $t = preg_replace('/^\s*none\.?\s*$/mi', '', $t) ?? $t;

        // Collapse excessive blank lines
        $t = preg_replace("/\n{3,}/", "\n\n", $t) ?? $t;

        return trim($t);
    }

    private function isOpinionListRequest(string $prompt): bool
    {
        $t = mb_strtolower(trim($prompt));
        if ($t === '') {
            return false;
        }

        $patterns = [
            '/\b(legal\s+opinions?|opinions?)\s+(about|on|regarding|of)\b/u',
            '/\b(legal\s+opinion|opinion)\s+of\b/u',
            '/\b(list|show|give)\s+me\b.*\b(legal\s+opinions?|opinions?)\b/u',
            '/\b(mga|listahan)\b.*\b(legal\s+opinions?|opinion)\b/u',
            '/\b(tungkol|patungkol)\b.*\b(legal\s+opinions?|opinion)\b/u',
            '/\bano\b.*\b(mga)\b.*\b(legal\s+opinions?|opinion)\b/u',
            '/\b(related|relevant)\b.*\b(legal\s+opinions?|opinions?)\b/u',
        ];

        foreach ($patterns as $p) {
            if (preg_match($p, $t) === 1) {
                return true;
            }
        }

        return false;
    }

    private function isAnswerMode(string $prompt): bool
    {
        $t = trim((string) $prompt);
        if ($t === '') {
            return false;
        }

        if (str_contains($t, '?')) {
            return true;
        }

        $lower = mb_strtolower($t);
        $starts = [
            'am i', 'can i', 'is it', 'is it allowed', 'is it legal', 'are we', 'are they', 'what is', 'what are', 'what\'s',
            'how', 'when', 'where', 'who',
            'pwede ba', 'pwede', 'qualified ba', 'qualified', 'allowed ba', 'allowed',
            'ano ang', 'ano ba', 'paano', 'kelan', 'sino',
        ];

        foreach ($starts as $s) {
            if (str_starts_with($lower, $s)) {
                return true;
            }
        }

        return false;
    }

    private function extractOpinionListTopic(string $prompt): string
    {
        $t = trim((string) $prompt);
        if ($t === '') {
            return '';
        }

        $lower = mb_strtolower($t);
        $topic = $t;

        if (preg_match('/\b(legal\s+opinions?|opinions?|legal\s+opinion|opinion)\s+(about|on|regarding|of)\s+(.+)$/iu', $lower, $m) === 1) {
            $topic = trim((string) ($m[3] ?? $t));
        } elseif (preg_match('/\b(tungkol|patungkol)\s+sa?\s+(.+)$/iu', $lower, $m) === 1) {
            $topic = trim((string) ($m[2] ?? $t));
        } elseif (preg_match('/\babout\s+(.+)$/iu', $lower, $m) === 1) {
            $topic = trim((string) ($m[1] ?? $t));
        } elseif (preg_match('/\bregarding\s+(.+)$/iu', $lower, $m) === 1) {
            $topic = trim((string) ($m[1] ?? $t));
        } elseif (preg_match('/\bon\s+(.+)$/iu', $lower, $m) === 1) {
            $topic = trim((string) ($m[1] ?? $t));
        }

        $topic = preg_replace('/[?.!]+$/u', '', $topic) ?? $topic;
        $topic = trim((string) $topic);

        return $topic;
    }

    private function tokenizeOpinionListTopic(string $topic): array
    {
        $text = trim((string) $topic);
        $text = mb_strtolower($text);
        $text = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $text) ?? $text;
        $text = trim(preg_replace('/\s+/u', ' ', $text) ?? $text);

        if ($text === '') {
            return [];
        }

        $terms = preg_split('/\s+/u', $text) ?: [];
        $stop = [
            'a', 'an', 'and', 'are', 'as', 'at', 'about', 'be', 'by', 'for', 'from', 'in', 'is', 'it', 'of', 'on', 'or', 'that', 'the', 'this', 'to', 'with',
            'legal', 'lgal', 'opinion', 'opinions', 'dilg', 'director', 'region',
            'act', 'law', 'code', 'rules', 'rule', 'regulation', 'regulations', 'section', 'article', 'republic', 'philippines', 'philippine',
            'ang', 'mga', 'ng', 'na', 'sa', 'si', 'kay', 'kayo', 'ko', 'ako', 'ito', 'yan', 'dito', 'doon', 'para', 'tungkol', 'patungkol',
        ];

        $out = [];
        foreach ($terms as $t) {
            $t = trim((string) $t);
            if (mb_strlen($t) < 3) {
                continue;
            }
            if (in_array($t, $stop, true)) {
                continue;
            }
            $out[] = $t;
        }

        return array_values(array_unique($out));
    }

    private function minRequiredMatches(array $tokens): int
    {
        $n = count($tokens);
        if ($n <= 1) {
            return 1;
        }
        if ($n <= 3) {
            return 2;
        }
        return 2;
    }

    private function isBroadTopicToken(string $token): bool
    {
        $t = mb_strtolower(trim($token));
        if ($t === '') {
            return false;
        }

        $broad = [
            'visaya', 'visayas',
            'luzon',
            'mindanao',
            'philippines', 'philippine',
            'region', 'rehiyon',
            'province', 'probinsya',
            'city', 'siyudad',
            'municipality', 'munisipyo',
            'barangay', 'brgy',
        ];

        return in_array($t, $broad, true);
    }

    private function fallbackOpinionListByTopic(string $topic, int $limit): array
    {
        $topic = trim($topic);
        if ($topic === '') {
            return [];
        }

        $tokens = $this->tokenizeOpinionListTopic($topic);
        if (count($tokens) === 0) {
            return [];
        }

        $query = LegalOpinionLibrary::query();

        if (count($tokens) === 1) {
            $name = (string) $tokens[0];
            $allowContext = ! $this->isBroadTopicToken($name);
            $query->where(function ($q) use ($name, $allowContext) {
                $q->where('title', 'like', $name.' -%')
                    ->orWhere('title', 'like', $name.' —%')
                    ->orWhere('title', 'like', $name.' –%')
                    ->orWhere('title', 'like', $name.'-%')
                    ->orWhere('title', 'like', $name.'%')
                    ->orWhere('title', 'like', '%'.$name.'%')
                    ->orWhere('opinion_number', 'like', '%'.$name.'%')
                    ->orWhere('keywords', 'like', '%'.$name.'%');

                if ($allowContext) {
                    $q->orWhere('context', 'like', '%'.$name.'%');
                }
            });
        } else {
            $query->where(function ($q) use ($tokens) {
                foreach ($tokens as $t) {
                    $q->where(function ($qq) use ($t) {
                        $qq->where('title', 'like', '%'.$t.'%')
                            ->orWhere('opinion_number', 'like', '%'.$t.'%')
                            ->orWhere('keywords', 'like', '%'.$t.'%')
                            ->orWhere('context', 'like', '%'.$t.'%');
                    });
                }
            });
        }

        $models = $query
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit($limit)
            ->get(['id', 'title', 'opinion_number', 'date', 'keywords', 'context']);

        $items = [];
        $minMatches = $this->minRequiredMatches($tokens);
        foreach ($models as $op) {
            $allowContextForMatch = true;
            if (count($tokens) === 1) {
                $allowContextForMatch = ! $this->isBroadTopicToken((string) $tokens[0]);
            }

            $haystack = mb_strtolower(
                (string) ($op->title ?? '')."\n".
                (string) ($op->opinion_number ?? '')."\n".
                (string) ($op->keywords ?? '')."\n".
                ($allowContextForMatch ? mb_substr((string) ($op->context ?? ''), 0, 4000) : '')
            );

            $matchCount = 0;
            foreach ($tokens as $t) {
                if ($t !== '' && str_contains($haystack, mb_strtolower($t))) {
                    $matchCount++;
                }
            }
            if ($matchCount < $minMatches) {
                continue;
            }

            $context = (string) ($op->context ?? '');
            $author = $this->extractOpinionAuthor($context);
            $summary = $this->extractBriefSummaryFromContext($context);
            $items[] = [
                'id' => $op->id,
                'title' => $op->title,
                'opinion_number' => $op->opinion_number,
                'date' => optional($op->date)->format('Y-m-d'),
                'author' => $author,
                'summary' => $summary,
                'url' => route('opinions.public.show', $op),
            ];
        }

        return $items;
    }

    private function buildOpinionListHtml(string $prompt, array $opinions): string
    {
        $escape = static fn (string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $tokens = $this->tokenizeOpinionListTopic($prompt);

        $title = $escape(trim($prompt) !== '' ? $prompt : 'your topic');
        if (count($opinions) === 0) {
            return '<div><strong>No matching DILG legal opinions found for: </strong>'.$title.'</div>';
        }

        $html = '<div><strong>Here are the matching DILG legal opinions about: </strong>'.$title.'</div>';
        $html .= '<div style="margin-top: 6px; font-size: 12px; opacity: 0.8;">Click a title to open the full opinion.</div>';
        $html .= '<ol style="margin-top: 12px; padding-left: 18px;">';

        foreach ($opinions as $op) {
            $opTitle = $escape((string) ($op['title'] ?? 'Untitled'));
            $opNum = $escape((string) ($op['opinion_number'] ?? ''));
            $opDate = $escape((string) ($op['date'] ?? ''));
            $opAuthor = $escape((string) ($op['author'] ?? 'DILG'));
            $opUrl = $escape((string) ($op['url'] ?? '#'));
            $opId = (int) ($op['id'] ?? 0);
            $summary = (string) ($op['summary'] ?? '');

            $metaPieces = array_values(array_filter([$opNum, $opDate, $opAuthor], fn ($v) => trim((string) $v) !== ''));
            $meta = implode(' • ', $metaPieces);
            $metaHtml = $meta !== '' ? '<div style="margin-top: 4px; font-size: 12px; opacity: 0.85;">'.$this->highlightMatchedKeywords($meta, $tokens).'</div>' : '';
            $summaryHtml = $summary !== '' ? '<div style="margin-top: 6px; font-size: 13px; opacity: 0.95;"><strong>Brief Summary:</strong> '.$this->highlightMatchedKeywords($summary, $tokens).'</div>' : '';

            $html .= '<li style="margin: 10px 0;">'
                .'<a href="'.$opUrl.'" data-opinion-id="'.$opId.'" class="opinion-link text-blue-600 underline font-bold" style="color: blue; text-decoration: underline;">'.$opTitle.'</a>'
                .$metaHtml
                .$summaryHtml
                .'</li>';
        }

        $html .= '</ol>';
        return $html;
    }

    private function extractBriefSummaryFromContext(string $context): string
    {
        $text = trim((string) $context);
        if ($text === '') {
            return '';
        }

        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $flat = trim((string) preg_replace('/\s+/u', ' ', $text));

        $needles = [
            'This refers',
            'This pertains',
            'This has reference',
            'In reply',
            'In view',
        ];

        $bestPos = null;
        foreach ($needles as $needle) {
            $pos = stripos($flat, $needle);
            if ($pos !== false) {
                $bestPos = $bestPos === null ? $pos : min($bestPos, $pos);
            }
        }

        if ($bestPos !== null) {
            $flat = trim((string) substr($flat, $bestPos));
        }

        return Str::limit($flat, 260, '…');
    }

    private function highlightMatchedKeywords(string $text, array $tokens): string
    {
        $text = (string) $text;
        if ($text === '') {
            return '';
        }

        $tokens = array_values(array_filter(array_map(fn ($t) => trim((string) $t), $tokens), fn ($t) => $t !== ''));
        if (count($tokens) === 0) {
            return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        usort($tokens, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));
        $pattern = '/(' . implode('|', array_map(fn ($t) => preg_quote($t, '/'), $tokens)) . ')/iu';

        $parts = preg_split($pattern, $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (!is_array($parts) || count($parts) === 0) {
            return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        }

        $out = '';
        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }

            if (preg_match($pattern, $part) === 1) {
                $out .= '<strong>' . htmlspecialchars($part, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '</strong>';
            } else {
                $out .= htmlspecialchars($part, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            }
        }

        return $out;
    }

    private function extractOpinionAuthor(string $context): string
    {
        $text = (string) $context;
        if ($text === '') {
            return 'DILG';
        }

        $text = str_replace(["\r\n", "\r"], "\n", $text);

        if (preg_match('/(?:very truly yours|respectfully yours|truly yours)[^\n]*\n\s*\n?\s*([A-Z][A-Z .,\-\'"]{3,})\s*\n/iu', $text, $m) === 1) {
            $name = trim((string) ($m[1] ?? ''));
            $name = preg_replace('/\s+/', ' ', $name) ?? $name;
            if ($name !== '') {
                return $name;
            }
        }

        if (preg_match('/\n\s*([A-Z][A-Z .,\-\'"]{3,})\s*\n\s*(Undersecretary|Secretary)\b/iu', $text, $m) === 1) {
            $name = trim((string) ($m[1] ?? ''));
            $name = preg_replace('/\s+/', ' ', $name) ?? $name;
            if ($name !== '') {
                return $name;
            }
        }

        return 'DILG';
    }

    private function shouldValidateResponse(string $userPrompt, string $aiResponse): bool
    {
        $aiResponse = (string) $aiResponse;
        if (mb_strlen($aiResponse) < 220) {
            return false;
        }

        if (stripos($aiResponse, 'Summary:') !== false) {
            return true;
        }
        if (preg_match('/\b(since you are|as a)\b/i', $aiResponse) === 1) {
            return true;
        }

        $prompt = (string) $userPrompt;
        if (preg_match('/\b(19|20)\d{2}\b/', $aiResponse) === 1 && preg_match('/\b(19|20)\d{2}\b/', $prompt) !== 1) {
            return true;
        }

        return false;
    }

    /**
     * Validation step to ensure the AI response strictly adheres to user input
     * and does not invent or assume facts.
     */
    private function validateAndCorrectResponse(string $userPrompt, string $aiResponse, OpenAiChatClient $openai, GeminiChatClient $gemini, GroqChatClient $groq): string
    {
        // If the response is very short (e.g., small talk), skip validation to save tokens/time
        if (mb_strlen($aiResponse) < 100) {
            return $aiResponse;
        }

        $validationInstruction = 'You are a Response Fidelity Validator. Your task is to ensure that the AI response strictly uses ONLY the facts provided in the user prompt and follows the required format.

CHECKLIST:
1. Does the response assume or invent an age, position (e.g., "Kagawad"), or any other fact not in the user prompt?
2. Did the AI use personal pronouns like "Since you are..." or "As a..." when the user didn\'t state those facts?
3. Did the AI use the word "Summary:" in references? (This is NOT allowed).
4. Does the response follow the required section order: Direct Answer → Legal Basis / Supporting Reference → Explanation → (optional) For general information... → Conclusion?
5. If there is no directly applicable DILG legal opinion, does the response avoid outputting the word "None" and instead proceed to general information (with a reminder) when needed?
6. Did the AI include general information when the legal opinions already provided a direct and complete answer? (General info should ONLY be present if library content was insufficient).
7. If the response includes general information, does it use the exact header: "For general information (not based on stored DILG legal opinions):"?
8. If the response includes the general information section, does the final conclusion explicitly separate what is based on DILG legal opinions vs what is only general information?

CORRECTION RULES:
- REMOVE any assumed facts if they are not in the user prompt.
- REMOVE the word "Summary:" from all references.
- Remove markdown symbols like "**" and "*" if present; output should be plain text labels.
- Remove standalone "None"/"None." lines.
- REMOVE general information if the legal library references already provided a direct and sufficient answer to the user\'s question.
- REMOVE the entire general information section if the response has a valid DILG Legal Basis citation and the library content is sufficient to answer.
- ENSURE any necessary general legal knowledge has the exact header "For general information (not based on stored DILG legal opinions):" and is positioned before the final conclusion.
- If the general information section exists, UPDATE the final conclusion so it clearly states (1) what the DILG opinions cited actually establish (or that they do not directly answer), and (2) what the general information indicates, without presenting the general information as a DILG-library-based ruling.
- If the response is already accurate and faithful, return it EXACTLY as it is.

OUTPUT: Return only the corrected (or original) response text. Do not add any meta-commentary.';

        $messages = [
            ['role' => 'system', 'content' => $validationInstruction],
            ['role' => 'user', 'content' => "USER PROMPT:\n{$userPrompt}\n\nAI RESPONSE TO VALIDATE:\n{$aiResponse}"]
        ];

        try {
            // Use a faster/cheaper model for validation if possible, or just the default fallback chain
            $resp = $this->chatWithFallback($messages, $openai, $gemini, $groq);
            return (string) ($resp['content'] ?? $aiResponse);
        } catch (\Throwable $e) {
            // If validation fails, return original response as a fallback
            return $aiResponse;
        }
    }

    private function chatWithFallback(array $messages, OpenAiChatClient $openai, GeminiChatClient $gemini, GroqChatClient $groq, ?string $model = null): array
    {
        $openAiKey = (string) config('services.openai.api_key', '');
        $geminiKey = (string) config('services.gemini.api_key', '');
        $groqKey = (string) config('services.groq.api_key', '');

        $providers = [];
        // Priority order: Groq > Gemini > OpenAI
        if ($groqKey !== '' && $groqKey !== 'your_groq_api_key_here') {
            $providers[] = 'groq';
        }
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
                if ($provider === 'groq') {
                    $resp = $groq->chat($messages, $model);
                } elseif ($provider === 'openai') {
                    $resp = $openai->chat($messages, $model);
                } else {
                    $resp = $gemini->chat($messages, $model);
                }

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

        // Exact matches for common phrases
        $exactPhrases = [
            'hi', 'hello', 'hey', 'yo', 'sup', 'kamusta', 'kumusta', 'hello ai', 'hi ai',
            'good morning', 'good afternoon', 'good evening', 'good day',
            'how are you', 'how r u', 'how are u', 'whats up', 'what is up',
            'thank you', 'thanks', 'ty', 'salamat', 'thank u',
            'love you', 'i love you', 'haha', 'lol',
            'who are you', 'what are you', 'who r u', 'what can you do', 'what can u do',
            'bye', 'goodbye', 'see you', 'cya',
        ];

        $lowerT = mb_strtolower($t);
        foreach ($exactPhrases as $phrase) {
            if ($lowerT === $phrase || preg_match('/^' . preg_quote($phrase, '/') . '[!.\\s]*$/i', $t)) {
                return true;
            }
        }

        // Keyword based matches for very short messages
        if (mb_strlen($t) < 30) {
            $keywords = ['hello', 'hi', 'hey', 'kumusta', 'kamusta', 'thanks', 'salamat', 'who are you', 'who r u'];
            foreach ($keywords as $kw) {
                if (str_contains($lowerT, $kw)) {
                    return true;
                }
            }
        }

        return false;
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
