@php
    $isPro = ($theme ?? '') === 'pro';
@endphp

<style>
    .chat-shell {
        background: {{ $isPro ? 'transparent' : 'radial-gradient(circle at top left, rgba(14, 165, 233, 0.14), transparent 28%), radial-gradient(circle at bottom right, rgba(99, 102, 241, 0.14), transparent 32%), linear-gradient(180deg, #f8fbff 0%, #eef4ff 100%)' }};
    }

    .chat-panel {
        backdrop-filter: blur(24px);
        background: {{ $isPro ? '#13132b' : 'rgba(255, 255, 255, 0.82)' }};
        box-shadow: {{ $isPro ? '0 25px 50px -12px rgba(0, 0, 0, 0.5)' : '0 24px 80px rgba(15, 23, 42, 0.08)' }};
        border: {{ $isPro ? '1px solid rgba(255, 255, 255, 0.05)' : '1px solid rgba(255, 255, 255, 0.7)' }};
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .pro-input-wrapper {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 1.5rem;
        transition: all 0.3s ease;
    }

    .pro-input-wrapper:focus-within {
        background: rgba(255, 255, 255, 0.05);
        border-color: #6366f1;
        box-shadow: 0 0 30px rgba(99, 102, 241, 0.15);
    }

    .message-bubble-user {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        box-shadow: 0 10px 30px -5px rgba(99, 102, 241, 0.4);
    }

    .message-bubble-ai {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.06);
        backdrop-filter: blur(10px);
    }

    .chat-scrollbar::-webkit-scrollbar {
        width: 5px;
    }

    .chat-scrollbar::-webkit-scrollbar-thumb {
        background: {{ $isPro ? 'rgba(255, 255, 255, 0.08)' : 'rgba(148, 163, 184, 0.45)' }};
        border-radius: 999px;
    }

    .chat-scrollbar::-webkit-scrollbar-thumb:hover {
        background: {{ $isPro ? 'rgba(255, 255, 255, 0.15)' : 'rgba(148, 163, 184, 0.6)' }};
    }

    @keyframes pulse-glow {
        0%, 100% { opacity: 0.5; transform: scale(1); }
        50% { opacity: 0.8; transform: scale(1.05); }
    }

    .glow-dot {
        position: absolute;
        width: 4px;
        height: 4px;
        background: #6366f1;
        border-radius: 50%;
        filter: blur(2px);
        animation: pulse-glow 2s infinite;
    }

    .chat-scrollbar::-webkit-scrollbar-track {
        background: transparent;
    }

    .message-enter {
        animation: message-in 220ms ease-out;
    }

    @keyframes message-in {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>

<div class="chat-shell {{ $isPro ? '' : 'min-h-[calc(100vh-4rem)] px-4 py-4 sm:px-6 lg:px-8' }}">
    <div class="mx-auto flex h-[calc(100vh-16rem)] {{ $isPro ? 'max-w-full' : 'max-w-[1700px]' }} flex-col gap-4 lg:flex-row">
        <aside class="chat-panel flex w-full shrink-0 flex-col overflow-hidden rounded-[2.5rem] lg:w-[380px]">
            <div class="border-b {{ $isPro ? 'border-white/5' : 'border-slate-200/70' }} p-8">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <div class="text-[10px] font-black uppercase tracking-[0.2em] {{ $isPro ? 'text-indigo-400' : 'text-sky-600' }}">Workspace</div>
                        <h1 class="mt-1 text-2xl font-black tracking-tight {{ $isPro ? 'text-white' : 'text-slate-950' }}">Conversations</h1>
                    </div>
                    <div class="h-10 w-10 flex items-center justify-center rounded-xl {{ $isPro ? 'bg-white/5 text-slate-400' : 'bg-slate-100 text-slate-600' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 20.25c4.97 0 9-3.694 9-8.25s-4.03-8.25-9-8.25S3 7.444 3 12c0 2.104.859 4.023 2.273 5.48.432.447.74 1.04.586 1.641a4.483 4.483 0 01-.923 1.785A5.969 5.969 0 006 21c1.282 0 2.47-.402 3.445-1.087.81.22 1.668.337 2.555.337z" />
                        </svg>
                    </div>
                </div>

                <form method="POST" action="{{ route('conversations.store') }}">
                    @csrf
                    <button type="submit" class="group relative flex w-full items-center justify-center gap-3 rounded-2xl {{ $isPro ? 'bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20' : 'bg-slate-950 hover:bg-slate-800' }} px-6 py-4 text-sm font-bold text-white transition-all duration-300 hover:-translate-y-1">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-4 w-4 transition-transform group-hover:rotate-90">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        <span>New Conversation</span>
                    </button>
                </form>

                <div class="mt-6 flex rounded-2xl {{ $isPro ? 'bg-white/[0.03] p-1.5' : 'bg-slate-100 p-1' }} text-xs font-bold uppercase tracking-widest">
                    <a href="{{ route($indexRoute ?? 'chat.index') }}" class="flex-1 rounded-xl px-4 py-3 text-center transition-all duration-300 {{ $mode === 'all' ? ($isPro ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'bg-white text-slate-950 shadow-sm') : ($isPro ? 'text-slate-500 hover:text-slate-300' : 'text-slate-500 hover:text-slate-900') }}">
                        All
                    </a>
                    <a href="{{ route($savedRoute ?? 'chat.saved') }}" class="flex-1 rounded-xl px-4 py-3 text-center transition-all duration-300 {{ $mode === 'saved' ? ($isPro ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20' : 'bg-white text-slate-950 shadow-sm') : ($isPro ? 'text-slate-500 hover:text-slate-300' : 'text-slate-500 hover:text-slate-900') }}">
                        Saved
                    </a>
                </div>
            </div>

            <div class="chat-scrollbar flex-1 space-y-3 overflow-y-auto p-6">
                @forelse ($conversations as $conversation)
                    <a
                        href="{{ route($showRoute ?? 'chat.show', $conversation) }}"
                        class="{{ $activeConversation && $activeConversation->id === $conversation->id ? ($isPro ? 'bg-indigo-600/10 border-indigo-500/30 text-white ring-1 ring-indigo-500/20 shadow-[0_0_20px_rgba(99,102,241,0.1)]' : 'border-slate-900 bg-slate-950 text-white shadow-[0_18px_40px_rgba(15,23,42,0.16)]') : ($isPro ? 'border-white/5 bg-white/[0.02] text-slate-400 hover:border-white/10 hover:bg-white/[0.04] hover:text-slate-200' : 'border-transparent bg-white/60 text-slate-900 hover:border-slate-200 hover:bg-white') }} group relative block rounded-[1.5rem] border px-6 py-5 transition-all duration-500"
                    >
                        @if($activeConversation && $activeConversation->id === $conversation->id && $isPro)
                            <div class="absolute inset-y-4 left-0 w-1 rounded-full bg-indigo-500 shadow-[0_0_10px_rgba(99,102,241,0.5)]"></div>
                        @endif
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <div class="truncate text-sm font-bold tracking-tight">
                                    {{ $conversation->title ?: 'Untitled Thread' }}
                                </div>
                                <div class="mt-1.5 flex items-center gap-2 text-[10px] font-bold uppercase tracking-widest {{ $activeConversation && $activeConversation->id === $conversation->id ? ($isPro ? 'text-blue-400/80' : 'text-slate-300') : ($isPro ? 'text-slate-600' : 'text-slate-400') }}">
                                    <span>{{ ($conversation->last_message_at ?: $conversation->created_at)->diffForHumans() }}</span>
                                </div>
                            </div>
                            @if ($conversation->is_saved)
                                <div class="{{ $activeConversation && $activeConversation->id === $conversation->id ? 'text-blue-400' : 'text-amber-500/50' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-4 w-4">
                                        <path fill-rule="evenodd" d="M6.32 2.577a4.915 4.915 0 015.68 0l.73.492a1.325 1.325 0 001.54 0l.73-.492a4.915 4.915 0 015.68 0l.16.108a1.325 1.325 0 01.62 1.111v14.733a1.325 1.325 0 01-2.14 1.051l-5.68-4.46a1.325 1.325 0 00-1.54 0l-5.68 4.46a1.325 1.325 0 01-2.14-1.051V3.796a1.325 1.325 0 01.62-1.111l.16-.108z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="rounded-3xl border border-dashed {{ $isPro ? 'border-white/10 bg-white/5' : 'border-slate-200 bg-white/70' }} px-5 py-10 text-center">
                        <p class="text-sm font-medium {{ $isPro ? 'text-white' : 'text-slate-900' }}">No conversations yet</p>
                        <p class="mt-2 text-sm leading-6 {{ $isPro ? 'text-white/40' : 'text-slate-500' }}">Create your first thread to start asking about DILG opinions and legal references.</p>
                    </div>
                @endforelse
            </div>
        </aside>

        <section class="chat-panel flex min-w-0 flex-1 flex-col overflow-hidden rounded-[2.5rem]">
            <div class="border-b {{ $isPro ? 'border-white/5' : 'border-slate-200/70' }} p-8">
                <div class="flex flex-col gap-6 xl:flex-row xl:items-center xl:justify-between">
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-3 mb-4">
                            <span class="flex items-center gap-2 rounded-full {{ $isPro ? 'bg-indigo-500/10 text-indigo-400 ring-1 ring-indigo-500/20 shadow-[0_0_15px_rgba(99,102,241,0.1)]' : 'bg-sky-50 text-sky-700' }} px-4 py-1.5 text-[10px] font-black uppercase tracking-[0.2em]">
                                <div class="glow-dot relative"></div>
                                Live Session
                            </span>
                            @if ($activeConversation && $activeConversation->is_saved)
                                <span class="rounded-full {{ $isPro ? 'bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20' : 'bg-amber-50 text-amber-700' }} px-4 py-1.5 text-[10px] font-black uppercase tracking-[0.2em]">Archived</span>
                            @endif
                        </div>

                        @if ($activeConversation)
                            <form method="POST" action="{{ route('conversations.update', $activeConversation) }}" class="flex max-w-4xl items-center gap-4">
                                @csrf
                                @method('PATCH')
                                <div class="relative flex-1 group">
                                    <div class="{{ $isPro ? 'pro-input-wrapper' : '' }}">
                                        <input
                                            name="title"
                                            value="{{ old('title', $activeConversation->title) }}"
                                            class="w-full border-0 bg-transparent px-6 py-4 text-xl font-black tracking-tight {{ $isPro ? 'text-white placeholder:text-slate-600 focus:ring-0' : 'border-slate-200 bg-white/90 focus:border-sky-500 focus:ring-sky-500' }} shadow-sm"
                                            placeholder="Untitled Session"
                                        >
                                    </div>
                                    <div class="absolute inset-x-6 bottom-0 h-px bg-gradient-to-r from-transparent via-blue-500/50 to-transparent opacity-0 group-focus-within:opacity-100 transition-opacity"></div>
                                </div>
                                <button type="submit" class="shrink-0 rounded-2xl border {{ $isPro ? 'border-white/10 bg-white/5 text-white hover:bg-blue-600 hover:border-blue-500 shadow-lg hover:shadow-blue-600/20' : 'border-slate-200 bg-white text-slate-700 hover:border-slate-300 hover:bg-slate-50' }} px-8 py-4 text-sm font-black uppercase tracking-widest transition-all duration-300 hover:-translate-y-1">
                                    Rename
                                </button>
                            </form>
                        @else
                            <h2 class="text-4xl font-black tracking-tight {{ $isPro ? 'text-white' : 'text-slate-950' }}">Intelligence Hub</h2>
                            <p class="mt-2 text-slate-500 font-medium">Select a conversation or start a new inquiry to begin research.</p>
                        @endif
                    </div>

                    @if ($activeConversation)
                        <div class="flex items-center gap-3">
                            <form method="POST" action="{{ route('conversations.toggle-save', $activeConversation) }}">
                                @csrf
                                <button type="submit" class="h-14 px-6 rounded-2xl border {{ $isPro ? 'border-white/10 bg-white/5 text-slate-300 hover:text-white hover:bg-white/10' : 'border-slate-200 bg-white text-slate-700' }} text-sm font-bold transition-all duration-300">
                                    {{ $activeConversation->is_saved ? 'Unarchive' : 'Archive' }}
                                </button>
                            </form>
                            <form method="POST" action="{{ route('conversations.destroy', $activeConversation) }}" onsubmit="return confirm('Delete this conversation?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="h-14 w-14 flex items-center justify-center rounded-2xl border {{ $isPro ? 'border-rose-500/20 bg-rose-500/5 text-rose-500 hover:bg-rose-500 hover:text-white shadow-lg hover:shadow-rose-500/20' : 'border-rose-200 bg-rose-50 text-rose-700' }} transition-all duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-1.806A2.25 2.25 0 0013.813 1.5h-3.626a2.25 2.25 0 00-2.25 2.25V3m7.5 0H9" />
                                    </svg>
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <div id="chat-scroll" class="chat-scrollbar flex-1 overflow-y-auto p-8">
                @if ($messages->isEmpty())
                    <div class="flex h-full flex-col items-center justify-center text-center">
                        <div class="relative mb-8">
                            <div class="absolute inset-0 bg-indigo-500 blur-[40px] opacity-20 animate-pulse"></div>
                            <div class="relative flex h-24 w-24 items-center justify-center rounded-[2rem] {{ $isPro ? 'bg-gradient-to-br from-indigo-500 to-purple-600' : 'bg-slate-950' }} shadow-2xl">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-10 w-10 text-white">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                                </svg>
                            </div>
                        </div>
                        <h3 class="text-3xl font-black tracking-tight {{ $isPro ? 'text-white' : 'text-slate-950' }}">AI Legal Intelligence</h3>
                        <p class="mt-4 max-w-md text-lg text-slate-500 font-medium leading-relaxed">Ask anything about DILG opinions, local statutes, or procurement laws. Our AI is ready to assist.</p>
                    </div>
                @else
                    <div data-message-stack="true" class="mx-auto flex w-full max-w-5xl flex-col gap-10">
                        @foreach ($messages as $message)
                            <div class="message-enter {{ $message->role === 'user' ? 'ml-auto max-w-2xl' : 'mr-auto max-w-3xl' }}">
                                <div class="mb-4 flex items-center gap-4 px-2 {{ $message->role === 'user' ? 'flex-row-reverse text-right' : '' }}">
                                    <div class="shrink-0 {{ $message->role === 'user' ? ($isPro ? 'bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/20' : 'bg-slate-950') : ($isPro ? 'bg-white/10 ring-1 ring-white/10' : 'bg-sky-100') }} flex h-10 w-10 items-center justify-center rounded-xl text-[10px] font-black uppercase tracking-widest text-white">
                                        {{ $message->role === 'user' ? 'You' : 'AI' }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs font-black uppercase tracking-[0.2em] {{ $isPro ? 'text-white' : 'text-slate-900' }}">{{ $message->role === 'user' ? 'You' : 'LYRA Assistant' }}</div>
                                        <div class="text-[10px] font-bold text-slate-600 uppercase tracking-widest mt-0.5">
                                            {{ $message->created_at->format('h:i A') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="{{ $message->role === 'user' ? ($isPro ? 'rounded-[2rem_2rem_0.5rem_2rem] message-bubble-user text-white' : 'rounded-[2rem_2rem_0.5rem_2rem] bg-slate-950 text-white') : ($isPro ? 'rounded-[2rem_2rem_2rem_0.5rem] message-bubble-ai text-white/90' : 'rounded-[2rem_2rem_2rem_0.5rem] border border-slate-200 bg-white text-slate-800 shadow-sm') }} px-8 py-6 shadow-2xl">
                                    <div class="whitespace-pre-wrap text-[15px] leading-relaxed font-medium tracking-wide">{{ $message->content }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="border-t {{ $isPro ? 'border-white/5 bg-white/[0.01]' : 'border-slate-200/70 bg-white/70' }} p-8">
                <form
                    id="chat-form"
                    class="mx-auto flex w-full max-w-5xl flex-col gap-4"
                    data-create-url="{{ route('conversations.store') }}"
                    data-messages-url="{{ $activeConversation ? route('messages.store', $activeConversation) : '' }}"
                    data-active-conversation-url="{{ $activeConversation ? route($showRoute ?? 'chat.show', $activeConversation) : '' }}"
                >
                    <div class="group relative overflow-hidden {{ $isPro ? 'pro-input-wrapper' : 'rounded-[2.5rem] border border-slate-200 bg-white' }} transition-all duration-500">
                        <textarea
                            id="chat-prompt"
                            rows="3"
                            class="w-full resize-none border-0 bg-transparent px-8 py-6 text-base font-medium {{ $isPro ? 'text-white placeholder:text-slate-600 focus:ring-0' : 'text-slate-800 placeholder:text-slate-400' }}"
                            placeholder="Type your legal inquiry here..."
                        ></textarea>
                        <div class="flex items-center justify-between border-t {{ $isPro ? 'border-white/5' : 'border-slate-100' }} px-8 py-5">
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] {{ $isPro ? 'text-slate-600' : 'text-slate-400' }}">Neural Engine Active</p>
                            <button id="chat-send" type="submit" class="group flex items-center gap-3 rounded-2xl {{ $isPro ? 'bg-indigo-600 hover:bg-indigo-500 shadow-lg shadow-indigo-600/20' : 'bg-slate-950 hover:bg-slate-800' }} px-8 py-3.5 text-[11px] font-black uppercase tracking-[0.2em] text-white transition-all duration-300 hover:-translate-y-1">
                                <span>Send Inquiry</span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-4 w-4 transition-transform group-hover:translate-x-1">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.76 0 013.27 20.876L5.999 12zm0 0h7.5" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
                <div id="chat-error" class="mx-auto hidden w-full max-w-5xl mt-4 rounded-2xl border border-rose-500/20 bg-rose-500/10 px-6 py-4 text-sm font-bold text-rose-400"></div>
            </div>
        </section>
    </div>
</div>

<script type="module">
    const form = document.getElementById('chat-form');
    const promptEl = document.getElementById('chat-prompt');
    const sendBtn = document.getElementById('chat-send');
    const scrollEl = document.getElementById('chat-scroll');
    const errorEl = document.getElementById('chat-error');
    const isPro = @json($isPro);

    const renderMessage = (role, content) => {
        const container = document.createElement('div');
        container.className = 'message-enter ' + (role === 'user' ? 'ml-auto max-w-2xl' : 'mr-auto max-w-3xl');

        const meta = document.createElement('div');
        meta.className = 'mb-4 flex items-center gap-4 px-2 ' + (role === 'user' ? 'flex-row-reverse text-right' : '');

        const avatar = document.createElement('div');
        if (isPro) {
            avatar.className = (role === 'user'
                ? 'bg-gradient-to-br from-indigo-500 to-purple-600 shadow-lg shadow-indigo-500/20'
                : 'bg-white/10 ring-1 ring-white/10') + ' shrink-0 flex h-10 w-10 items-center justify-center rounded-xl text-[10px] font-black uppercase tracking-widest text-white';
        } else {
            avatar.className = (role === 'user'
                ? 'bg-slate-950 text-white'
                : 'bg-sky-100 text-sky-800') + ' flex h-10 w-10 items-center justify-center rounded-xl text-[10px] font-black uppercase tracking-widest';
        }
        avatar.textContent = role === 'user' ? 'You' : 'AI';

        const metaText = document.createElement('div');
        metaText.className = 'min-w-0';

        const label = document.createElement('div');
        label.className = 'text-xs font-black uppercase tracking-[0.2em] ' + (isPro ? 'text-white' : 'text-slate-900');
        label.textContent = role === 'user' ? 'You' : 'LYRA Assistant';

        const stamp = document.createElement('div');
        stamp.className = 'text-[10px] font-bold text-slate-600 uppercase tracking-widest mt-0.5';
        stamp.textContent = new Date().toLocaleTimeString([], {
            hour: '2-digit',
            minute: '2-digit',
        });

        metaText.appendChild(label);
        metaText.appendChild(stamp);
        meta.appendChild(avatar);
        meta.appendChild(metaText);

        const bubble = document.createElement('div');
        if (isPro) {
            bubble.className = (role === 'user'
                ? 'rounded-[2rem_2rem_0.5rem_2rem] message-bubble-user text-white'
                : 'rounded-[2rem_2rem_2rem_0.5rem] message-bubble-ai text-white/90') + ' px-8 py-6 shadow-2xl';
        } else {
            bubble.className = (role === 'user'
                ? 'rounded-[2rem_2rem_0.5rem_2rem] bg-slate-950 text-white shadow-lg'
                : 'rounded-[2rem_2rem_2rem_0.5rem] border border-slate-200/80 bg-white text-slate-800 shadow-sm') + ' px-8 py-6';
        }

        const body = document.createElement('div');
        body.className = 'whitespace-pre-wrap text-[15px] leading-relaxed font-medium tracking-wide';
        body.textContent = content;

        bubble.appendChild(body);
        container.appendChild(meta);
        container.appendChild(bubble);

        let stack = scrollEl.querySelector('[data-message-stack]');
        if (!stack) {
            scrollEl.innerHTML = '';
            stack = document.createElement('div');
            stack.dataset.messageStack = 'true';
            stack.className = 'mx-auto flex w-full max-w-5xl flex-col gap-10';
            scrollEl.appendChild(stack);
        }

        stack.appendChild(container);
        return container;
    };

    const scrollToBottom = () => {
        scrollEl.scrollTop = scrollEl.scrollHeight;
    };

    const ensureConversation = async () => {
        const existingUrl = form.dataset.activeConversationUrl;
        const existingMessagesUrl = form.dataset.messagesUrl;

        if (existingUrl && existingMessagesUrl) {
            return { url: existingUrl, messagesUrl: existingMessagesUrl };
        }

        const resp = await window.axios.post(form.dataset.createUrl, {}, { headers: { Accept: 'application/json' } });
        form.dataset.activeConversationUrl = resp.data.url;
        form.dataset.messagesUrl = resp.data.messages_url;
        window.history.replaceState({}, '', resp.data.url);

        return { url: resp.data.url, messagesUrl: resp.data.messages_url };
    };

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        errorEl.classList.add('hidden');
        errorEl.textContent = '';

        const prompt = (promptEl.value || '').trim();
        if (!prompt) return;

        sendBtn.disabled = true;
        promptEl.disabled = true;

        renderMessage('user', prompt);
        promptEl.value = '';
        scrollToBottom();

        try {
            const conv = await ensureConversation();
            const resp = await window.axios.post(conv.messagesUrl, { prompt }, { headers: { Accept: 'application/json' } });
            renderMessage('assistant', resp.data.assistant_message.content);
            scrollToBottom();
        } catch (err) {
            const message = err?.response?.data?.message || 'Something went wrong while contacting the AI provider.';
            errorEl.textContent = message;
            errorEl.classList.remove('hidden');
        } finally {
            sendBtn.disabled = false;
            promptEl.disabled = false;
            promptEl.focus();
        }
    });

    promptEl.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            form.requestSubmit();
        }
    });

    scrollToBottom();
</script>
