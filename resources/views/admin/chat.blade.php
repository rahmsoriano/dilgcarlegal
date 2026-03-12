<x-admin-layout>
    @include('chat.partials.chat-interface', [
        'indexRoute' => 'admin.legal.ai',
        'savedRoute' => 'admin.legal.ai.saved',
        'showRoute' => 'admin.legal.ai.show',
        'theme' => 'pro',
    ])
</x-admin-layout>
