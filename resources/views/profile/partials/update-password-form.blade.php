<section>
    <header class="flex items-start justify-between gap-6">
        <div>
            <h2 class="text-xl font-black tracking-tight text-slate-900">{{ __('Update Password') }}</h2>
            <p class="mt-2 text-sm font-medium text-slate-600">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
        </div>
        @if (session('status') === 'password-updated')
            <div class="shrink-0 rounded-full bg-emerald-500/10 px-4 py-2 text-[10px] font-black uppercase tracking-[0.22em] text-emerald-700 ring-1 ring-emerald-500/20">
                Saved
            </div>
        @endif
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div class="grid gap-5 lg:grid-cols-2">
            <div>
                <label for="update_password_current_password" class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">{{ __('Current Password') }}</label>
                <x-text-input id="update_password_current_password" name="current_password" type="password" class="block w-full rounded-2xl bg-white/80 border-slate-900/10 px-6 py-3.5 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all" autocomplete="current-password" />
                @if ($errors->updatePassword->has('current_password'))
                    <div class="mt-2 text-sm font-semibold text-rose-600">{{ $errors->updatePassword->first('current_password') }}</div>
                @endif
            </div>

            <div>
                <label for="update_password_password" class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">{{ __('New Password') }}</label>
                <x-text-input id="update_password_password" name="password" type="password" class="block w-full rounded-2xl bg-white/80 border-slate-900/10 px-6 py-3.5 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all" autocomplete="new-password" />
                @if ($errors->updatePassword->has('password'))
                    <div class="mt-2 text-sm font-semibold text-rose-600">{{ $errors->updatePassword->first('password') }}</div>
                @endif
            </div>
        </div>

        <div>
            <label for="update_password_password_confirmation" class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">{{ __('Confirm Password') }}</label>
            <x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="block w-full rounded-2xl bg-white/80 border-slate-900/10 px-6 py-3.5 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all" autocomplete="new-password" />
            @if ($errors->updatePassword->has('password_confirmation'))
                <div class="mt-2 text-sm font-semibold text-rose-600">{{ $errors->updatePassword->first('password_confirmation') }}</div>
            @endif
        </div>

        <div class="flex items-center justify-end gap-3">
            <button type="submit" class="inline-flex h-11 items-center justify-center rounded-2xl bg-blue-600 px-7 text-sm font-black tracking-tight text-white shadow-lg shadow-blue-600/20 hover:bg-blue-500 transition">
                {{ __('Save Changes') }}
            </button>
        </div>
    </form>
</section>
