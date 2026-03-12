class="flex items-center gap-3"<x-admin-layout>
    <x-slot name="title">Users</x-slot>
    <x-slot name="subtitle">Manage user access and admin privileges.</x-slot>

    <div class="rounded-3xl bg-white/5 p-5 ring-1 ring-white/10">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-white/60">
                        <th class="py-3 pr-4 font-medium">ID</th>
                        <th class="py-3 pr-4 font-medium">Name</th>
                        <th class="py-3 pr-4 font-medium">Email</th>
                        <th class="py-3 pr-4 font-medium">Conversations</th>
                        <th class="py-3 pr-4 font-medium">AI Requests</th>
                        <th class="py-3 pr-4 font-medium">Admin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach ($users as $user)
                        <tr>
                            <td class="py-3 pr-4 text-white/80">{{ $user->id }}</td>
                            <td class="py-3 pr-4">{{ $user->name }}</td>
                            <td class="py-3 pr-4 text-white/80">{{ $user->email }}</td>
                            <td class="py-3 pr-4 text-white/80">{{ number_format($user->conversations_count) }}</td>
                            <td class="py-3 pr-4 text-white/80">{{ number_format($user->ai_requests_count) }}</td>
                            <td class="py-3 pr-4">
                                <form method="POST" action="{{ route('admin.users.update', $user) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="is_admin" value="{{ $user->is_admin ? 0 : 1 }}">
                                    <button type="submit" class="{{ $user->is_admin ? 'bg-emerald-500/20 text-emerald-200 ring-emerald-500/30' : 'bg-white/10 text-white ring-white/10' }} rounded-2xl px-3 py-1.5 text-xs font-semibold ring-1 hover:bg-white/15">
                                        {{ $user->is_admin ? 'Yes' : 'No' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 text-white/80">{{ $users->links() }}</div>
    </div>
</x-admin-layout>
