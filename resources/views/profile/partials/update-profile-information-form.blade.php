<section>
    <header class="flex items-start justify-between gap-4">
        <div class="flex items-start gap-4">
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#edf4ff] text-[#2563eb]">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 100-8 4 4 0 000 8zM5 20a7 7 0 0114 0" />
                </svg>
            </div>
            <div>
                <h2 class="text-[0.95rem] font-black text-[#14214d]">{{ __('Profile Information') }}</h2>
                <p class="mt-1 text-[13px] font-medium text-[#58698d]">{{ __("Update your account's profile information and email address.") }}</p>
            </div>
        </div>

        @if (session('status') === 'profile-updated')
            <div class="rounded-full bg-emerald-50 px-3 py-1.5 text-[11px] font-black uppercase tracking-[0.16em] text-emerald-700 ring-1 ring-emerald-200">
                Saved
            </div>
        @endif
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-5 space-y-5">
        @csrf
        @method('patch')

        <div class="grid gap-5 xl:grid-cols-2">
            <div>
                <label for="name" class="mb-2 block text-[13px] font-medium text-[#44557d]">{{ __('Full Name') }}</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[#687999]">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 100-8 4 4 0 000 8zM5 20a7 7 0 0114 0" />
                        </svg>
                    </span>
                    <x-text-input id="name" name="name" type="text" x-bind:readonly="!profileEditing" x-bind:disabled="!profileEditing" x-bind:class="profileEditing ? 'bg-white' : 'bg-slate-50/80 text-slate-500'" class="block h-[44px] w-full rounded-xl border-[#d5e2fb] bg-white pl-12 pr-4 text-[14px] font-medium text-[#15224a] shadow-sm focus:border-[#8fb3ff] focus:ring-4 focus:ring-[#dbe8ff]" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                </div>
                @if ($errors->has('name'))
                    <div class="mt-2 text-sm font-semibold text-rose-600">{{ $errors->first('name') }}</div>
                @endif
            </div>

            <div>
                <label for="email" class="mb-2 block text-[13px] font-medium text-[#44557d]">{{ __('Email Address') }}</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-[#687999]">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v12H4V6z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 8l8 6 8-6" />
                        </svg>
                    </span>
                    <x-text-input id="email" name="email" type="email" x-bind:readonly="!profileEditing" x-bind:disabled="!profileEditing" x-bind:class="profileEditing ? 'bg-white' : 'bg-slate-50/80 text-slate-500'" class="block h-[44px] w-full rounded-xl border-[#d5e2fb] bg-white pl-12 pr-4 text-[14px] font-medium text-[#15224a] shadow-sm focus:border-[#8fb3ff] focus:ring-4 focus:ring-[#dbe8ff]" :value="old('email', $user->email)" required autocomplete="username" />
                </div>
                @if ($errors->has('email'))
                    <div class="mt-2 text-sm font-semibold text-rose-600">{{ $errors->first('email') }}</div>
                @endif
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="rounded-[0.95rem] border border-[#bcd1fe] bg-[#f7fbff] px-4 py-4">
                <div class="flex items-start gap-4">
                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-[#e7f0ff] text-[#2563eb]">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16v12H4V6z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 8l8 6 8-6" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <div class="text-[13px] font-bold text-[#16244c]">Unverified Email</div>
                        <div class="mt-1 text-[12px] font-medium text-[#58698d]">
                            {{ __('Your email address is unverified.') }}
                            <button type="submit" form="send-verification" class="font-bold text-[#1d5df0] underline underline-offset-4 hover:text-[#154fd2]">
                                {{ __('Re-send verification email') }}
                            </button>
                        </div>
                        @if (session('status') === 'verification-link-sent')
                            <div class="mt-2 text-sm font-semibold text-emerald-700">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </div>
                        @elseif (session('status') === 'verification-link-fallback')
                            <div class="mt-2 text-sm font-semibold text-amber-700">
                                {{ __('We could not send the verification email right now. Please check your mail settings and try again.') }}
                            </div>
                            @if (app()->environment('local'))
                                <form method="post" action="{{ route('verification.local') }}" class="mt-3">
                                    @csrf
                                    <button type="submit" class="inline-flex items-center rounded-lg border border-amber-300 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-800 transition hover:bg-amber-100">
                                        {{ __('Mark email as verified locally') }}
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div x-cloak x-show="profileEditing" x-transition.opacity.duration.200ms class="flex justify-end">
            <button type="submit" class="inline-flex h-[42px] items-center justify-center gap-2 rounded-[0.8rem] bg-[#1f5ff0] px-7 text-[14px] font-bold text-white shadow-[0_12px_24px_rgba(37,99,235,0.24)] transition hover:bg-[#184fd0]">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-8 8a1 1 0 01-1.415 0l-4-4a1 1 0 111.415-1.42l3.292 3.29 7.292-7.29a1 1 0 011.416 0z" clip-rule="evenodd" />
                </svg>
                {{ __('Save Changes') }}
            </button>
        </div>
    </form>
</section>
