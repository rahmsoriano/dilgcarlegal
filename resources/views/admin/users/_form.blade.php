@php
    $isEdit = $user->exists;
@endphp

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
    <div class="grid gap-5 md:grid-cols-2">
        <div>
            <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">First Name</label>
            <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-5 py-3.5 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">
        </div>

        <div>
            <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Last Name</label>
            <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-5 py-3.5 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">
        </div>

        <div>
            <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Birthday</label>
            <input type="date" name="birthday" value="{{ old('birthday', optional($user->birthday)->format('Y-m-d')) }}"
                class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-5 py-3.5 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">
        </div>

        <div>
            <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Email</label>
            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-5 py-3.5 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">
        </div>

        <div>
            <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">{{ $isEdit ? 'New Password (Optional)' : 'Password' }}</label>
            <input type="password" name="password" {{ $isEdit ? '' : 'required' }}
                class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-5 py-3.5 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">
        </div>

        <div>
            <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Confirm Password</label>
            <input type="password" name="password_confirmation" {{ $isEdit ? '' : 'required' }}
                class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-5 py-3.5 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">
        </div>

        <div>
            <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Role</label>
            <select name="role" required class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-5 py-3.5 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">
                <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                <option value="staff" @selected(old('role', $user->role) === 'staff')>Staff</option>
                <option value="user" @selected(old('role', $user->role) === 'user')>User</option>
            </select>
        </div>

        <div>
            <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Status</label>
            <select name="status" required class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-5 py-3.5 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">
                <option value="active" @selected(old('status', $user->status) === 'active')>Active</option>
                <option value="inactive" @selected(old('status', $user->status) === 'inactive')>Inactive</option>
            </select>
        </div>
    </div>

    <div class="mt-6 flex items-center justify-end gap-3">
        <a href="{{ route('admin.users.index') }}" class="inline-flex h-11 items-center justify-center rounded-2xl bg-white/80 px-6 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-slate-900/10 transition hover:bg-white hover:text-slate-900">
            Cancel
        </a>
        <button type="submit" class="inline-flex h-11 items-center justify-center rounded-2xl bg-blue-600 px-7 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-500">
            {{ $isEdit ? 'Update User' : 'Create User' }}
        </button>
    </div>
</div>
