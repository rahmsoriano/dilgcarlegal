<x-app-layout>
    @include('chat.partials.chat-interface', [
        'indexRoute' => 'chat.index',
        'savedRoute' => 'chat.saved',
        'showRoute' => 'chat.show',
    ])
</x-app-layout>
