<x-admin-layout>
    <div class="flex flex-col items-center justify-center min-h-[calc(100vh-12rem)] py-12 px-4 relative overflow-hidden bg-[#13132b] rounded-[3rem] shadow-2xl ring-1 ring-white/5">
        <!-- Background Decorative Glow -->
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-purple-600/10 blur-[120px] rounded-full pointer-events-none"></div>

        <!-- Central AI Sphere Icon -->
        <div class="relative mb-10 group">
            <div class="absolute inset-0 bg-purple-500 blur-[40px] opacity-20 group-hover:opacity-40 transition-opacity animate-pulse"></div>
            <div class="relative h-32 w-32 rounded-full bg-gradient-to-tr from-purple-600 via-indigo-600 to-blue-500 p-1 shadow-2xl overflow-hidden">
                <div class="h-full w-full rounded-full bg-[#0b0b1a] flex items-center justify-center overflow-hidden">
                    <!-- Purple Energy Effect -->
                    <div class="absolute inset-0 opacity-50">
                        <div class="absolute inset-0 bg-[radial-gradient(circle_at_50%_50%,rgba(168,85,247,0.4),transparent_70%)] animate-pulse"></div>
                        <div class="absolute inset-0 bg-[conic-gradient(from_0deg,transparent,rgba(168,85,247,0.2),transparent)] animate-spin-slow"></div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-16 w-16 text-purple-400 relative z-10">
                        <path d="M16.5 7.5h-9v9h9v-9z" />
                        <path fill-rule="evenodd" d="M8.25 2.25A.75.75 0 019 3v.75h2.25V3a.75.75 0 011.5 0v.75H15V3a.75.75 0 011.5 0v.75h.75a3 3 0 013 3v.75H21a.75.75 0 010 1.5h-.75V12h.75a.75.75 0 010 1.5h-.75v2.25h.75a.75.75 0 010 1.5h-.75v.75a3 3 0 01-3 3h-.75V21a.75.75 0 01-1.5 0v-.75h-2.25V21a.75.75 0 01-1.5 0v-.75H9V21a.75.75 0 01-1.5 0v-.75h-.75a3 3 0 01-3-3v-.75H3a.75.75 0 010-1.5h.75V12H3a.75.75 0 010-1.5h.75V8.25H3a.75.75 0 010-1.5h.75v-.75a3 3 0 013-3h.75V3a.75.75 0 01.75-.75zM5.25 6.75a1.5 1.5 0 00-1.5 1.5v7.5a1.5 1.5 0 001.5 1.5h13.5a1.5 1.5 0 001.5-1.5v-7.5a1.5 1.5 0 00-1.5-1.5H5.25z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
        </div>

        <h1 class="text-4xl font-bold text-white tracking-tight text-center mb-2">Hi, {{ explode(' ', auth()->user()->name)[0] }}! I'm LYRA, your intelligent assistant.</h1>
        <p class="text-slate-400 text-center mb-12 font-medium">Start a conversation, ask questions, or explore what I can help you with today.</p>

        <!-- Feature Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl w-full">
            <!-- AI Chatbot -->
            <a href="{{ route('admin.legal.ai') }}" class="group p-6 rounded-3xl bg-slate-800/20 border border-white/5 hover:bg-slate-800/40 hover:border-purple-500/30 transition-all duration-300">
                <div class="h-10 w-10 rounded-xl bg-emerald-500/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 text-emerald-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z" />
                    </svg>
                </div>
                <h3 class="text-white font-bold mb-1">AI Chatbot</h3>
                <p class="text-slate-500 text-sm leading-relaxed">Create text for ads, emails, and content instantly.</p>
            </a>

            <!-- Artwork Generation -->
            <div class="group p-6 rounded-3xl bg-slate-800/20 border border-white/5 hover:bg-slate-800/40 transition-all duration-300">
                <div class="h-10 w-10 rounded-xl bg-orange-500/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 text-orange-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                    </svg>
                </div>
                <h3 class="text-white font-bold mb-1">Artwork Generation</h3>
                <p class="text-slate-500 text-sm leading-relaxed">Design unique visuals with AI creativity.</p>
            </div>

            <!-- Research -->
            <a href="{{ route('admin.laws.index') }}" class="group p-6 rounded-3xl bg-slate-800/20 border border-white/5 hover:bg-slate-800/40 hover:border-blue-500/30 transition-all duration-300">
                <div class="h-10 w-10 rounded-xl bg-blue-500/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 text-blue-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <h3 class="text-white font-bold mb-1">Research</h3>
                <p class="text-slate-500 text-sm leading-relaxed">Find, summarize, and organize info fast.</p>
            </a>

            <!-- Generate Article -->
            <div class="group p-6 rounded-3xl bg-slate-800/20 border border-white/5 hover:bg-slate-800/40 transition-all duration-300">
                <div class="h-10 w-10 rounded-xl bg-amber-500/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 text-amber-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <h3 class="text-white font-bold mb-1">Generate Article</h3>
                <p class="text-slate-500 text-sm leading-relaxed">Write articles or blogs in seconds.</p>
            </div>

            <!-- Data Analytics -->
            <div class="group p-6 rounded-3xl bg-slate-800/20 border border-white/5 hover:bg-slate-800/40 transition-all duration-300">
                <div class="h-10 w-10 rounded-xl bg-purple-500/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 text-purple-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v18h16.5V3H3.75zm.75 16.5V4.5h15v15h-15z" />
                    </svg>
                </div>
                <h3 class="text-white font-bold mb-1">Data Analytics</h3>
                <p class="text-slate-500 text-sm leading-relaxed">Turn data into clear insights with LYRA AI.</p>
            </div>

            <!-- Dev Mode -->
            <div class="group p-6 rounded-3xl bg-slate-800/20 border border-white/5 hover:bg-slate-800/40 transition-all duration-300">
                <div class="h-10 w-10 rounded-xl bg-indigo-500/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5 text-indigo-500">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 7.5l3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0021 18V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v12a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                </div>
                <h3 class="text-white font-bold mb-1">Dev Mode</h3>
                <p class="text-slate-500 text-sm leading-relaxed">Generate and refine code effortlessly.</p>
            </div>
        </div>

        <!-- Dashboard Chat Input -->
        <div class="mt-16 w-full max-w-4xl">
            <div class="relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-purple-600 to-blue-600 rounded-[2rem] blur opacity-25 group-focus-within:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                <div class="relative flex flex-col bg-[#0b0b1a] rounded-[2rem] border border-white/10 p-4 shadow-2xl">
                    <div class="flex items-center gap-3 px-4 py-2 mb-2">
                        <div class="flex h-5 w-5 items-center justify-center rounded-full bg-gradient-to-r from-orange-500 to-red-500">
                            <div class="h-1.5 w-1.5 rounded-full bg-white animate-pulse"></div>
                        </div>
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-widest">What do you want to know?</span>
                    </div>
                    <div class="flex items-center gap-4 px-4 pb-2">
                        <input type="text" placeholder="Ask LYRA anything..." class="flex-1 bg-transparent border-0 text-white placeholder:text-slate-600 focus:ring-0 text-lg py-2">
                        <div class="flex items-center gap-2">
                            <button class="p-2 text-slate-500 hover:text-white transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32a1.5 1.5 0 01-2.121-2.121L16.243 6.415" />
                                </svg>
                            </button>
                            <button class="h-10 w-10 flex items-center justify-center rounded-xl bg-orange-600 text-white shadow-lg shadow-orange-600/20 hover:bg-orange-500 transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="h-5 w-5 rotate-45">
                                    <path d="M3.478 2.405a.75.75 0 00-.926.94l2.432 7.905H13.5a.75.75 0 010 1.5H4.984l-2.432 7.905a.75.75 0 00.926.94 60.519 60.519 0 0018.445-8.986.75.75 0 000-1.218A60.517 60.517 0 003.478 2.405z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes spin-slow {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        .animate-spin-slow {
            animation: spin-slow 12s linear infinite;
        }
    </style>
</x-admin-layout>
