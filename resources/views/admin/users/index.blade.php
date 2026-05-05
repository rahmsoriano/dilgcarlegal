<x-admin-layout>
    <div class="space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-3xl font-bold tracking-tight text-slate-900">User Management</h2>
                <p class="mt-2 text-slate-600">Review verification, update roles, manage account status, and safely control user access.</p>
            </div>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition-all duration-300 hover:bg-blue-500">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="mr-2 h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Create User
            </a>
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

        <div class="rounded-[2rem] bg-white/80 p-6 shadow-[0_24px_70px_rgba(15,23,42,0.08)] ring-1 ring-slate-900/5 backdrop-blur-xl">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid gap-4 lg:grid-cols-[minmax(0,2.3fr)_repeat(3,minmax(0,1fr))_auto] lg:items-end">
                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Search</label>
                    <div class="relative">
                        <div class="pointer-events-none absolute left-5 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 104.473 8.708l2.41 2.409a.75.75 0 101.06-1.06l-2.409-2.41A5.5 5.5 0 009 3.5zM4.5 9a4.5 4.5 0 119 0 4.5 4.5 0 01-9 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="Search by name or email"
                            class="w-full rounded-2xl border-slate-900/10 bg-white/80 py-3.5 pl-14 pr-5 text-sm text-slate-900 placeholder:text-slate-400 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Role</label>
                    <select name="role" class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-4 py-3.5 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">
                        <option value="">All Roles</option>
                        <option value="admin" @selected($filters['role'] === 'admin')>Admin</option>
                        <option value="staff" @selected($filters['role'] === 'staff')>Staff</option>
                        <option value="user" @selected($filters['role'] === 'user')>User</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Status</label>
                    <select name="status" class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-4 py-3.5 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">
                        <option value="">All Statuses</option>
                        <option value="active" @selected($filters['status'] === 'active')>Active</option>
                        <option value="inactive" @selected($filters['status'] === 'inactive')>Inactive</option>
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Verification</label>
                    <select name="verification" class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-4 py-3.5 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">
                        <option value="">All Users</option>
                        <option value="verified" @selected($filters['verification'] === 'verified')>Verified</option>
                        <option value="not_verified" @selected($filters['verification'] === 'not_verified')>Not Verified</option>
                    </select>
                </div>

                <div class="flex gap-3">
                    <button type="submit" class="inline-flex h-[52px] items-center justify-center rounded-2xl bg-white/80 px-6 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-slate-900/10 transition-all hover:bg-white hover:text-slate-900">
                        Apply
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="inline-flex h-[52px] items-center justify-center rounded-2xl bg-slate-100 px-6 text-sm font-bold text-slate-600 transition-all hover:bg-slate-200 hover:text-slate-900">
                        Reset
                    </a>
                </div>
            </form>
        </div>

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
                                    <div class="mt-1 text-xs font-medium text-slate-500">#{{ $user->id }}</div>
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

                                        <form method="POST" action="{{ route('admin.users.destroy', $user) }}" onsubmit="return confirm('Delete this user account?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                @disabled(auth()->id() === $user->id)
                                                class="inline-flex items-center rounded-xl bg-rose-50 px-4 py-2 text-xs font-bold uppercase tracking-wide text-rose-700 shadow-sm ring-1 ring-rose-200 transition hover:bg-rose-100 disabled:cursor-not-allowed disabled:opacity-50">
                                                Delete
                                            </button>
                                        </form>
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
    </div>
</x-admin-layout>
