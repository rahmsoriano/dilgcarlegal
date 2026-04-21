<section class="space-y-6">
    <header>
        <h2 class="text-xl font-black tracking-tight text-slate-900">{{ __('Delete Account') }}</h2>
        <p class="mt-2 text-sm font-medium text-slate-600">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</p>
    </header>

    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="inline-flex h-11 items-center justify-center rounded-2xl bg-rose-600 px-6 text-sm font-black tracking-tight text-white shadow-lg shadow-rose-600/20 hover:bg-rose-500 transition">
        {{ __('Delete Account') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8" data-confirm-skip>
            @csrf
            @method('delete')

            <div class="flex items-start justify-between gap-6">
                <div>
                    <h2 class="text-xl font-black tracking-tight text-slate-900">{{ __('Are you sure you want to delete your account?') }}</h2>
                    <p class="mt-2 text-sm font-medium text-slate-600">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</p>
                </div>
                <div class="shrink-0 rounded-full bg-rose-500/10 px-4 py-2 text-[10px] font-black uppercase tracking-[0.22em] text-rose-700 ring-1 ring-rose-500/20">
                    Danger
                </div>
            </div>

            <div class="mt-6">
                <label for="password" class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">{{ __('Password') }}</label>
                <x-text-input id="password" name="password" type="password" class="block w-full rounded-2xl bg-white/80 border-slate-900/10 px-6 py-3.5 text-sm font-semibold text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all" placeholder="{{ __('Password') }}" />
                @if ($errors->userDeletion->has('password'))
                    <div class="mt-2 text-sm font-semibold text-rose-600">{{ $errors->userDeletion->first('password') }}</div>
                @endif
            </div>

            <div class="mt-7 flex items-center justify-end gap-2">
                <button type="button" x-on:click="$dispatch('close')" class="h-11 rounded-2xl border border-slate-900/10 bg-white/60 px-6 text-xs font-black uppercase tracking-[0.18em] text-slate-700 hover:bg-white transition">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="h-11 rounded-2xl bg-rose-600 px-7 text-xs font-black uppercase tracking-[0.18em] text-white hover:bg-rose-500 transition">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
