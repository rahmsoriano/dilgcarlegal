<section
    x-data="{ isOpen: {{ $errors->userDeletion->isNotEmpty() ? 'true' : 'false' }} }"
    class="overflow-hidden rounded-[24px] border border-[#ffcaca] bg-[linear-gradient(180deg,#fffefe_0%,#fff9f9_100%)] shadow-[0_18px_48px_rgba(239,68,68,0.06)]"
>
    <button
        type="button"
        class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left transition duration-300 hover:bg-[#fffafa]"
        @click="isOpen = !isOpen"
        :aria-expanded="isOpen.toString()"
    >
        <span class="flex min-w-0 items-center gap-4">
            <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full border border-[#ffe0e0] bg-[linear-gradient(180deg,#fff5f5_0%,#ffeeee_100%)] text-[#ef4444] shadow-[0_10px_24px_rgba(239,68,68,0.08)]">
                <svg class="h-8 w-8" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 17h.01" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0Z" />
                </svg>
            </span>

            <span class="min-w-0">
                <span class="block text-[14px] font-black tracking-tight text-[#ef4444]">Danger Zone</span>
                <span class="mt-1 block text-[18px] font-black tracking-tight text-[#182f69]">{{ __('Delete Account') }}</span>
                <span class="mt-2 block text-[13px] font-medium leading-6 text-[#5c7197]">Once you delete your account, there is no going back. Please be certain.</span>
            </span>
        </span>

        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-[#ef4444] transition duration-300" :class="isOpen ? 'bg-[#fff1f1]' : 'bg-transparent'">
            <svg x-show="!isOpen" x-cloak class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 0 1 1.06.02L10 11.168l3.71-3.938a.75.75 0 1 1 1.08 1.04l-4.25 4.51a.75.75 0 0 1-1.08 0L5.21 8.27a.75.75 0 0 1 .02-1.06Z" clip-rule="evenodd" />
            </svg>
            <svg x-show="isOpen" x-cloak class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M14.77 12.79a.75.75 0 0 1-1.06-.02L10 8.832 6.29 12.77a.75.75 0 1 1-1.08-1.04l4.25-4.51a.75.75 0 0 1 1.08 0l4.25 4.51a.75.75 0 0 1-.02 1.06Z" clip-rule="evenodd" />
            </svg>
        </span>
    </button>

    <div
        x-cloak
        x-ref="dangerPanel"
        class="overflow-hidden border-t border-[#ffd7d7] transition-all duration-300 ease-out"
        x-bind:style="isOpen ? 'max-height: ' + ($refs.dangerPanel.scrollHeight + 32) + 'px; opacity: 1;' : 'max-height: 0px; opacity: 0;'"
    >
        <div class="space-y-7 px-6 py-6">
            <div class="border-t border-[#ffdede] pt-7 text-[14px] font-medium leading-8 text-[#5c7197]">
                {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
            </div>

            <div class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
                <div class="min-w-0">
                    <div class="text-[18px] font-black tracking-tight text-[#182f69]">{{ __('Delete Account') }}</div>
                    <div class="mt-2 text-[14px] font-medium leading-7 text-[#5c7197]">Once you delete your account, there is no going back. Please be certain.</div>
                </div>

                <button
                    type="button"
                    x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                    class="inline-flex h-12 w-full items-center justify-center gap-3 rounded-2xl border border-[#ff5a5a] bg-white px-7 text-[14px] font-black tracking-tight text-[#ef4444] shadow-[0_14px_30px_rgba(239,68,68,0.08)] transition duration-300 hover:-translate-y-0.5 hover:bg-[#fff8f8] hover:shadow-[0_18px_34px_rgba(239,68,68,0.14)] lg:w-auto lg:min-w-[290px]"
                >
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M7 3.75A1.75 1.75 0 0 1 8.75 2h2.5A1.75 1.75 0 0 1 13 3.75V4h3.25a.75.75 0 0 1 0 1.5h-.638l-.56 9.248A2.25 2.25 0 0 1 12.806 17H7.194a2.25 2.25 0 0 1-2.246-2.252L4.388 5.5H3.75a.75.75 0 0 1 0-1.5H7v-.25ZM8.5 4h3v-.25a.25.25 0 0 0-.25-.25h-2.5a.25.25 0 0 0-.25.25V4Zm-.25 3.25a.75.75 0 0 1 .75.75v5a.75.75 0 0 1-1.5 0V8a.75.75 0 0 1 .75-.75Zm3.5 0A.75.75 0 0 1 12.5 8v5a.75.75 0 0 1-1.5 0V8a.75.75 0 0 1 .75-.75Z" />
                    </svg>
                    {{ __('Delete Account') }}
                </button>
            </div>
        </div>
    </div>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-8" data-confirm-skip>
            @csrf
            @method('delete')

            <div class="flex items-start justify-between gap-6">
                <div>
                    <h2 class="text-xl font-black tracking-tight text-slate-900">{{ __('Are you sure you want to delete your account?') }}</h2>
                    <p class="mt-2 text-sm font-medium leading-7 text-slate-600">{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</p>
                </div>
                <div class="shrink-0 rounded-full bg-rose-500/10 px-4 py-2 text-[10px] font-black uppercase tracking-[0.22em] text-rose-700 ring-1 ring-rose-500/20">
                    Danger
                </div>
            </div>

            <div class="mt-6">
                <label for="password" class="mb-2 block text-xs font-black uppercase tracking-widest text-slate-500">{{ __('Password') }}</label>
                <x-text-input id="password" name="password" type="password" class="block w-full rounded-2xl border-slate-200 bg-white px-6 py-3.5 text-sm font-semibold text-slate-900 shadow-[0_8px_20px_rgba(15,23,42,0.04)] transition focus:border-blue-400 focus:ring-blue-100" placeholder="{{ __('Password') }}" />
                @if ($errors->userDeletion->has('password'))
                    <div class="mt-2 text-sm font-semibold text-rose-600">{{ $errors->userDeletion->first('password') }}</div>
                @endif
            </div>

            <div class="mt-7 flex items-center justify-end gap-2">
                <button type="button" x-on:click="$dispatch('close')" class="h-11 rounded-2xl border border-slate-200 bg-white px-6 text-xs font-black uppercase tracking-[0.18em] text-slate-700 transition hover:bg-slate-50">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="h-11 rounded-2xl bg-rose-600 px-7 text-xs font-black uppercase tracking-[0.18em] text-white transition hover:bg-rose-500">
                    {{ __('Delete Account') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
