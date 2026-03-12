<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

class ChatController extends Controller
{
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
}
