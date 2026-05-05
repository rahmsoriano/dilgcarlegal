<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConversationController extends Controller
{
    public function storePublic(Request $request)
    {
        $conversation = $this->publicCreateConversation($request);

        $payload = [
            'id' => $conversation['id'],
            'title' => $conversation['title'],
            'is_pinned' => (bool) $conversation['is_pinned'],
            'url' => route('legal.ai.show', $conversation['id']),
            'messages_url' => route('legal.ai.messages.store', $conversation['id']),
            'update_url' => route('legal.ai.conversations.update', $conversation['id']),
            'toggle_pin_url' => route('legal.ai.conversations.toggle-pin', $conversation['id']),
            'toggle_save_url' => route('legal.ai.conversations.toggle-save', $conversation['id']),
            'delete_url' => route('legal.ai.conversations.destroy', $conversation['id']),
        ];

        if ($request->wantsJson()) {
            return response()->json($payload);
        }

        return redirect()->route('legal.ai.show', $conversation['id']);
    }

    public function updatePublic(Request $request, int $conversationId)
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:80'],
        ]);

        $rows = $this->publicGetConversations($request);
        $found = false;

        foreach ($rows as &$row) {
            if ((int) ($row['id'] ?? 0) !== $conversationId) {
                continue;
            }
            $row['title'] = $validated['title'] ?: null;
            $found = true;
            break;
        }
        unset($row);

        abort_unless($found, 404);

        $this->publicPutConversations($request, $rows);

        return response()->json([
            'id' => $conversationId,
            'title' => $validated['title'] ?: null,
        ]);
    }

    public function toggleSavePublic(Request $request, int $conversationId)
    {
        $rows = $this->publicGetConversations($request);
        $found = false;
        $isSaved = false;

        foreach ($rows as &$row) {
            if ((int) ($row['id'] ?? 0) !== $conversationId) {
                continue;
            }
            $isSaved = ! (bool) ($row['is_saved'] ?? false);
            $row['is_saved'] = $isSaved;
            $row['saved_at'] = $isSaved ? now()->toIso8601String() : null;
            $found = true;
            break;
        }
        unset($row);

        abort_unless($found, 404);

        $this->publicPutConversations($request, $rows);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $conversationId,
                'is_saved' => $isSaved,
            ]);
        }

        return back();
    }

    public function togglePinPublic(Request $request, int $conversationId)
    {
        $rows = $this->publicGetConversations($request);
        $found = false;
        $unpinnedIds = [];
        $isPinned = false;
        $pinnedAt = null;

        foreach ($rows as &$row) {
            if ((int) ($row['id'] ?? 0) !== $conversationId) {
                continue;
            }

            $isPinned = ! (bool) ($row['is_pinned'] ?? false);

            if ($isPinned) {
                foreach ($rows as &$other) {
                    if ((int) ($other['id'] ?? 0) === $conversationId) {
                        continue;
                    }
                    if (!empty($other['is_pinned'])) {
                        $unpinnedIds[] = (int) $other['id'];
                        $other['is_pinned'] = false;
                        $other['pinned_at'] = null;
                    }
                }
                unset($other);
                $pinnedAt = now()->toIso8601String();
            }

            $row['is_pinned'] = $isPinned;
            $row['pinned_at'] = $isPinned ? $pinnedAt : null;
            $found = true;
            break;
        }
        unset($row);

        abort_unless($found, 404);

        $this->publicPutConversations($request, $rows);

        return response()->json([
            'id' => $conversationId,
            'is_pinned' => $isPinned,
            'pinned_at' => $isPinned ? $pinnedAt : null,
            'unpinned_ids' => $unpinnedIds,
        ]);
    }

    public function destroyPublic(Request $request, int $conversationId)
    {
        $rows = $this->publicGetConversations($request);
        $before = count($rows);
        $rows = array_values(array_filter($rows, fn ($c) => (int) ($c['id'] ?? 0) !== $conversationId));

        abort_unless(count($rows) !== $before, 404);

        $this->publicPutConversations($request, $rows);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $conversationId,
                'deleted' => true,
            ]);
        }

        return redirect()->route('legal.ai.saved');
    }

    public function store(Request $request)
    {
        $conversation = Conversation::create([
            'user_id' => $request->user()->id,
            'title' => null,
            'last_message_at' => now(),
        ]);

        $previousPath = (string) (parse_url(url()->previous(), PHP_URL_PATH) ?? '');
        $isAdmin = (bool) ($request->user()?->is_admin);
        $showRoute = $isAdmin ? 'admin.legal.ai.show' : 'chat.show';
        $indexRoute = $isAdmin ? 'admin.dashboard' : 'chat.index';

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $conversation->id,
                'title' => $conversation->title,
                'is_pinned' => (bool) $conversation->is_pinned,
                'url' => route($showRoute, $conversation),
                'messages_url' => route('messages.store', $conversation),
            ]);
        }

        if ($isAdmin && str_contains($previousPath, '/admin/legal-ai')) {
            return redirect()->route($showRoute, $conversation);
        }

        return redirect()->route($indexRoute);
    }

    public function update(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->user_id === $request->user()->id, 404);

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:80'],
        ]);

        $conversation->update([
            'title' => $validated['title'] ?: null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $conversation->id,
                'title' => $conversation->title,
            ]);
        }

        return back();
    }

    public function toggleSave(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->user_id === $request->user()->id, 404);

        $isSaved = ! $conversation->is_saved;

        $conversation->update([
            'is_saved' => $isSaved,
            'saved_at' => $isSaved ? now() : null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $conversation->id,
                'is_saved' => (bool) $conversation->is_saved,
            ]);
        }

        return back();
    }

    public function togglePin(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->user_id === $request->user()->id, 404);

        $unpinnedIds = [];

        DB::transaction(function () use ($conversation, &$unpinnedIds) {
            $conversation->refresh();
            $isPinned = ! $conversation->is_pinned;

            if ($isPinned) {
                $unpinnedIds = Conversation::query()
                    ->where('user_id', $conversation->user_id)
                    ->where('id', '!=', $conversation->id)
                    ->where('is_pinned', true)
                    ->pluck('id')
                    ->all();

                Conversation::query()
                    ->whereIn('id', $unpinnedIds)
                    ->update([
                        'is_pinned' => false,
                        'pinned_at' => null,
                    ]);
            }

            $conversation->update([
                'is_pinned' => $isPinned,
                'pinned_at' => $isPinned ? now() : null,
            ]);
        });

        if ($request->wantsJson()) {
            $conversation->refresh();
            return response()->json([
                'id' => $conversation->id,
                'is_pinned' => (bool) $conversation->is_pinned,
                'pinned_at' => $conversation->pinned_at?->toIso8601String(),
                'unpinned_ids' => $unpinnedIds,
            ]);
        }

        return back();
    }

    public function destroy(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->user_id === $request->user()->id, 404);

        $conversationId = $conversation->id;
        $conversation->delete();

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $conversationId,
                'deleted' => true,
            ]);
        }

        $previousPath = (string) (parse_url(url()->previous(), PHP_URL_PATH) ?? '');
        $isAdmin = (bool) ($request->user()?->is_admin);

        if ($isAdmin && str_contains($previousPath, '/admin/legal-ai/saved')) {
            return redirect()->route('admin.legal.ai.saved');
        }

        if ($isAdmin && str_contains($previousPath, '/admin/legal-ai')) {
            return redirect()->route('admin.legal.ai');
        }

        if ($isAdmin) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('chat.index');
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

        $rows = $this->publicGetConversations($request);
        $rows[] = $conversation;
        $this->publicPutConversations($request, $rows);

        return $conversation;
    }

    private function publicGetConversations(Request $request): array
    {
        return (array) $request->session()->get('public_conversations', []);
    }

    private function publicPutConversations(Request $request, array $rows): void
    {
        $request->session()->put('public_conversations', array_values($rows));
    }
}
