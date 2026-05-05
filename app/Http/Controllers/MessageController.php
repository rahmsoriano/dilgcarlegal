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
use Illuminate\Support\Facades\Http;
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
            $firstUser = null;
            foreach ($conversationMessages as $m) {
                if (($m['role'] ?? null) !== 'user') {
                    continue;
                }
                $c = trim((string) ($m['content'] ?? ''));
                if ($c !== '') {
                    $firstUser = $c;
                    break;
                }
            }
            $titleSeed = preg_replace('/\\s+/', ' ', (string) ($firstUser ?? $prompt));
            $rows[$idx]['title'] = Str::limit($titleSeed, 60, '');
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
            $firstUser = $conversation->messages()
                ->where('role', 'user')
                ->orderBy('id')
                ->value('content');
            $titleSeed = preg_replace('/\\s+/', ' ', trim((string) ($firstUser ?? $prompt)));
            $conversation->update([
                'title' => Str::limit($titleSeed, 60, ''),
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

        if ($isSmallTalk) {
            return [
                'content' => $this->sanitizeAssistantText($this->fallbackChatReply($prompt)),
                'model' => 'smalltalk',
                'provider' => 'smalltalk',
            ];
        }

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
            $retrievedOpinions = $isSmallTalk ? [] : $retriever->retrieve($prompt, 12);
        } catch (\Throwable $e) {
            $retrievedOpinions = [];
        }

        $opinions = $retrievedOpinions;
        if (count($retrievedOpinions) > 0) {
            $directOpinions = $this->filterOpinionsForDirectUse($prompt, $retrievedOpinions);

            if (count($directOpinions) > 0) {
                $opinions = $directOpinions;
            } else {
                $content = $this->buildNoLibraryGeneralInfoAnswer($prompt, $openai, $gemini, $groq);
                return [
                    'content' => $this->sanitizeAssistantText($content),
                    'model' => 'general_info_no_direct_library',
                    'provider' => 'ai_fallback_general_info',
                ];
            }
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

            try {
                $content = $this->buildNoLibraryGeneralInfoAnswer($prompt, $openai, $gemini, $groq);

                return [
                    'content' => $this->sanitizeAssistantText($content),
                    'model' => 'general_info_no_library',
                    'provider' => 'ai_fallback_general_info',
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

        try {
            $content = $this->buildStrictLibraryAnswer($prompt, $opinions, $openai, $gemini, $groq);

            return [
                'content' => $this->sanitizeAssistantText($content),
                'model' => 'strict_library_answer',
                'provider' => 'opinion_retriever',
            ];
        } catch (\Throwable $e) {
            $content = $this->buildStrictLibraryAnswerFallback($prompt, $opinions);

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

    private function enforceLinkedLegalBasisAndRemoveGeneralInfo(string $content, array $opinions): string
    {
        if (empty($opinions)) {
            return $content;
        }

        $content = str_replace(["\r\n", "\r"], "\n", $content);

        $escape = static fn (string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $basisLines = [];
        foreach (array_slice($opinions, 0, 3) as $op) {
            $id = (int) ($op['id'] ?? 0);
            $url = (string) ($op['url'] ?? '#');
            $title = trim((string) ($op['title'] ?? ''));
            $num = trim((string) ($op['opinion_number'] ?? ''));
            $date = trim((string) ($op['date'] ?? ''));

            if ($id <= 0 || $title === '') {
                continue;
            }

            $link = '<a href="'.$escape($url).'" data-opinion-id="'.$id.'" class="opinion-link text-blue-600 underline font-bold" style="color: blue; text-decoration: underline;">'.$escape($title).'</a>';
            $line = '- '.$link;
            if ($num !== '') {
                $line .= ' — '.$escape($num);
            }
            if ($date !== '') {
                $line .= ' ('.$escape($date).')';
            }
            $basisLines[] = $line;
        }

        if (count($basisLines) > 0) {
            $basisBlock = "Legal Basis / Supporting Reference:\n".implode("\n", $basisLines)."\n";

            $replaced = 0;
            $content = preg_replace(
                '/(^|\n)\s*Legal\s*Basis\s*\/\s*Supporting\s*Reference\s*:\s*\n[\s\S]*?(?=\n\s*(Explanation:|Conclusion:)|\z)/i',
                "\n\n".$basisBlock."\n",
                $content,
                1,
                $replaced
            ) ?? $content;

            if ($replaced === 0) {
                $inserted = 0;
                $content = preg_replace(
                    '/\n\s*(Explanation:)/i',
                    "\n\n".$basisBlock."\n$1",
                    $content,
                    1,
                    $inserted
                ) ?? $content;

                if ($inserted === 0) {
                    $content = rtrim($content)."\n\n".$basisBlock;
                }
            }
        }

        $content = preg_replace(
            '/\n\s*For\s+general\s+information[^\n]*:\s*\n[\s\S]*?(?=\n\s*Conclusion:|\z)/i',
            "\n",
            $content
        ) ?? $content;

        $content = preg_replace(
            '/\n\s*General\s+information\s*:\s*\n[\s\S]*?(?=\n\s*Conclusion:|\z)/i',
            "\n",
            $content
        ) ?? $content;

        $content = preg_replace("/\n{3,}/", "\n\n", (string) $content) ?? $content;

        return trim((string) $content);
    }

    private function extractCoreTokensForDirectMatch(string $prompt): array
    {
        $t = mb_strtolower(trim((string) $prompt));
        $t = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $t) ?? $t;
        $t = trim(preg_replace('/\s+/u', ' ', $t) ?? $t);
        if ($t === '') {
            return [];
        }

        $terms = preg_split('/\s+/u', $t) ?: [];
        $allowShort = ['sk', 'sb', 'kk'];
        $stop = [
            'a', 'an', 'and', 'are', 'as', 'at', 'about', 'be', 'by', 'for', 'from', 'in', 'is', 'it', 'of', 'on', 'or', 'that', 'the', 'this', 'to', 'with',
            'legal', 'opinion', 'opinions', 'dilg',
            'act', 'law', 'code', 'rules', 'rule', 'regulation', 'regulations', 'section', 'article', 'republic', 'philippines', 'philippine',
            'allowed', 'allow', 'can', 'may', 'should', 'must', 'shall', 'what', 'who', 'when', 'where', 'how', 'is', 'are', 'pwede', 'ba', 'ano', 'paano', 'sino', 'kelan',
            'barangay', 'brgy', 'city', 'municipality', 'province', 'region', 'local', 'official', 'officer', 'government',
            'kagawad', 'kapitan', 'chairman', 'chairperson', 'vice', 'mayor',
        ];

        $out = [];
        foreach ($terms as $w) {
            $w = trim((string) $w);
            if ($w === '') {
                continue;
            }
            if (mb_strlen($w) < 4 && !in_array($w, $allowShort, true)) {
                continue;
            }
            if (in_array($w, $stop, true)) {
                continue;
            }
            $out[] = $w;
        }

        return array_values(array_unique($out));
    }

    private function filterOpinionsForDirectUse(string $prompt, array $opinions): array
    {
        $core = $this->extractCoreTokensForDirectMatch($prompt);
        if (count($core) === 0) {
            return $opinions;
        }

        $filtered = [];
        foreach ($opinions as $op) {
            if (!is_array($op)) {
                continue;
            }
            $haystack = mb_strtolower(
                (string) ($op['title'] ?? '')."\n".
                (string) ($op['opinion_number'] ?? '')."\n".
                (string) ($op['keywords'] ?? '')."\n".
                mb_substr((string) ($op['context'] ?? ''), 0, 5000)
            );

            $hit = false;
            foreach ($core as $tok) {
                if ($tok !== '' && str_contains($haystack, $tok)) {
                    $hit = true;
                    break;
                }
            }

            if ($hit) {
                $filtered[] = $op;
            }
        }

        return count($filtered) > 0 ? array_values($filtered) : [];
    }

    private function buildNoLibraryGeneralInfoAnswer(string $prompt, OpenAiChatClient $openai, GeminiChatClient $gemini, GroqChatClient $groq): string
    {
        $generalBlock = $this->buildGeneralInfoWithSources($prompt, $openai, $gemini, $groq);
        $general = $this->limitToSentences((string) ($generalBlock['paragraph'] ?? ''), 3);
        $sources = (array) ($generalBlock['sources'] ?? []);
        $maxSources = 8;

        $conclusion = $this->generateConclusionFromGeneralInfo($prompt, $general, $openai, $gemini, $groq);
        $conclusion = $this->limitToSentences($conclusion, 2);
        if (trim($conclusion) === '') {
            $conclusion = $this->limitToSentences($general, 2);
        }

        $divider = "\n<hr class=\"chat-section-divider\" />\n\n";
        $out = "Direct Answer:\nNo legal opinion in the Opinion Library directly addresses your exact question. Below is general information from external sources.\n";
        $out .= $divider;
        $out .= "Conclusion:\n".$conclusion."\n";
        $out .= $divider;
        $out .= "General Information:\n".$general."\n";
        $sources = array_slice(array_values(array_filter(array_map('trim', $sources))), 0, $maxSources);
        $out .= $divider;
        if (count($sources) === 0) {
            $out .= "No reliable or accessible source found for this information.\n";
        } else {
            foreach ($sources as $src) {
                if ($src === '') {
                    continue;
                }
                $href = htmlspecialchars($src, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $label = htmlspecialchars($src, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
                $out .= 'Source: <a class="external-source-link" href="'.$href.'" target="_blank" rel="noopener noreferrer">'.$label."</a>\n";
            }
        }
        $out .= "\n";

        return trim($out);
    }

    private function buildRelatedOnlyAnswer(string $prompt, array $opinions, OpenAiChatClient $openai, GeminiChatClient $gemini, GroqChatClient $groq): string
    {
        return $this->buildNoLibraryGeneralInfoAnswer($prompt, $openai, $gemini, $groq);
    }

    private function buildGeneralInfoParagraph(string $prompt, OpenAiChatClient $openai, GeminiChatClient $gemini, GroqChatClient $groq): string
    {
        $instruction = 'You are Lex, the GABAY-Lex AI.

Provide a concise general-information answer to the user\'s question in the Philippines.

Strict rules:
- This must be general legal information outside the stored DILG opinion library.
- Do not cite or invent any DILG opinion numbers, circular numbers, case names, or exact dates.
- Output a single paragraph only, 2–3 sentences.';

        try {
            $resp = $this->chatWithFallback(
                [
                    ['role' => 'system', 'content' => $instruction],
                    ['role' => 'user', 'content' => $prompt],
                ],
                $openai,
                $gemini,
                $groq
            );
            $text = $this->sanitizeAssistantText((string) ($resp['content'] ?? ''));
        } catch (\Throwable $e) {
            $text = 'General guidance is available, but I could not reach the AI provider to generate it at the moment.';
        }

        $text = trim((string) preg_replace('/\s+/u', ' ', $text));
        return $text;
    }

    private function buildGeneralInfoWithSources(string $prompt, OpenAiChatClient $openai, GeminiChatClient $gemini, GroqChatClient $groq): array
    {
        $instruction = 'You are Lex, the GABAY-Lex AI.

Provide a concise general-information answer to the user\'s question in the Philippines.

Strict rules:
- This must be general legal information outside the stored DILG opinion library.
- Do not cite or invent any DILG opinion numbers, circular numbers, case names, or exact dates.
- Write 2–3 sentences only for the explanation.
- After the explanation, you MAY add source lines in the exact format:
Source: https://example.com
- Only use HTTPS URLs from these legitimate Philippine sources (choose the most relevant):
  - https://www.officialgazette.gov.ph/
  - https://lawphil.net/ (PRIORITY: prefer direct LawPhil statute/rule pages when applicable)
  - https://senate.gov.ph/
  - https://www.congress.gov.ph/
  - https://dilg.gov.ph/
  - https://comelec.gov.ph/
  - https://www.csc.gov.ph/
  - https://ombudsman.gov.ph/
  - https://sc.judiciary.gov.ph/
- Use a direct page URL that actually covers the topic (avoid generic homepages).
- Do NOT invent URLs. If you cannot provide reliable, accessible, direct-page URLs, output ZERO source lines.

Output only the explanation + source lines (no extra headers).';

        try {
            $resp = $this->chatWithFallback(
                [
                    ['role' => 'system', 'content' => $instruction],
                    ['role' => 'user', 'content' => $prompt],
                ],
                $openai,
                $gemini,
                $groq
            );
            $text = $this->sanitizeAssistantText((string) ($resp['content'] ?? ''));
        } catch (\Throwable $e) {
            $text = 'General guidance is available, but I could not reach the AI provider to generate it at the moment.';
        }

        $text = str_replace(["\r\n", "\r"], "\n", trim((string) $text));
        $text = preg_replace("/\n{3,}/", "\n\n", $text) ?? $text;

        $lines = preg_split("/\n/u", $text) ?: [];
        $paragraphLines = [];
        $sources = [];
        $seenSource = false;

        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }

            if (preg_match('/^\s*sources?\s*:/i', $line) === 1) {
                $seenSource = true;
                if (preg_match('/https?:\/\/\S+/i', $line, $m) === 1) {
                    $sources[] = rtrim((string) $m[0], '|');
                }
                continue;
            }

            if (preg_match('/^\s*source\s*:/i', $line) === 1) {
                $seenSource = true;
                if (preg_match('/https?:\/\/\S+/i', $line, $m) === 1) {
                    $sources[] = rtrim((string) $m[0], '|');
                }
                continue;
            }

            if (! $seenSource) {
                $paragraphLines[] = $line;
            }
        }

        $paragraph = trim((string) preg_replace('/\s+/u', ' ', implode(' ', $paragraphLines)));
        $paragraph = $this->limitToSentences($paragraph, 3);

        $sources = array_values(array_unique(array_filter(array_map('trim', $sources))));
        $sources = $this->filterTrustedSources($sources, $prompt);
        $sources = $this->filterAccessibleSources($sources, 8);
        if (count($sources) === 0) {
            $lawphil = $this->generateLawphilSourcesOnly($prompt, $openai, $gemini, $groq);
            $lawphil = $this->filterTrustedSources($lawphil, $prompt);
            $lawphil = $this->filterAccessibleSources($lawphil, 8);
            $sources = $lawphil;
        }

        return [
            'paragraph' => $paragraph,
            'sources' => array_slice($sources, 0, 8),
        ];
    }

    private function filterTrustedSources(array $sources, string $prompt, int $max = 8): array
    {
        $tokens = $this->extractCoreTokensForDirectMatch($prompt);

        $scored = [];
        foreach ($sources as $raw) {
            $url = trim((string) $raw);
            if ($url === '') {
                continue;
            }
            $score = $this->scoreTrustedSourceUrl($url, $tokens);
            if ($score === null) {
                continue;
            }
            $scored[] = ['url' => $url, 'score' => $score];
        }

        usort($scored, static fn ($a, $b) => ($b['score'] <=> $a['score']));

        $picked = [];
        foreach ($scored as $row) {
            $picked[] = (string) $row['url'];
            if (count($picked) >= $max) {
                break;
            }
        }

        return array_values(array_unique($picked));
    }

    private function scoreTrustedSourceUrl(string $url, array $tokens): ?int
    {
        $url = trim($url);
        if ($url === '') {
            return null;
        }

        if (!preg_match('/^https:\/\//i', $url)) {
            return null;
        }

        $parts = parse_url($url);
        $host = isset($parts['host']) ? mb_strtolower((string) $parts['host']) : '';
        if ($host === '') {
            return null;
        }

        $allow = [
            'officialgazette.gov.ph',
            'lawphil.net',
            'senate.gov.ph',
            'congress.gov.ph',
            'dilg.gov.ph',
            'comelec.gov.ph',
            'csc.gov.ph',
            'ombudsman.gov.ph',
            'sc.judiciary.gov.ph',
        ];

        $okHost = false;
        foreach ($allow as $d) {
            if ($host === $d || str_ends_with($host, '.'.$d)) {
                $okHost = true;
                break;
            }
        }
        if (! $okHost) {
            return null;
        }

        $path = (string) ($parts['path'] ?? '');
        $query = (string) ($parts['query'] ?? '');

        $isGeneric = ($path === '' || $path === '/') && $query === '';
        if ($isGeneric) {
            return null;
        }

        $score = 0;
        $score += 10;

        if ($host === 'lawphil.net' || str_ends_with($host, '.lawphil.net')) {
            $score += 8;
        }

        $lurl = mb_strtolower($url);
        $hints = ['statutes', 'republic', 'act', 'ra', 'circular', 'resolution', 'memo', 'memorandum', 'faq', 'guidelines', 'download'];
        foreach ($hints as $h) {
            if (str_contains($lurl, $h)) {
                $score += 2;
            }
        }

        $hits = 0;
        foreach ($tokens as $t) {
            $t = mb_strtolower(trim((string) $t));
            if ($t === '' || mb_strlen($t) < 4) {
                continue;
            }
            if (str_contains($lurl, $t)) {
                $hits++;
            }
        }
        $score += min(8, $hits * 2);

        return $score;
    }

    private function generateLawphilSourcesOnly(string $prompt, OpenAiChatClient $openai, GeminiChatClient $gemini, GroqChatClient $groq): array
    {
        $instruction = 'You are Lex, the GABAY-Lex AI.

Task:
Provide 1–5 LawPhil source links relevant to the user question.

Strict rules:
- Output ONLY lines in this exact format (no other text):
Source: https://lawphil.net/...
- Use only LawPhil direct pages (avoid https://lawphil.net/ homepage).
- Do NOT invent or guess URLs. If you are not confident the exact URL exists, output ZERO lines.';

        try {
            $resp = $this->chatWithFallback(
                [
                    ['role' => 'system', 'content' => $instruction],
                    ['role' => 'user', 'content' => $prompt],
                ],
                $openai,
                $gemini,
                $groq
            );
            $text = $this->sanitizeAssistantText((string) ($resp['content'] ?? ''));
        } catch (\Throwable $e) {
            return [];
        }

        $text = str_replace(["\r\n", "\r"], "\n", trim((string) $text));
        if ($text === '') {
            return [];
        }

        $lines = preg_split("/\n/u", $text) ?: [];
        $out = [];
        foreach ($lines as $line) {
            $line = trim((string) $line);
            if ($line === '') {
                continue;
            }
            if (preg_match('/^\s*source\s*:/i', $line) !== 1) {
                continue;
            }
            if (preg_match('/https?:\/\/\S+/i', $line, $m) === 1) {
                $out[] = rtrim((string) $m[0], '|');
            }
        }

        return array_values(array_unique(array_filter(array_map('trim', $out))));
    }

    private function filterAccessibleSources(array $sources, int $max = 8): array
    {
        $picked = [];
        $seen = [];
        foreach ($sources as $raw) {
            $url = trim((string) $raw);
            if ($url === '' || isset($seen[$url])) {
                continue;
            }
            $seen[$url] = true;
            if ($this->isAccessibleTrustedUrl($url)) {
                $picked[] = $url;
                if (count($picked) >= $max) {
                    break;
                }
            }
        }
        return $picked;
    }

    private function isAccessibleTrustedUrl(string $url): bool
    {
        $url = trim($url);
        if ($url === '') {
            return false;
        }

        try {
            $resp = Http::timeout(4)
                ->withHeaders([
                    'Accept' => 'text/html,application/xhtml+xml,application/pdf;q=0.9,*/*;q=0.8',
                    'User-Agent' => 'Mozilla/5.0 (compatible; GABAY-Lex/1.0)',
                ])
                ->get($url);
        } catch (\Throwable $e) {
            return false;
        }

        if (! $resp->successful()) {
            return false;
        }

        $ct = mb_strtolower((string) ($resp->header('Content-Type') ?? ''));
        if ($ct !== '' && !str_contains($ct, 'text/html') && !str_contains($ct, 'application/pdf') && !str_contains($ct, 'text/plain') && !str_contains($ct, 'application/octet-stream')) {
            return false;
        }

        if (str_contains($ct, 'text/html')) {
            $body = (string) $resp->body();
            $snippet = mb_strtolower(mb_substr($body, 0, 2500));
            if ($snippet !== '' && (str_contains($snippet, 'page not found') || str_contains($snippet, '404') || str_contains($snippet, 'not found'))) {
                return false;
            }
        }

        return true;
    }

    private function generateConclusionFromGeneralInfo(string $prompt, string $generalInfo, OpenAiChatClient $openai, GeminiChatClient $gemini, GroqChatClient $groq): string
    {
        $generalInfo = trim((string) $generalInfo);
        if ($generalInfo === '') {
            return '';
        }

        $instruction = 'You are Lex, the GABAY-Lex AI.

Write a conclusion in 1–2 sentences that summarizes the practical legal takeaway for the user\'s question using ONLY the provided general information paragraph.

Strict rules:
- Do not cite or invent any DILG opinion numbers, circular numbers, case names, or exact dates.
- No extra sections. Output the conclusion text only.';

        try {
            $resp = $this->chatWithFallback(
                [
                    ['role' => 'system', 'content' => $instruction],
                    ['role' => 'user', 'content' => "USER QUESTION:\n".$prompt."\n\nGENERAL INFORMATION:\n".$generalInfo],
                ],
                $openai,
                $gemini,
                $groq
            );
            $text = $this->sanitizeAssistantText((string) ($resp['content'] ?? ''));
        } catch (\Throwable $e) {
            return '';
        }

        return trim((string) $text);
    }

    private function buildStrictLibraryAnswer(string $prompt, array $opinions, OpenAiChatClient $openai, GeminiChatClient $gemini, GroqChatClient $groq): string
    {
        $tokens = $this->tokenizeOpinionListTopic($prompt);

        $main = $opinions[0] ?? null;
        if (!is_array($main)) {
            return $this->buildStrictLibraryAnswerFallback($prompt, $opinions);
        }

        $mainContext = (string) ($main['context'] ?? '');
        $mainContext = mb_substr($mainContext, 0, 3000);

        $ai = $this->generateDirectAnswerAndConclusion($prompt, $mainContext, $openai, $gemini, $groq);
        $direct = $this->limitToSentences((string) ($ai['direct'] ?? ''), 2);
        $conclusion = $this->limitToSentences((string) ($ai['conclusion'] ?? ''), 2);

        if (trim($direct) === '') {
            $direct = 'Please refer to the DILG legal opinion cited below for the applicable rule on this question.';
        }
        if (trim($conclusion) === '') {
            $conclusion = 'Refer to the cited DILG legal opinion as the primary guidance for the issue raised.';
        }

        $mainCitation = $this->buildOpinionCitationHtml($main, true);
        $mainExcerptRaw = $this->limitToSentences($this->extractRelevantExcerptFromOpinion($main, $tokens), 3);

        $others = array_slice($opinions, 1, 3);

        $refs = [
            ['label' => 'Main', 'excerpt' => $mainExcerptRaw],
        ];
        foreach ($others as $idx => $op) {
            if (!is_array($op)) {
                continue;
            }
            $refs[] = [
                'label' => 'Other'.($idx + 1),
                'excerpt' => $this->limitToSentences($this->extractRelevantExcerptFromOpinion($op, $tokens), 3),
            ];
        }

        $summaries = $this->summarizeReferenceExcerpts($prompt, $refs, $openai, $gemini, $groq);
        $mainSummary = $this->limitToSentences((string) ($summaries['Main'] ?? $mainExcerptRaw), 3);

        if ($this->isNonAnswerFromLibrary($direct."\n".$mainSummary)) {
            return $this->buildRelatedOnlyAnswer($prompt, $opinions, $openai, $gemini, $groq);
        }

        $divider = "\n<hr class=\"chat-section-divider\" />\n\n";
        $out = "Direct Answer:\n".$direct."\n";
        $out .= $divider;
        $out .= "Legal Basis / Supporting Reference:\n".$mainCitation."\n".$mainSummary."\n";
        $out .= $divider;
        $out .= "Conclusion:\n".$conclusion."\n";
        $out .= $divider;
        $out .= "Other Related References That Might Help:\n";

        if (count($others) === 0) {
            $out .= "- No other related DILG legal opinions found.\n\n";
        } else {
            foreach ($others as $idx => $op) {
                if (!is_array($op)) {
                    continue;
                }
                $citation = $this->buildOpinionCitationHtml($op, true);
                $key = 'Other'.($idx + 1);
                $summary = $this->limitToSentences((string) ($summaries[$key] ?? ''), 3);
                if (trim($summary) === '') {
                    $summary = $this->limitToSentences($this->extractRelevantExcerptFromOpinion($op, $tokens), 3);
                }
                $out .= '<div class="ref-accordion" role="button" tabindex="0" aria-expanded="false">';
                $out .= '<div class="ref-accordion-head">';
                $out .= '<span class="ref-accordion-arrow">↳</span>';
                $out .= '<div class="ref-accordion-title">'.$citation.'</div>';
                $out .= '</div>';
                $out .= '<div class="ref-accordion-body">'.htmlspecialchars($summary, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</div>';
                $out .= "</div>\n";
            }
            $out .= "\n";
        }

        $out = rtrim($out);

        return trim($out);
    }

    private function isNonAnswerFromLibrary(string $text): bool
    {
        $t = mb_strtolower(trim((string) $text));
        if ($t === '') {
            return false;
        }

        $signals = [
            'does not address',
            'doesn\'t address',
            'not address',
            'no direct',
            'offers no direct',
            'does not discuss',
            'doesn\'t discuss',
            'does not mention',
            'doesn\'t mention',
            'unrelated',
            'not related',
            'not directly',
            'cannot confirm',
            'cannot conclude',
            'no basis to conclude',
            'does not provide',
            'doesn\'t provide',
        ];

        foreach ($signals as $s) {
            if (str_contains($t, $s)) {
                return true;
            }
        }

        return false;
    }

    private function buildStrictLibraryAnswerFallback(string $prompt, array $opinions): string
    {
        $main = $opinions[0] ?? null;
        if (!is_array($main)) {
            return "Direct Answer:\nI could not generate an AI explanation at the moment.\n<hr class=\"chat-section-divider\" />\n\nLegal Basis / Supporting Reference:\n(No DILG legal opinion reference available)\n<hr class=\"chat-section-divider\" />\n\nConclusion:\nPlease try again.\n<hr class=\"chat-section-divider\" />\n\nOther Related References That Might Help:\n- (None)";
        }

        $tokens = $this->tokenizeOpinionListTopic($prompt);
        $mainCitation = $this->buildOpinionCitationHtml($main, true);
        $mainExcerpt = $this->limitToSentences($this->extractRelevantExcerptFromOpinion($main, $tokens), 3);

        $divider = "\n<hr class=\"chat-section-divider\" />\n\n";
        $out = "Direct Answer:\nA directly relevant DILG legal opinion is available; please see the main reference below.\n";
        $out .= $divider;
        $out .= "Legal Basis / Supporting Reference:\n".$mainCitation."\n".$mainExcerpt."\n";
        $out .= $divider;
        $out .= "Conclusion:\nPlease refer to the cited DILG legal opinion(s) as the primary guidance for this issue.\n";
        $out .= $divider;
        $out .= "Other Related References That Might Help:\n";
        $others = array_slice($opinions, 1, 3);
        if (count($others) === 0) {
            $out .= "- No other related DILG legal opinions found.\n\n";
        } else {
            foreach ($others as $op) {
                if (!is_array($op)) {
                    continue;
                }
                $citation = $this->buildOpinionCitationHtml($op, true);
                $excerpt = $this->limitToSentences($this->extractRelevantExcerptFromOpinion($op, $tokens), 3);
                $out .= '<div class="ref-accordion" role="button" tabindex="0" aria-expanded="false">';
                $out .= '<div class="ref-accordion-head">';
                $out .= '<span class="ref-accordion-arrow">↳</span>';
                $out .= '<div class="ref-accordion-title">'.$citation.'</div>';
                $out .= '</div>';
                $out .= '<div class="ref-accordion-body">'.htmlspecialchars($excerpt, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8').'</div>';
                $out .= "</div>\n";
            }
            $out .= "\n";
        }
        $out = rtrim($out);

        return trim($out);
    }

    private function generateDirectAnswerAndConclusion(string $prompt, string $mainContextExcerpt, OpenAiChatClient $openai, GeminiChatClient $gemini, GroqChatClient $groq): array
    {
        $instruction = 'You are Lex, the GABAY-Lex AI.

Use ONLY the user question and the provided DILG opinion context excerpt. Do not add general information.

Output ONLY the following two sections (no extra text):
Direct Answer:
[1–2 sentences, clear and final]

Conclusion:
[1–2 sentences, summary]';

        $userText = "USER QUESTION:\n".$prompt."\n\nDILG OPINION CONTEXT EXCERPT:\n".$mainContextExcerpt;

        $resp = $this->chatWithFallback(
            [
                ['role' => 'system', 'content' => $instruction],
                ['role' => 'user', 'content' => $userText],
            ],
            $openai,
            $gemini,
            $groq
        );

        $content = $this->sanitizeAssistantText((string) ($resp['content'] ?? ''));

        $direct = '';
        $conclusion = '';

        if (preg_match('/Direct\s*Answer:\s*(.+?)(?=\n\s*Conclusion:|\z)/is', $content, $m) === 1) {
            $direct = trim((string) ($m[1] ?? ''));
        }
        if (preg_match('/Conclusion:\s*(.+)\z/is', $content, $m) === 1) {
            $conclusion = trim((string) ($m[1] ?? ''));
        }

        return [
            'direct' => $direct,
            'conclusion' => $conclusion,
        ];
    }

    private function summarizeReferenceExcerpts(string $prompt, array $refs, OpenAiChatClient $openai, GeminiChatClient $gemini, GroqChatClient $groq): array
    {
        $items = [];
        foreach ($refs as $r) {
            $label = trim((string) ($r['label'] ?? ''));
            $excerpt = trim((string) ($r['excerpt'] ?? ''));
            if ($label === '' || $excerpt === '') {
                continue;
            }
            $items[] = [$label, $excerpt];
        }

        if (count($items) === 0) {
            return [];
        }

        $instruction = 'You are Lex, the GABAY-Lex AI.

You will be given a USER QUESTION and multiple EXCERPTS from DILG legal opinions.

For each excerpt, write a 2–3 sentence summary that explains only the IMPORTANT details and why it supports or relates to the user question.

Strict rules:
- Use ONLY the excerpt content. Do not add outside laws or general information.
- No bullets. No markdown. No asterisks.
- Output must be exactly one block per label, using this format:
Main: [2–3 sentences]
Other1: [2–3 sentences]
Other2: [2–3 sentences]
Other3: [2–3 sentences]

Only include labels that are present in the input.';

        $body = "USER QUESTION:\n".$prompt."\n\n";
        foreach ($items as [$label, $excerpt]) {
            $body .= $label." EXCERPT:\n".$excerpt."\n\n";
        }

        try {
            $resp = $this->chatWithFallback(
                [
                    ['role' => 'system', 'content' => $instruction],
                    ['role' => 'user', 'content' => $body],
                ],
                $openai,
                $gemini,
                $groq
            );

            $text = $this->sanitizeAssistantText((string) ($resp['content'] ?? ''));
        } catch (\Throwable $e) {
            return [];
        }

        $out = [];
        foreach ($items as [$label]) {
            $pattern = '/^'.preg_quote($label, '/').'\s*:\s*(.+?)(?=^\w+\s*:|\z)/ims';
            if (preg_match($pattern, $text, $m) === 1) {
                $out[$label] = trim((string) ($m[1] ?? ''));
            }
        }

        return $out;
    }

    private function buildOpinionCitationHtml(array $op, bool $linkTitle): string
    {
        $escape = static fn (string $v): string => htmlspecialchars($v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

        $id = (int) ($op['id'] ?? 0);
        $url = (string) ($op['url'] ?? '#');
        $title = trim((string) ($op['title'] ?? ''));
        $num = trim((string) ($op['opinion_number'] ?? ''));
        $date = trim((string) ($op['date'] ?? ''));

        $titleHtml = $escape($title);
        if ($linkTitle && $id > 0 && $title !== '') {
            $titleHtml = '<a href="'.$escape($url).'" data-opinion-id="'.$id.'" class="opinion-link text-blue-600 underline font-bold" style="color: blue; text-decoration: underline; font-style: italic;">'.$escape($title).'</a>';
        }

        $parts = [];
        if ($titleHtml !== '') {
            $parts[] = $titleHtml;
        }
        if ($num !== '') {
            $parts[] = '— '.$escape($num);
        }
        if ($date !== '') {
            $parts[] = '('.$escape($date).')';
        }

        return trim(implode(' ', $parts));
    }

    private function extractRelevantExcerptFromOpinion(array $op, array $tokens): string
    {
        $context = trim((string) ($op['context'] ?? ''));
        if ($context === '') {
            $snippet = trim((string) ($op['snippet'] ?? ''));
            return $snippet !== '' ? $snippet : 'No excerpt available.';
        }

        $flat = trim((string) preg_replace('/\s+/u', ' ', $context));
        $sentences = preg_split('/(?<=[\.\!\?])\s+/u', $flat) ?: [];

        $needles = array_values(array_filter($tokens, fn ($t) => ! $this->isBroadTopicToken((string) $t)));
        foreach ($sentences as $i => $s) {
            $s = trim((string) $s);
            if ($s === '') {
                continue;
            }
            $ls = mb_strtolower($s);
            foreach ($needles as $t) {
                $t = mb_strtolower(trim((string) $t));
                if ($t !== '' && str_contains($ls, $t)) {
                    $next = $sentences[$i + 1] ?? '';
                    $pick = trim($s.' '.trim((string) $next));
                    return $pick !== '' ? $pick : $s;
                }
            }
        }

        $summary = trim((string) $this->extractBriefSummaryFromContext($context));
        if ($summary !== '') {
            return $summary;
        }

        return mb_substr($flat, 0, 280).(mb_strlen($flat) > 280 ? '…' : '');
    }

    private function limitToSentences(string $text, int $maxSentences): string
    {
        $t = trim((string) $text);
        if ($t === '' || $maxSentences <= 0) {
            return $t;
        }

        $t = preg_replace('/\s+/u', ' ', $t) ?? $t;
        $parts = preg_split('/(?<=[\.\!\?])\s+/u', $t) ?: [$t];
        $parts = array_values(array_filter(array_map('trim', $parts), fn ($p) => $p !== ''));

        if (count($parts) <= $maxSentences) {
            return implode(' ', $parts);
        }

        return implode(' ', array_slice($parts, 0, $maxSentences));
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
4. Does the response follow an acceptable section order?
   - If a directly applicable DILG legal opinion is used: Direct Answer → Legal Basis / Supporting Reference → (optional) Other Related References That Might Help → Conclusion.
   - If no directly applicable DILG legal opinion is used: Direct Answer → General Information → Conclusion.
5. If there is no directly applicable DILG legal opinion, does the response avoid outputting the word "None" and instead proceed to general information (with a reminder) when needed?
6. Did the AI include general information when the legal opinions already provided a direct and complete answer? (General info should ONLY be present if library content was insufficient).
7. If the response includes general information, does it clearly indicate it is not based on the stored Opinion Library (either in Direct Answer or in the General Information section)?
8. If the response includes the general information section, does the final conclusion explicitly separate what is based on DILG legal opinions vs what is only general information?

CORRECTION RULES:
- REMOVE any assumed facts if they are not in the user prompt.
- REMOVE the word "Summary:" from all references.
- Remove markdown symbols like "**" and "*" if present; output should be plain text labels.
- Remove standalone "None"/"None." lines.
- REMOVE general information if the legal library references already provided a direct and sufficient answer to the user\'s question.
- REMOVE the entire general information section if the response has a valid DILG Legal Basis citation and the library content is sufficient to answer.
- ENSURE any necessary general legal knowledge is clearly labeled as not based on the stored Opinion Library and is positioned before the final conclusion.
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
            return "Hi! I’m Lex. How can I help you today?";
        }

        return "I’m having trouble connecting right now. Please try again in a moment.";
    }
}
