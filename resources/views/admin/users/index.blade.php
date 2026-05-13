@php
    use Illuminate\Support\Str;

    $avatarPalettes = [
        ['bg' => 'linear-gradient(135deg,#e7efff 0%,#dbeafe 100%)', 'color' => '#2563eb'],
        ['bg' => 'linear-gradient(135deg,#f3e8ff 0%,#e9d5ff 100%)', 'color' => '#7c3aed'],
        ['bg' => 'linear-gradient(135deg,#ecfdf5 0%,#d1fae5 100%)', 'color' => '#059669'],
        ['bg' => 'linear-gradient(135deg,#fff7ed 0%,#fed7aa 100%)', 'color' => '#ea580c'],
        ['bg' => 'linear-gradient(135deg,#ecfeff 0%,#cffafe 100%)', 'color' => '#0891b2'],
    ];
@endphp

<x-admin-layout>
    <style>
        .user-delete-modal[hidden] {
            display: none !important;
        }

        .users-shell {
            border: 1px solid rgba(226, 232, 240, 0.8);
            background: linear-gradient(180deg, rgba(255,255,255,0.97) 0%, rgba(248,250,252,0.94) 100%);
            box-shadow: 0 15px 40px rgba(15,23,42,0.06);
            border-radius: 32px;
            padding: 30px 32px 34px;
        }

        .users-toolbar-card,
        .users-table-card,
        .users-flash-card {
            border: 1px solid #e8eef8;
            background: rgba(255,255,255,0.9);
            box-shadow: 0 10px 30px rgba(15,23,42,0.04);
            border-radius: 28px;
        }

        .users-toolbar-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.6fr) repeat(3, minmax(190px, 0.72fr));
            gap: 16px;
            align-items: center;
        }

        .users-table-wrap {
            overflow-x: auto;
        }

        .users-table {
            min-width: 1220px;
        }

        .users-table-head,
        .users-table-row {
            display: grid;
            grid-template-columns: minmax(210px, 1.4fr) minmax(220px, 1.2fr) minmax(150px, 0.95fr) minmax(110px, 0.8fr) minmax(130px, 0.9fr) minmax(180px, 1.1fr) minmax(170px, 1fr) 168px;
            gap: 16px;
            align-items: center;
        }

        .users-table-head {
            padding: 28px 28px 24px;
            border-bottom: 1px solid #edf2f9;
            color: #64748b;
            font-size: 11px;
            font-weight: 900;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .users-table-row {
            padding: 28px;
            transition: transform 220ms ease, background-color 220ms ease, box-shadow 220ms ease;
        }

        .users-table-row:hover {
            background: #fbfdff;
            transform: translateY(-1px);
            box-shadow: inset 0 0 0 1px #dbe7fb;
        }

        .users-table-row + .users-table-row {
            border-top: 1px solid #edf2f9;
        }

        .users-action-btn {
            position: relative;
            width: 44px;
            height: 44px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            box-shadow: 0 10px 24px rgba(15,23,42,0.06);
            transition: transform 180ms ease, box-shadow 180ms ease, background-color 180ms ease, border-color 180ms ease, color 180ms ease;
        }

        .users-action-btn:hover {
            transform: translateY(-1px);
        }

        .users-action-btn[data-tooltip]::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 50%;
            bottom: calc(100% + 10px);
            transform: translateX(-50%) translateY(4px);
            background: rgba(15, 23, 42, 0.94);
            color: #fff;
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 0.02em;
            white-space: nowrap;
            padding: 7px 10px;
            border-radius: 10px;
            opacity: 0;
            visibility: hidden;
            pointer-events: none;
            transition: opacity 160ms ease, transform 160ms ease, visibility 160ms ease;
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.18);
            z-index: 30;
        }

        .users-action-btn:hover::after,
        .users-action-btn:focus-visible::after {
            opacity: 1;
            visibility: visible;
            transform: translateX(-50%) translateY(0);
        }

        @media (max-width: 1180px) {
            .users-toolbar-grid {
                grid-template-columns: 1fr 1fr;
            }

            .users-toolbar-grid .users-search-wrap {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 640px) {
            .users-shell {
                padding: 20px 16px 24px;
                border-radius: 24px;
            }

            .users-toolbar-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="mx-auto max-w-[1520px]">
        <section class="users-shell space-y-6">
            <div class="flex items-start gap-5">
                <div class="flex h-[96px] w-[96px] shrink-0 items-center justify-center rounded-[28px] bg-[linear-gradient(135deg,#eef4ff_0%,#dbeafe_100%)] text-[#2563eb] shadow-[0_18px_34px_rgba(37,99,235,0.12)]">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="h-11 w-11">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a8.965 8.965 0 003.75-7.47A8.25 8.25 0 0012 3a8.25 8.25 0 00-8.25 8.25A8.965 8.965 0 007.5 18.72M15 19.5a3 3 0 11-6 0m6 0a3 3 0 00-6 0m6 0h1.5m-7.5 0H7.5" />
                    </svg>
                </div>
                <div class="pt-2">
                    <h1 class="text-[2.15rem] font-black tracking-tight text-[#13204a] sm:text-[2.35rem]">User Management</h1>
                    <p class="mt-2 max-w-4xl text-[15px] font-medium leading-7 text-[#65779b]">Review verification, update roles, manage account status, and safely control user access.</p>
                </div>
            </div>

            @if (session('status'))
                <div class="users-flash-card border-emerald-200 bg-emerald-50/90 px-6 py-4 text-sm font-semibold text-emerald-700">
                    {{ session('status') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="users-flash-card border-rose-200 bg-rose-50/90 px-6 py-4 text-sm text-rose-700">
                    <ul class="list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @php
                $roleLabels = [
                    '' => 'All Roles',
                    'admin' => 'Admin',
                    'user' => 'User',
                ];

                $statusLabels = [
                    '' => 'All Status',
                    'active' => 'Active',
                    'inactive' => 'Inactive',
                ];

                $verificationLabels = [
                    '' => 'All Verification',
                    'verified' => 'Verified',
                    'not_verified' => 'Not Verified',
                ];
            @endphp

            <div class="users-toolbar-card p-5 sm:p-6">
                <form id="users-filter-form" method="GET" action="{{ route('admin.users.index') }}">
                    <input id="users-role-input" type="hidden" name="role" value="{{ $filters['role'] }}">
                    <input id="users-status-input" type="hidden" name="status" value="{{ $filters['status'] }}">
                    <input id="users-verification-input" type="hidden" name="verification" value="{{ $filters['verification'] }}">

                <div class="users-toolbar-grid">
                    <label class="users-search-wrap relative block">
                        <span class="pointer-events-none absolute left-5 top-1/2 -translate-y-1/2 text-[#7084ad]">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.9" stroke="currentColor" class="h-5 w-5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35m1.85-5.15a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </span>
                        <input id="users-search" name="search" type="search" value="{{ $filters['search'] }}" placeholder="Search users..." class="h-[58px] w-full rounded-full border border-[#e3eaf6] bg-white pl-14 pr-4 text-[15px] font-medium text-[#1c274b] shadow-[0_8px_24px_rgba(15,23,42,0.04)] outline-none transition placeholder:text-[#8193b6] focus:border-[#b9d0fb] focus:ring-4 focus:ring-[#e8f1ff]" />
                    </label>

                    <div class="relative">
                        <button type="button" id="users-role-trigger" class="inline-flex h-[58px] w-full items-center justify-between rounded-[18px] border border-[#e3eaf6] bg-white px-6 text-[15px] font-bold text-[#1f2b4e] shadow-[0_8px_24px_rgba(15,23,42,0.04)] transition hover:border-[#cfdbf4]">
                            <span id="users-role-label">{{ $roleLabels[$filters['role']] ?? 'All Roles' }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5 text-[#253961]">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div id="users-role-menu" class="absolute left-0 top-full z-30 mt-3 hidden min-w-full overflow-hidden rounded-[20px] border border-white/70 bg-white/95 p-2 shadow-[0_22px_48px_rgba(15,23,42,0.12)] ring-1 ring-slate-900/6 backdrop-blur-xl">
                            <button type="button" data-role-filter="" class="flex w-full rounded-xl px-3 py-2.5 text-left text-[13px] font-semibold text-[#32466e] transition hover:bg-[#f3f7ff]">All Roles</button>
                            <button type="button" data-role-filter="admin" class="flex w-full rounded-xl px-3 py-2.5 text-left text-[13px] font-semibold text-[#32466e] transition hover:bg-[#f3f7ff]">Admin</button>
                            <button type="button" data-role-filter="user" class="flex w-full rounded-xl px-3 py-2.5 text-left text-[13px] font-semibold text-[#32466e] transition hover:bg-[#f3f7ff]">User</button>
                        </div>
                    </div>

                    <div class="relative">
                        <button type="button" id="users-status-trigger" class="inline-flex h-[58px] w-full items-center justify-between rounded-[18px] border border-[#e3eaf6] bg-white px-6 text-[15px] font-bold text-[#1f2b4e] shadow-[0_8px_24px_rgba(15,23,42,0.04)] transition hover:border-[#cfdbf4]">
                            <span id="users-status-label">{{ $statusLabels[$filters['status']] ?? 'All Status' }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5 text-[#253961]">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div id="users-status-menu" class="absolute left-0 top-full z-30 mt-3 hidden min-w-full overflow-hidden rounded-[20px] border border-white/70 bg-white/95 p-2 shadow-[0_22px_48px_rgba(15,23,42,0.12)] ring-1 ring-slate-900/6 backdrop-blur-xl">
                            <button type="button" data-status-filter="" class="flex w-full rounded-xl px-3 py-2.5 text-left text-[13px] font-semibold text-[#32466e] transition hover:bg-[#f3f7ff]">All Status</button>
                            <button type="button" data-status-filter="active" class="flex w-full rounded-xl px-3 py-2.5 text-left text-[13px] font-semibold text-[#32466e] transition hover:bg-[#f3f7ff]">Active</button>
                            <button type="button" data-status-filter="inactive" class="flex w-full rounded-xl px-3 py-2.5 text-left text-[13px] font-semibold text-[#32466e] transition hover:bg-[#f3f7ff]">Inactive</button>
                        </div>
                    </div>

                    <div class="relative">
                        <button type="button" id="users-verification-trigger" class="inline-flex h-[58px] w-full items-center justify-between rounded-[18px] border border-[#e3eaf6] bg-white px-6 text-[15px] font-bold text-[#1f2b4e] shadow-[0_8px_24px_rgba(15,23,42,0.04)] transition hover:border-[#cfdbf4]">
                            <span id="users-verification-label">{{ $verificationLabels[$filters['verification']] ?? 'All Verification' }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5 text-[#253961]">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                        <div id="users-verification-menu" class="absolute left-0 top-full z-30 mt-3 hidden min-w-full overflow-hidden rounded-[20px] border border-white/70 bg-white/95 p-2 shadow-[0_22px_48px_rgba(15,23,42,0.12)] ring-1 ring-slate-900/6 backdrop-blur-xl">
                            <button type="button" data-verification-filter="" class="flex w-full rounded-xl px-3 py-2.5 text-left text-[13px] font-semibold text-[#32466e] transition hover:bg-[#f3f7ff]">All Verification</button>
                            <button type="button" data-verification-filter="verified" class="flex w-full rounded-xl px-3 py-2.5 text-left text-[13px] font-semibold text-[#32466e] transition hover:bg-[#f3f7ff]">Verified</button>
                            <button type="button" data-verification-filter="not_verified" class="flex w-full rounded-xl px-3 py-2.5 text-left text-[13px] font-semibold text-[#32466e] transition hover:bg-[#f3f7ff]">Not Verified</button>
                        </div>
                    </div>
                </div>
                </form>
            </div>

            <div class="users-table-card overflow-hidden">
                <div class="users-table-wrap">
                    <div class="users-table">
                        <div class="users-table-head">
                            <div class="flex items-center gap-2">Full Name <span class="text-[#94a3b8]">↕</span></div>
                            <div>Email</div>
                            <div>Birthday</div>
                            <div>Role</div>
                            <div>Status</div>
                            <div>Email Verification Status</div>
                            <div class="flex items-center gap-2">Date Registered <span class="text-[#94a3b8]">↕</span></div>
                            <div style="text-align:right;">Actions</div>
                        </div>

                        <div id="users-table-body">
                            @forelse ($users as $user)
                                @php
                                    $avatar = $avatarPalettes[$loop->index % count($avatarPalettes)];
                                    $initials = Str::upper(Str::substr($user->first_name ?: $user->full_name ?: 'U', 0, 1) . Str::substr($user->last_name ?: Str::after($user->full_name ?: 'U', ' '), 0, 1));
                                    $role = Str::lower($user->role);
                                    $status = Str::lower($user->status);
                                    $verification = $user->email_verified_at ? 'verified' : 'not-verified';
                                @endphp
                                <div
                                    class="users-table-row user-row"
                                    data-name="{{ Str::lower($user->full_name) }}"
                                    data-email="{{ Str::lower($user->email) }}"
                                    data-role="{{ $role }}"
                                    data-status="{{ $status }}"
                                    data-verification="{{ $verification }}"
                                >
                                    <div class="flex min-w-0 items-center gap-4">
                                        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full text-[1.4rem] font-black shadow-[0_14px_28px_rgba(59,130,246,0.10)]" style="background: {{ $avatar['bg'] }}; color: {{ $avatar['color'] }};">
                                            {{ $initials }}
                                        </div>
                                        <div class="min-w-0">
                                            <div class="truncate text-[1rem] font-black tracking-tight text-[#17234b]">{{ $user->full_name }}</div>
                                            <div class="mt-1 truncate text-[14px] font-medium text-[#6b7c9f] xl:hidden">{{ $user->email }}</div>
                                        </div>
                                    </div>

                                    <div class="truncate text-[14px] font-semibold text-[#334261]">{{ $user->email }}</div>

                                    <div class="flex items-center gap-3 text-[#5b6b8f]">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.9" stroke="currentColor" class="h-5 w-5 shrink-0">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M4.5 6h15A1.5 1.5 0 0121 7.5v11.25A1.5 1.5 0 0119.5 20.25h-15A1.5 1.5 0 013 18.75V7.5A1.5 1.5 0 014.5 6z" />
                                        </svg>
                                        <span class="text-[14px] font-semibold text-[#334261]">{{ $user->birthday?->format('M d, Y') ?? 'N/A' }}</span>
                                    </div>

                                    <div>
                                        <span @class([
                                            'inline-flex rounded-full px-4 py-2 text-[12px] font-black uppercase tracking-[0.14em]',
                                            'bg-[linear-gradient(135deg,#e9f1ff_0%,#dbeafe_100%)] text-[#2563eb]' => $role === 'user',
                                            'bg-[linear-gradient(135deg,#eef2ff_0%,#ddd6fe_100%)] text-[#5b3df5]' => $role === 'admin',
                                            'bg-slate-100 text-slate-700' => ! in_array($role, ['admin', 'user'], true),
                                        ])>
                                            {{ Str::upper($user->role) }}
                                        </span>
                                    </div>

                                    <div>
                                        <span @class([
                                            'inline-flex items-center gap-2 rounded-full px-4 py-2 text-[12px] font-black uppercase tracking-[0.14em]',
                                            'bg-[#eaf8ef] text-[#14a44d]' => $status === 'active',
                                            'bg-[#fff1f2] text-[#e11d48]' => $status === 'inactive',
                                        ])>
                                            <span @class([
                                                'h-2.5 w-2.5 rounded-full',
                                                'bg-[#22c55e]' => $status === 'active',
                                                'bg-[#ef4444]' => $status === 'inactive',
                                            ])></span>
                                            {{ Str::upper($user->status) }}
                                        </span>
                                    </div>

                                    <div>
                                        <span @class([
                                            'inline-flex items-center gap-2 rounded-full px-4 py-2 text-[12px] font-black uppercase tracking-[0.14em]',
                                            'bg-[#eaf8ef] text-[#14a44d]' => $verification === 'verified',
                                            'bg-[#fff1f2] text-[#e11d48]' => $verification === 'not-verified',
                                        ])>
                                            @if ($verification === 'verified')
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                                    <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.2 7.2a1 1 0 01-1.415 0l-3-3a1 1 0 111.415-1.42l2.292 2.294 6.492-6.494a1 1 0 011.416 0z" clip-rule="evenodd" />
                                                </svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-4 w-4">
                                                    <path fill-rule="evenodd" d="M18 10A8 8 0 112 10a8 8 0 0116 0zM9 7a1 1 0 112 0v3a1 1 0 11-2 0V7zm1 7a1.25 1.25 0 100-2.5A1.25 1.25 0 0010 14z" clip-rule="evenodd" />
                                                </svg>
                                            @endif
                                            {{ $verification === 'verified' ? 'Verified' : 'Not Verified' }}
                                        </span>
                                    </div>

                                    <div class="flex items-start gap-3 text-[#5b6b8f]">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.9" stroke="currentColor" class="mt-0.5 h-5 w-5 shrink-0">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5M4.5 6h15A1.5 1.5 0 0121 7.5v11.25A1.5 1.5 0 0119.5 20.25h-15A1.5 1.5 0 013 18.75V7.5A1.5 1.5 0 014.5 6z" />
                                        </svg>
                                        <div>
                                            <div class="text-[14px] font-bold text-[#24365f]">{{ $user->created_at?->format('M d, Y') }}</div>
                                            <div class="mt-1 text-[13px] font-medium text-[#7f8fad]">{{ $user->created_at?->format('h:i A') }}</div>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="users-action-btn border border-[#cfe0ff] text-[#2563eb] hover:border-[#a9c6ff] hover:bg-[#f4f8ff] hover:shadow-[0_14px_26px_rgba(37,99,235,0.12)]" aria-label="Edit user" title="Edit" data-tooltip="Edit">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20" class="h-5 w-5">
                                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-8.9 8.9a2 2 0 01-.878.51l-2.54.726a.75.75 0 01-.927-.927l.726-2.54a2 2 0 01.51-.878l8.9-8.9z" />
                                            </svg>
                                        </a>

                                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button
                                                type="submit"
                                                @disabled(auth()->id() === $user->id)
                                                class="users-action-btn border border-[#ffd9b8] text-[#f59e0b] hover:border-[#ffc78d] hover:bg-[#fff8ef] hover:shadow-[0_14px_26px_rgba(245,158,11,0.12)] disabled:cursor-not-allowed disabled:opacity-50"
                                                aria-label="{{ $user->status === 'active' ? 'Deactivate user' : 'Activate user' }}"
                                                title="{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}"
                                                data-tooltip="{{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.1" stroke="currentColor" class="h-5 w-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v9m6.364-5.364a9 9 0 11-12.728 0" />
                                                </svg>
                                            </button>
                                        </form>

                                        <button
                                            type="button"
                                            data-delete-trigger
                                            data-delete-action="{{ route('admin.users.destroy', $user) }}"
                                            data-delete-name="{{ $user->full_name }}"
                                            @disabled(auth()->id() === $user->id)
                                            class="users-action-btn border border-[#fecdd3] text-[#ef4444] hover:border-[#fda4af] hover:bg-[#fff1f2] hover:shadow-[0_14px_26px_rgba(239,68,68,0.12)] disabled:cursor-not-allowed disabled:opacity-50"
                                            aria-label="Delete user"
                                            title="Delete"
                                            data-tooltip="Delete"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-1.806A2.25 2.25 0 0013.813 1.5h-3.626a2.25 2.25 0 00-2.25 2.25V3m7.5 0H9" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="px-8 py-14 text-center">
                                    <div class="text-[11px] font-black uppercase tracking-[0.24em] text-[#73829f]">No users found</div>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div id="users-empty-filtered" class="hidden rounded-[1.5rem] border border-dashed border-[#d9e5f8] bg-[#fbfdff] px-8 py-12 text-center">
                    <div class="text-[11px] font-black uppercase tracking-[0.24em] text-[#73829f]">No matching users</div>
                    <p class="mt-3 text-[14px] font-medium text-[#7485a7]">Try another search term or filter option.</p>
                </div>

                <div class="flex flex-col gap-4 px-2 sm:flex-row sm:items-center sm:justify-between">
                    <div id="users-results-text" class="text-[14px] font-medium text-[#334261]">
                        Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
                    </div>

                    @if ($users->hasPages())
                        <div class="flex items-center gap-4">
                            @if ($users->onFirstPage())
                                <span class="inline-flex h-12 w-12 items-center justify-center rounded-[16px] border border-[#e1e8f5] bg-white text-[#94a3b8] shadow-[0_8px_20px_rgba(15,23,42,0.05)]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                    </svg>
                                </span>
                            @else
                                <a href="{{ $users->previousPageUrl() }}" class="inline-flex h-12 w-12 items-center justify-center rounded-[16px] border border-[#e1e8f5] bg-white text-[#6d7f9f] shadow-[0_8px_20px_rgba(15,23,42,0.05)] transition hover:-translate-y-0.5 hover:bg-[#f8fbff]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                                    </svg>
                                </a>
                            @endif

                            <span class="inline-flex h-12 min-w-[3.1rem] items-center justify-center rounded-[16px] bg-[#2563eb] px-4 text-[15px] font-bold text-white shadow-[0_14px_28px_rgba(37,99,235,0.24)]">
                                {{ $users->currentPage() }}
                            </span>

                            @if ($users->hasMorePages())
                                <a href="{{ $users->nextPageUrl() }}" class="inline-flex h-12 w-12 items-center justify-center rounded-[16px] border border-[#e1e8f5] bg-white text-[#6d7f9f] shadow-[0_8px_20px_rgba(15,23,42,0.05)] transition hover:-translate-y-0.5 hover:bg-[#f8fbff]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                    </svg>
                                </a>
                            @else
                                <span class="inline-flex h-12 w-12 items-center justify-center rounded-[16px] border border-[#e1e8f5] bg-white text-[#94a3b8] shadow-[0_8px_20px_rgba(15,23,42,0.05)]">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                    </svg>
                                </span>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </section>

        <div id="delete-user-modal" class="user-delete-modal fixed inset-0 z-[90] flex items-center justify-center bg-slate-950/35 px-4 backdrop-blur-sm" hidden aria-hidden="true">
            <div class="w-full max-w-2xl overflow-hidden rounded-[2rem] bg-white shadow-[0_32px_80px_rgba(15,23,42,0.24)] ring-1 ring-slate-900/10">
                <div class="px-10 py-8">
                    <h3 class="text-2xl font-black tracking-tight text-slate-900">Confirm</h3>
                    <p id="delete-user-modal-text" class="mt-3 text-lg text-slate-600">Delete this user account?</p>

                    <div class="mt-8 flex items-center justify-end gap-3">
                        <button type="button" id="delete-user-cancel" class="inline-flex min-w-[146px] items-center justify-center rounded-[1.35rem] border border-slate-200 bg-white px-8 py-3.5 text-sm font-black uppercase tracking-[0.28em] text-slate-700 transition hover:bg-slate-50 hover:text-slate-900">
                            Cancel
                        </button>

                        <form id="delete-user-form" method="POST" data-confirm-skip>
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex min-w-[146px] items-center justify-center rounded-[1.35rem] bg-rose-600 px-8 py-3.5 text-sm font-black uppercase tracking-[0.28em] text-white shadow-lg shadow-rose-600/20 transition hover:bg-rose-500">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (() => {
            const filterForm = document.getElementById('users-filter-form');
            const roleTrigger = document.getElementById('users-role-trigger');
            const roleMenu = document.getElementById('users-role-menu');
            const roleLabel = document.getElementById('users-role-label');
            const roleInput = document.getElementById('users-role-input');
            const statusTrigger = document.getElementById('users-status-trigger');
            const statusMenu = document.getElementById('users-status-menu');
            const statusLabel = document.getElementById('users-status-label');
            const statusInput = document.getElementById('users-status-input');
            const verificationTrigger = document.getElementById('users-verification-trigger');
            const verificationMenu = document.getElementById('users-verification-menu');
            const verificationLabel = document.getElementById('users-verification-label');
            const verificationInput = document.getElementById('users-verification-input');
            const searchInput = document.getElementById('users-search');
            let searchDebounce = null;

            const closeMenus = () => {
                roleMenu?.classList.add('hidden');
                statusMenu?.classList.add('hidden');
                verificationMenu?.classList.add('hidden');
            };

            searchInput?.addEventListener('input', (event) => {
                const target = event.target;
                if (!(target instanceof HTMLInputElement)) return;
                window.clearTimeout(searchDebounce);
                searchDebounce = window.setTimeout(() => {
                    filterForm?.requestSubmit();
                }, 350);
            });

            roleTrigger?.addEventListener('click', (event) => {
                event.preventDefault();
                const hidden = roleMenu?.classList.contains('hidden');
                closeMenus();
                if (hidden) roleMenu?.classList.remove('hidden');
            });

            statusTrigger?.addEventListener('click', (event) => {
                event.preventDefault();
                const hidden = statusMenu?.classList.contains('hidden');
                closeMenus();
                if (hidden) statusMenu?.classList.remove('hidden');
            });

            verificationTrigger?.addEventListener('click', (event) => {
                event.preventDefault();
                const hidden = verificationMenu?.classList.contains('hidden');
                closeMenus();
                if (hidden) verificationMenu?.classList.remove('hidden');
            });

            roleMenu?.querySelectorAll('[data-role-filter]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (roleInput) roleInput.value = String(button.getAttribute('data-role-filter') || '');
                    if (roleLabel) roleLabel.textContent = button.textContent?.trim() || 'All Roles';
                    roleMenu.classList.add('hidden');
                    filterForm?.requestSubmit();
                });
            });

            statusMenu?.querySelectorAll('[data-status-filter]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (statusInput) statusInput.value = String(button.getAttribute('data-status-filter') || '');
                    if (statusLabel) statusLabel.textContent = button.textContent?.trim() || 'All Status';
                    statusMenu.classList.add('hidden');
                    filterForm?.requestSubmit();
                });
            });

            verificationMenu?.querySelectorAll('[data-verification-filter]').forEach((button) => {
                button.addEventListener('click', () => {
                    if (verificationInput) verificationInput.value = String(button.getAttribute('data-verification-filter') || '');
                    if (verificationLabel) verificationLabel.textContent = button.textContent?.trim() || 'All Verification';
                    verificationMenu.classList.add('hidden');
                    filterForm?.requestSubmit();
                });
            });

            document.addEventListener('click', (event) => {
                const target = event.target;
                if (!(target instanceof Element)) return;
                if (target.closest('#users-role-trigger') || target.closest('#users-role-menu') || target.closest('#users-status-trigger') || target.closest('#users-status-menu') || target.closest('#users-verification-trigger') || target.closest('#users-verification-menu')) return;
                closeMenus();
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') closeMenus();
            });

            const modal = document.getElementById('delete-user-modal');
            const form = document.getElementById('delete-user-form');
            const text = document.getElementById('delete-user-modal-text');
            const cancelButton = document.getElementById('delete-user-cancel');
            const triggers = document.querySelectorAll('[data-delete-trigger]');

            if (modal && form && text && cancelButton && triggers.length) {
                let activeTrigger = null;

                const closeModal = () => {
                    modal.hidden = true;
                    modal.setAttribute('aria-hidden', 'true');

                    if (activeTrigger) {
                        activeTrigger.focus();
                        activeTrigger = null;
                    }
                };

                const openModal = (trigger) => {
                    const action = trigger.getAttribute('data-delete-action');
                    const name = trigger.getAttribute('data-delete-name') || 'this user';
                    if (!action) return;

                    activeTrigger = trigger;
                    form.setAttribute('action', action);
                    text.textContent = `Delete ${name}'s user account?`;
                    modal.hidden = false;
                    modal.setAttribute('aria-hidden', 'false');
                    cancelButton.focus();
                };

                triggers.forEach((trigger) => {
                    trigger.addEventListener('click', () => openModal(trigger));
                });

                cancelButton.addEventListener('click', closeModal);

                modal.addEventListener('click', (event) => {
                    if (event.target === modal) closeModal();
                });

                document.addEventListener('keydown', (event) => {
                    if (event.key === 'Escape' && !modal.hidden) closeModal();
                });
            }

            applyFilters();
        })();
    </script>
</x-admin-layout>
