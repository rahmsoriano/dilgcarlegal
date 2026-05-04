@php
    $isPro = ($theme ?? '') === 'pro';
@endphp

<style>
    .chat-shell {
        background: {{ $isPro ? 'transparent' : 'radial-gradient(circle at top left, rgba(14, 165, 233, 0.14), transparent 28%), radial-gradient(circle at bottom right, rgba(99, 102, 241, 0.14), transparent 32%), linear-gradient(180deg, #f8fbff 0%, #eef4ff 100%)' }};
    }

    .chat-panel {
        position: relative;
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
        background: transparent;
        border: 0;
        backdrop-filter: none;
    }

    .chat-user-bubble {
        position: relative;
    }

    .chat-msg-tools {
        position: absolute;
        right: 10px;
        bottom: -20px;
        display: flex;
        align-items: center;
        gap: 6px;
        opacity: 0;
        transform: translateY(-2px);
        pointer-events: none;
        transition: opacity 180ms ease-in-out, transform 180ms ease-in-out;
        z-index: 5;
    }

    .chat-user-bubble:hover .chat-msg-tools {
        opacity: 1;
        transform: translateY(0);
        pointer-events: auto;
    }

    .chat-msg-tools-btn {
        width: 36px;
        height: 36px;
        border-radius: 12px;
        border: 1px solid rgba(15, 23, 42, 0.14);
        background: rgba(255, 255, 255, 0.92);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        cursor: pointer;
        position: relative;
    }

    .chat-msg-tools-btn svg {
        width: 18px;
        height: 18px;
        color: rgba(15, 23, 42, 0.72);
    }

    .chat-msg-tools-btn::after {
        content: attr(data-tooltip);
        position: absolute;
        left: 50%;
        top: 100%;
        transform: translate(-50%, 8px);
        padding: 6px 10px;
        border-radius: 9999px;
        background: rgba(15, 23, 42, 0.92);
        color: #ffffff;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.01em;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 140ms ease-in-out, transform 140ms ease-in-out;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.25);
    }

    .chat-msg-tools-btn:hover::after {
        opacity: 1;
        transform: translate(-50%, 12px);
    }

    .chat-user-bubble.is-editing .chat-msg-tools {
        display: none;
    }

    .message-enter.is-editing-wide {
        max-width: none !important;
        width: 100% !important;
    }

    .chat-user-bubble.is-editing {
        width: min(1100px, 100%);
        margin-left: auto;
        background: rgba(0, 44, 118, 0.12) !important;
        border: 1px solid rgba(0, 44, 118, 0.18) !important;
        border-radius: 26px !important;
        padding: 18px 18px 16px !important;
        box-shadow: 0 22px 60px rgba(15, 23, 42, 0.14) !important;
    }

    .chat-edit-textarea {
        width: 100%;
        min-height: 130px;
        resize: none;
        border: 1px solid rgba(0, 44, 118, 0.25);
        outline: none;
        background: rgba(255, 255, 255, 0.85);
        color: rgba(15, 23, 42, 0.95);
        font: inherit;
        line-height: 1.4;
        padding: 16px 18px;
        border-radius: 20px;
    }

    .chat-user-bubble.is-editing .chat-edit-textarea {
        background: rgba(255, 255, 255, 0.92);
    }

    .chat-edit-actions {
        margin-top: 14px;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    .chat-edit-action-btn {
        height: 38px;
        padding: 0 18px;
        border-radius: 9999px;
        border: 1px solid rgba(0, 44, 118, 0.22);
        background: rgba(255, 255, 255, 0.75);
        color: rgba(15, 23, 42, 0.92);
        font-weight: 800;
        font-size: 14px;
        cursor: pointer;
    }

    .chat-edit-action-btn.primary {
        background: rgba(0, 44, 118, 0.92);
        border-color: rgba(0, 44, 118, 0.92);
        color: #ffffff;
    }

    .chat-scroll-bottom-btn {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        bottom: 104px;
        width: 42px;
        height: 42px;
        border-radius: 9999px;
        border: 1px solid rgba(15, 23, 42, 0.14);
        background: rgba(255, 255, 255, 0.92);
        box-shadow: 0 16px 40px rgba(15, 23, 42, 0.18);
        display: none;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 20;
    }

    .chat-scroll-bottom-btn.is-visible {
        display: flex;
    }

    .chat-scroll-bottom-btn svg {
        width: 20px;
        height: 20px;
        color: rgba(15, 23, 42, 0.72);
    }

    @media (max-width: 640px) {
        .chat-scroll-bottom-btn {
            left: 50%;
            transform: translateX(-50%);
            bottom: 96px;
        }
    }

    .ref-accordion {
        --ref-fade-rgb: 255, 255, 255;
        --ref-fade-alpha: 1;
        margin-top: 1px;
        padding-top: 1px;
        border-top: 1px solid rgba(15, 23, 42, 0.10);
        border-radius: 14px;
        user-select: none;
        cursor: pointer;
        padding: 6px 8px;
        transition: background-color 160ms ease-in-out;
    }

    .ref-accordion:hover {
        background: rgba(15, 23, 42, 0.04);
    }

    .ref-accordion:focus-visible {
        outline: 2px solid rgba(0, 44, 118, 0.35);
        outline-offset: 2px;
        background: rgba(0, 44, 118, 0.10);
    }

    .ref-accordion-head {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .ref-accordion-arrow {
        flex: 0 0 auto;
        color: rgba(15, 23, 42, 0.65);
    }

    .ref-accordion-title {
        flex: 1 1 auto;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ref-accordion-toggle {
        flex: 0 0 auto;
        width: 24px;
        height: 24px;
        border-radius: 8px;
        border: 1px solid rgba(15, 23, 42, 0.10);
        background: rgba(255, 255, 255, 0.75);
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: transform 220ms ease-in-out, background-color 220ms ease-in-out, border-color 220ms ease-in-out;
        padding: 0;
    }

    .ref-accordion-toggle:hover {
        background: rgba(15, 23, 42, 0.04);
        border-color: rgba(15, 23, 42, 0.16);
    }

    .ref-accordion-chevron {
        width: 14px;
        height: 14px;
        color: rgba(15, 23, 42, 0.70);
        transition: transform 220ms ease-in-out;
    }

    .ref-accordion.is-open .ref-accordion-chevron {
        transform: rotate(180deg);
    }

    .ref-accordion-body {
        margin-top: 4px;
        padding: 0;
        border-radius: 0;
        background: transparent;
        border: 0;
        white-space: normal;
        position: relative;
        overflow: hidden;
        line-height: 1.35;
        max-height: calc(1 * 1.35em);
        transition: max-height 220ms ease-in-out;
        color: rgba(15, 23, 42, 0.72);
    }

    .ref-accordion-body::after {
        content: "";
        position: absolute;
        left: 0;
        right: 0;
        bottom: 0;
        height: calc(1.35em);
        background: linear-gradient(
            to bottom,
            rgba(var(--ref-fade-rgb), 0) 0%,
            rgba(var(--ref-fade-rgb), 0.35) 35%,
            rgba(var(--ref-fade-rgb), var(--ref-fade-alpha)) 100%
        );
        pointer-events: none;
    }

    .ref-accordion.is-open .ref-accordion-body {
        max-height: 800px;
    }

    .ref-accordion.is-open .ref-accordion-body::after {
        display: none;
    }

    .external-source-link {
        font-style: italic;
        color: #2563eb;
        text-decoration: underline;
        word-break: break-all;
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
                            <div class="message-enter {{ $message->role === 'user' ? 'ml-auto max-w-2xl' : 'mr-auto w-full' }}">
                                <div class="mb-4 flex items-center gap-4 px-2 {{ $message->role === 'user' ? 'flex-row-reverse text-right' : '' }}">
                                    <div class="shrink-0 {{ $message->role === 'user' ? ($isPro ? 'shadow-sm' : 'bg-slate-950') : ($isPro ? 'bg-slate-900/[0.04] ring-1 ring-slate-900/10' : 'bg-sky-100') }} flex h-10 w-10 items-center justify-center rounded-xl text-[10px] font-black uppercase tracking-widest {{ $message->role === 'user' ? 'text-white' : ($isPro ? 'text-slate-800' : 'text-sky-800') }}" style="{{ $message->role === 'user' && $isPro ? 'background-color: #002C76 !important;' : '' }}">
                                        {{ $message->role === 'user' ? 'You' : 'LX' }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-xs font-black uppercase tracking-[0.2em] {{ $isPro ? 'text-slate-900' : 'text-slate-900' }}">{{ $message->role === 'user' ? 'You' : 'Lex' }}</div>
                                    </div>
                                </div>

                                <div class="{{ $message->role === 'user' ? ($isPro ? 'rounded-[2rem_2rem_0.5rem_2rem] message-bubble-user text-white chat-user-bubble' : 'rounded-[2rem_2rem_0.5rem_2rem] bg-slate-950 text-white chat-user-bubble') : 'message-bubble-ai text-slate-800' }} {{ $message->role === 'user' ? 'px-8 py-6 shadow-2xl' : 'px-2 py-1' }}" @if($message->role === 'user') data-user-message="{{ e($message->content) }}" @endif>
                                    @if ($message->role === 'user')
                                        <div class="chat-msg-tools" data-msg-tools>
                                            <button type="button" class="chat-msg-tools-btn" data-msg-copy data-tooltip="Copy message" aria-label="Copy message">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M7.75 3A2.75 2.75 0 005 5.75v7.5A2.75 2.75 0 007.75 16h5.5A2.75 2.75 0 0016 13.25v-7.5A2.75 2.75 0 0013.25 3h-5.5z"/>
                                                    <path d="M4 7.25A3.25 3.25 0 017.25 4H13a.75.75 0 010 1.5H7.25A1.75 1.75 0 005.5 7.25V13a.75.75 0 01-1.5 0V7.25z"/>
                                                </svg>
                                            </button>
                                            <button type="button" class="chat-msg-tools-btn" data-msg-edit data-tooltip="Edit message" aria-label="Edit message">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-8.25 8.25a1 1 0 01-.414.257l-3 1a1 1 0 01-1.257-1.257l1-3a1 1 0 01.257-.414l8.25-8.25z"/>
                                                    <path d="M11.5 5.5l3 3"/>
                                                </svg>
                                            </button>
                                        </div>
                                    @endif
                                    <div class="whitespace-pre-wrap leading-relaxed font-medium tracking-wide" style="font-size: 20px;">{!! $message->content !!}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <button id="chat-scroll-bottom" type="button" class="chat-scroll-bottom-btn" aria-label="Scroll to latest">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 3.25a.75.75 0 01.75.75v10.19l3.22-3.22a.75.75 0 111.06 1.06l-4.5 4.5a.75.75 0 01-1.06 0l-4.5-4.5a.75.75 0 111.06-1.06l3.22 3.22V4a.75.75 0 01.75-.75z" clip-rule="evenodd" />
                </svg>
            </button>

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
    const scrollBottomBtn = document.getElementById('chat-scroll-bottom');
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
        container.className = 'message-enter ' + (role === 'user' ? 'ml-auto max-w-2xl' : 'mr-auto w-full');

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
                ? 'rounded-[2rem_2rem_0.5rem_2rem] message-bubble-user text-white chat-user-bubble'
                : 'message-bubble-ai text-slate-800') + (role === 'user' ? ' px-8 py-6 shadow-2xl' : ' px-2 py-1');
        } else {
            bubble.className = (role === 'user'
                ? 'rounded-[2rem_2rem_0.5rem_2rem] bg-slate-950 text-white shadow-lg chat-user-bubble'
                : 'message-bubble-ai text-slate-800') + (role === 'user' ? ' px-8 py-6' : ' px-2 py-1');
        }
        if (role === 'user') {
            bubble.dataset.userMessage = String(content ?? '');
            const tools = document.createElement('div');
            tools.className = 'chat-msg-tools';
            tools.dataset.msgTools = 'true';
            tools.innerHTML = `
                <button type="button" class="chat-msg-tools-btn" data-msg-copy data-tooltip="Copy message" aria-label="Copy message">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M7.75 3A2.75 2.75 0 005 5.75v7.5A2.75 2.75 0 007.75 16h5.5A2.75 2.75 0 0016 13.25v-7.5A2.75 2.75 0 0013.25 3h-5.5z"/>
                        <path d="M4 7.25A3.25 3.25 0 017.25 4H13a.75.75 0 010 1.5H7.25A1.75 1.75 0 005.5 7.25V13a.75.75 0 01-1.5 0V7.25z"/>
                    </svg>
                </button>
                <button type="button" class="chat-msg-tools-btn" data-msg-edit data-tooltip="Edit message" aria-label="Edit message">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-8.25 8.25a1 1 0 01-.414.257l-3 1a1 1 0 01-1.257-1.257l1-3a1 1 0 01.257-.414l8.25-8.25z"/>
                        <path d="M11.5 5.5l3 3"/>
                    </svg>
                </button>
            `;
            bubble.appendChild(tools);
        }

        const body = document.createElement('div');
        body.className = 'whitespace-pre-wrap leading-relaxed font-medium tracking-wide';
        body.style.fontSize = '20px';
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
            body.innerHTML = '<span class="chat-thinking-dots"><span>.</span><span>.</span><span>.</span></span>';
        }
        el.dataset.thinking = 'true';
        return el;
    };

    const htmlToPlainText = (html) => {
        const tmp = document.createElement('div');
        tmp.innerHTML = String(html ?? '');
        return (tmp.innerText || tmp.textContent || '').replace(/\u00A0/g, ' ');
    };

    const typeAssistantResponse = async (bodyEl, html) => {
        if (!bodyEl) return;

        const fullHtml = String(html ?? '');
        const text = htmlToPlainText(fullHtml).trimEnd();

        if (!text) {
            bodyEl.innerHTML = fullHtml;
            return;
        }

        const startDistance = scrollEl.scrollHeight - scrollEl.scrollTop - scrollEl.clientHeight;
        const shouldAutoScroll = startDistance < 180;

        const minMs = 450;
        const maxMs = 2600;
        const estimated = Math.round(Math.min(maxMs, Math.max(minMs, text.length * 10)));
        const frameMs = 16;
        const steps = Math.max(12, Math.round(estimated / frameMs));
        const charsPerStep = Math.max(1, Math.ceil(text.length / steps));

        let i = 0;
        bodyEl.textContent = '▍';

        await new Promise((resolve) => {
            const tick = () => {
                i = Math.min(text.length, i + charsPerStep);
                bodyEl.textContent = text.slice(0, i) + (i < text.length ? '▍' : '');
                if (shouldAutoScroll) scrollToBottom();
                if (i >= text.length) {
                    resolve();
                    return;
                }
                window.requestAnimationFrame(tick);
            };
            window.requestAnimationFrame(tick);
        });

        bodyEl.innerHTML = fullHtml;
        if (shouldAutoScroll) scrollToBottom();
    };

    const updateScrollBottomBtn = () => {
        if (!scrollBottomBtn) return;
        const distance = scrollEl.scrollHeight - scrollEl.scrollTop - scrollEl.clientHeight;
        if (distance > 240) {
            scrollBottomBtn.classList.add('is-visible');
        } else {
            scrollBottomBtn.classList.remove('is-visible');
        }
    };

    const scrollToBottom = (smooth = false) => {
        if (smooth && typeof scrollEl.scrollTo === 'function') {
            scrollEl.scrollTo({ top: scrollEl.scrollHeight, behavior: 'smooth' });
        } else {
            scrollEl.scrollTop = scrollEl.scrollHeight;
        }
        updateScrollBottomBtn();
    };

    if (scrollBottomBtn) {
        scrollBottomBtn.addEventListener('click', () => scrollToBottom(true));
    }

    scrollEl.addEventListener('scroll', () => {
        window.requestAnimationFrame(updateScrollBottomBtn);
    }, { passive: true });

    scrollEl.addEventListener('keydown', (e) => {
        if (e.key !== 'Enter' && e.key !== ' ') return;
        const item = e.target.closest('.ref-accordion[data-ref-toggle]');
        if (!item) return;
        e.preventDefault();
        const open = item.classList.toggle('is-open');
        item.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

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
                    await typeAssistantResponse(body, content);
                } else {
                    thinkingEl.remove();
                    const el = renderMessage('assistant', '');
                    const b = el.querySelector('.whitespace-pre-wrap');
                    await typeAssistantResponse(b, content);
                }
                delete thinkingEl.dataset.thinking;
            } else {
                const el = renderMessage('assistant', '');
                const body = el.querySelector('.whitespace-pre-wrap');
                await typeAssistantResponse(body, content);
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

    scrollEl.addEventListener('click', (e) => {
        const copyBtn = e.target.closest('[data-msg-copy]');
        if (copyBtn) {
            const bubble = copyBtn.closest('.chat-user-bubble');
            if (!bubble) return;
            const contentEl = bubble.querySelector('.whitespace-pre-wrap');
            const text = (contentEl && typeof contentEl.innerText === 'string' && contentEl.innerText.trim() !== '')
                ? contentEl.innerText
                : (bubble.dataset.userMessage || '');
            const originalHtml = copyBtn.dataset.originalHtml || copyBtn.innerHTML;
            copyBtn.dataset.originalHtml = originalHtml;
            const write = async () => {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    await navigator.clipboard.writeText(text);
                    return true;
                }
                return false;
            };
            write()
                .then((ok) => {
                    if (!ok) return;
                    copyBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="width: 18px; height: 18px; display: block;"><path fill-rule="evenodd" d="M16.704 5.296a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3.25-3.25a1 1 0 011.414-1.414l2.543 2.543 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>';
                    if (copyBtn.dataset.copyTimeoutId) {
                        clearTimeout(Number(copyBtn.dataset.copyTimeoutId));
                    }
                    const timeoutId = window.setTimeout(() => {
                        copyBtn.innerHTML = copyBtn.dataset.originalHtml || originalHtml;
                        delete copyBtn.dataset.copyTimeoutId;
                    }, 3000);
                    copyBtn.dataset.copyTimeoutId = String(timeoutId);
                })
                .catch(() => {});
            return;
        }

        const editBtn = e.target.closest('[data-msg-edit]');
        if (editBtn) {
            const bubble = editBtn.closest('.chat-user-bubble');
            if (!bubble) return;
            if (bubble.classList.contains('is-editing')) return;

            const contentEl = bubble.querySelector('.whitespace-pre-wrap');
            const originalText = (contentEl && typeof contentEl.innerText === 'string' && contentEl.innerText.trim() !== '')
                ? contentEl.innerText
                : (bubble.dataset.userMessage || '');

            bubble.dataset.originalUserMessage = originalText;
            bubble.classList.add('is-editing');
            const msgContainer = bubble.closest('.message-enter');
            if (msgContainer) msgContainer.classList.add('is-editing-wide');

            if (contentEl) {
                contentEl.style.display = 'none';
            }

            const textarea = document.createElement('textarea');
            textarea.className = 'chat-edit-textarea whitespace-pre-wrap leading-relaxed font-medium tracking-wide';
            textarea.value = originalText;
            textarea.style.fontSize = '20px';
            textarea.rows = 3;

            const actions = document.createElement('div');
            actions.className = 'chat-edit-actions';
            actions.innerHTML = `
                <button type="button" class="chat-edit-action-btn" data-msg-edit-cancel>Cancel</button>
                <button type="button" class="chat-edit-action-btn primary" data-msg-edit-send>Send</button>
            `;

            bubble.appendChild(textarea);
            bubble.appendChild(actions);

            textarea.focus();
            textarea.setSelectionRange(textarea.value.length, textarea.value.length);
            return;
        }

        const cancelEditBtn = e.target.closest('[data-msg-edit-cancel]');
        if (cancelEditBtn) {
            const bubble = cancelEditBtn.closest('.chat-user-bubble');
            if (!bubble) return;
            const originalText = bubble.dataset.originalUserMessage || bubble.dataset.userMessage || '';
            const contentEl = bubble.querySelector('.whitespace-pre-wrap');
            const textarea = bubble.querySelector('.chat-edit-textarea');
            const actions = bubble.querySelector('.chat-edit-actions');
            if (textarea) textarea.remove();
            if (actions) actions.remove();
            if (contentEl) {
                contentEl.style.display = '';
                contentEl.textContent = originalText;
            }
            bubble.dataset.userMessage = originalText;
            bubble.classList.remove('is-editing');
            const msgContainer = bubble.closest('.message-enter');
            if (msgContainer) msgContainer.classList.remove('is-editing-wide');
            return;
        }

        const sendEditBtn = e.target.closest('[data-msg-edit-send]');
        if (sendEditBtn) {
            const bubble = sendEditBtn.closest('.chat-user-bubble');
            if (!bubble) return;
            const textarea = bubble.querySelector('.chat-edit-textarea');
            if (!textarea) return;
            const editedText = String(textarea.value || '').trim();
            if (!editedText) return;

            const contentEl = bubble.querySelector('.whitespace-pre-wrap');
            const actions = bubble.querySelector('.chat-edit-actions');
            if (contentEl) {
                contentEl.style.display = '';
                contentEl.textContent = editedText;
            }
            bubble.dataset.userMessage = editedText;
            if (textarea) textarea.remove();
            if (actions) actions.remove();
            bubble.classList.remove('is-editing');
            const msgContainer = bubble.closest('.message-enter');
            if (msgContainer) msgContainer.classList.remove('is-editing-wide');

            const stack = scrollEl.querySelector('[data-message-stack]');
            if (msgContainer && stack) {
                let node = msgContainer.nextElementSibling;
                while (node) {
                    const next = node.nextElementSibling;
                    node.remove();
                    node = next;
                }
            }

            sendBtn.disabled = true;
            promptEl.disabled = true;

            const thinkingEl = renderThinkingMessage();
            scrollToBottom();

            (async () => {
                try {
                    const conv = await ensureConversation();
                    const resp = await window.axios.post(conv.messagesUrl, { prompt: editedText }, {
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
                            await typeAssistantResponse(body, content);
                        } else {
                            thinkingEl.remove();
                            const el = renderMessage('assistant', '');
                            const b = el.querySelector('.whitespace-pre-wrap');
                            await typeAssistantResponse(b, content);
                        }
                        delete thinkingEl.dataset.thinking;
                    } else {
                        const el = renderMessage('assistant', '');
                        const b = el.querySelector('.whitespace-pre-wrap');
                        await typeAssistantResponse(b, content);
                    }
                    upsertSidebarConversation({ id: conv.id, url: conv.url, title: normalizeTitle(editedText), is_pinned: false });
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
            })();

            return;
        }

        const item = e.target.closest('.ref-accordion[data-ref-toggle]');
        if (!item) return;
        const body = item.querySelector('.ref-accordion-body');
        if (!body) return;
        const open = item.classList.toggle('is-open');
        item.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    scrollToBottom();
    updateScrollBottomBtn();

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
