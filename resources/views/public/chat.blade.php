<x-admin-layout mode="public">
    @include('chat.partials.chat-interface', [
        'indexRoute' => 'legal.ai',
        'savedRoute' => 'legal.ai.saved',
        'showRoute' => 'legal.ai.show',
        'createRoute' => 'legal.ai.conversations.store',
        'messagesRoute' => 'legal.ai.messages.store',
        'documentReviewRoute' => 'legal.ai.document-review',
        'toggleSaveRoute' => 'legal.ai.conversations.toggle-save',
        'destroyRoute' => 'legal.ai.conversations.destroy',
        'reviewableOpinions' => $reviewableOpinions ?? collect(),
        'theme' => 'pro',
    ])
</x-admin-layout>
