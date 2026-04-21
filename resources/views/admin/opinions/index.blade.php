<x-admin-layout>
    <div class="space-y-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Gallery Library</h2>
                <p class="mt-2 text-slate-600">Manage legal opinions used as the chatbot knowledge base.</p>
            </div>
            <a href="{{ route('admin.opinions.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-500 transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="mr-2 h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Legal Opinion
            </a>
        </div>

        <div class="rounded-[2rem] bg-white/80 backdrop-blur-xl p-6 ring-1 ring-slate-900/5 shadow-[0_24px_70px_rgba(15,23,42,0.08)]">
            <form id="opinions-filter-form" action="{{ route('admin.opinions.index') }}" method="GET" data-loader-skip class="flex flex-col gap-4 md:flex-row md:items-end">
                <div class="flex-1">
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Search</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute left-5 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 104.473 8.708l2.41 2.409a.75.75 0 101.06-1.06l-2.409-2.41A5.5 5.5 0 009 3.5zM4.5 9a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input id="opinions-search" type="search" name="q" value="{{ $q }}" autocomplete="off" spellcheck="false" placeholder="Search by title, opinion number, year, or context..."
                            class="w-full rounded-2xl bg-white/80 border-slate-900/10 py-3.5 pl-14 pr-12 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all">
                        <button id="opinions-search-clear" type="button" aria-label="Clear search" class="absolute right-3 top-1/2 hidden h-9 w-9 -translate-y-1/2 items-center justify-center rounded-xl text-slate-500 transition hover:bg-slate-900/[0.04] hover:text-slate-900">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                <path fill-rule="evenodd" d="M4.22 4.22a.75.75 0 011.06 0L10 8.94l4.72-4.72a.75.75 0 111.06 1.06L11.06 10l4.72 4.72a.75.75 0 11-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 11-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Year</label>
                    <div>
                        <select id="opinions-year" name="year"
                                class="h-11 rounded-2xl bg-white/80 ring-1 ring-slate-900/10 px-4 text-xs font-semibold text-slate-700 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all">
                            @php
                                $selectedYear = ($year ?? 'all');
                                $current = (int) date('Y');
                            @endphp
                            <option value="" {{ $selectedYear === 'all' || $selectedYear === '' ? 'selected' : '' }}>All</option>
                            @for ($y = 2000; $y <= $current; $y++)
                                <option value="{{ $y }}" {{ (string)$selectedYear === (string)$y ? 'selected' : '' }}>{{ $y }}</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <button type="submit" class="h-[52px] px-8 flex items-center justify-center rounded-2xl bg-white/80 text-slate-700 ring-1 ring-slate-900/10 hover:bg-white hover:text-slate-900 transition-all text-sm font-bold shadow-sm">
                    Apply
                </button>
            </form>
        </div>

        <div id="opinions-results" class="overflow-hidden rounded-[2rem] bg-white/80 backdrop-blur-xl ring-1 ring-slate-900/5 shadow-[0_24px_70px_rgba(15,23,42,0.08)]">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-slate-900/5 bg-white/40">
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Title</th>
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Opinion Number</th>
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Date</th>
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-900/5">
                        @forelse ($opinions as $opinion)
                            <tr class="group hover:bg-slate-900/[0.02] transition-colors">
                                <td class="px-8 py-6">
                                    <div class="text-sm font-bold text-slate-900">{{ $opinion->title }}</div>
                                    <div class="mt-1 text-xs font-medium text-slate-500">Updated {{ $opinion->updated_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-sm font-semibold text-slate-700">{{ $opinion->opinion_number }}</div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-sm font-semibold text-slate-700">{{ optional($opinion->date)->format('m/d/Y') }}</div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('admin.opinions.show', $opinion) }}" class="p-2 rounded-xl bg-white/70 text-slate-600 hover:text-slate-900 hover:bg-white transition-all shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.opinions.edit', $opinion) }}" class="p-2 rounded-xl bg-white/70 text-slate-600 hover:text-blue-600 hover:bg-blue-500/10 transition-all shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.opinions.destroy', $opinion) }}" data-confirm="delete" data-confirm-title="Delete Legal Opinion" data-confirm-message="Delete this legal opinion?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl bg-rose-500/5 text-rose-400 ring-1 ring-rose-500/20 hover:bg-rose-500 hover:text-white transition-all">
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
                                <td class="px-8 py-10 text-center text-slate-500" colspan="4">No legal opinions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($opinions->hasPages())
                <div class="border-t border-slate-900/5 bg-white/40 px-8 py-5">
                    {{ $opinions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
<style>
select[name="year"] option { color: #0f172a; background-color: #ffffff; }
#opinions-search::-webkit-search-cancel-button,
#opinions-search::-webkit-search-decoration {
    -webkit-appearance: none;
    appearance: none;
}
</style>
<script type="module">
    const form = document.getElementById('opinions-filter-form');
    const searchInput = document.getElementById('opinions-search');
    const clearBtn = document.getElementById('opinions-search-clear');
    const yearSelect = document.getElementById('opinions-year');
    const resultsEl = document.getElementById('opinions-results');
    const tbody = resultsEl?.querySelector('tbody');

    if (form && searchInput && yearSelect && resultsEl) {
        let debounceTimer = null;
        let activeController = null;

        const setSearching = (isSearching) => {
            if (!tbody) return;
            tbody.classList.toggle('opacity-50', isSearching);
            tbody.style.pointerEvents = isSearching ? 'none' : 'auto';
        };

        const setClearVisible = (visible) => {
            if (!clearBtn) return;
            clearBtn.classList.toggle('hidden', !visible);
            clearBtn.classList.toggle('flex', visible);
        };

        const buildUrl = () => {
            const url = new URL(form.action, window.location.origin);
            url.searchParams.set('q', searchInput.value || '');
            url.searchParams.set('year', yearSelect.value || '');
            return url.toString();
        };

        const replaceResultsFromHtml = (html) => {
            const doc = new DOMParser().parseFromString(html, 'text/html');
            const next = doc.getElementById('opinions-results');
            if (!next) return;
            resultsEl.innerHTML = next.innerHTML;
        };

        const fetchAndRender = async (url) => {
            if (activeController) activeController.abort();
            activeController = new AbortController();

            setSearching(true);
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
            } catch (err) {
                if (err.name !== 'AbortError') {
                    console.error('Search error:', err);
                }
            } finally {
                setSearching(false);
            }
        };

        const scheduleFetch = () => {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                fetchAndRender(buildUrl());
            }, 250);
        };

        form.addEventListener('submit', (e) => {
            e.preventDefault();
            fetchAndRender(buildUrl());
        });

        searchInput.addEventListener('input', () => {
            setClearVisible((searchInput.value || '').trim() !== '');
            scheduleFetch();
        });

        yearSelect.addEventListener('change', () => {
            fetchAndRender(buildUrl());
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

        resultsEl.addEventListener('click', (e) => {
            const link = e.target?.closest?.('a');
            if (!link || !link.href) return;

            const url = new URL(link.href, window.location.origin);
            if (url.pathname !== window.location.pathname) return;
            if (!url.searchParams.has('page')) return;

            e.preventDefault();
            fetchAndRender(url.toString());
        });
    }
</script>
