@php
    use Illuminate\Support\Str;

    $archiveTotal = $conversations->count();
    $filterLabels = [
        '' => 'All Archives',
        'recent' => 'Recent Archives',
        'older' => 'Older Archives',
    ];
@endphp

<x-admin-layout>
    <style>
        .archive-shell {
            border: 1px solid rgba(226, 232, 240, 0.8);
            background: linear-gradient(180deg, rgba(255,255,255,0.97) 0%, rgba(248,250,252,0.94) 100%);
            box-shadow: 0 15px 40px rgba(15,23,42,0.06);
            border-radius: 32px;
            padding: 30px 32px 34px;
        }

        .archive-toolbar-card,
        .archive-table-card {
            border: 1px solid #e8eef8;
            background: rgba(255,255,255,0.9);
            box-shadow: 0 10px 30px rgba(15,23,42,0.04);
            border-radius: 28px;
        }

        .archive-toolbar-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.45fr) repeat(2, minmax(180px, 0.55fr));
            gap: 16px;
            align-items: center;
        }

        .archive-table-head,
        .archive-table-row {
            display: grid;
            grid-template-columns: minmax(220px, 1.45fr) minmax(240px, 1.45fr) minmax(160px, 0.95fr) minmax(170px, 0.95fr) 120px;
            gap: 20px;
            align-items: center;
        }

        .archive-table-head {
            padding: 28px 28px 24px;
            color: #64748b;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            border-bottom: 1px solid #edf2f9;
        }

        .archive-table-row {
            position: relative;
            z-index: 0;
            padding: 26px 28px;
            transition: transform 220ms ease, background-color 220ms ease, box-shadow 220ms ease;
        }

        .archive-table-row:hover {
            background: #fbfdff;
            transform: translateY(-1px);
            box-shadow: inset 0 0 0 1px #dbe7fb;
        }

        .archive-table-row + .archive-table-row {
            border-top: 1px solid #edf2f9;
        }

        .archive-table-card.has-open-menu .archive-table-row {
            pointer-events: none;
        }

        .archive-table-card.has-open-menu .archive-actions-wrap,
        .archive-table-card.has-open-menu .archive-actions-menu {
            pointer-events: auto;
        }

        .archive-table-card.has-open-menu .archive-actions-wrap:not(.is-open) .archive-actions-trigger {
            opacity: 0;
        }

        .archive-table-row:has(.archive-actions-wrap.is-open) {
            z-index: 90;
        }

        .archive-menu-panel {
            display: none;
        }

        .archive-actions-wrap {
            position: relative;
            isolation: isolate;
        }

        .archive-actions-wrap.is-open {
            z-index: 80;
        }

        .archive-menu-panel.is-open {
            display: block;
            animation: archiveMenuFade 140ms ease;
        }

        @keyframes archiveMenuFade {
            from { opacity: 0; transform: translateY(6px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @media (max-width: 1180px) {
            .archive-toolbar-grid {
                grid-template-columns: 1fr 1fr;
            }

            .archive-toolbar-grid .archive-search-wrap {
                grid-column: 1 / -1;
            }

            .archive-table-head {
                display: none;
            }

            .archive-table-row {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }

        @media (max-width: 640px) {
            .archive-shell {
                padding: 20px 16px 24px;
                border-radius: 24px;
            }

            .archive-toolbar-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="mx-auto w-full max-w-[1520px]">
        <section class="archive-shell">
            <div class="flex flex-col gap-6">
                <div class="flex items-start gap-5">
                    <div class="flex h-[96px] w-[96px] shrink-0 items-center justify-center rounded-[28px] bg-[linear-gradient(135deg,#eef4ff_0%,#dbeafe_100%)] text-[#2563eb] shadow-[0_18px_34px_rgba(37,99,235,0.12)]">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-11 w-11">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m16.5 0H3.75m16.5 0V5.625A1.875 1.875 0 0018.375 3.75H5.625A1.875 1.875 0 003.75 5.625V7.5" />
                        </svg>
                    </div>
                    <div class="pt-2">
                        <h1 class="text-[2.15rem] font-black tracking-tight text-[#13204a] sm:text-[2.35rem]">Archived Chats</h1>
                        <p class="mt-2 text-[15px] font-medium leading-7 text-[#65779b]">Restore or permanently remove saved conversations.</p>
                    </div>
                </div>

                <div class="archive-toolbar-card p-5 sm:p-6">
                    <form id="archive-filter-form" method="GET" action="{{ route('admin.legal.ai.saved') }}">
                        <input id="archive-filter-input" type="hidden" name="filter" value="{{ $filters['filter'] ?? '' }}">

                        <div class="archive-toolbar-grid">
                            <label class="archive-search-wrap relative block">
                                <span class="pointer-events-none absolute left-5 top-1/2 -translate-y-1/2 text-[#7084ad]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.9" stroke="currentColor" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input id="archive-search" name="search" type="search" value="{{ $filters['search'] ?? '' }}" placeholder="Search archived chats..." class="h-[58px] w-full rounded-[18px] border border-[#e3eaf6] bg-white pl-14 pr-4 text-[15px] font-medium text-[#1c274b] shadow-[0_8px_24px_rgba(15,23,42,0.04)] outline-none transition placeholder:text-[#8193b6] focus:border-[#b9d0fb] focus:ring-4 focus:ring-[#e8f1ff]" />
                            </label>

                            <div class="relative">
                                <button type="button" id="archive-filter-trigger" class="inline-flex h-[58px] w-full items-center justify-between rounded-[18px] border border-[#e3eaf6] bg-white px-6 text-[15px] font-bold text-[#1f2b4e] shadow-[0_8px_24px_rgba(15,23,42,0.04)] transition hover:border-[#cfdbf4]">
                                    <span id="archive-filter-label">{{ $filterLabels[$filters['filter'] ?? ''] ?? 'All Archives' }}</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5 text-[#253961]">
                                        <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                                <div id="archive-filter-menu" class="archive-toolbar-menu absolute left-0 top-full z-30 mt-3 hidden min-w-full overflow-hidden rounded-[20px] border border-white/70 bg-white/95 p-2 shadow-[0_22px_48px_rgba(15,23,42,0.12)] ring-1 ring-slate-900/6 backdrop-blur-xl">
                                    <button type="button" data-filter="" class="flex w-full rounded-xl px-3 py-2.5 text-left text-[13px] font-semibold text-[#32466e] transition hover:bg-[#f3f7ff]">All Archives</button>
                                    <button type="button" data-filter="recent" class="flex w-full rounded-xl px-3 py-2.5 text-left text-[13px] font-semibold text-[#32466e] transition hover:bg-[#f3f7ff]">Recent Archives</button>
                                    <button type="button" data-filter="older" class="flex w-full rounded-xl px-3 py-2.5 text-left text-[13px] font-semibold text-[#32466e] transition hover:bg-[#f3f7ff]">Older Archives</button>
                                </div>
                            </div>

                            <div id="archive-count-badge" class="inline-flex h-[58px] items-center gap-3 rounded-[18px] bg-[#eef4ff] px-5 text-[14px] font-bold text-[#2563eb] ring-1 ring-[#d9e7ff]">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.9" stroke="currentColor" class="h-5 w-5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m16.5 0H3.75m16.5 0V5.625A1.875 1.875 0 0018.375 3.75H5.625A1.875 1.875 0 003.75 5.625V7.5" />
                                </svg>
                                <span>{{ $archiveTotal }} Archived Chats</span>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="archive-table-card overflow-hidden">
                    @if ($conversations->isEmpty())
                        <div class="px-8 py-14 text-center">
                            <div class="text-[11px] font-black uppercase tracking-[0.24em] text-[#73829f]">No archived chats yet</div>
                            <p class="mt-3 text-[14px] font-medium text-[#7485a7]">Saved conversations will appear here once you archive them.</p>
                        </div>
                    @else
                        <div class="archive-table-head">
                            <div>Conversation</div>
                            <div>Preview</div>
                            <div>Date Saved</div>
                            <div>Status</div>
                            <div style="text-align:right;">Actions</div>
                        </div>

                        <div id="archive-list">
                            @foreach ($conversations as $conversation)
                                @php
                                    $preview = $conversation->title
                                        ? 'This is a saved conversation preview that shows the first few lines from the archived thread.'
                                        : 'This archived conversation is available for review, restoration, or permanent deletion.';
                                @endphp
                                <article class="archive-table-row archive-row" data-title="{{ Str::lower($conversation->title ?: 'untitled thread') }}">
                                    <div class="min-w-0">
                                        <div class="truncate text-[1rem] font-black tracking-tight text-[#17234b]">
                                            {{ $conversation->title ?: 'Untitled Thread' }}
                                        </div>
                                    </div>

                                    <div class="text-[13px] font-medium leading-6 text-[#6d7f9f]">
                                        {{ Str::limit($preview, 72) }}
                                    </div>

                                    <div class="flex items-start gap-3 text-[#5b6b8f]">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.9" stroke="currentColor" class="mt-0.5 h-5 w-5 shrink-0">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M4.5 6h15A1.5 1.5 0 0121 7.5v11.25A1.5 1.5 0 0119.5 20.25h-15A1.5 1.5 0 013 18.75V7.5A1.5 1.5 0 014.5 6z" />
                                        </svg>
                                        <div>
                                            <div class="text-[14px] font-bold text-[#24365f]">{{ $conversation->created_at?->format('M d, Y') }}</div>
                                            <div class="mt-1 text-[13px] font-medium text-[#7f8fad]">{{ $conversation->created_at?->format('h:i A') }}</div>
                                        </div>
                                    </div>

                                    <div>
                                        <span class="inline-flex items-center gap-2 rounded-full bg-[#eaf8ef] px-4 py-2 text-[13px] font-bold text-[#14a44d]">
                                            <span class="h-2.5 w-2.5 rounded-full bg-[#22c55e]"></span>
                                            Archived
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-end">
                                        <div class="archive-actions-wrap">
                                            <button type="button" class="archive-actions-trigger flex h-12 w-12 items-center justify-center rounded-[16px] border border-[#e1e8f5] bg-white text-[#4f648d] shadow-[0_10px_24px_rgba(15,23,42,0.06)] transition hover:-translate-y-0.5 hover:border-[#cad9f4] hover:text-[#2563eb]">
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                                    <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 14a1.5 1.5 0 110 3 1.5 1.5 0 010-3z" />
                                                </svg>
                                            </button>

                                            <div class="archive-actions-menu archive-menu-panel absolute right-0 top-full z-50 mt-3 w-56 overflow-hidden rounded-[20px] border border-white/70 bg-white/95 p-2 shadow-[0_22px_52px_rgba(15,23,42,0.16)] ring-1 ring-slate-900/8 backdrop-blur-xl">
                                                <form method="POST" action="{{ route('conversations.toggle-save', $conversation) }}" class="m-0">
                                                    @csrf
                                                    <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3.5 py-3 text-left text-[13px] font-semibold text-[#2563eb] transition hover:bg-[#eef4ff]">
                                                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#ecf3ff] text-[#2563eb]">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.9" stroke="currentColor" class="h-4.5 w-4.5">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75V6.75A2.25 2.25 0 0014.25 4.5h-7.5A2.25 2.25 0 004.5 6.75v10.5A2.25 2.25 0 006.75 19.5h7.5a2.25 2.25 0 002.25-2.25v-6m0 0L19.5 15m-3-3 3-3m-3 3H9" />
                                                            </svg>
                                                        </span>
                                                        <span>Restore Chat</span>
                                                    </button>
                                                </form>

                                                <a href="{{ route('admin.legal.ai.show', $conversation) }}" class="flex w-full items-center gap-3 rounded-xl px-3.5 py-3 text-left text-[13px] font-semibold text-[#5d6b85] transition hover:bg-[#f5f7fb]">
                                                    <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#f1f5f9] text-[#64748b]">
                                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.9" stroke="currentColor" class="h-4.5 w-4.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12s3.75-6.75 9.75-6.75S21.75 12 21.75 12 18 18.75 12 18.75 2.25 12 2.25 12z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 14.25A2.25 2.25 0 1012 9.75a2.25 2.25 0 000 4.5z" />
                                                        </svg>
                                                    </span>
                                                    <span>View Details</span>
                                                </a>

                                                <div class="my-1 h-px bg-slate-900/8"></div>

                                                <form method="POST" action="{{ route('conversations.destroy', $conversation) }}" class="m-0" data-confirm="delete" data-confirm-title="Delete conversations?" data-confirm-message="This will permanently delete 1 conversation(s). This action cannot be undone." data-confirm-ok="Delete" data-confirm-cancel="Cancel">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="flex w-full items-center gap-3 rounded-xl px-3.5 py-3 text-left text-[13px] font-semibold text-[#ef4444] transition hover:bg-[#fff1f2]">
                                                        <span class="flex h-9 w-9 items-center justify-center rounded-full bg-[#fff1f2] text-[#ef4444]">
                                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.9" stroke="currentColor" class="h-4.5 w-4.5">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-1.806A2.25 2.25 0 0013.813 1.5h-3.626a2.25 2.25 0 00-2.25 2.25V3m7.5 0H9" />
                                                            </svg>
                                                        </span>
                                                        <span>Delete Permanently</span>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        <div id="archive-empty-filtered" class="hidden px-8 py-14 text-center">
                            <div class="text-[11px] font-black uppercase tracking-[0.24em] text-[#73829f]">No matching archives</div>
                            <p class="mt-3 text-[14px] font-medium text-[#7485a7]">Try another search term or filter option.</p>
                        </div>

                        <div class="flex flex-col gap-4 border-t border-[#edf2f9] px-7 py-8 sm:flex-row sm:items-center sm:justify-between">
                            <div id="archive-results-text" class="text-[14px] font-medium text-[#334261]">
                                Showing 1 to {{ $archiveTotal }} of {{ $archiveTotal }} archives
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>

    <script type="module">
        const filterForm = document.getElementById('archive-filter-form');
        const archiveTableCard = document.querySelector('.archive-table-card');
        const searchInput = document.getElementById('archive-search');
        const filterTrigger = document.getElementById('archive-filter-trigger');
        const filterMenu = document.getElementById('archive-filter-menu');
        const filterInput = document.getElementById('archive-filter-input');
        const filterLabel = document.getElementById('archive-filter-label');
        let searchDebounce = null;

        const syncArchiveMenuState = () => {
            const hasOpenMenu = Boolean(document.querySelector('.archive-actions-wrap.is-open'));
            archiveTableCard?.classList.toggle('has-open-menu', hasOpenMenu);
        };

        const closeArchiveMenus = () => {
            document.querySelectorAll('.archive-actions-menu').forEach((el) => el.classList.remove('is-open'));
            document.querySelectorAll('.archive-actions-wrap').forEach((el) => el.classList.remove('is-open'));
            filterMenu?.classList.add('hidden');
            syncArchiveMenuState();
        };

        searchInput?.addEventListener('input', (event) => {
            const target = event.target;
            if (!(target instanceof HTMLInputElement)) return;
            window.clearTimeout(searchDebounce);
            searchDebounce = window.setTimeout(() => {
                filterForm?.requestSubmit();
            }, 350);
        });

        filterTrigger?.addEventListener('click', (event) => {
            event.preventDefault();
            const hidden = filterMenu?.classList.contains('hidden');
            closeArchiveMenus();
            if (hidden) filterMenu?.classList.remove('hidden');
        });

        filterMenu?.querySelectorAll('[data-filter]').forEach((button) => {
            button.addEventListener('click', () => {
                const value = String(button.getAttribute('data-filter') || '');
                if (filterInput) filterInput.value = value;
                if (filterLabel) filterLabel.textContent = button.textContent?.trim() || 'All Archives';
                filterMenu?.classList.add('hidden');
                filterForm?.requestSubmit();
            });
        });

        document.querySelectorAll('.archive-actions-trigger').forEach((button) => {
            const menu = button.parentElement?.querySelector('.archive-actions-menu');
            const wrap = button.closest('.archive-actions-wrap');

            if (!menu) return;

            button.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();

                const open = menu.classList.contains('is-open');

                document.querySelectorAll('.archive-actions-menu').forEach((element) => element.classList.remove('is-open'));
                document.querySelectorAll('.archive-actions-wrap').forEach((element) => element.classList.remove('is-open'));
                filterMenu?.classList.add('hidden');

                if (!open) {
                    menu.classList.add('is-open');
                    wrap?.classList.add('is-open');
                }

                syncArchiveMenuState();
            });
        });

        document.addEventListener('click', (event) => {
            const target = event.target;
            if (!(target instanceof Element)) return;
            if (target.closest('.archive-actions-trigger') || target.closest('.archive-actions-menu') || target.closest('#archive-filter-trigger') || target.closest('#archive-filter-menu')) return;
            closeArchiveMenus();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') closeArchiveMenus();
        });
    </script>
</x-admin-layout>
