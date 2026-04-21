<x-admin-layout>
    <div class="mx-auto w-full max-w-5xl">
        <div class="mb-8">
            <div class="flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-black tracking-tight text-slate-900">Archived Chats</h2>
                    <p class="mt-2 text-slate-600 font-medium">Restore or delete saved conversations.</p>
                </div>
            </div>
        </div>

        <div class="rounded-[2.5rem] bg-white/80 backdrop-blur-xl ring-1 ring-slate-900/5 shadow-[0_24px_70px_rgba(15,23,42,0.08)] overflow-visible">
            @if ($conversations->isEmpty())
                <div class="px-10 py-12 text-center">
                    <div class="text-xs font-black uppercase tracking-[0.2em] text-slate-600">No archived chats yet</div>
                </div>
            @else
                <div class="divide-y divide-slate-900/5">
                    @foreach ($conversations as $conversation)
                        <div class="flex items-center gap-4 px-8 py-6">
                            <div class="min-w-0 flex-1">
                                <div class="truncate text-base font-bold text-slate-900">
                                    {{ $conversation->title ?: 'Untitled Thread' }}
                                </div>
                                <div class="mt-1 text-[11px] font-bold uppercase tracking-[0.18em] text-slate-600">
                                    {{ $conversation->created_at?->format('M d, Y') }}
                                </div>
                            </div>

                            <div class="relative">
                                <button type="button" class="archive-actions-trigger flex h-10 w-10 items-center justify-center rounded-xl border border-slate-900/10 bg-white/70 text-slate-600 hover:bg-white hover:text-slate-900 transition shadow-sm">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                        <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 14a1.5 1.5 0 110 3 1.5 1.5 0 010-3z" />
                                    </svg>
                                </button>

                                <div class="archive-actions-menu absolute right-0 bottom-full mb-3 hidden w-44 overflow-hidden rounded-2xl bg-white/95 ring-1 ring-slate-900/10 backdrop-blur-xl shadow-[0_24px_70px_rgba(15,23,42,0.14)] z-50">
                                    <form method="POST" action="{{ route('conversations.toggle-save', $conversation) }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-900/[0.03] transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-slate-500">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75A2.25 2.25 0 0014.25 4.5h-7.5A2.25 2.25 0 004.5 6.75v10.5A2.25 2.25 0 006.75 19.5h7.5A2.25 2.25 0 0016.5 17.25V13.5m3 0L21 12m0 0l-1.5-1.5M21 12H9" />
                                            </svg>
                                            <span>Restore</span>
                                        </button>
                                    </form>
                                    <div class="h-px bg-slate-900/10"></div>
                                    <form method="POST" action="{{ route('conversations.destroy', $conversation) }}" class="m-0" data-confirm="delete" data-confirm-title="Delete Conversation" data-confirm-message="Delete this conversation?">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 text-sm font-semibold text-rose-700 hover:bg-rose-500/10 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 text-rose-600">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-1.806A2.25 2.25 0 0013.813 1.5h-3.626a2.25 2.25 0 00-2.25 2.25V3m7.5 0H9" />
                                            </svg>
                                            <span>Delete</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <script type="module">
        const triggers = Array.from(document.querySelectorAll('.archive-actions-trigger'));

        const closeAll = () => {
            document.querySelectorAll('.archive-actions-menu').forEach((el) => el.classList.add('hidden'));
        };

        triggers.forEach((btn) => {
            const menu = btn.parentElement?.querySelector('.archive-actions-menu');
            if (!menu) return;

            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const isHidden = menu.classList.contains('hidden');
                closeAll();
                if (isHidden) menu.classList.remove('hidden');
            });
        });

        document.addEventListener('click', (e) => {
            const target = e.target;
            if (!(target instanceof Element)) return;
            if (target.closest('.archive-actions-trigger') || target.closest('.archive-actions-menu')) return;
            closeAll();
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeAll();
        });
    </script>
</x-admin-layout>
