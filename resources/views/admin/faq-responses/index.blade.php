<x-admin-layout>
    <div class="space-y-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight">FAQ Response Manager</h2>
                <p class="mt-2 text-slate-600">Define exact inquiry–response pairs that override the chatbot response.</p>
            </div>
            <button id="faq-add-toggle" type="button" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-500 transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="mr-2 h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Response
            </button>
        </div>

        <div class="rounded-[2rem] bg-white/80 backdrop-blur-xl p-6 ring-1 ring-slate-900/5 shadow-[0_24px_70px_rgba(15,23,42,0.08)]">
            <form id="faq-filter-form" action="{{ route('admin.faq-responses.index') }}" method="GET" class="flex flex-col gap-4 md:flex-row md:items-end">
                <div class="flex-1">
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Search</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute left-5 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 104.473 8.708l2.41 2.409a.75.75 0 101.06-1.06l-2.409-2.41A5.5 5.5 0 009 3.5zM4.5 9a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input id="faq-search" type="search" name="q" value="{{ $q }}" autocomplete="off" spellcheck="false" placeholder="Search inquiry or response..."
                            class="w-full rounded-2xl bg-white/80 border-slate-900/10 py-3.5 pl-14 pr-12 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all">
                        <button id="faq-search-clear" type="button" aria-label="Clear search" class="absolute right-3 top-1/2 hidden h-9 w-9 -translate-y-1/2 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-900/[0.04] hover:text-slate-900">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                <path fill-rule="evenodd" d="M4.22 4.22a.75.75 0 011.06 0L10 8.94l4.72-4.72a.75.75 0 111.06 1.06L11.06 10l4.72 4.72a.75.75 0 11-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 11-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="submit" class="h-[52px] px-8 flex items-center justify-center rounded-2xl bg-white/80 text-slate-700 ring-1 ring-slate-900/10 hover:bg-white hover:text-slate-900 transition-all text-sm font-bold shadow-sm">
                    Apply
                </button>
            </form>
        </div>

        <div id="faq-add-panel" class="hidden rounded-[2rem] bg-white/80 backdrop-blur-xl p-6 ring-1 ring-slate-900/5 shadow-[0_24px_70px_rgba(15,23,42,0.08)]">
            <form method="POST" action="{{ route('admin.faq-responses.store') }}" class="space-y-5">
                @csrf
                <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Inquiry</label>
                        <textarea name="inquiry" rows="4" required class="w-full rounded-2xl bg-white/80 border border-slate-900/10 px-6 py-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all resize-y">{{ old('inquiry') }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Response</label>
                        <textarea name="response" rows="4" required class="w-full rounded-2xl bg-white/80 border border-slate-900/10 px-6 py-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all resize-y">{{ old('response') }}</textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" id="faq-add-cancel" class="h-11 px-6 rounded-2xl bg-white/80 text-slate-700 ring-1 ring-slate-900/10 hover:bg-white hover:text-slate-900 transition text-sm font-bold shadow-sm">
                        Cancel
                    </button>
                    <button type="submit" class="h-11 px-7 rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/20 hover:bg-blue-500 transition text-sm font-bold">
                        Save
                    </button>
                </div>
            </form>
        </div>

        <div id="faq-results" class="overflow-hidden rounded-[2rem] bg-white/80 backdrop-blur-xl ring-1 ring-slate-900/5 shadow-[0_24px_70px_rgba(15,23,42,0.08)]">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-900/5 bg-white/40">
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Inquiry</th>
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Response</th>
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-900/5">
                        @forelse ($items as $item)
                            <tr class="group hover:bg-slate-900/[0.02] transition-colors">
                                <td class="px-8 py-6 align-top">
                                    <div class="text-sm font-bold text-slate-900 line-clamp-2">{{ $item->inquiry }}</div>
                                    <div class="mt-1 text-xs font-medium text-slate-500">Updated {{ $item->updated_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-8 py-6 align-top">
                                    <div class="text-sm font-semibold text-slate-700 line-clamp-3 whitespace-pre-wrap">{{ $item->response }}</div>
                                </td>
                                <td class="px-8 py-6 text-right align-top">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('admin.faq-responses.edit', $item) }}" class="p-2 rounded-xl bg-white/70 text-slate-600 hover:text-blue-600 hover:bg-blue-500/10 transition-all shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.faq-responses.destroy', $item) }}" data-confirm="delete" data-confirm-title="Delete FAQ Response" data-confirm-message="Delete this FAQ response?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl bg-rose-500/5 text-rose-600 ring-1 ring-rose-500/20 hover:bg-rose-600 hover:text-white transition-all">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-1.806A2.25 2.25 0 0013.813 1.5h-3.626a2.25 2.25 0 00-2.25 2.25V3m7.5 0H9" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-8 py-12 text-center">
                                    <div class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">No FAQ responses yet</div>
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
        const toggleBtn = document.getElementById('faq-add-toggle');
        const panel = document.getElementById('faq-add-panel');
        const cancelBtn = document.getElementById('faq-add-cancel');
        const form = document.getElementById('faq-filter-form');
        const searchInput = document.getElementById('faq-search');
        const clearBtn = document.getElementById('faq-search-clear');
        const resultsEl = document.getElementById('faq-results');

        const setOpen = (open) => {
            if (!panel) return;
            panel.classList.toggle('hidden', !open);
            if (open) {
                panel.scrollIntoView({ block: 'start', behavior: 'smooth' });
                const first = panel.querySelector('textarea[name="inquiry"]');
                if (first instanceof HTMLElement) first.focus();
            }
        };

        if (toggleBtn && panel) {
            toggleBtn.addEventListener('click', () => setOpen(panel.classList.contains('hidden')));
        }

        if (cancelBtn && panel) {
            cancelBtn.addEventListener('click', () => setOpen(false));
        }

        if (form && searchInput && resultsEl) {
            let debounceTimer = null;
            let activeController = null;

            const setClearVisible = (visible) => {
                if (!clearBtn) return;
                clearBtn.classList.toggle('hidden', !visible);
                clearBtn.classList.toggle('flex', visible);
            };

            const buildUrl = () => {
                const params = new URLSearchParams(new FormData(form));
                const url = new URL(form.action, window.location.origin);
                const qs = params.toString();
                url.search = qs;
                return url.toString();
            };

            const replaceResultsFromHtml = (html) => {
                const doc = new DOMParser().parseFromString(html, 'text/html');
                const next = doc.getElementById('faq-results');
                if (!next) return;
                resultsEl.innerHTML = next.innerHTML;
            };

            const fetchAndRender = async (url) => {
                if (activeController) activeController.abort();
                activeController = new AbortController();

                try {
                    const resp = await fetch(url, {
                        method: 'GET',
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        signal: activeController.signal,
                    });
                    if (!resp.ok) return;
                    const html = await resp.text();
                    replaceResultsFromHtml(html);
                    window.history.replaceState({}, '', url);
                } catch (_) {
                }
            };

            const scheduleFetch = () => {
                clearTimeout(debounceTimer);
                debounceTimer = setTimeout(() => {
                    fetchAndRender(buildUrl());
                }, 180);
            };

            form.addEventListener('submit', (e) => {
                e.preventDefault();
                fetchAndRender(buildUrl());
            });

            searchInput.addEventListener('input', () => {
                setClearVisible((searchInput.value || '').trim() !== '');
                scheduleFetch();
            });

            resultsEl.addEventListener('click', (e) => {
                const link = e.target?.closest?.('a');
                if (!link || !link.href) return;

                const url = new URL(link.href, window.location.origin);
                if (url.pathname !== window.location.pathname) return;
                if (!url.searchParams.has('page')) return;

                e.preventDefault();
                fetchAndRender(url.toString());
            });

            if (clearBtn) {
                setClearVisible((searchInput.value || '').trim() !== '');
                clearBtn.addEventListener('click', (e) => {
                    e.preventDefault();
                    searchInput.value = '';
                    setClearVisible(false);
                    fetchAndRender(buildUrl());
                    searchInput.focus();
                });
            }
        }
    </script>
</x-admin-layout>
<style>
#faq-search::-webkit-search-cancel-button,
#faq-search::-webkit-search-decoration {
    -webkit-appearance: none;
    appearance: none;
}
</style>
