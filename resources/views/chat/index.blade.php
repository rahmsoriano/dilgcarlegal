<x-admin-layout
    mode="user"
    :showOpinionsNav="false"
    :showProfileMenu="true"
    :showLoginButton="false"
    newChatRoute="chat.new"
    archiveRoute="chat.saved"
    chatIndexRoute="chat.index"
    chatShowRoute="chat.show"
    sidebarUpdateRoute="conversations.update"
    sidebarTogglePinRoute="conversations.toggle-pin"
    sidebarToggleSaveRoute="conversations.toggle-save"
    sidebarDeleteRoute="conversations.destroy"
>
    @include('chat.partials.chat-interface', [
        'indexRoute' => 'chat.index',
        'savedRoute' => 'chat.saved',
        'showRoute' => 'chat.show',
        'createRoute' => 'conversations.store',
        'messagesRoute' => 'messages.store',
        'toggleSaveRoute' => 'conversations.toggle-save',
        'destroyRoute' => 'conversations.destroy',
        'theme' => 'pro',
    ])
</x-admin-layout>
