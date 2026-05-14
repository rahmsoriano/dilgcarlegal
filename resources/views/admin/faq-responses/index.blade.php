<x-admin-layout>
    <div class="space-y-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-3xl font-bold tracking-tight text-slate-900">FAQ Response Manager</h2>
                <p class="mt-2 text-slate-600">Manage database-driven FAQ answers, alternative question phrasing, and chatbot-ready response formatting.</p>
            </div>
            <button id="faq-add-toggle" type="button" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition-all duration-300 hover:bg-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="mr-2 h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Response
            </button>
        </div>

        @if (session('status'))
            <div class="rounded-2xl border border-emerald-500/20 bg-emerald-500/10 px-6 py-4 text-sm font-semibold text-emerald-700">
                {{ session('status') }}
            </div>
        @endif

        <div class="rounded-[2rem] bg-white/80 p-6 shadow-[0_24px_70px_rgba(15,23,42,0.08)] ring-1 ring-slate-900/5 backdrop-blur-xl">
            <form id="faq-filter-form" action="{{ route('admin.faq-responses.index') }}" method="GET" class="flex flex-col gap-4 md:flex-row md:items-end">
                <div class="flex-1">
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Search</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute left-5 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 4.473 8.708l2.41 2.409a.75.75 0 1 0 1.06-1.06l-2.409-2.41A5.5 5.5 0 0 0 9 3.5ZM4.5 9a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0Z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input id="faq-search" type="search" name="q" value="{{ $q }}" autocomplete="off" spellcheck="false" placeholder="Search question, aliases, or answer..." class="w-full rounded-2xl border-slate-900/10 bg-white/80 py-3.5 pl-14 pr-12 text-sm text-slate-900 placeholder:text-slate-400 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">
                        <button id="faq-search-clear" type="button" aria-label="Clear search" class="absolute right-3 top-1/2 hidden h-9 w-9 -translate-y-1/2 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-900/[0.04] hover:text-slate-900">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                <path fill-rule="evenodd" d="M4.22 4.22a.75.75 0 0 1 1.06 0L10 8.94l4.72-4.72a.75.75 0 1 1 1.06 1.06L11.06 10l4.72 4.72a.75.75 0 1 1-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 1 1-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <button type="submit" class="flex h-[52px] items-center justify-center rounded-2xl bg-white/80 px-8 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-slate-900/10 transition-all hover:bg-white hover:text-slate-900">
                    Apply
                </button>
            </form>
        </div>

        <div id="faq-add-panel" class="hidden rounded-[2rem] bg-white/80 p-6 shadow-[0_24px_70px_rgba(15,23,42,0.08)] ring-1 ring-slate-900/5 backdrop-blur-xl">
            <form method="POST" action="{{ route('admin.faq-responses.store') }}" class="space-y-5">
                @csrf
                <div class="grid gap-5 lg:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Question</label>
                        <textarea name="inquiry" rows="4" required class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-6 py-4 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">{{ old('inquiry') }}</textarea>
                        @error('inquiry')
                            <div class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Response</label>
                        <textarea name="response" rows="4" required class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-6 py-4 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">{{ old('response') }}</textarea>
                        @error('response')
                            <div class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Alternative Questions</label>
                    <textarea name="aliases" rows="3" class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-6 py-4 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15" placeholder="One variation per line&#10;Who is the present Regional Director?&#10;Sino ang kasalukuyang Regional Director?">{{ old('aliases') }}</textarea>
                    <div class="mt-2 text-xs font-medium text-slate-500">Optional. Add one alternate wording per line so similar questions can return the same answer.</div>
                    @error('aliases')
                        <div class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>
                <div class="rounded-2xl bg-slate-50/90 px-5 py-4 text-xs leading-6 text-slate-500 ring-1 ring-slate-900/5">
                    The chatbot preserves line breaks in the answer and keeps web links readable in separate lines or paragraphs.
                </div>
                <div class="flex items-center justify-end gap-3">
                    <button type="button" id="faq-add-cancel" class="h-11 rounded-2xl bg-white/80 px-6 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-slate-900/10 transition hover:bg-white hover:text-slate-900">
                        Cancel
                    </button>
                    <button type="submit" class="h-11 rounded-2xl bg-blue-600 px-7 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-500">
                        Save
                    </button>
                </div>
            </form>
        </div>

        <div id="faq-results" class="overflow-hidden rounded-[2rem] bg-white/80 ring-1 ring-slate-900/5 shadow-[0_24px_70px_rgba(15,23,42,0.08)] backdrop-blur-xl">
            <div class="overflow-x-auto">
                <table class="w-full border-collapse text-left">
                    <thead>
                        <tr class="border-b border-slate-900/5 bg-white/40">
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Question</th>
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Aliases</th>
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Response</th>
                            <th class="px-8 py-5 text-right text-[10px] font-bold uppercase tracking-widest text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-900/5">
                        @forelse ($items as $item)
                            <tr class="group transition-colors hover:bg-slate-900/[0.02]">
                                <td class="px-8 py-6 align-top">
                                    <div class="line-clamp-2 text-sm font-bold text-slate-900">{{ $item->inquiry }}</div>
                                    <div class="mt-1 text-xs font-medium text-slate-500">Updated {{ $item->updated_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-8 py-6 align-top">
                                    <div class="whitespace-pre-wrap text-sm font-medium text-slate-600">{{ $item->aliases ?: 'No aliases added' }}</div>
                                </td>
                                <td class="px-8 py-6 align-top">
                                    <div class="line-clamp-4 whitespace-pre-wrap text-sm font-semibold text-slate-700">{{ $item->response }}</div>
                                </td>
                                <td class="px-8 py-6 align-top text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 transition-opacity group-hover:opacity-100">
                                        <a href="{{ route('admin.faq-responses.edit', $item) }}" class="rounded-xl bg-white/70 p-2 text-slate-600 shadow-sm transition-all hover:bg-blue-500/10 hover:text-blue-600">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.faq-responses.destroy', $item) }}" data-confirm="delete" data-confirm-title="Delete FAQ Response" data-confirm-message="Delete this FAQ response?">
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
                url.search = params.toString();
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
                debounceTimer = setTimeout(() => fetchAndRender(buildUrl()), 180);
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
                if (url.pathname !== window.location.pathname || !url.searchParams.has('page')) return;

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
