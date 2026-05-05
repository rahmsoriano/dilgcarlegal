<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class ChatController extends Controller
{
    public function publicIndex(Request $request)
    {
        $conversations = $this->publicConversationsCollection($request)
            ->where('is_saved', false)
            ->sortByDesc('is_pinned')
            ->sortByDesc(fn ($c) => $c->pinned_at?->getTimestamp() ?? 0)
            ->sortByDesc(fn ($c) => $c->last_message_at?->getTimestamp() ?? 0)
            ->sortByDesc('id')
            ->values();

        $activeConversation = $conversations->first();
        $messages = $activeConversation
            ? $this->publicMessagesCollection($request, (int) $activeConversation->id)
            : collect();

        return view('public.chat', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'messages' => $messages,
            'mode' => 'all',
        ]);
    }

    public function publicNew(Request $request)
    {
        $conversations = $this->publicConversationsCollection($request)
            ->where('is_saved', false)
            ->sortByDesc('is_pinned')
            ->sortByDesc(fn ($c) => $c->pinned_at?->getTimestamp() ?? 0)
            ->sortByDesc(fn ($c) => $c->last_message_at?->getTimestamp() ?? 0)
            ->sortByDesc('id')
            ->values();

        return view('public.chat', [
            'conversations' => $conversations,
            'activeConversation' => null,
            'messages' => collect(),
            'mode' => 'all',
        ]);
    }

    public function publicShow(Request $request, int $conversationId)
    {
        $conversations = $this->publicConversationsCollection($request)
            ->where('is_saved', false)
            ->sortByDesc('is_pinned')
            ->sortByDesc(fn ($c) => $c->pinned_at?->getTimestamp() ?? 0)
            ->sortByDesc(fn ($c) => $c->last_message_at?->getTimestamp() ?? 0)
            ->sortByDesc('id')
            ->values();

        $activeConversation = $this->publicConversationsCollection($request)->firstWhere('id', $conversationId);
        abort_unless($activeConversation, 404);

        $messages = $this->publicMessagesCollection($request, $conversationId);

        return view('public.chat', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'messages' => $messages,
            'mode' => 'all',
        ]);
    }

    public function publicSaved(Request $request)
    {
        $conversations = $this->publicConversationsCollection($request)
            ->where('is_saved', true)
            ->sortByDesc(fn ($c) => $c->saved_at?->getTimestamp() ?? 0)
            ->sortByDesc('id')
            ->values();

        return view('public.archive', [
            'conversations' => $conversations,
        ]);
    }

    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->is_admin) {
            return redirect()->route('admin.legal.ai');
        }

        $conversations = $user->conversations()
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        $activeConversation = $conversations->first();
        $messages = $activeConversation
            ? $activeConversation->messages()->orderBy('id')->get()
            : collect();

        return view('chat.index', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'messages' => $messages,
            'mode' => 'all',
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();

        if ($user->is_admin) {
            return redirect()->route('admin.legal.ai.new');
        }

        $conversations = $user->conversations()
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return view('chat.index', [
            'conversations' => $conversations,
            'activeConversation' => null,
            'messages' => collect(),
            'mode' => 'all',
        ]);
    }

    public function show(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        if ($user->is_admin) {
            return redirect()->route('admin.legal.ai.show', $conversation);
        }

        abort_unless($conversation->user_id === $user->id, 404);

        $conversations = $user->conversations()
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        $messages = $conversation->messages()->orderBy('id')->get();

        return view('chat.index', [
            'conversations' => $conversations,
            'activeConversation' => $conversation,
            'messages' => $messages,
            'mode' => 'all',
        ]);
    }

    public function saved(Request $request)
    {
        $user = $request->user();

        if ($user->is_admin) {
            return redirect()->route('admin.legal.ai.saved');
        }

        $conversations = $user->conversations()
            ->where('is_saved', true)
            ->orderByDesc('saved_at')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        $activeConversation = $conversations->first();
        $messages = $activeConversation
            ? $activeConversation->messages()->orderBy('id')->get()
            : collect();

        return view('chat.index', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'messages' => $messages,
            'mode' => 'saved',
        ]);
    }

    private function publicCreateConversation(Request $request): array
    {
        $id = (int) $request->session()->get('public_next_conversation_id', 1);
        $request->session()->put('public_next_conversation_id', $id + 1);

        $now = now()->toIso8601String();
        $conversation = [
            'id' => $id,
            'title' => null,
            'is_saved' => false,
            'saved_at' => null,
            'is_pinned' => false,
            'pinned_at' => null,
            'last_message_at' => $now,
            'created_at' => $now,
            'messages' => [],
        ];

        $rows = (array) $request->session()->get('public_conversations', []);
        $rows[] = $conversation;
        $request->session()->put('public_conversations', $rows);

        return $conversation;
    }

    private function publicConversationsCollection(Request $request): Collection
    {
        $rows = (array) $request->session()->get('public_conversations', []);

        return collect($rows)->map(function ($c) {
            return (object) [
                'id' => (int) ($c['id'] ?? 0),
                'title' => $c['title'] ?? null,
                'is_saved' => (bool) ($c['is_saved'] ?? false),
                'saved_at' => !empty($c['saved_at']) ? Carbon::parse((string) $c['saved_at']) : null,
                'is_pinned' => (bool) ($c['is_pinned'] ?? false),
                'pinned_at' => !empty($c['pinned_at']) ? Carbon::parse((string) $c['pinned_at']) : null,
                'last_message_at' => !empty($c['last_message_at']) ? Carbon::parse((string) $c['last_message_at']) : null,
                'created_at' => !empty($c['created_at']) ? Carbon::parse((string) $c['created_at']) : null,
            ];
        });
    }

    private function publicMessagesCollection(Request $request, int $conversationId): Collection
    {
        $rows = (array) $request->session()->get('public_conversations', []);
        $conversation = collect($rows)->firstWhere('id', $conversationId);
        if (!is_array($conversation)) {
            return collect();
        }

        $messages = (array) ($conversation['messages'] ?? []);

        return collect($messages)->map(function ($m) {
            return (object) [
                'role' => (string) ($m['role'] ?? 'assistant'),
                'content' => (string) ($m['content'] ?? ''),
                'created_at' => !empty($m['created_at']) ? Carbon::parse((string) $m['created_at']) : now(),
            ];
        });
    }
}
