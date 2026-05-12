<section>
    <header class="flex items-start justify-between gap-6">
        <div>
            <h2 class="text-xl font-black tracking-tight text-slate-900">{{ __('Profile Information') }}</h2>
            <p class="mt-2 text-sm font-medium text-slate-600">{{ __("Update your account's profile information and email address.") }}</p>
        </div>
        @if (session('status') === 'profile-updated')
            <div class="shrink-0 rounded-full bg-emerald-500/10 px-4 py-2 text-[10px] font-black uppercase tracking-[0.22em] text-emerald-700 ring-1 ring-emerald-500/20">
                Saved
            </div>
        @endif
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="grid gap-5 lg:grid-cols-2">
            <div>
                <label for="name" class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">{{ __('Name') }}</label>
                <x-text-input id="name" name="name" type="text" class="block w-full rounded-2xl bg-white/80 border-slate-900/10 px-6 py-3.5 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                @if ($errors->has('name'))
                    <div class="mt-2 text-sm font-semibold text-rose-600">{{ $errors->first('name') }}</div>
                @endif
            </div>

            <div>
                <label for="email" class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">{{ __('Email') }}</label>
                <x-text-input id="email" name="email" type="email" class="block w-full rounded-2xl bg-white/80 border-slate-900/10 px-6 py-3.5 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all" :value="old('email', $user->email)" required autocomplete="username" />
                @if ($errors->has('email'))
                    <div class="mt-2 text-sm font-semibold text-rose-600">{{ $errors->first('email') }}</div>
                @endif

                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                    <div class="mt-3 rounded-2xl bg-amber-500/10 px-5 py-4 ring-1 ring-amber-500/20">
                        <div class="text-xs font-black uppercase tracking-[0.2em] text-amber-800">Unverified email</div>
                        <div class="mt-2 text-sm font-medium text-slate-700">
                            {{ __('Your email address is unverified.') }}
                            <button form="send-verification" class="ml-1 font-black text-slate-900 underline underline-offset-4 hover:text-slate-700">
                                {{ __('Re-send verification email') }}
                            </button>
                        </div>
                        @if (session('status') === 'verification-link-sent')
                            <div class="mt-2 text-sm font-semibold text-emerald-700">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <div class="flex items-center justify-end gap-3">
            <button type="submit" class="inline-flex h-11 items-center justify-center rounded-2xl bg-blue-600 px-7 text-sm font-black tracking-tight text-white shadow-lg shadow-blue-600/20 hover:bg-blue-500 transition">
                {{ __('Save Changes') }}
            </button>
        </div>
    </form>
</section>
