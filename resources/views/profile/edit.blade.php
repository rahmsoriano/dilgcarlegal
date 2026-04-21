<x-app-layout>
    <div class="py-10">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-black tracking-tight text-slate-900">Profile Settings</h1>
                <p class="mt-2 text-slate-600 font-medium">Manage your account details, password, and security.</p>
            </div>

            <div class="grid gap-6 lg:grid-cols-12">
                <div class="lg:col-span-4">
                    <div class="rounded-[2rem] bg-white/80 backdrop-blur-xl p-6 ring-1 ring-slate-900/5 shadow-[0_24px_70px_rgba(15,23,42,0.08)]">
                        <div class="flex items-center gap-4">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name ?? auth()->user()?->name ?? 'User') }}&background=6366f1&color=fff" alt="User Avatar" class="h-14 w-14 rounded-2xl ring-1 ring-slate-900/10">
                            <div class="min-w-0">
                                <div class="truncate text-base font-black tracking-tight text-slate-900">{{ $user->name ?? auth()->user()?->name }}</div>
                                <div class="truncate text-xs font-semibold text-slate-500">{{ $user->email ?? auth()->user()?->email }}</div>
                            </div>
                        </div>

                        <div class="mt-6 space-y-3">
                            <div class="rounded-2xl bg-white/60 px-4 py-3 ring-1 ring-slate-900/5">
                                <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">Account</div>
                                <div class="mt-1 text-sm font-semibold text-slate-900">Personal information</div>
                            </div>
                            <div class="rounded-2xl bg-white/60 px-4 py-3 ring-1 ring-slate-900/5">
                                <div class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-500">Security</div>
                                <div class="mt-1 text-sm font-semibold text-slate-900">Password & deletion</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-y-6 lg:col-span-8">
                    <div class="rounded-[2rem] bg-white/80 backdrop-blur-xl p-6 sm:p-8 ring-1 ring-slate-900/5 shadow-[0_24px_70px_rgba(15,23,42,0.08)]">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <div class="rounded-[2rem] bg-white/80 backdrop-blur-xl p-6 sm:p-8 ring-1 ring-slate-900/5 shadow-[0_24px_70px_rgba(15,23,42,0.08)]">
                        @include('profile.partials.update-password-form')
                    </div>

                    <div class="rounded-[2rem] bg-white/80 backdrop-blur-xl p-6 sm:p-8 ring-1 ring-slate-900/5 shadow-[0_24px_70px_rgba(15,23,42,0.08)]">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
