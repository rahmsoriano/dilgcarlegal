<x-admin-layout>
    <div class="space-y-8">
        <div>
            <h2 class="text-3xl font-bold tracking-tight text-slate-900">Create User</h2>
            <p class="mt-2 text-slate-600">Add a new account with a secure default flow and email verification.</p>
        </div>

        <form method="POST" action="{{ route('admin.users.store') }}" class="space-y-6">
            @csrf
            @include('admin.users._form')
        </form>
    </div>
</x-admin-layout>
