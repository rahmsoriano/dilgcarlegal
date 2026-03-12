<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use Illuminate\Http\Request;

class ConversationController extends Controller
{
    public function store(Request $request)
    {
        $conversation = Conversation::create([
            'user_id' => $request->user()->id,
            'title' => null,
            'last_message_at' => now(),
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'id' => $conversation->id,
                'url' => route('chat.show', $conversation),
                'messages_url' => route('messages.store', $conversation),
            ]);
        }

        return redirect()->route('chat.show', $conversation);
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

        return back();
    }

    public function destroy(Request $request, Conversation $conversation)
    {
        abort_unless($conversation->user_id === $request->user()->id, 404);

        $conversation->delete();

        return redirect()->route('chat.index');
    }
}
