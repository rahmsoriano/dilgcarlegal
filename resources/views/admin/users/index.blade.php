<x-admin-layout>
    <style>
        .user-delete-modal[hidden] {
            display: none !important;
        }
    </style>

    <div class="space-y-8">
        <div>
            <div>
                <h2 class="text-3xl font-bold tracking-tight text-slate-900">User Management</h2>
                <p class="mt-2 text-slate-600">Review verification, update roles, manage account status, and safely control user access.</p>
            </div>
        </div>

        @if (session('status'))
            <div class="rounded-[1.75rem] border border-emerald-200 bg-emerald-50 px-6 py-4 text-sm font-semibold text-emerald-700 shadow-sm">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="rounded-[1.75rem] border border-rose-200 bg-rose-50 px-6 py-4 text-sm text-rose-700 shadow-sm">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="overflow-hidden rounded-[2rem] bg-white/80 shadow-[0_24px_70px_rgba(15,23,42,0.08)] ring-1 ring-slate-900/5 backdrop-blur-xl">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1100px] border-collapse text-left">
                    <thead>
                        <tr class="border-b border-slate-900/5 bg-white/40">
                            <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Full Name</th>
                            <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Email</th>
                            <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Birthday</th>
                            <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Role</th>
                            <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Status</th>
                            <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Email Verification Status</th>
                            <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Date Registered</th>
                            <th class="px-6 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-900/5">
                        @forelse ($users as $user)
                            <tr class="hover:bg-slate-900/[0.02] transition-colors">
                                <td class="px-6 py-5 align-top">
                                    <div class="text-sm font-bold text-slate-900">{{ $user->full_name }}</div>
                                </td>
                                <td class="px-6 py-5 align-top text-sm font-semibold text-slate-700">{{ $user->email }}</td>
                                <td class="px-6 py-5 align-top text-sm text-slate-700">
                                    {{ $user->birthday?->format('M d, Y') ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-5 align-top">
                                    <span @class([
                                        'inline-flex rounded-full px-3 py-1 text-[11px] font-black uppercase tracking-[0.14em]',
                                        'bg-blue-100 text-blue-700' => $user->role === 'admin',
                                        'bg-amber-100 text-amber-700' => $user->role === 'staff',
                                        'bg-slate-100 text-slate-600' => $user->role === 'user',
                                    ])>
                                        {{ $user->role }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 align-top">
                                    <span @class([
                                        'inline-flex rounded-full px-3 py-1 text-[11px] font-black uppercase tracking-[0.14em]',
                                        'bg-emerald-100 text-emerald-700' => $user->status === 'active',
                                        'bg-rose-100 text-rose-700' => $user->status === 'inactive',
                                    ])>
                                        {{ $user->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 align-top">
                                    <span @class([
                                        'inline-flex rounded-full px-3 py-1 text-[11px] font-black uppercase tracking-[0.14em]',
                                        'bg-emerald-100 text-emerald-700' => $user->email_verified_at,
                                        'bg-rose-100 text-rose-700' => ! $user->email_verified_at,
                                    ])>
                                        {{ $user->email_verified_at ? 'Verified' : 'Not Verified' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 align-top text-sm text-slate-700">
                                    {{ $user->created_at?->format('M d, Y h:i A') }}
                                </td>
                                <td class="px-6 py-5 align-top">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center rounded-xl bg-white px-4 py-2 text-xs font-bold uppercase tracking-wide text-slate-700 shadow-sm ring-1 ring-slate-900/10 transition hover:bg-slate-50 hover:text-slate-900">
                                            Edit
                                        </a>

                                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                @disabled(auth()->id() === $user->id)
                                                class="inline-flex items-center rounded-xl px-4 py-2 text-xs font-bold uppercase tracking-wide shadow-sm ring-1 transition {{ $user->status === 'active' ? 'bg-amber-50 text-amber-700 ring-amber-200 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-700 ring-emerald-200 hover:bg-emerald-100' }} disabled:cursor-not-allowed disabled:opacity-50">
                                                {{ $user->status === 'active' ? 'Deactivate' : 'Activate' }}
                                            </button>
                                        </form>

                                        <button type="button"
                                            data-delete-trigger
                                            data-delete-action="{{ route('admin.users.destroy', $user) }}"
                                            data-delete-name="{{ $user->full_name }}"
                                            @disabled(auth()->id() === $user->id)
                                            class="inline-flex items-center rounded-xl bg-rose-50 px-4 py-2 text-xs font-bold uppercase tracking-wide text-rose-700 shadow-sm ring-1 ring-rose-200 transition hover:bg-rose-100 disabled:cursor-not-allowed disabled:opacity-50">
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-8 py-14 text-center">
                                    <div class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">No users found</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($users->hasPages())
                <div class="border-t border-slate-900/5 bg-white/40 px-8 py-5">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

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
            const modal = document.getElementById('delete-user-modal');
            const form = document.getElementById('delete-user-form');
            const text = document.getElementById('delete-user-modal-text');
            const cancelButton = document.getElementById('delete-user-cancel');
            const triggers = document.querySelectorAll('[data-delete-trigger]');

            if (!modal || !form || !text || !cancelButton || !triggers.length) {
                return;
            }

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

                if (!action) {
                    return;
                }

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
                if (event.target === modal) {
                    closeModal();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape' && !modal.hidden) {
                    closeModal();
                }
            });
        })();
    </script>
</x-admin-layout>
