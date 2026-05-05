<x-admin-layout>
    <div class="space-y-8">
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-slate-900">Edit User</h2>
            <p class="mt-2 text-slate-600">Update account details, role, status, and resend verification automatically if the email changes.</p>
        </div>

        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
            @csrf
            @method('PUT')
            @include('admin.users._form')
        </form>
    </div>
</x-admin-layout>
