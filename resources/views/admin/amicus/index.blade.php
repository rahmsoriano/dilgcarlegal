<x-admin-layout>
    <div class="space-y-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-3xl font-bold tracking-tight text-slate-900">Amicus Sections</h2>
                <p class="mt-2 text-slate-600">Manage AMICUS knowledge by section so the legal assistant stays organized and easy to maintain.</p>
            </div>
            <button id="amicus-add-toggle" type="button" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition-all duration-300 hover:bg-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="mr-2 h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Section
            </button>
        </div>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-6 py-4 text-sm font-semibold text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-[2rem] bg-white/80 p-6 shadow-[0_24px_70px_rgba(15,23,42,0.08)] ring-1 ring-slate-900/5 backdrop-blur-xl">
            <form action="{{ route('admin.amicus.index') }}" method="GET" class="flex flex-col gap-4 md:flex-row md:items-end">
                <div class="flex-1">
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Search</label>
                    <input type="search" name="q" value="{{ $q }}" placeholder="Search section title, category, or content..." class="w-full rounded-2xl border-slate-900/10 bg-white/80 px-5 py-3.5 text-sm text-slate-900 placeholder:text-slate-400 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">
                </div>
                <button type="submit" class="flex h-[52px] items-center justify-center rounded-2xl bg-white/80 px-8 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-slate-900/10 transition-all hover:bg-white hover:text-slate-900">
                    Apply
                </button>
            </form>
        </div>

        <div id="amicus-add-panel" class="hidden rounded-[2rem] bg-white/80 p-6 shadow-[0_24px_70px_rgba(15,23,42,0.08)] ring-1 ring-slate-900/5 backdrop-blur-xl">
            <form method="POST" action="{{ route('admin.amicus.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Section Title</label>
                    <input type="text" name="section_title" value="{{ old('section_title') }}" required class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-6 py-4 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">
                    @error('section_title')
                        <div class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>
                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Section Content</label>
                    <textarea name="section_content" rows="10" required class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-6 py-4 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">{{ old('section_content') }}</textarea>
                    @error('section_content')
                        <div class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" id="amicus-add-cancel" class="h-11 rounded-2xl bg-white/80 px-6 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-slate-900/10 transition hover:bg-white hover:text-slate-900">
                        Cancel
                    </button>
                    <button type="submit" class="h-11 rounded-2xl bg-blue-600 px-7 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-500">
                        Save Section
                    </button>
                </div>
            </form>
        </div>

        <div class="overflow-hidden rounded-[2rem] bg-white/80 ring-1 ring-slate-900/5 shadow-[0_24px_70px_rgba(15,23,42,0.08)] backdrop-blur-xl">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-slate-900/5 bg-white/40">
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Section</th>
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Category</th>
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Content Preview</th>
                            <th class="px-8 py-5 text-right text-[10px] font-bold uppercase tracking-widest text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-900/5">
                        @forelse ($items as $item)
                            <tr class="group transition-colors hover:bg-slate-900/[0.02]">
                                <td class="px-8 py-6 align-top">
                                    <div class="text-sm font-bold text-slate-900">{{ $item->section_title }}</div>
                                    <div class="mt-1 text-xs font-medium text-slate-500">Updated {{ $item->updated_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-8 py-6 align-top">
                                    <div class="text-sm font-medium text-slate-600">{{ $item->category ?: 'Uncategorized' }}</div>
                                </td>
                                <td class="px-8 py-6 align-top">
                                    <div class="line-clamp-4 whitespace-pre-wrap text-sm font-semibold text-slate-700">{{ $item->section_content }}</div>
                                </td>
                                <td class="px-8 py-6 align-top text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 transition-opacity group-hover:opacity-100">
                                        <a href="{{ route('admin.amicus.edit', $item) }}" class="rounded-xl bg-white/70 p-2 text-slate-600 shadow-sm transition-all hover:bg-blue-500/10 hover:text-blue-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.amicus.destroy', $item) }}" data-confirm="delete" data-confirm-title="Delete AMICUS Section" data-confirm-message="Delete this AMICUS section?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="rounded-xl bg-rose-500/5 p-2 text-rose-600 ring-1 ring-rose-500/20 transition-all hover:bg-rose-600 hover:text-white">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9 14.394 18m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0V3.984A2.25 2.25 0 0 0 13.813 1.5h-3.626a2.25 2.25 0 0 0-2.25 2.25v1.806m7.5 0H9" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-12 text-center">
                                    <div class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">No AMICUS sections yet</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($items->hasPages())
                <div class="border-t border-slate-900/5 bg-white/40 px-8 py-5">
                    {{ $items->links() }}
                </div>
            @endif
        </div>
    </div>

    <script type="module">
        const toggleBtn = document.getElementById('amicus-add-toggle');
        const panel = document.getElementById('amicus-add-panel');
        const cancelBtn = document.getElementById('amicus-add-cancel');

        const setOpen = (open) => {
            if (!panel) return;
            panel.classList.toggle('hidden', !open);
            if (open) {
                panel.scrollIntoView({ block: 'start', behavior: 'smooth' });
                const first = panel.querySelector('input[name="section_title"]');
                if (first instanceof HTMLElement) first.focus();
            }
        };

        if (toggleBtn && panel) {
            toggleBtn.addEventListener('click', () => setOpen(panel.classList.contains('hidden')));
        }

        if (cancelBtn && panel) {
            cancelBtn.addEventListener('click', () => setOpen(false));
        }

        @if ($errors->any())
            setOpen(true);
        @endif
    </script>
</x-admin-layout>
