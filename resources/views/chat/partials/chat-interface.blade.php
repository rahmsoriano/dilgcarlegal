@php
    $isPro = ($theme ?? '') === 'pro';
@endphp

<style>
    .chat-shell {
        background: {{ $isPro ? 'transparent' : 'radial-gradient(circle at top left, rgba(14, 165, 233, 0.14), transparent 28%), radial-gradient(circle at bottom right, rgba(99, 102, 241, 0.14), transparent 32%), linear-gradient(180deg, #f8fbff 0%, #eef4ff 100%)' }};
    }

    .chat-panel {
        backdrop-filter: blur(24px);
        background: {{ $isPro ? 'rgba(255, 255, 255, 0.85)' : 'rgba(255, 255, 255, 0.82)' }};
        box-shadow: {{ $isPro ? '0 24px 70px rgba(15, 23, 42, 0.08)' : '0 24px 80px rgba(15, 23, 42, 0.08)' }};
        border: {{ $isPro ? '1px solid rgba(15, 23, 42, 0.08)' : '1px solid rgba(255, 255, 255, 0.7)' }};
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .pro-input-wrapper {
        background: rgba(248, 250, 252, 0.9);
        border: 1px solid rgba(15, 23, 42, 0.10);
        border-radius: 1.5rem;
        transition: all 0.3s ease;
    }

    .pro-input-wrapper:focus-within {
        background: rgba(255, 255, 255, 1);
        border-color: rgba(99, 102, 241, 0.45);
        box-shadow: 0 0 30px rgba(99, 102, 241, 0.14);
    }

    .message-bubble-user {
        background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
        box-shadow: 0 12px 30px -10px rgba(37, 99, 235, 0.35);
    }

    .message-bubble-ai {
        background: rgba(255, 255, 255, 0.7);
        border: 1px solid rgba(15, 23, 42, 0.08);
        backdrop-filter: blur(10px);
    }

    @keyframes chat-fade-in {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .chat-reply-fade-in {
        animation: chat-fade-in 260ms ease-out both;
    }

    @keyframes chat-dot-blink {
        0%, 80%, 100% { opacity: 0.25; }
        40% { opacity: 1; }
    }

    .chat-thinking-dots span {
        display: inline-block;
        width: 0.5em;
        text-align: center;
        animation: chat-dot-blink 1.2s infinite;
    }
    .chat-thinking-dots span:nth-child(2) { animation-delay: 0.2s; }
    .chat-thinking-dots span:nth-child(3) { animation-delay: 0.4s; }

    .chat-scrollbar::-webkit-scrollbar {
        width: 5px;
    }

    .chat-scrollbar::-webkit-scrollbar-thumb {
        background: {{ $isPro ? 'rgba(15, 23, 42, 0.12)' : 'rgba(148, 163, 184, 0.45)' }};
        border-radius: 999px;
    }

    .chat-scrollbar::-webkit-scrollbar-thumb:hover {
        background: {{ $isPro ? 'rgba(15, 23, 42, 0.2)' : 'rgba(148, 163, 184, 0.6)' }};
    }

    @keyframes pulse-glow {
        0%, 100% { opacity: 0.5; transform: scale(1); }
        50% { opacity: 0.8; transform: scale(1.05); }
    }

    .glow-dot {
        position: absolute;
        width: 4px;
        height: 4px;
        background: #2563eb;
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

<style>
    /* Force hide the global loading overlay for the chat interface as requested */
    #global-loading-overlay {
        display: none !important;
    }
</style>

<div class="chat-shell h-full min-h-0 {{ $isPro ? '' : 'px-4 py-4 sm:px-6 lg:px-8' }}">
    <div class="mx-auto flex h-full min-h-0 w-full {{ $isPro ? 'max-w-full' : 'max-w-[1700px]' }}">
        <section class="chat-panel flex h-full min-h-0 w-full flex-col overflow-hidden rounded-[2.5rem]">
            <div class="flex flex-1 min-h-0 flex-col">
                <div class="h-2"></div>

            <div id="chat-scroll" class="chat-scrollbar flex-1 overflow-y-auto p-8">
                @if ($messages->isEmpty())
                    <div class="flex h-full flex-col items-center justify-center text-center">
                        <div class="relative mb-8">
                            <div class="absolute inset-0 bg-[#002C76] blur-[40px] opacity-20 animate-pulse"></div>
                            <div class="relative flex h-24 w-24 items-center justify-center overflow-hidden rounded-full bg-white shadow-2xl">
                                <img
                                    src="https://upload.wikimedia.org/wikipedia/commons/c/c9/Department_of_the_Interior_and_Local_Government_%28DILG%29_Seal_-_Logo.svg"
                                    alt="DILG Seal"
                                    class="h-full w-full object-contain"
                                >
                            </div>
                        </div>
                        <h3 class="text-3xl font-black tracking-tight {{ $isPro ? 'text-slate-900' : 'text-slate-950' }}">What can Lex assist you today?</h3>
                        <p class="mt-4 max-w-md text-lg text-slate-500 font-medium leading-relaxed">Ask about legal opinions.</p>
                    </div>
                @else
                    <div data-message-stack="true" class="mx-auto flex w-full max-w-6xl flex-col gap-10">
                        @foreach ($messages as $message)
                            <div class="message-enter {{ $message->role === 'user' ? 'ml-auto max-w-2xl' : 'mr-auto max-w-3xl' }}">
                                <div class="mb-4 flex items-center gap-4 px-2 {{ $message->role === 'user' ? 'flex-row-reverse text-right' : '' }}">
                                    <div class="shrink-0 {{ $message->role === 'user' ? ($isPro ? 'shadow-sm' : 'bg-slate-950') : ($isPro ? 'bg-slate-900/[0.04] ring-1 ring-slate-900/10' : 'bg-sky-100') }} flex h-10 w-10 items-center justify-center rounded-xl text-[10px] font-black uppercase tracking-widest {{ $message->role === 'user' ? 'text-white' : ($isPro ? 'text-slate-800' : 'text-sky-800') }}" style="{{ $message->role === 'user' && $isPro ? 'background-color: #002C76 !important;' : '' }}">
                                        {{ $message->role === 'user' ? 'You' : 'LX' }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs font-black uppercase tracking-[0.2em] {{ $isPro ? 'text-slate-900' : 'text-slate-900' }}">{{ $message->role === 'user' ? 'You' : 'Lex' }}</div>
                                    </div>
                                </div>

                                <div class="{{ $message->role === 'user' ? ($isPro ? 'rounded-[2rem_2rem_0.5rem_2rem] message-bubble-user text-white' : 'rounded-[2rem_2rem_0.5rem_2rem] bg-slate-950 text-white') : ($isPro ? 'rounded-[2rem_2rem_2rem_0.5rem] message-bubble-ai text-slate-800' : 'rounded-[2rem_2rem_2rem_0.5rem] border border-slate-200 bg-white text-slate-800 shadow-sm') }} px-8 py-6 shadow-2xl">
                                    <div class="whitespace-pre-wrap text-[30px] leading-relaxed font-medium tracking-wide">{!! $message->content !!}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="border-t {{ $isPro ? 'border-slate-900/[0.03] bg-white/50' : 'border-slate-200/50 bg-white/70' }} px-6 py-4 sm:px-8 sm:py-5">
                <form
                    id="chat-form"
                    data-loader-skip
                    class="mx-auto flex w-full max-w-6xl flex-col gap-4"
                    data-create-url="{{ route($createRoute ?? 'conversations.store') }}"
                    data-messages-url="{{ $activeConversation ? route($messagesRoute ?? 'messages.store', $activeConversation->id) : '' }}"
                    data-active-conversation-url="{{ $activeConversation ? route($showRoute ?? 'chat.show', $activeConversation->id) : '' }}"
                    data-conversation-id="{{ $activeConversation?->id }}"
                >
                    <div class="group relative overflow-hidden {{ $isPro ? 'pro-input-wrapper' : 'rounded-[2rem] border border-slate-200 bg-white' }} transition-all duration-500">
                        <div class="flex items-center gap-3 px-8 py-3">
                            <textarea
                                id="chat-prompt"
                                rows="1"
                                class="min-w-0 flex-1 resize-none border-0 bg-transparent p-0 text-base font-medium leading-6 {{ $isPro ? 'text-slate-900 placeholder:text-slate-400 focus:ring-0' : 'text-slate-800 placeholder:text-slate-400' }}"
                                placeholder="Type your legal inquiry here..."
                            ></textarea>
                            <button id="chat-send" type="submit" aria-label="Send" class="group flex h-10 w-10 items-center justify-center rounded-xl bg-[#002C76] text-[#FFDE15] shadow-md shadow-slate-900/10 transition-all duration-200 hover:bg-[#002C76]/95" style="background-color: #002C76 !important; color: #FFDE15 !important;">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 transition-transform duration-200 group-hover:translate-x-0.5" style="width: 20px; height: 20px; display: block; color: #FFDE15 !important;">
                                    <path d="M3.478 2.405a.75.75 0 0 1 .81-.163l18 8.25a.75.75 0 0 1 0 1.362l-18 8.25A.75.75 0 0 1 3 19.5v-6.764a.75.75 0 0 1 .553-.724L12 9.75 3.553 7.488A.75.75 0 0 1 3 6.764V3a.75.75 0 0 1 .478-.595Z"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                </form>
                <div id="chat-error" class="mx-auto hidden w-full max-w-6xl mt-4 rounded-2xl border border-rose-500/20 bg-rose-500/10 px-6 py-4 text-sm font-bold text-rose-400"></div>
            </div>
            </div>
        </section>
    </div>

    <!-- Opinion Viewer Modal -->
    <div id="opinion-modal" class="fixed inset-0 z-[9999] hidden flex items-center justify-center p-4 sm:p-6" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/40 backdrop-blur-[6px] transition-opacity" id="opinion-modal-overlay"></div>

        <div class="relative w-full max-w-4xl max-h-[90vh] flex flex-col transform overflow-hidden rounded-[2.5rem] bg-white ring-1 ring-slate-900/10 shadow-[0_24px_70px_rgba(15,23,42,0.18)] transition-all">
            <div class="absolute right-6 top-6 z-10">
                <button type="button" id="close-opinion-modal" class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 hover:text-slate-700 transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto px-8 pb-10 pt-12 sm:px-12 sm:pb-12 sm:pt-16 chat-scrollbar">
                <div id="opinion-modal-content" class="opacity-0 transition-opacity duration-300">
                    <div class="mb-8">
                        <div id="opinion-modal-number" class="inline-flex rounded-full bg-indigo-50 px-4 py-1.5 text-xs font-black uppercase tracking-[0.2em] text-indigo-600 ring-1 ring-indigo-500/10 mb-4"></div>
                        <h2 id="opinion-modal-title" class="text-3xl font-black tracking-tight text-slate-900 leading-tight"></h2>
                        <div id="opinion-modal-date" class="mt-3 text-sm font-bold text-slate-500 uppercase tracking-widest"></div>
                    </div>

                    <div class="prose prose-slate max-w-none">
                        <div id="opinion-modal-body" class="whitespace-pre-wrap text-base leading-relaxed text-slate-700 font-medium"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="module">
    const form = document.getElementById('chat-form');
    const promptEl = document.getElementById('chat-prompt');
    const sendBtn = document.getElementById('chat-send');
    const scrollEl = document.getElementById('chat-scroll');
    const errorEl = document.getElementById('chat-error');
    const isPro = @json($isPro);
    const sidebarList = document.getElementById('sidebar-chats-list');
    const sidebarEmpty = document.getElementById('sidebar-chats-empty');

    const upsertSidebarConversation = ({ id, url, title, is_pinned, update_url, toggle_pin_url, toggle_save_url, delete_url }) => {
        if (window.__adminSidebarUpsertConversation) {
            return window.__adminSidebarUpsertConversation({ id, url, title, is_pinned, update_url, toggle_pin_url, toggle_save_url, delete_url });
        }

        if (!sidebarList || !id || !url) return null;

        const displayTitle = (title && String(title).trim()) ? String(title).trim() : 'Untitled Thread';
        const selector = `[data-conversation-id="${id}"]`;
        let link = sidebarList.querySelector(selector);

        if (!link) {
            link = document.createElement('a');
            link.dataset.conversationId = String(id);
            link.href = url;
            link.className = 'block rounded-2xl border px-4 py-3 transition-all';
            link.style.borderColor = '#FFDE15';
            link.style.backgroundColor = 'white';
            link.style.color = '#002C76';
            link.style.boxShadow = '0 1px 2px 0 rgba(0, 0, 0, 0.05)';
            const text = document.createElement('div');
            text.className = 'truncate text-sm font-semibold tracking-tight';
            link.appendChild(text);
            sidebarList.prepend(link);
        }

        const textEl = link.querySelector('div');
        if (textEl) textEl.textContent = displayTitle;

        if (sidebarEmpty) sidebarEmpty.remove();

        if (link !== sidebarList.firstElementChild) {
            sidebarList.prepend(link);
        }

        return link;
    };

    const normalizeTitle = (text) => {
        const cleaned = String(text || '').replace(/\s+/g, ' ').trim();
        return cleaned.length > 60 ? cleaned.slice(0, 60) : cleaned;
    };

    const renderMessage = (role, content) => {
        const container = document.createElement('div');
        container.className = 'message-enter ' + (role === 'user' ? 'ml-auto max-w-2xl' : 'mr-auto max-w-3xl');

        const meta = document.createElement('div');
        meta.className = 'mb-4 flex items-center gap-4 px-2 ' + (role === 'user' ? 'flex-row-reverse text-right' : '');

        const avatar = document.createElement('div');
        if (isPro) {
            avatar.className = (role === 'user'
                ? 'bg-[#002C76] shadow-sm text-white'
                : 'bg-slate-900/[0.04] ring-1 ring-slate-900/10 text-slate-800') + ' shrink-0 flex h-10 w-10 items-center justify-center rounded-xl text-[10px] font-black uppercase tracking-widest';
        } else {
            avatar.className = (role === 'user'
                ? 'bg-slate-950 text-white'
                : 'bg-sky-100 text-sky-800') + ' flex h-10 w-10 items-center justify-center rounded-xl text-[10px] font-black uppercase tracking-widest';
        }
        avatar.textContent = role === 'user' ? 'You' : 'LX';

        const metaText = document.createElement('div');
        metaText.className = 'min-w-0';

        const label = document.createElement('div');
        label.className = 'text-xs font-black uppercase tracking-[0.2em] ' + (isPro ? 'text-slate-900' : 'text-slate-900');
        label.textContent = role === 'user' ? 'You' : 'Lex';

        metaText.appendChild(label);
        meta.appendChild(avatar);
        meta.appendChild(metaText);

        const bubble = document.createElement('div');
        if (isPro) {
            bubble.className = (role === 'user'
                ? 'rounded-[2rem_2rem_0.5rem_2rem] message-bubble-user text-white'
                : 'rounded-[2rem_2rem_2rem_0.5rem] message-bubble-ai text-slate-800') + ' px-8 py-6 shadow-2xl';
        } else {
            bubble.className = (role === 'user'
                ? 'rounded-[2rem_2rem_0.5rem_2rem] bg-slate-950 text-white shadow-lg'
                : 'rounded-[2rem_2rem_2rem_0.5rem] border border-slate-200/80 bg-white text-slate-800 shadow-sm') + ' px-8 py-6';
        }

        const body = document.createElement('div');
        body.className = 'whitespace-pre-wrap text-[24px] leading-relaxed font-medium tracking-wide';
        if (role === 'user') {
            body.textContent = content;
        } else {
            body.innerHTML = content;
        }

        bubble.appendChild(body);
        container.appendChild(meta);
        container.appendChild(bubble);

        let stack = scrollEl.querySelector('[data-message-stack]');
        if (!stack) {
            scrollEl.innerHTML = '';
            stack = document.createElement('div');
            stack.dataset.messageStack = 'true';
            stack.className = 'mx-auto flex w-full max-w-6xl flex-col gap-10';
            scrollEl.appendChild(stack);
        }

        stack.appendChild(container);
        return container;
    };

    const renderThinkingMessage = () => {
        const el = renderMessage('assistant', 'Thinking');
        const body = el.querySelector('.whitespace-pre-wrap');
        if (body) {
            body.innerHTML = 'Thinking<span class="chat-thinking-dots"><span>.</span><span>.</span><span>.</span></span>';
        }
        el.dataset.thinking = 'true';
        return el;
    };

    const scrollToBottom = () => {
        scrollEl.scrollTop = scrollEl.scrollHeight;
    };

    const ensureConversation = async () => {
        const existingUrl = form.dataset.activeConversationUrl;
        const existingMessagesUrl = form.dataset.messagesUrl;
        const existingId = form.dataset.conversationId;

        if (existingUrl && existingMessagesUrl && existingId) {
            return { id: existingId, url: existingUrl, messagesUrl: existingMessagesUrl };
        }

        const resp = await window.axios.post(form.dataset.createUrl, {}, { 
            headers: { 
                Accept: 'application/json',
                'X-Loader-Skip': 'true'
            },
            timeout: 45000,
        });
        form.dataset.conversationId = String(resp.data.id);
        form.dataset.activeConversationUrl = resp.data.url;
        form.dataset.messagesUrl = resp.data.messages_url;
        window.history.replaceState({}, '', resp.data.url);

        upsertSidebarConversation({
            id: resp.data.id,
            url: resp.data.url,
            title: resp.data.title,
            is_pinned: false,
            update_url: resp.data.update_url,
            toggle_pin_url: resp.data.toggle_pin_url,
            toggle_save_url: resp.data.toggle_save_url,
            delete_url: resp.data.delete_url,
        });

        return { id: resp.data.id, url: resp.data.url, messagesUrl: resp.data.messages_url };
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
        const thinkingEl = renderThinkingMessage();
        scrollToBottom();

        try {
            const conv = await ensureConversation();
            const resp = await window.axios.post(conv.messagesUrl, { prompt }, { 
                headers: { 
                    Accept: 'application/json',
                    'X-Loader-Skip': 'true'
                },
                timeout: 45000,
            });
            const content = resp?.data?.assistant_message?.content ?? '';
            if (thinkingEl && thinkingEl.dataset.thinking === 'true') {
                const body = thinkingEl.querySelector('.whitespace-pre-wrap');
                if (body) {
                    body.innerHTML = content;
                    body.classList.remove('chat-reply-fade-in');
                    void body.offsetWidth;
                    body.classList.add('chat-reply-fade-in');
                } else {
                    thinkingEl.remove();
                    renderMessage('assistant', content);
                }
                delete thinkingEl.dataset.thinking;
            } else {
                const el = renderMessage('assistant', content);
                const body = el.querySelector('.whitespace-pre-wrap');
                if (body) {
                    body.classList.add('chat-reply-fade-in');
                }
            }
            upsertSidebarConversation({ id: conv.id, url: conv.url, title: normalizeTitle(prompt), is_pinned: false });
            scrollToBottom();
        } catch (err) {
            if (thinkingEl && thinkingEl.dataset.thinking === 'true') thinkingEl.remove();
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

    // Opinion Modal Logic
    const opinionModal = document.getElementById('opinion-modal');
    const opinionModalContent = document.getElementById('opinion-modal-content');
    const closeOpinionModalBtn = document.getElementById('close-opinion-modal');
    const opinionModalOverlay = document.getElementById('opinion-modal-overlay');

    const openOpinionModal = async (opinionId) => {
        // Force hide global loader if it's stuck
        if (window.__globalLoaderStop) window.__globalLoaderStop();
        
        opinionModal.classList.remove('hidden');
        opinionModalContent.classList.add('opacity-0');
        document.body.classList.add('overflow-hidden');

        try {
            const resp = await window.axios.get(`/api/opinions/${opinionId}`, {
                headers: { 'X-Loader-Skip': 'true' }
            });
            const data = resp.data;

            document.getElementById('opinion-modal-number').textContent = data.opinion_number;
            document.getElementById('opinion-modal-title').textContent = data.title;
            document.getElementById('opinion-modal-date').textContent = data.date;
            document.getElementById('opinion-modal-body').textContent = data.context;

            opinionModalContent.classList.remove('opacity-0');
        } catch (err) {
            console.error('Failed to fetch opinion details:', err);
            closeOpinionModal();
        }
    };

    const closeOpinionModal = () => {
        opinionModal.classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    };

    if (closeOpinionModalBtn) closeOpinionModalBtn.addEventListener('click', closeOpinionModal);
    if (opinionModalOverlay) opinionModalOverlay.addEventListener('click', closeOpinionModal);
    
    window.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeOpinionModal();
    });

    document.addEventListener('click', (e) => {
        const link = e.target.closest('.opinion-link');
        if (link) {
            e.preventDefault();
            e.stopPropagation(); // Stop event bubbling
            const opinionId = link.dataset.opinionId;
            openOpinionModal(opinionId);
            return false;
        }
    }, true); // Use capture phase to intercept early
</script>
