<section class="space-y-5">
    <div class="flex items-start justify-between gap-4">
        <button
            type="button"
            @click="securityOpen = securityOpen === 'delete' ? null : 'delete'"
            class="flex min-w-0 flex-1 items-start gap-4 text-left"
            :aria-expanded="(securityOpen === 'delete').toString()"
            aria-controls="delete-account-panel"
        >
            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#fff2f3] text-[#ef4444]">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4M12 17h.01M10.3 3.9L2.9 17a2 2 0 001.7 3h14.8a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z" />
                </svg>
            </div>
            <div class="min-w-0">
                <div class="inline-flex rounded-full bg-[#ffe4e7] px-2.5 py-1 text-[11px] font-black text-[#ef4444]">Danger Zone</div>
                <h2 class="mt-2 text-[0.95rem] font-black text-[#14214d]">{{ __('Delete Account') }}</h2>
                <p class="mt-3 text-[13px] font-medium leading-6 text-[#58698d]">{{ __('Permanently remove your account and data') }}</p>
            </div>
        </button>

        <button
            type="button"
            @click="securityOpen = securityOpen === 'delete' ? null : 'delete'"
            class="flex h-10 w-10 items-center justify-center rounded-full border border-[#f7d2d6] bg-white text-[#a16168] shadow-sm transition hover:bg-[#fff8f8]"
            :aria-expanded="(securityOpen === 'delete').toString()"
            aria-controls="delete-account-panel"
        >
            <svg class="h-4 w-4 transition duration-300" :class="securityOpen === 'delete' ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
        </button>
    </div>

    <div
        id="delete-account-panel"
        x-ref="panel"
        class="overflow-hidden transition-all duration-300 ease-out"
        :style="securityOpen === 'delete' ? `max-height:${$refs.panel.scrollHeight}px;opacity:1;transform:translateY(0)` : 'max-height:0px;opacity:0;transform:translateY(-6px)'"
    >
    <div class="rounded-[1rem] border border-[#ffd1d5] bg-[#fff8f8] px-4 py-4">
        <div class="flex items-start gap-3">
            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-white text-[#ef4444] shadow-sm">
                <svg class="h-4.5 w-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4M12 17h.01M10.3 3.9L2.9 17a2 2 0 001.7 3h14.8a2 2 0 001.7-3L13.7 3.9a2 2 0 00-3.4 0z" />
                </svg>
            </div>
            <div>
                <div class="text-[13px] font-bold text-[#c62828]">This action cannot be undone.</div>
                <div class="mt-1 text-[12px] font-medium text-[#7f5256]">All your data will be permanently removed from our system.</div>
            </div>
        </div>
    </div>

    <button type="button" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')" class="inline-flex h-[46px] items-center justify-center gap-2 rounded-[0.8rem] bg-[#ef1d25] px-8 text-[14px] font-bold text-white shadow-[0_12px_24px_rgba(239,29,37,0.22)] transition hover:bg-[#dc1a22]">
        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
            <path d="M6 2a1 1 0 00-1 1v1H3.5a.5.5 0 000 1h.64l.714 10A2 2 0 006.85 17h6.3a2 2 0 001.996-1.85l.714-10h.64a.5.5 0 000-1H15V3a1 1 0 00-1-1H6zm2 2V3h4v1H8z" />
        </svg>
        {{ __('Delete Account') }}
    </button>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8" data-confirm-skip>
            @csrf
            @method('delete')

            <div class="space-y-5">
                <div>
                    <div class="inline-flex rounded-full bg-[#ffe4e7] px-2.5 py-1 text-[11px] font-black text-[#ef4444]">Danger Zone</div>
                    <h2 class="mt-3 text-xl font-black tracking-tight text-slate-900">{{ __('Are you sure you want to delete your account?') }}</h2>
                    <p class="mt-2 text-sm font-medium leading-6 text-slate-600">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</p>
                </div>

                <div class="rounded-[1rem] border border-[#ffd1d5] bg-[#fff8f8] px-4 py-4 text-sm font-medium text-[#7f5256]">
                    {{ __('This action cannot be undone.') }}
                </div>

                <div>
                    <label for="password" class="mb-2 block text-[14px] font-medium text-[#44557d]">{{ __('Password') }}</label>
                    <x-text-input id="password" name="password" type="password" class="block h-[44px] w-full rounded-xl border-[#f0c6cb] bg-white px-4 text-[15px] font-medium text-[#15224a] shadow-sm focus:border-[#f39aa2] focus:ring-4 focus:ring-[#ffe1e4]" placeholder="{{ __('Password') }}" />
                    @if ($errors->userDeletion->has('password'))
                        <div class="mt-2 text-sm font-semibold text-rose-600">{{ $errors->userDeletion->first('password') }}</div>
                    @endif
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" x-on:click="$dispatch('close')" class="h-11 rounded-xl border border-slate-200 bg-white px-6 text-xs font-black uppercase tracking-[0.18em] text-slate-700 transition hover:bg-slate-50">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="h-11 rounded-xl bg-[#ef1d25] px-7 text-xs font-black uppercase tracking-[0.18em] text-white transition hover:bg-[#dc1a22]">
                        {{ __('Delete Account') }}
                    </button>
                </div>
            </div>
        </form>
    </x-modal>
</section>
