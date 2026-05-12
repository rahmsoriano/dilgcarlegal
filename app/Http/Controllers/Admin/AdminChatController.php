<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;

class AdminChatController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $conversations = $user->conversations()
            ->where('is_saved', false)
            ->orderByDesc('is_pinned')
            ->orderByDesc('pinned_at')
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        $activeConversation = $conversations->first();
        $messages = $activeConversation
            ? $activeConversation->messages()->orderBy('id')->get()
            : collect();

        return view('admin.chat', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'messages' => $messages,
            'mode' => 'all',
        ]);
    }

    public function create(Request $request)
    {
        $user = $request->user();

        $conversations = $user->conversations()
            ->where('is_saved', false)
            ->orderByDesc('is_pinned')
            ->orderByDesc('pinned_at')
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        return view('admin.chat', [
            'conversations' => $conversations,
            'activeConversation' => null,
            'messages' => collect(),
            'mode' => 'all',
        ]);
    }

    public function show(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        abort_unless($conversation->user_id === $user->id, 404);

        $conversations = $user->conversations()
            ->where('is_saved', false)
            ->orderByDesc('is_pinned')
            ->orderByDesc('pinned_at')
            ->orderByDesc('last_message_at')
            ->orderByDesc('id')
            ->limit(100)
            ->get();

        $messages = $conversation->messages()->orderBy('id')->get();

        return view('admin.chat', [
            'conversations' => $conversations,
            'activeConversation' => $conversation,
            'messages' => $messages,
            'mode' => 'all',
        ]);
    }

    public function saved(Request $request)
    {
        $user = $request->user();
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'filter' => (string) $request->query('filter', ''),
            'sort' => (string) $request->query('sort', 'newest'),
        ];

        $conversations = $user->conversations()
            ->where('is_saved', true)
            ->when($filters['search'] !== '', function ($query) use ($filters) {
                $query->where('title', 'like', '%'.$filters['search'].'%');
            })
            ->when($filters['filter'] === 'recent', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery
                        ->whereNotNull('saved_at')
                        ->where('saved_at', '>=', now()->subDays(30));
                });
            })
            ->when($filters['filter'] === 'older', function ($query) {
                $query->where(function ($subQuery) {
                    $subQuery
                        ->whereNull('saved_at')
                        ->orWhere('saved_at', '<', now()->subDays(30));
                });
            })
            ->when($filters['sort'] === 'oldest', function ($query) {
                $query->orderByRaw('COALESCE(saved_at, created_at) asc')->orderBy('id');
            }, function ($query) {
                $query->orderByRaw('COALESCE(saved_at, created_at) desc')->orderByDesc('id');
            })
            ->limit(100)
            ->get();

        return view('admin.archive', [
            'conversations' => $conversations,
            'filters' => $filters,
        ]);
    }
}
