<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased selection:bg-purple-500/30 selection:text-purple-200">
        <div class="admin-shell min-h-screen bg-[#0b0b1a] text-slate-200 overflow-x-hidden relative">
            <div class="mx-auto max-w-[1600px] px-4 py-6 lg:px-8">
                <!-- Top Header -->
                <header class="flex items-center justify-between mb-8 px-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-800/50 ring-1 ring-white/10">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-6 w-6 text-slate-400">
                                <path d="M16.5 7.5h-9v9h9v-9z" />
                                <path fill-rule="evenodd" d="M8.25 2.25A.75.75 0 019 3v.75h2.25V3a.75.75 0 011.5 0v.75H15V3a.75.75 0 011.5 0v.75h.75a3 3 0 013 3v.75H21a.75.75 0 010 1.5h-.75V12h.75a.75.75 0 010 1.5h-.75v2.25h.75a.75.75 0 010 1.5h-.75v.75a3 3 0 01-3 3h-.75V21a.75.75 0 01-1.5 0v-.75h-2.25V21a.75.75 0 01-1.5 0v-.75H9V21a.75.75 0 01-1.5 0v-.75h-.75a3 3 0 01-3-3v-.75H3a.75.75 0 010-1.5h.75V12H3a.75.75 0 010-1.5h.75V8.25H3a.75.75 0 010-1.5h.75v-.75a3 3 0 013-3h.75V3a.75.75 0 01.75-.75zM5.25 6.75a1.5 1.5 0 00-1.5 1.5v7.5a1.5 1.5 0 001.5 1.5h13.5a1.5 1.5 0 001.5-1.5v-7.5a1.5 1.5 0 00-1.5-1.5H5.25z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <span class="text-2xl font-bold tracking-tight text-white uppercase">LYRA</span>
                    </div>

                    <div class="flex items-center gap-3">
                        <button class="flex items-center gap-2 rounded-full bg-slate-800/50 px-5 py-2 text-sm font-semibold text-white ring-1 ring-white/10 hover:bg-slate-700/50 transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 13.5l10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75z" />
                            </svg>
                            Upgrade
                        </button>
                        <button class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-800/50 text-slate-400 ring-1 ring-white/10 hover:text-white transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.171-1.025 3.071-1.025 4.242 0 1.172 1.025 1.172 2.687 0 3.712-.203.179-.43.326-.67.442-.745.361-1.45.999-1.45 1.827v.75M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9 5.25h.008v.008H12v-.008z" />
                            </svg>
                        </button>
                        <button class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-800/50 text-slate-400 ring-1 ring-white/10 hover:text-white transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 11.25v8.25a1.5 1.5 0 01-1.5 1.5H5.25a1.5 1.5 0 01-1.5-1.5v-8.25M12 4.875A2.625 2.625 0 109.375 7.5H12m0-2.625V7.5m0-2.625A2.625 2.625 0 1114.625 7.5H12m0 0V21m-8.625-9.75h17.25" />
                            </svg>
                        </button>
                        <button class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-800/50 text-slate-400 ring-1 ring-white/10 hover:text-white transition-all">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.438.995a6.19 6.19 0 010 1.902c0 .382.145.755.438.995l1.003.827c.424.35.534.954.26 1.431l-1.296 2.247a1.125 1.125 0 01-1.37.49l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.063-.374-.313-.686-.645-.87a6.52 6.52 0 01-.22-.127c-.324-.196-.72-.257-1.075-.124l-1.217.456a1.125 1.125 0 01-1.37-.49l-1.296-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.437-.995a6.19 6.19 0 010-1.902c0-.382-.145-.755-.437-.995l-1.004-.827a1.125 1.125 0 01-.26-1.431l1.296-2.247a1.125 1.125 0 011.37-.49l1.217.456c.355.133.75.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                        </button>
                    </div>
                </header>

                <div class="grid grid-cols-1 gap-8 lg:grid-cols-[280px_1fr]">
                    <aside class="flex flex-col h-[calc(100vh-10rem)] bg-[#13132b] rounded-[2rem] p-6 shadow-2xl relative">
                        <nav class="space-y-3 flex-1">
                            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('dashboard') ? 'bg-slate-800/50 text-white' : 'text-slate-400 hover:text-white' }}">
                                <div class="flex h-6 w-6 items-center justify-center rounded bg-amber-500/20 text-amber-500 text-[10px] font-bold">A</div>
                                <span class="text-sm font-medium">My Workspace</span>
                            </a>
                            
                            <a href="{{ route('admin.legal.ai') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('admin.legal.ai*') ? 'bg-gradient-to-r from-purple-600/30 to-blue-600/30 text-white ring-1 ring-white/10' : 'text-slate-400 hover:text-white' }}">
                                <div class="relative flex h-6 w-6 items-center justify-center rounded-full bg-purple-500 shadow-[0_0_10px_rgba(168,85,247,0.5)]">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-3 w-3 text-white">
                                        <path d="M15.98 1.804a1 1 0 00-1.96 0l-.24 1.192a1 1 0 01-.784.784l-1.192.24a1 1 0 000 1.96l1.192.24a1 1 0 01.784.784l.24 1.192a1 1 0 001.96 0l.24-1.192a1 1 0 01.784-.784l1.192-.24a1 1 0 000-1.96l-1.192-.24a1 1 0 01-.784-.784l-.24-1.192zM5 5a5 5 0 00-5 5v3a5 5 0 005 5h1a1 1 0 001-1v-3a1 1 0 00-1-1H5a3 3 0 01-3-3v-3a3 3 0 013-3h1a1 1 0 001-1V6a1 1 0 00-1-1H5zm7 4a1 1 0 011 1v5a1 1 0 01-1 1h-1a1 1 0 01-1-1v-5a1 1 0 011-1h1zm5 2a1 1 0 011 1v3a1 1 0 01-1 1h-1a1 1 0 01-1-1v-3a1 1 0 011-1h1z" />
                                    </svg>
                                </div>
                                <span class="text-sm font-medium">Chat With LYRA</span>
                            </a>

                            <a href="{{ route('admin.laws.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 opacity-60">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                                <span class="text-sm font-medium">Gallery Library</span>
                            </a>

                            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 opacity-60">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 9.776c.112-.001.224-.001.336-.001a11.039 11.039 0 0110.14 7.151 11.042 11.042 0 01-10.14 7.151c-.112 0-.224 0-.336-.001L3.75 9.776zM12 3v3m0 0a3 3 0 100 6 3 3 0 000-6zm0 0l3-3m-3 3l-3-3" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                                <span class="text-sm font-medium">Space</span>
                            </a>

                            <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-400 hover:text-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 opacity-60">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                                <span class="text-sm font-medium">Archive</span>
                            </a>
                        </nav>

                        <div class="mt-auto space-y-4">
                            <a href="#" class="flex items-center gap-3 px-4 py-2 text-slate-400 hover:text-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 opacity-60">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.5 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                </svg>
                                <span class="text-xs font-medium">Invite People</span>
                            </a>
                            <a href="#" class="flex items-center gap-3 px-4 py-2 text-slate-400 hover:text-white transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 opacity-60">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.438.995a6.19 6.19 0 010 1.902c0 .382.145.755.438.995l1.003.827c.424.35.534.954.26 1.431l-1.296 2.247a1.125 1.125 0 01-1.37.49l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.063-.374-.313-.686-.645-.87a6.52 6.52 0 01-.22-.127c-.324-.196-.72-.257-1.075-.124l-1.217.456a1.125 1.125 0 01-1.37-.49l-1.296-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.437-.995a6.19 6.19 0 010-1.902c0-.382-.145-.755-.437-.995l-1.004-.827a1.125 1.125 0 01-.26-1.431l1.296-2.247a1.125 1.125 0 011.37-.49l1.217.456c.355.133.75.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                </svg>
                                <span class="text-xs font-medium">Settings</span>
                            </a>

                            <div class="flex items-center gap-3 px-2 py-4">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=6366f1&color=fff" alt="User Avatar" class="h-10 w-10 rounded-xl shadow-lg">
                                <div class="min-w-0">
                                    <div class="truncate text-sm font-bold text-white">{{ auth()->user()->name }}</div>
                                    <div class="truncate text-[10px] font-medium text-slate-500">{{ auth()->user()->email }}</div>
                                </div>
                            </div>
                        </div>
                    </aside>

                    <main class="min-w-0">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>


                        <!-- Main Content -->
                        <div class="flex-1">
                            {{ $slot }}
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </body>
</html>
