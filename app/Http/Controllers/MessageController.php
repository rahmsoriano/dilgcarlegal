<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Services\OpinionRetriever;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MessageController extends Controller
{
    public function store(Request $request, Conversation $conversation, OpinionRetriever $retriever)
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

        $opinions = $retriever->retrieve($prompt, 5);

        if (count($opinions) === 0) {
            $assistantContent = 'The requested legal opinion is not currently available in the system database.';
        } else {
            $lines = [];
            foreach ($opinions as $op) {
                $line = $op['title'].' — '.$op['opinion_number'];
                if ($op['date']) {
                    $line .= ' ('.$op['date'].')';
                }
                $line .= "\n".$op['snippet'];
                $lines[] = $line;
            }

            $assistantContent = "Here are the most relevant legal opinions from the system database:\n\n".implode("\n\n", $lines);
        }

        $assistantMessage = Message::create([
            'conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => $assistantContent,
            'model' => 'library',
            'response_meta' => [
                'provider' => 'library',
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
                'content' => $assistantMessage->content,
                'created_at' => $assistantMessage->created_at?->toIso8601String(),
                'model' => $assistantMessage->model,
                'usage' => [
                    'prompt_tokens' => null,
                    'completion_tokens' => null,
                    'total_tokens' => null,
                ],
            ],
        ]);
    }
}
