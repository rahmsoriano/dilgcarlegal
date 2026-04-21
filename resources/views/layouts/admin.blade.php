<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <style>
            @media (min-width: 1024px) {
                .admin-content {
                    zoom: 1;
                }
            }

            @media (min-width: 1024px) {
                .admin-grid {
                    display: flex;
                }

                .admin-sidebar {
                    width: 320px;
                    flex: 0 0 auto;
                }

                .sidebar-collapsed .admin-sidebar {
                    width: 92px;
                }

                .admin-main {
                    flex: 1 1 0%;
                    min-width: 0;
                }
            }

            @media (min-width: 1280px) {
                .admin-content {
                    max-width: 1780px;
                }
            }

            .admin-sidebar {
                transition: width 220ms ease, padding 220ms ease, border-radius 220ms ease;
            }

            .sidebar-toggle-btn svg {
                transition: transform 220ms ease;
            }

            .sidebar-collapsed .sidebar-toggle-btn svg {
                transform: rotate(180deg);
            }

            .sidebar-collapsed .sidebar-nav-label,
            .sidebar-collapsed .sidebar-section-label,
            .sidebar-collapsed .sidebar-chat-title,
            .sidebar-collapsed .sidebar-account-details,
            .sidebar-collapsed .sidebar-login-label,
            .sidebar-collapsed .sidebar-account-chevron {
                display: none;
            }

            .sidebar-collapsed .sidebar-nav-link {
                justify-content: center;
                padding-left: 0.75rem;
                padding-right: 0.75rem;
            }

            .sidebar-collapsed .sidebar-history {
                display: flex;
                flex: 1 1 auto;
                min-height: 0;
            }

            .sidebar-collapsed .sidebar-history-inner {
                display: none;
            }

            .sidebar-collapsed .sidebar-login-btn {
                width: 48px;
                height: 48px;
                padding: 0;
                border-radius: 9999px;
                margin-left: auto;
                margin-right: auto;
                gap: 0;
            }

            .sidebar-collapsed #sidebar-profile-trigger {
                width: 48px;
                height: 48px;
                padding: 0;
                border-radius: 9999px;
                margin-left: auto;
                margin-right: auto;
                justify-content: center;
                gap: 0;
            }

            .sidebar-collapsed #sidebar-profile-trigger img {
                display: block;
                border-radius: 9999px;
                box-shadow: none;
            }

            .sidebar-collapsed .sidebar-bottom {
                display: flex;
                flex-direction: column;
                align-items: stretch;
            }

            .sidebar-collapsed .sidebar-bottom > div {
                padding-left: 0;
                padding-right: 0;
            }

            .sidebar-search-expanded {
                display: block;
            }

            .sidebar-search-collapsed {
                display: none;
            }

            .sidebar-collapsed .sidebar-search-expanded {
                display: none;
            }

            .sidebar-collapsed .sidebar-search-collapsed {
                display: flex;
            }

            .sidebar-search-fade {
                max-height: 520px;
                opacity: 1;
                transform: translateY(0);
                transition: max-height 220ms ease, opacity 200ms ease, transform 200ms ease;
                overflow: hidden;
            }

            .sidebar-search-mode .sidebar-search-fade {
                max-height: 0;
                opacity: 0;
                transform: translateY(-8px);
                pointer-events: none;
            }

            .sidebar-search-mode #sidebar-collapse-toggle {
                opacity: 0;
                pointer-events: none;
                transition: opacity 200ms ease;
            }

            .sidebar-search-mode .sidebar-history {
                margin-top: 0 !important;
            }

            .sidebar-search-mode #sidebar-bottom-wrap {
                margin-top: 0 !important;
            }

            #sidebar-chat-search::-webkit-search-cancel-button,
            #sidebar-chat-search::-webkit-search-decoration {
                -webkit-appearance: none;
                appearance: none;
            }
        </style>
    </head>
    <body class="font-sans antialiased selection:bg-slate-900/10 selection:text-slate-900">
        <div class="admin-shell h-screen bg-[radial-gradient(circle_at_top_left,rgba(15,23,42,0.05),transparent_55%),radial-gradient(circle_at_bottom_right,rgba(15,23,42,0.035),transparent_60%),linear-gradient(180deg,#f8fafc_0%,#f1f5f9_55%,#ffffff_100%)] text-slate-900 overflow-hidden relative">
            <div class="admin-content mx-auto flex h-full max-w-[1600px] flex-col px-4 py-6 lg:px-8">
                <!-- Top Header -->
                <header class="flex items-center justify-between mb-8 px-2 lg:px-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white shadow-sm ring-1 ring-slate-900/10 overflow-hidden">
                            <img
                                src="https://upload.wikimedia.org/wikipedia/commons/c/c9/Department_of_the_Interior_and_Local_Government_%28DILG%29_Seal_-_Logo.svg"
                                alt="DILG Seal"
                                class="h-full w-full object-contain"
                            >
                        </div>
                        <span class="text-xl font-bold tracking-tight text-slate-900">{{ config('app.name', 'DILG Legal Assistant') }}</span>
                    </div>

                    <div></div>
                </header>

                <div class="admin-grid grid flex-1 min-h-0 grid-cols-1 gap-6 lg:gap-8">
                    <aside id="app-sidebar" class="admin-sidebar relative flex min-h-0 flex-col overflow-visible rounded-[2rem] bg-white/80 backdrop-blur-xl p-6 ring-1 ring-slate-900/5 shadow-[0_24px_70px_rgba(15,23,42,0.08)]">
                        @php
                            $routeConversation = request()->route('conversation');
                            $activeConversationId = is_object($routeConversation) && isset($routeConversation->id)
                                ? $routeConversation->id
                                : request()->route('conversationId');

                            $sidebarChats = collect();
                            if ($mode === 'public') {
                                $rows = (array) session('public_conversations', []);
                                $sidebarChats = collect($rows)
                                    ->filter(fn ($c) => empty($c['is_saved']))
                                    ->sortByDesc(fn ($c) => !empty($c['is_pinned']) ? 1 : 0)
                                    ->sortByDesc(fn ($c) => !empty($c['pinned_at']) ? strtotime((string) $c['pinned_at']) : 0)
                                    ->sortByDesc(fn ($c) => !empty($c['last_message_at']) ? strtotime((string) $c['last_message_at']) : 0)
                                    ->sortByDesc(fn ($c) => (int) ($c['id'] ?? 0))
                                    ->map(function ($c) {
                                        return (object) [
                                            'id' => (int) ($c['id'] ?? 0),
                                            'title' => $c['title'] ?? null,
                                            'is_pinned' => (bool) ($c['is_pinned'] ?? false),
                                            'pinned_at' => !empty($c['pinned_at']) ? \Illuminate\Support\Carbon::parse((string) $c['pinned_at']) : null,
                                            'last_message_at' => !empty($c['last_message_at']) ? \Illuminate\Support\Carbon::parse((string) $c['last_message_at']) : null,
                                            'created_at' => !empty($c['created_at']) ? \Illuminate\Support\Carbon::parse((string) $c['created_at']) : null,
                                        ];
                                    })
                                    ->values();
                            } elseif (auth()->check()) {
                                $sidebarChats = auth()->user()->conversations()
                                    ->select(['id', 'title', 'is_pinned', 'pinned_at', 'last_message_at', 'created_at'])
                                    ->where('is_saved', false)
                                    ->orderByDesc('is_pinned')
                                    ->orderByDesc('pinned_at')
                                    ->orderByDesc('last_message_at')
                                    ->orderByDesc('id')
                                    ->get();
                            }
                        @endphp
                        <div class="mb-4">
                            <button id="sidebar-chat-search-collapsed" type="button" aria-label="Search chats" class="sidebar-search-collapsed mx-auto h-12 w-12 items-center justify-center rounded-2xl bg-white/60 text-slate-700 ring-1 ring-slate-900/10 transition hover:bg-white hover:text-slate-900">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                    <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 104.473 8.708l2.41 2.409a.75.75 0 101.06-1.06l-2.409-2.41A5.5 5.5 0 009 3.5zM4.5 9a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div class="sidebar-search-expanded px-4">
                                <div class="relative">
                                    <div class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 104.473 8.708l2.41 2.409a.75.75 0 101.06-1.06l-2.409-2.41A5.5 5.5 0 009 3.5zM4.5 9a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <input id="sidebar-chat-search" type="search" autocomplete="off" spellcheck="false" placeholder="Search chats..." class="w-full rounded-2xl border border-slate-900/10 bg-white/70 py-2.5 pl-10 pr-10 text-sm font-semibold text-slate-900 placeholder:text-slate-400 outline-none ring-1 ring-transparent transition focus:border-indigo-500/30 focus:ring-2 focus:ring-indigo-500/20">
                                    <button id="sidebar-chat-search-clear" type="button" aria-label="Exit search" class="absolute right-2 top-1/2 hidden h-7 w-7 -translate-y-1/2 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-900/[0.04] hover:text-slate-900">
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                            <path fill-rule="evenodd" d="M4.22 4.22a.75.75 0 011.06 0L10 8.94l4.72-4.72a.75.75 0 111.06 1.06L11.06 10l4.72 4.72a.75.75 0 11-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 11-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="sidebar-primary" class="sidebar-search-fade">
                            <nav class="shrink-0 space-y-3">
                            <a href="{{ route($newChatRoute) }}" class="sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-2xl transition-all text-slate-700 hover:text-slate-900 hover:bg-white/40 ring-1 ring-transparent hover:ring-black/10">
                                <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-amber-500/15 text-amber-700 ring-1 ring-amber-600/20">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                    </svg>
                                </div>
                                <span class="sidebar-nav-label text-sm font-semibold">New Chat</span>
                            </a>

                            @if ($showOpinionsNav)
                                <a href="{{ route('admin.opinions.index') }}" class="sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('admin.opinions.*') ? 'bg-white/70 text-slate-900 ring-1 ring-black/10 shadow-sm' : 'text-slate-700 hover:text-slate-900 hover:bg-white/40 ring-1 ring-transparent hover:ring-black/10' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 text-slate-600">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5-3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                                    </svg>
                                    <span class="sidebar-nav-label text-sm font-semibold">Opinions Library</span>
                                </a>

                                @if (auth()->check() && auth()->user()->is_admin)
                                    <a href="{{ route('admin.faq-responses.index') }}" class="sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs('admin.faq-responses.*') ? 'bg-white/70 text-slate-900 ring-1 ring-black/10 shadow-sm' : 'text-slate-700 hover:text-slate-900 hover:bg-white/40 ring-1 ring-transparent hover:ring-black/10' }}">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 text-slate-600">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a3.375 3.375 0 116.75 0c0 1.354-.784 2.535-1.917 3.091-.806.393-1.333 1.19-1.333 2.084v.225m0 3.75h.008v.008H12v-.008z" />
                                        </svg>
                                        <span class="sidebar-nav-label text-sm font-semibold">FAQ Response Manager</span>
                                    </a>
                                @endif
                            @endif

                            <a href="{{ route($archiveRoute) }}" class="sidebar-nav-link flex items-center gap-3 px-4 py-3 rounded-2xl transition-all {{ request()->routeIs($archiveRoute) ? 'bg-white/70 text-slate-900 ring-1 ring-black/10 shadow-sm' : 'text-slate-700 hover:text-slate-900 hover:bg-white/40 ring-1 ring-transparent hover:ring-black/10' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 text-slate-600">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                                <span class="sidebar-nav-label text-sm font-semibold">Archive</span>
                            </a>
                            </nav>
                        </div>

                        <button id="sidebar-collapse-toggle" type="button" class="sidebar-toggle-btn absolute -right-3 top-1/2 -translate-y-1/2 flex h-9 w-9 items-center justify-center rounded-full bg-white ring-1 ring-black/10 shadow-md hover:shadow-lg transition">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4 text-slate-700">
                                <path fill-rule="evenodd" d="M12.53 4.47a.75.75 0 010 1.06L8.06 10l4.47 4.47a.75.75 0 11-1.06 1.06l-5-5a.75.75 0 010-1.06l5-5a.75.75 0 011.06 0z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        <div class="sidebar-history mt-6 flex min-h-0 flex-1 flex-col overflow-hidden">
                            <div class="sidebar-history-inner flex min-h-0 flex-1 flex-col">
                                <button id="sidebar-chats-toggle" type="button" class="flex items-center justify-between px-4 py-2 text-left">
                                    <div class="sidebar-section-label text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">Your Chats</div>
                                    <svg id="sidebar-chats-chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-4 w-4 text-slate-600 transition-transform duration-200">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>

                                <div id="sidebar-chats-panel" class="mt-3 min-h-0 flex-1 overflow-hidden">
                                    <div id="sidebar-chats-scroll" class="sidebar-chats-scroll h-full overflow-y-auto px-2 pr-1">
                                        <div id="sidebar-chats-list" class="space-y-1.5">
                                            @forelse ($sidebarChats as $conversation)
                                                <div
                                                    data-conversation-id="{{ $conversation->id }}"
                                                    data-is-pinned="{{ $conversation->is_pinned ? '1' : '0' }}"
                                                    data-pinned-at="{{ $conversation->pinned_at?->toIso8601String() }}"
                                                    data-last-message-at="{{ $conversation->last_message_at?->toIso8601String() }}"
                                                    data-show-url="{{ route($chatShowRoute, $conversation->id) }}"
                                                    data-update-url="{{ route($sidebarUpdateRoute, $conversation->id) }}"
                                                    data-toggle-pin-url="{{ route($sidebarTogglePinRoute, $conversation->id) }}"
                                                    data-toggle-save-url="{{ route($sidebarToggleSaveRoute, $conversation->id) }}"
                                                    data-delete-url="{{ route($sidebarDeleteRoute, $conversation->id) }}"
                                                    class="sidebar-chat-item group relative flex items-center gap-2 rounded-2xl border transition-all {{ (string) $activeConversationId === (string) $conversation->id ? 'border-indigo-500/30 bg-white/70 text-slate-900 ring-1 ring-indigo-500/20 shadow-sm' : 'border-black/10 bg-white/35 text-slate-700 hover:border-black/20 hover:bg-white/55 hover:text-slate-900' }}"
                                                >
                                                    <a href="{{ route($chatShowRoute, $conversation->id) }}" class="min-w-0 flex-1 px-4 py-3">
                                                        <div class="sidebar-chat-title truncate text-sm font-semibold tracking-tight">
                                                            {{ $conversation->title ?: 'Untitled Thread' }}
                                                        </div>
                                                    </a>

                                                    <div class="sidebar-pin-indicator pointer-events-none absolute right-10 top-2 z-10 {{ $conversation->is_pinned ? '' : 'hidden' }}" aria-hidden="true">
                                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4">
                                                            <circle cx="12" cy="6.5" r="3.25" class="text-rose-400" fill="currentColor" />
                                                            <path d="M12 10v10" class="text-slate-700" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                            <path d="M12 20l-1.4 2" class="text-slate-700" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                                                        </svg>
                                                    </div>

                                                    <div class="relative pr-2">
                                                        <button type="button" class="sidebar-chat-menu-btn flex h-8 w-8 items-center justify-center rounded-xl border border-transparent text-slate-500 opacity-100 transition lg:opacity-0 lg:group-hover:opacity-100 hover:border-slate-900/10 hover:bg-slate-900/[0.03] hover:text-slate-900">
                                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                                                <path d="M6 10a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM11.5 10a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM17 10a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                                                            </svg>
                                                        </button>

                                                        <div class="sidebar-chat-menu absolute right-0 bottom-full mb-2 hidden w-44 overflow-hidden rounded-2xl bg-white/95 ring-1 ring-slate-900/10 backdrop-blur-xl shadow-[0_24px_70px_rgba(15,23,42,0.14)]">
                                                            <button type="button" data-action="rename" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-900/[0.03] transition">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-slate-500">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125L16.875 4.5" />
                                                                </svg>
                                                                <span>Rename</span>
                                                            </button>
                                                            <button type="button" data-action="pin" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-900/[0.03] transition">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-slate-500">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 3.75l-9 9m0 0H3.75m3.75 0V21m0-8.25L16.5 3.75m0 0V8.25m0-4.5h4.5" />
                                                                </svg>
                                                                <span>{{ $conversation->is_pinned ? 'Unpin Chat' : 'Pin Chat' }}</span>
                                                            </button>
                                                            <div class="h-px bg-slate-900/10"></div>
                                                            <button type="button" data-action="archive" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-900/[0.03] transition">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-slate-500">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                                                </svg>
                                                                <span>Archive</span>
                                                            </button>
                                                            <button type="button" data-action="delete" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-rose-700 hover:bg-rose-500/10 transition">
                                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-rose-600">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-1.806A2.25 2.25 0 0013.813 1.5h-3.626a2.25 2.25 0 00-2.25 2.25V3m7.5 0H9" />
                                                                </svg>
                                                                <span>Delete</span>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div id="sidebar-chats-empty" class="px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-widest">
                                                    No chats yet
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div id="sidebar-bottom-wrap" class="sidebar-bottom sidebar-search-fade shrink-0 mt-6 space-y-4">
                            @if ($showLoginButton)
                                <div class="px-2 py-4">
                                    <a href="{{ route($loginRoute) }}" data-open-login-modal data-loader-skip class="sidebar-login-btn flex w-full items-center justify-center gap-3 rounded-2xl border border-slate-900/10 bg-white/60 px-4 py-3 text-xs font-black uppercase tracking-[0.2em] text-slate-700 hover:bg-white hover:text-slate-900 transition shadow-sm">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9l3 3m0 0l-3 3m3-3H8.25" />
                                        </svg>
                                        <span class="sidebar-login-label">Login</span>
                                    </a>
                                </div>
                            @elseif ($showProfileMenu && auth()->check())
                                <div class="relative px-2 py-4">
                                    <div id="sidebar-profile-menu" class="hidden w-56 overflow-hidden rounded-2xl bg-white/95 ring-1 ring-slate-900/10 backdrop-blur-xl shadow-[0_24px_70px_rgba(15,23,42,0.14)]">
                                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-900/[0.03] transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-slate-500">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 20.118a7.5 7.5 0 0115 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.5-1.632z" />
                                            </svg>
                                            <span>Profile Settings</span>
                                        </a>
                                        <div class="h-px bg-slate-900/10"></div>
                                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                                            @csrf
                                            <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-rose-700 hover:bg-rose-500/10 transition">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-rose-600">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6A2.25 2.25 0 005.25 5.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9l3 3m0 0l-3 3m3-3H8.25" />
                                                </svg>
                                                <span>Logout</span>
                                            </button>
                                        </form>
                                    </div>

                                    <button id="sidebar-profile-trigger" type="button" class="w-full flex items-center gap-3 rounded-2xl px-2 py-2 hover:bg-slate-900/[0.03] transition">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=6366f1&color=fff" alt="User Avatar" class="h-10 w-10 rounded-xl shadow-lg">
                                        <div class="sidebar-account-details min-w-0 flex-1 text-left">
                                            <div class="truncate text-sm font-bold text-slate-900">{{ auth()->user()->name }}</div>
                                            <div class="truncate text-[10px] font-medium text-slate-500">{{ auth()->user()->email }}</div>
                                        </div>
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="sidebar-account-chevron h-4 w-4 text-slate-500">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                        </svg>
                                    </button>
                                </div>
                            @endif
                        </div>
                    </aside>

                    <main class="admin-main min-w-0 min-h-0 {{ (request()->routeIs('admin.legal.ai*') || request()->routeIs('legal.ai') || request()->routeIs('legal.ai.show')) ? 'flex flex-col overflow-hidden' : 'overflow-y-auto' }}">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
        <div id="global-loading-overlay" class="hidden fixed inset-0 z-[2147483200]" aria-hidden="true">
            <div class="global-loading-backdrop absolute inset-0"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="global-loading-card">
                    <div class="global-loading-spinner" role="status" aria-label="Loading"></div>
                    <div class="global-loading-text">Loading</div>
                </div>
            </div>
        </div>
        <div id="auth-login-modal" class="auth-modal hidden fixed inset-0 z-[2147483100]" aria-hidden="true">
            <div class="auth-modal-backdrop absolute inset-0" data-auth-modal-close></div>
            <div class="absolute inset-0 flex items-center justify-center px-4 py-6 sm:px-6 lg:px-8">
                <div class="auth-modal-panel relative w-full max-w-5xl">
                    <button type="button" class="auth-modal-close" aria-label="Close" data-auth-modal-close>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                    </button>

                    <div class="auth-login-card overflow-hidden rounded-[36px] bg-white">
                        <div class="grid h-[min(640px,calc(100vh-3.5rem))] lg:grid-cols-[1.08fr_0.92fr]">
                            <section class="auth-grid relative hidden h-full overflow-hidden px-8 py-8 text-white lg:block">
                                <div class="relative flex h-full flex-col">
                                    <div class="flex items-start gap-4">
                                        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-full bg-white shadow-[0_18px_36px_rgba(0,0,0,0.18)]">
                                            <img
                                                src="https://upload.wikimedia.org/wikipedia/commons/c/c9/Department_of_the_Interior_and_Local_Government_%28DILG%29_Seal_-_Logo.svg"
                                                alt="DILG Seal"
                                                class="h-full w-full object-contain"
                                            >
                                        </div>

                                        <div class="min-w-0">
                                            <div class="text-sm font-black uppercase tracking-wide text-white">
                                                Department of the Interior and Local Government
                                            </div>
                                            <div class="mt-1 text-xs font-semibold uppercase tracking-[0.22em] text-white/80">
                                                Cordillera Administrative Region
                                            </div>
                                            <div class="mt-1 text-xs italic text-white/80">
                                                Matino. Mahusay.at Maaasahan.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-1 flex-col justify-center">
                                        <div class="max-w-sm">
                                            <p class="text-sm font-semibold uppercase tracking-[0.38em] text-cyan-200/90">DILG Legal-Assist AI</p>
                                            <h2 class="mt-5 text-4xl font-semibold leading-tight text-white">
                                                Smart legal support for efficient public service.
                                            </h2>
                                            <p class="mt-4 text-base leading-7 text-blue-100/78">
                                                Instant help, document assistance, and reliable guidance all in one place.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="flex h-full items-center bg-white px-6 py-6 sm:px-10 lg:px-12 overflow-y-auto">
                                <div class="mx-auto w-full max-w-md text-slate-900">
                                    <div data-auth-views-root data-initial-mode="login" class="relative">
                                        <div class="auth-view" data-view="login" aria-hidden="false">
                                            <div class="mx-auto flex h-16 w-16 items-center justify-center overflow-hidden rounded-full bg-white shadow-[0_18px_36px_rgba(59,130,246,0.22)]">
                                                <img
                                                    src="https://upload.wikimedia.org/wikipedia/commons/c/c9/Department_of_the_Interior_and_Local_Government_%28DILG%29_Seal_-_Logo.svg"
                                                    alt="DILG Seal"
                                                    class="h-full w-full object-contain"
                                                >
                                            </div>

                                            <h2 id="auth-modal-title" class="mt-6 text-center text-2xl font-semibold tracking-tight">Welcome Back</h2>
                                            <p class="mt-2 text-center text-sm leading-6 text-slate-500">
                                                Sign in to access your saved legal conversations.
                                            </p>

                                            <x-auth-session-status class="mt-6 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-700" :status="session('status')" />

                                            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-3.5">
                                                @csrf

                                                <div>
                                                    <label class="mb-2 block text-xs font-medium text-slate-700">Email address</label>
                                                    <div class="auth-input flex items-center gap-3 rounded-full px-5 py-3 transition focus-within:ring-2 focus-within:ring-slate-900/10">
                                                        <svg class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="none">
                                                            <path d="M3.333 5.833A1.667 1.667 0 0 1 5 4.167h10A1.667 1.667 0 0 1 16.667 5.833v8.334A1.667 1.667 0 0 1 15 15.833H5a1.667 1.667 0 0 1-1.667-1.666V5.833Z" stroke="currentColor" stroke-width="1.5"/>
                                                            <path d="m4.167 6.25 5.284 4.224a.833.833 0 0 0 1.04 0l5.276-4.224" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                        </svg>
                                                        <input name="email" type="email" required autocomplete="username" class="min-w-0 w-full border-0 bg-transparent p-0 text-xs text-slate-900 outline-none focus:ring-0" placeholder="Enter your email">
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="mb-2 block text-xs font-medium text-slate-700">Password</label>
                                                    <div class="auth-input flex items-center gap-3 rounded-full px-5 py-3 transition focus-within:ring-2 focus-within:ring-slate-900/10">
                                                        <svg class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="none">
                                                            <path d="M6.667 8.333V6.667a3.333 3.333 0 1 1 6.666 0v1.666" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                            <path d="M5.833 8.333h8.334c.92 0 1.666.746 1.666 1.667v5A1.667 1.667 0 0 1 14.167 16.667H5.833A1.667 1.667 0 0 1 4.167 15v-5c0-.92.746-1.667 1.666-1.667Z" stroke="currentColor" stroke-width="1.5"/>
                                                        </svg>
                                                        <input name="password" type="password" required autocomplete="current-password" class="min-w-0 w-full border-0 bg-transparent p-0 text-xs text-slate-900 outline-none focus:ring-0" placeholder="Enter your password">
                                                    </div>
                                                </div>

                                                <div class="flex items-center justify-between text-xs font-semibold text-slate-600 py-3">
                                                    <label class="inline-flex items-center gap-2">
                                                        <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900/20">
                                                        <span>Remember me</span>
                                                    </label>

                                                    @if (Route::has('password.request'))
                                                        <a href="{{ route('password.request') }}" class="text-slate-500 hover:underline">
                                                            Forgot password?
                                                        </a>
                                                    @endif
                                                </div>

                                                <button type="submit" class="auth-login-button mt-4 w-full rounded-full py-3 text-[12px] font-black uppercase tracking-[0.22em] text-white transition duration-200 focus:outline-none focus:ring-4 focus:ring-slate-900/10">
                                                    Log In
                                                </button>
                                            </form>

                                            <p class="mt-4 text-center text-xs font-semibold text-slate-600">
                                                Don’t have an account?
                                                <button type="button" data-auth-switch="register" class="text-slate-900 hover:underline">Sign Up</button>
                                            </p>
                                        </div>

                                        <div class="auth-view absolute inset-0" data-view="register" aria-hidden="true">
                                            <div class="mx-auto flex h-16 w-16 items-center justify-center overflow-hidden rounded-full bg-white shadow-[0_18px_36px_rgba(59,130,246,0.22)]">
                                                <img
                                                    src="https://upload.wikimedia.org/wikipedia/commons/c/c9/Department_of_the_Interior_and_Local_Government_%28DILG%29_Seal_-_Logo.svg"
                                                    alt="DILG Seal"
                                                    class="h-full w-full object-contain"
                                                >
                                            </div>

                                            <h2 class="mt-5 text-center text-xl font-semibold tracking-tight">Create Account</h2>
                                            <p class="mt-1.5 text-center text-xs leading-5 text-slate-500">
                                                Sign up to start your legal research workspace.
                                            </p>

                                            <form method="POST" action="{{ route('register') }}" class="mt-5 space-y-3">
                                                @csrf

                                                <div class="flex flex-col gap-3 sm:flex-row">
                                                    <div class="sm:flex-1">
                                                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">First Name</label>
                                                        <div class="auth-input flex items-center gap-2.5 rounded-full px-5 py-3 transition focus-within:ring-2 focus-within:ring-slate-900/10">
                                                            <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="none">
                                                                <path d="M10 10a3.5 3.5 0 1 0-3.5-3.5A3.5 3.5 0 0 0 10 10Z" stroke="currentColor" stroke-width="1.5"/>
                                                                <path d="M3.333 16.667c0-2.577 3.134-4.667 6.667-4.667s6.667 2.09 6.667 4.667" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                            </svg>
                                                            <input name="first_name" type="text" value="{{ old('first_name') }}" required autocomplete="given-name" class="min-w-0 w-full border-0 bg-transparent p-0 text-xs text-slate-900 outline-none focus:ring-0" placeholder="First name">
                                                        </div>
                                                    </div>

                                                    <div class="sm:flex-1">
                                                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Last Name</label>
                                                        <div class="auth-input flex items-center gap-2.5 rounded-full px-5 py-3 transition focus-within:ring-2 focus-within:ring-slate-900/10">
                                                            <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="none">
                                                                <path d="M10 10a3.5 3.5 0 1 0-3.5-3.5A3.5 3.5 0 0 0 10 10Z" stroke="currentColor" stroke-width="1.5"/>
                                                                <path d="M3.333 16.667c0-2.577 3.134-4.667 6.667-4.667s6.667 2.09 6.667 4.667" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                            </svg>
                                                            <input name="last_name" type="text" value="{{ old('last_name') }}" required autocomplete="family-name" class="min-w-0 w-full border-0 bg-transparent p-0 text-xs text-slate-900 outline-none focus:ring-0" placeholder="Last name">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="flex flex-col gap-3 sm:flex-row">
                                                    <div class="sm:flex-[0.85]">
                                                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Birthday</label>
                                                        <div class="auth-input flex items-center gap-2.5 rounded-full px-5 py-3 transition focus-within:ring-2 focus-within:ring-slate-900/10">
                                                            <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="none">
                                                                <path d="M6.667 3.333v2.5M13.333 3.333v2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                                <path d="M3.333 7.5h13.334" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                                <path d="M4.167 5.833h11.666c.92 0 1.667.746 1.667 1.667v8.333c0 .92-.747 1.667-1.667 1.667H4.167c-.92 0-1.667-.747-1.667-1.667V7.5c0-.92.747-1.667 1.667-1.667Z" stroke="currentColor" stroke-width="1.5"/>
                                                            </svg>
                                                            <input name="birthday" type="date" value="{{ old('birthday') }}" required class="min-w-0 w-full border-0 bg-transparent p-0 text-xs text-slate-900 outline-none focus:ring-0">
                                                        </div>
                                                    </div>

                                                    <div class="sm:flex-1">
                                                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Email address</label>
                                                        <div class="auth-input flex items-center gap-2.5 rounded-full px-5 py-3 transition focus-within:ring-2 focus-within:ring-slate-900/10">
                                                            <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="none">
                                                                <path d="M3.333 5.833A1.667 1.667 0 0 1 5 4.167h10A1.667 1.667 0 0 1 16.667 5.833v8.334A1.667 1.667 0 0 1 15 15.833H5a1.667 1.667 0 0 1-1.667-1.666V5.833Z" stroke="currentColor" stroke-width="1.5"/>
                                                                <path d="m4.167 6.25 5.284 4.224a.833.833 0 0 0 1.04 0l5.276-4.224" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                            </svg>
                                                            <input name="email" type="email" value="{{ old('email') }}" required autocomplete="username" class="min-w-0 w-full border-0 bg-transparent p-0 text-xs text-slate-900 outline-none focus:ring-0" placeholder="Email address">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="flex flex-col gap-3 sm:flex-row">
                                                    <div class="sm:flex-1">
                                                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Password</label>
                                                        <div class="auth-input flex items-center gap-2.5 rounded-full px-5 py-3 transition focus-within:ring-2 focus-within:ring-slate-900/10">
                                                            <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="none">
                                                                <path d="M6.667 8.333V6.667a3.333 3.333 0 1 1 6.666 0v1.666" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                                <path d="M5.833 8.333h8.334c.92 0 1.666.746 1.666 1.667v5A1.667 1.667 0 0 1 14.167 16.667H5.833A1.667 1.667 0 0 1 4.167 15v-5c0-.92.746-1.667 1.666-1.667Z" stroke="currentColor" stroke-width="1.5"/>
                                                            </svg>
                                                            <input name="password" type="password" required autocomplete="new-password" class="min-w-0 w-full border-0 bg-transparent p-0 text-xs text-slate-900 outline-none focus:ring-0" placeholder="Password">
                                                        </div>
                                                    </div>

                                                    <div class="sm:flex-1">
                                                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Confirm Password</label>
                                                        <div class="auth-input flex items-center gap-2.5 rounded-full px-5 py-3 transition focus-within:ring-2 focus-within:ring-slate-900/10">
                                                            <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="none">
                                                                <path d="M6.667 8.333V6.667a3.333 3.333 0 1 1 6.666 0v1.666" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                                <path d="M5.833 8.333h8.334c.92 0 1.666.746 1.666 1.667v5A1.667 1.667 0 0 1 14.167 16.667H5.833A1.667 1.667 0 0 1 4.167 15v-5c0-.92.746-1.667 1.666-1.667Z" stroke="currentColor" stroke-width="1.5"/>
                                                            </svg>
                                                            <input name="password_confirmation" type="password" required autocomplete="new-password" class="min-w-0 w-full border-0 bg-transparent p-0 text-xs text-slate-900 outline-none focus:ring-0" placeholder="Confirm">
                                                        </div>
                                                    </div>
                                                </div>

                                                <button type="submit" class="auth-login-button mt-3.5 w-full rounded-full py-3 text-[11px] font-black uppercase tracking-[0.22em] text-white transition duration-200 focus:outline-none focus:ring-4 focus:ring-slate-900/10">
                                                    Sign Up
                                                </button>
                                            </form>

                                            <p class="mt-3.5 text-center text-xs font-semibold text-slate-600 py-3">
                                                Already have an account?
                                                <button type="button" data-auth-switch="login" class="text-slate-900 hover:underline">Log In</button>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script type="module">
            const sidebarCollapseBtn = document.getElementById('sidebar-collapse-toggle');
            const sidebarCollapseKey = 'dilg_ai_sidebar_collapsed';
            const sidebarEl = document.getElementById('app-sidebar');

            const setSidebarCollapsed = (collapsed) => {
                document.body.classList.toggle('sidebar-collapsed', collapsed);
                if (sidebarCollapseBtn) {
                    sidebarCollapseBtn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
                }
            };

            if (sidebarCollapseBtn) {
                setSidebarCollapsed(localStorage.getItem(sidebarCollapseKey) === '1');
                sidebarCollapseBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const next = !document.body.classList.contains('sidebar-collapsed');
                    setSidebarCollapsed(next);
                    localStorage.setItem(sidebarCollapseKey, next ? '1' : '0');
                });
            }

            const toggle = document.getElementById('sidebar-chats-toggle');
            const chevron = document.getElementById('sidebar-chats-chevron');
            const panel = document.getElementById('sidebar-chats-panel');

            if (toggle && chevron && panel) {
                let expanded = true;
                const wrapper = toggle.parentElement;

                const setExpanded = (next) => {
                    expanded = next;
                    chevron.classList.toggle('rotate-180', expanded);

                    const available = wrapper ? Math.max(0, wrapper.clientHeight - toggle.offsetHeight - 12) : 0;
                    panel.style.height = expanded ? `${available}px` : '0px';
                    panel.style.opacity = expanded ? '1' : '0';
                    panel.style.pointerEvents = expanded ? 'auto' : 'none';
                };

                panel.style.transition = 'height 200ms ease, opacity 200ms ease';
                setExpanded(true);

                toggle.addEventListener('click', () => {
                    setExpanded(!expanded);
                });

                if (wrapper) {
                    const ro = new ResizeObserver(() => {
                        if (expanded) {
                            const available = Math.max(0, wrapper.clientHeight - toggle.offsetHeight - 12);
                            panel.style.height = `${available}px`;
                        }
                    });
                    ro.observe(wrapper);
                }
            }

            const profileTrigger = document.getElementById('sidebar-profile-trigger');
            const profileMenu = document.getElementById('sidebar-profile-menu');

            if (profileTrigger && profileMenu) {
                let open = false;
                const placeholder = document.createComment('profile-menu-placeholder');
                profileMenu.parentNode?.insertBefore(placeholder, profileMenu);
                document.body.appendChild(profileMenu);

                const positionProfileMenu = () => {
                    const collapsed = document.body.classList.contains('sidebar-collapsed');
                    const anchor = (!collapsed && profileTrigger.querySelector('img')) ? profileTrigger.querySelector('img') : profileTrigger;
                    const rect = anchor.getBoundingClientRect();

                    profileMenu.style.position = 'fixed';
                    profileMenu.style.zIndex = '2147483190';

                    const menuRect = profileMenu.getBoundingClientRect();
                    const menuWidth = menuRect.width || 224;
                    const menuHeight = menuRect.height || 160;

                    const padding = 12;
                    const gap = 8;

                    let left = collapsed
                        ? (rect.right + gap)
                        : rect.left;
                    left = Math.min(left, window.innerWidth - menuWidth - padding);
                    left = Math.max(padding, left);

                    const preferredAbove = rect.top - menuHeight - gap;
                    let top = preferredAbove >= padding ? preferredAbove : (rect.bottom + gap);
                    top = Math.min(top, window.innerHeight - menuHeight - padding);
                    top = Math.max(padding, top);

                    profileMenu.style.left = `${left}px`;
                    profileMenu.style.top = `${top}px`;
                };

                const setOpen = (next) => {
                    open = next;
                    profileMenu.classList.toggle('hidden', !open);
                    if (open) {
                        profileMenu.style.visibility = 'hidden';
                        requestAnimationFrame(() => requestAnimationFrame(() => {
                            positionProfileMenu();
                            profileMenu.style.visibility = 'visible';
                        }));
                    } else {
                        profileMenu.style.visibility = '';
                    }
                    profileTrigger.setAttribute('aria-expanded', open ? 'true' : 'false');
                };

                setOpen(false);

                profileTrigger.addEventListener('click', (e) => {
                    e.preventDefault();
                    setOpen(!open);
                });

                window.addEventListener('resize', () => {
                    if (open) positionProfileMenu();
                });

                document.addEventListener('click', (e) => {
                    if (!open) return;
                    if (profileMenu.contains(e.target) || profileTrigger.contains(e.target)) return;
                    setOpen(false);
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape') setOpen(false);
                });
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const chatsList = document.getElementById('sidebar-chats-list');
            const activeConversationId = @json($activeConversationId);
            const searchInput = document.getElementById('sidebar-chat-search');
            const searchClearBtn = document.getElementById('sidebar-chat-search-clear');
            const searchCollapsedBtn = document.getElementById('sidebar-chat-search-collapsed');

            const normalizeSearch = (value) => String(value || '').toLowerCase().trim();

            const setSearchClearVisible = (visible) => {
                if (!searchClearBtn) return;
                searchClearBtn.classList.toggle('hidden', !visible);
                searchClearBtn.classList.toggle('flex', visible);
            };

            const ensureChatsPanelOpenForSearch = () => {
                const toggleEl = document.getElementById('sidebar-chats-toggle');
                const chevronEl = document.getElementById('sidebar-chats-chevron');
                const panelEl = document.getElementById('sidebar-chats-panel');
                if (!toggleEl || !chevronEl || !panelEl) return;
                const wrapper = toggleEl.parentElement;
                const available = wrapper ? Math.max(0, wrapper.clientHeight - toggleEl.offsetHeight - 12) : 0;
                chevronEl.classList.add('rotate-180');
                panelEl.style.height = `${available}px`;
                panelEl.style.opacity = '1';
                panelEl.style.pointerEvents = 'auto';
            };

            const resetChatSearchFilter = () => {
                if (!chatsList) return;
                chatsList.querySelectorAll('.sidebar-chat-item').forEach((item) => {
                    item.style.display = '';
                });
                const empty = document.getElementById('sidebar-search-empty');
                if (empty) empty.remove();
            };

            const applyChatSearchFilter = () => {
                if (!chatsList || !searchInput) return;
                const query = normalizeSearch(searchInput.value);
                const items = Array.from(chatsList.querySelectorAll('.sidebar-chat-item'));
                let shown = 0;

                items.forEach((item) => {
                    const title = item.querySelector('.sidebar-chat-title')?.textContent || '';
                    const ok = query === '' ? true : normalizeSearch(title).includes(query);
                    item.style.display = ok ? '' : 'none';
                    if (ok) shown += 1;
                });

                const existing = document.getElementById('sidebar-search-empty');
                if (query !== '' && shown === 0) {
                    if (!existing) {
                        const el = document.createElement('div');
                        el.id = 'sidebar-search-empty';
                        el.className = 'px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-widest';
                        el.textContent = 'No matching chats';
                        chatsList.appendChild(el);
                    }
                } else if (existing) {
                    existing.remove();
                }

                const inSearchMode = Boolean(sidebarEl?.classList.contains('sidebar-search-mode'));
                setSearchClearVisible(inSearchMode || query !== '');
            };

            const setSidebarSearchMode = (enabled) => {
                if (!sidebarEl) return;
                sidebarEl.classList.toggle('sidebar-search-mode', enabled);
                if (enabled) {
                    ensureChatsPanelOpenForSearch();
                    applyChatSearchFilter();
                    setSearchClearVisible(true);
                } else {
                    resetChatSearchFilter();
                    if (searchInput) searchInput.value = '';
                    setSearchClearVisible(false);
                }
            };

            if (searchInput && searchClearBtn && chatsList) {
                searchInput.addEventListener('focus', () => {
                    setSidebarSearchMode(true);
                });

                searchInput.addEventListener('input', () => {
                    applyChatSearchFilter();
                });

                searchClearBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    setSidebarSearchMode(false);
                });

                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Escape' && sidebarEl?.classList.contains('sidebar-search-mode')) {
                        setSidebarSearchMode(false);
                    }
                });

                const mo = new MutationObserver(() => {
                    if (!sidebarEl?.classList.contains('sidebar-search-mode')) return;
                    applyChatSearchFilter();
                });
                mo.observe(chatsList, { childList: true, subtree: true });
            }

            if (searchCollapsedBtn) {
                searchCollapsedBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    setSidebarCollapsed(false);
                    localStorage.setItem(sidebarCollapseKey, '0');
                    requestAnimationFrame(() => requestAnimationFrame(() => {
                        setSidebarSearchMode(true);
                        searchInput?.focus();
                    }));
                });
            }

            const requestJson = async (method, url, data) => {
                if (!url) throw new Error('Missing URL');
                if (window.axios) {
                    const resp = await window.axios.request({
                        method,
                        url,
                        data,
                        headers: { Accept: 'application/json', 'X-CSRF-TOKEN': csrfToken },
                    });
                    return resp.data;
                }

                const resp = await fetch(url, {
                    method,
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    },
                    body: data ? JSON.stringify(data) : undefined,
                });
                if (!resp.ok) throw new Error('Request failed');
                return resp.json();
            };

            const globalMenu = document.createElement('div');
            globalMenu.id = 'sidebar-chat-global-menu';
            globalMenu.className = 'fixed hidden w-52 overflow-hidden rounded-2xl bg-white/95 ring-1 ring-slate-900/10 backdrop-blur-xl shadow-[0_24px_70px_rgba(15,23,42,0.14)]';
            globalMenu.style.zIndex = '9999';
            document.body.appendChild(globalMenu);
            const confirmDialog = window.__confirmDialog || (({ message, title } = {}) => Promise.resolve(window.confirm(String(message || title || 'Confirm'))));

            let menuOpenForItem = null;
            let menuOpenForButton = null;

            const closeAllChatMenus = () => {
                menuOpenForItem = null;
                menuOpenForButton = null;
                globalMenu.classList.add('hidden');
                globalMenu.innerHTML = '';
                document.querySelectorAll('.sidebar-chat-menu').forEach((m) => m.classList.add('hidden'));
            };

            const openChatMenu = (btn, item) => {
                if (!btn || !item) return;

                const isPinned = item.dataset.isPinned === '1';
                const pinLabel = isPinned ? 'Unpin Chat' : 'Pin Chat';

                menuOpenForItem = item;
                menuOpenForButton = btn;

                globalMenu.innerHTML = `
                    <button type="button" data-action="rename" class="w-full flex items-center gap-2 px-3 py-1.5 text-[12px] font-semibold text-slate-700 hover:bg-slate-900/[0.03] transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-slate-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125L16.875 4.5" />
                        </svg>
                        <span>Rename</span>
                    </button>
                    <button type="button" data-action="pin" class="w-full flex items-center gap-2 px-3 py-1.5 text-[12px] font-semibold text-slate-700 hover:bg-slate-900/[0.03] transition">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4">
                            <circle cx="12" cy="6.5" r="3.25" class="text-rose-400" fill="currentColor" />
                            <path d="M12 10v10" class="text-slate-700" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M12 20l-1.4 2" class="text-slate-700" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                        <span>${pinLabel}</span>
                    </button>
                    <div class="h-px bg-slate-900/10"></div>
                    <button type="button" data-action="archive" class="w-full flex items-center gap-2 px-3 py-1.5 text-[12px] font-semibold text-slate-700 hover:bg-slate-900/[0.03] transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-slate-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                        <span>Archive</span>
                    </button>
                    <button type="button" data-action="delete" class="w-full flex items-center gap-2 px-3 py-1.5 text-[12px] font-semibold text-rose-700 hover:bg-rose-500/10 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-rose-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-1.806A2.25 2.25 0 0013.813 1.5h-3.626a2.25 2.25 0 00-2.25 2.25V3m7.5 0H9" />
                        </svg>
                        <span>Delete</span>
                    </button>
                `;

                globalMenu.classList.remove('hidden');
                globalMenu.style.left = '0px';
                globalMenu.style.top = '0px';
                globalMenu.style.visibility = 'hidden';

                requestAnimationFrame(() => {
                    const rect = btn.getBoundingClientRect();
                    const menuRect = globalMenu.getBoundingClientRect();

                    const margin = 8;
                    const viewportW = window.innerWidth;
                    const viewportH = window.innerHeight;

                    const maxLeft = viewportW - menuRect.width - margin;
                    const left = Math.max(margin, Math.min(rect.right - menuRect.width, maxLeft));

                    const spaceBelow = viewportH - rect.bottom;
                    const spaceAbove = rect.top;
                    const openDown = spaceBelow >= menuRect.height + margin || spaceBelow >= spaceAbove;

                    let top = openDown ? rect.bottom + margin : rect.top - menuRect.height - margin;
                    top = Math.max(margin, Math.min(top, viewportH - menuRect.height - margin));

                    globalMenu.style.left = `${left}px`;
                    globalMenu.style.top = `${top}px`;
                    globalMenu.style.visibility = 'visible';
                });
            };

            const ensureEmptyState = () => {
                if (!chatsList) return;
                const items = chatsList.querySelectorAll('.sidebar-chat-item');
                const existing = document.getElementById('sidebar-chats-empty');
                if (items.length === 0 && !existing) {
                    const empty = document.createElement('div');
                    empty.id = 'sidebar-chats-empty';
                    empty.className = 'px-4 py-3 text-xs font-bold text-slate-600 uppercase tracking-widest';
                    empty.textContent = 'No chats yet';
                    chatsList.appendChild(empty);
                }
                if (items.length > 0 && existing) existing.remove();
            };

            const updateMenuLabels = (item) => {
                const pinned = item.dataset.isPinned === '1';
                const pinText = item.querySelector('[data-action="pin"] span');
                if (pinText) pinText.textContent = pinned ? 'Unpin Chat' : 'Pin Chat';
            };

            const setPinnedVisual = (item, pinned, pinnedAt) => {
                item.dataset.isPinned = pinned ? '1' : '0';
                item.dataset.pinnedAt = pinned ? (pinnedAt || new Date().toISOString()) : '';
                const indicator = item.querySelector('.sidebar-pin-indicator');
                if (indicator) indicator.classList.toggle('hidden', !pinned);
            };

            const toMs = (value) => {
                if (!value) return 0;
                const ms = Date.parse(String(value));
                return Number.isFinite(ms) ? ms : 0;
            };

            const resortChatsList = () => {
                if (!chatsList) return;
                const items = Array.from(chatsList.querySelectorAll('.sidebar-chat-item'));
                items.sort((a, b) => {
                    const ap = a.dataset.isPinned === '1' ? 1 : 0;
                    const bp = b.dataset.isPinned === '1' ? 1 : 0;
                    if (ap !== bp) return bp - ap;

                    const apAt = toMs(a.dataset.pinnedAt);
                    const bpAt = toMs(b.dataset.pinnedAt);
                    if (apAt !== bpAt) return bpAt - apAt;

                    const am = toMs(a.dataset.lastMessageAt);
                    const bm = toMs(b.dataset.lastMessageAt);
                    if (am !== bm) return bm - am;

                    const aid = Number(a.dataset.conversationId || 0);
                    const bid = Number(b.dataset.conversationId || 0);
                    return bid - aid;
                });

                items.forEach((el) => chatsList.appendChild(el));
            };

            const moveForPinned = (item) => {
                if (!chatsList) return;
                resortChatsList();
            };

            const startRename = (item) => {
                const titleEl = item.querySelector('.sidebar-chat-title');
                if (!titleEl) return;

                const current = titleEl.textContent || '';
                const link = item.querySelector('a');
                if (!link) return;

                const linkHref = link.getAttribute('href') || item.dataset.showUrl || '#';
                const linkClass = link.className || '';

                const container = document.createElement('div');
                container.className = linkClass;

                const input = document.createElement('input');
                input.type = 'text';
                input.value = current.trim() === 'Untitled Thread' ? '' : current.trim();
                input.maxLength = 80;
                input.autocomplete = 'off';
                input.spellcheck = false;
                input.className = 'w-full rounded-xl bg-white px-3 py-2 text-sm font-semibold text-slate-900 outline-none ring-2 ring-indigo-500/40 focus:ring-indigo-500/60 placeholder:text-slate-400 selection:bg-indigo-200 selection:text-slate-900';

                container.appendChild(input);
                link.replaceWith(container);

                input.focus();
                input.select();

                let finished = false;
                const onOutsidePointerDown = (e) => {
                    if (finished) return;
                    const target = e.target;
                    if (!(target instanceof Element)) return;
                    if (target === input || container.contains(target)) return;
                    cancel();
                };

                const cleanup = () => {
                    document.removeEventListener('pointerdown', onOutsidePointerDown, true);
                };

                document.addEventListener('pointerdown', onOutsidePointerDown, true);

                const finish = (text) => {
                    finished = true;
                    cleanup();
                    const nextLink = document.createElement('a');
                    nextLink.href = linkHref;
                    nextLink.className = linkClass;

                    const div = document.createElement('div');
                    div.className = 'sidebar-chat-title truncate text-sm font-semibold tracking-tight';
                    div.textContent = text;

                    nextLink.appendChild(div);
                    container.replaceWith(nextLink);
                };

                const commit = async () => {
                    const next = input.value.replace(/\s+/g, ' ').trim();
                    const payloadTitle = next === '' ? null : next;
                    try {
                        const data = await requestJson('patch', item.dataset.updateUrl, { title: payloadTitle });
                        const finalTitle = (data?.title && String(data.title).trim()) ? String(data.title).trim() : 'Untitled Thread';
                        finish(finalTitle);
                    } catch (e) {
                        finish(current.trim() || 'Untitled Thread');
                    }
                };

                const cancel = () => {
                    finish(current.trim() || 'Untitled Thread');
                };

                input.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        commit();
                    }
                    if (e.key === 'Escape') {
                        e.preventDefault();
                        cancel();
                    }
                });
            };

            window.__adminSidebarUpsertConversation = ({ id, url, title, is_pinned, update_url, toggle_pin_url, toggle_save_url, delete_url }) => {
                if (!chatsList || !id) return null;

                const selector = `[data-conversation-id="${id}"]`;
                let item = chatsList.querySelector(selector);
                const displayTitle = (title && String(title).trim()) ? String(title).trim() : 'Untitled Thread';

                if (!item) {
                    item = document.createElement('div');
                    item.dataset.conversationId = String(id);
                    item.dataset.isPinned = is_pinned ? '1' : '0';
                    item.dataset.pinnedAt = is_pinned ? new Date().toISOString() : '';
                    item.dataset.lastMessageAt = new Date().toISOString();
                    item.dataset.showUrl = url;
                    item.dataset.updateUrl = update_url || `/conversations/${id}`;
                    item.dataset.togglePinUrl = toggle_pin_url || `/conversations/${id}/toggle-pin`;
                    item.dataset.toggleSaveUrl = toggle_save_url || `/conversations/${id}/toggle-save`;
                    item.dataset.deleteUrl = delete_url || `/conversations/${id}`;
                    item.className = 'sidebar-chat-item group relative flex items-center gap-2 rounded-2xl border transition-all border-indigo-500/30 bg-white/70 text-slate-900 ring-1 ring-indigo-500/20 shadow-sm';

                    const a = document.createElement('a');
                    a.href = url;
                    a.className = 'min-w-0 flex-1 px-4 py-3';
                    const t = document.createElement('div');
                    t.className = 'sidebar-chat-title truncate text-sm font-semibold tracking-tight';
                    a.appendChild(t);

                    const actions = document.createElement('div');
                    actions.className = 'relative pr-2';
                    actions.innerHTML = `
                        <button type="button" class="sidebar-chat-menu-btn flex h-8 w-8 items-center justify-center rounded-xl border border-transparent text-slate-500 opacity-100 transition lg:opacity-0 lg:group-hover:opacity-100 hover:border-slate-900/10 hover:bg-slate-900/[0.03] hover:text-slate-900">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                <path d="M6 10a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM11.5 10a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM17 10a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0z" />
                            </svg>
                        </button>
                        <div class="sidebar-chat-menu absolute right-0 bottom-full mb-2 hidden w-44 overflow-hidden rounded-2xl bg-white/95 ring-1 ring-slate-900/10 backdrop-blur-xl shadow-[0_24px_70px_rgba(15,23,42,0.14)]">
                            <button type="button" data-action="rename" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-900/[0.03] transition"><span>Rename</span></button>
                            <button type="button" data-action="pin" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-900/[0.03] transition"><span>Pin Chat</span></button>
                            <div class="h-px bg-slate-900/10"></div>
                            <button type="button" data-action="archive" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-900/[0.03] transition"><span>Archive</span></button>
                            <button type="button" data-action="delete" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-rose-700 hover:bg-rose-500/10 transition"><span>Delete</span></button>
                        </div>
                    `;

                    item.appendChild(a);
                    const pin = document.createElement('div');
                    pin.className = 'sidebar-pin-indicator pointer-events-none absolute right-10 top-2 z-10' + (is_pinned ? '' : ' hidden');
                    pin.innerHTML = `
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4">
                            <circle cx="12" cy="6.5" r="3.25" class="text-rose-400" fill="currentColor" />
                            <path d="M12 10v10" class="text-slate-700" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                            <path d="M12 20l-1.4 2" class="text-slate-700" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    `;
                    item.appendChild(pin);
                    item.appendChild(actions);
                    chatsList.prepend(item);
                    ensureEmptyState();
                }

                const link = item.querySelector('a');
                if (link && url) link.href = url;
                if (is_pinned !== undefined) item.dataset.isPinned = is_pinned ? '1' : '0';

                const titleEl = item.querySelector('.sidebar-chat-title');
                if (titleEl) titleEl.textContent = displayTitle;

                updateMenuLabels(item);
                moveForPinned(item);

                return item;
            };

            if (chatsList) {
                chatsList.querySelectorAll('.sidebar-chat-item').forEach(updateMenuLabels);
            }

            document.addEventListener('click', async (e) => {
                const target = e.target;
                if (!(target instanceof Element)) return;

                const menuBtn = target.closest('.sidebar-chat-menu-btn');
                if (menuBtn) {
                    closeAllChatMenus();
                    const item = menuBtn.closest('.sidebar-chat-item');
                    if (!item) return;
                    openChatMenu(menuBtn, item);
                    return;
                }

                const actionBtn = target.closest('#sidebar-chat-global-menu [data-action]');
                if (actionBtn) {
                    const action = actionBtn.getAttribute('data-action');
                    const item = menuOpenForItem;
                    if (!action || !item) return;
                    closeAllChatMenus();

                    if (action === 'rename') {
                        startRename(item);
                        return;
                    }

                    if (action === 'pin') {
                        requestJson('post', item.dataset.togglePinUrl).then((data) => {
                            const unpinnedIds = Array.isArray(data?.unpinned_ids) ? data.unpinned_ids : [];
                            unpinnedIds.forEach((id) => {
                                const other = chatsList?.querySelector(`[data-conversation-id="${id}"]`);
                                if (other) setPinnedVisual(other, false);
                            });

                            setPinnedVisual(item, !!data?.is_pinned, data?.pinned_at || '');
                            updateMenuLabels(item);
                            resortChatsList();
                        }).catch(() => {});
                        return;
                    }

                    if (action === 'archive') {
                        requestJson('post', item.dataset.toggleSaveUrl).then((data) => {
                            if (data?.is_saved) {
                                item.style.transition = 'opacity 160ms ease, transform 160ms ease';
                                item.style.opacity = '0';
                                item.style.transform = 'translateY(-4px)';
                                window.setTimeout(() => {
                                    item.remove();
                                    ensureEmptyState();
                                }, 180);
                            }
                        }).catch(() => {});
                        return;
                    }

                    if (action === 'delete') {
                        const title = item.querySelector('.sidebar-chat-title')?.textContent?.trim() || 'this conversation';
                        const ok = await confirmDialog({
                            title: 'Delete conversation?',
                            message: `This will permanently delete "${title}". This action cannot be undone.`,
                            okText: 'Delete',
                            cancelText: 'Cancel',
                        });
                        if (!ok) return;
                        requestJson('delete', item.dataset.deleteUrl).then(() => {
                            item.remove();
                            ensureEmptyState();
                            if (activeConversationId && String(activeConversationId) === String(item.dataset.conversationId)) {
                                window.location.href = @json(route($chatIndexRoute));
                            }
                        }).catch(() => {});
                        return;
                    }
                }

                if (!target.closest('#sidebar-chat-global-menu')) {
                    closeAllChatMenus();
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeAllChatMenus();
            });

            window.addEventListener('scroll', () => {
                if (!globalMenu.classList.contains('hidden')) closeAllChatMenus();
            }, true);

            window.addEventListener('resize', () => {
                if (!globalMenu.classList.contains('hidden')) closeAllChatMenus();
            });
        </script>
    </body>
</html>
