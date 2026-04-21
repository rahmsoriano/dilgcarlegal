<x-admin-layout mode="public">
    @include('chat.partials.chat-interface', [
        'indexRoute' => 'legal.ai',
        'savedRoute' => 'legal.ai.saved',
        'showRoute' => 'legal.ai.show',
        'createRoute' => 'legal.ai.conversations.store',
        'messagesRoute' => 'legal.ai.messages.store',
        'toggleSaveRoute' => 'legal.ai.conversations.toggle-save',
        'destroyRoute' => 'legal.ai.conversations.destroy',
        'theme' => 'pro',
    ])
</x-admin-layout>
