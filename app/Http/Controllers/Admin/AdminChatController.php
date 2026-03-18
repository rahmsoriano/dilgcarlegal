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
        $conversation = Conversation::create([
            'user_id' => $request->user()->id,
            'title' => null,
            'last_message_at' => now(),
        ]);

        return redirect()->route('admin.legal.ai.show', $conversation);
    }

    public function show(Request $request, Conversation $conversation)
    {
        $user = $request->user();

        abort_unless($conversation->user_id === $user->id, 404);

        $conversations = $user->conversations()
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

        return view('admin.chat', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'messages' => $messages,
            'mode' => 'saved',
        ]);
    }
}
