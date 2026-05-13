<section
    x-data="{
        isOpen: {{ $errors->updatePassword->isNotEmpty() ? 'true' : 'false' }},
        currentVisible: false,
        passwordVisible: false,
        confirmVisible: false,
        passwordValue: '',
        get checks() {
            return {
                length: this.passwordValue.length >= 8,
                upper: /[A-Z]/.test(this.passwordValue),
                lower: /[a-z]/.test(this.passwordValue),
                number: /[0-9]/.test(this.passwordValue),
                special: /[^A-Za-z0-9]/.test(this.passwordValue),
            };
        },
        get score() {
            return Object.values(this.checks).filter(Boolean).length;
        },
        get strengthLabel() {
            if (this.score >= 5) return 'Strong';
            if (this.score >= 3) return 'Medium';
            if (this.score >= 1) return 'Weak';
            return 'Empty';
        }
    }"
    class="overflow-hidden rounded-[24px] border border-[#dce8fb] bg-[linear-gradient(180deg,#ffffff_0%,#fbfdff_100%)] shadow-[0_18px_48px_rgba(23,58,118,0.06)]"
>
    <button
        type="button"
        class="flex w-full items-center justify-between gap-4 px-6 py-5 text-left transition duration-300 hover:bg-[#fbfdff]"
        @click="isOpen = !isOpen"
        :aria-expanded="isOpen.toString()"
    >
        <span class="flex min-w-0 items-center gap-4">
            <span class="flex h-16 w-16 shrink-0 items-center justify-center rounded-full border border-[#dbe8fb] bg-[linear-gradient(180deg,#f7faff_0%,#edf4ff_100%)] text-[#2563eb] shadow-[0_10px_24px_rgba(37,99,235,0.08)]">
                <svg class="h-7 w-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V8a4 4 0 1 1 8 0v3" />
                    <rect x="5" y="11" width="14" height="10" rx="2.5" />
                </svg>
            </span>

            <span class="min-w-0">
                <span class="block text-[18px] font-black tracking-tight text-[#182f69]">Update Password</span>
                <span class="mt-1 block text-[13px] font-medium leading-6 text-[#5c7197]">Ensure your account is using a long, random password to stay secure.</span>
            </span>
        </span>

        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full text-[#1a3672] transition duration-300" :class="isOpen ? 'bg-[#f3f7ff]' : 'bg-transparent'">
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
        x-ref="passwordPanel"
        class="overflow-hidden border-t border-[#e4edf9] transition-all duration-300 ease-out"
        x-bind:style="isOpen ? 'max-height: ' + ($refs.passwordPanel.scrollHeight + 32) + 'px; opacity: 1;' : 'max-height: 0px; opacity: 0;'"
    >
        <form method="post" action="{{ route('password.update') }}" class="space-y-5 px-6 py-6">
            @csrf
            @method('put')

            <div>
                <label for="update_password_current_password" class="mb-2 block text-[12px] font-black tracking-tight text-[#243f79]">{{ __('Current Password') }}</label>
                <div class="relative">
                    <x-text-input id="update_password_current_password" name="current_password" x-bind:type="currentVisible ? 'text' : 'password'" class="block w-full rounded-[14px] border-[#d9e5f7] bg-white py-3.5 pl-4 pr-[3.25rem] text-[14px] font-semibold text-[#1f376d] shadow-[0_8px_20px_rgba(15,23,42,0.04)] transition focus:border-[#90b7ff] focus:ring-[#dfeeff]" autocomplete="current-password" />
                    <button type="button" @click="currentVisible = !currentVisible" class="absolute inset-y-0 right-3 z-10 flex w-6 items-center justify-center text-[#5f7398] transition hover:text-[#274f96] focus:outline-none">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>
                </div>
                @if ($errors->updatePassword->has('current_password'))
                    <div class="mt-2 text-sm font-semibold text-rose-600">{{ $errors->updatePassword->first('current_password') }}</div>
                @endif
            </div>

            <div>
                <label for="update_password_password" class="mb-2 block text-[12px] font-black tracking-tight text-[#243f79]">{{ __('New Password') }}</label>
                <div class="relative">
                    <x-text-input id="update_password_password" name="password" x-model="passwordValue" x-bind:type="passwordVisible ? 'text' : 'password'" class="block w-full rounded-[14px] border-[#d9e5f7] bg-white py-3.5 pl-4 pr-[3.25rem] text-[14px] font-semibold text-[#1f376d] shadow-[0_8px_20px_rgba(15,23,42,0.04)] transition focus:border-[#90b7ff] focus:ring-[#dfeeff]" autocomplete="new-password" />
                    <button type="button" @click="passwordVisible = !passwordVisible" class="absolute inset-y-0 right-3 z-10 flex w-6 items-center justify-center text-[#5f7398] transition hover:text-[#274f96] focus:outline-none">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>
                </div>
                @if ($errors->updatePassword->has('password'))
                    <div class="mt-2 text-sm font-semibold text-rose-600">{{ $errors->updatePassword->first('password') }}</div>
                @endif

                <div class="mt-4 rounded-[16px] border border-[#d7e5fd] bg-[linear-gradient(180deg,#f8fbff_0%,#f3f7ff_100%)] p-5">
                    <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                        <div class="min-w-0">
                            <div class="text-[13px] font-black tracking-tight text-[#243f79]">Password Requirements</div>

                            <div class="mt-4 space-y-2.5">
                                <template x-for="item in [
                                    ['8 characters or more', checks.length],
                                    ['Uppercase letter', checks.upper],
                                    ['Lowercase letter', checks.lower],
                                    ['Number', checks.number],
                                    ['Special character (!@#$%^&*)', checks.special]
                                ]" :key="item[0]">
                                    <div class="flex items-center gap-2.5 text-[12px] font-medium" :class="item[1] ? 'text-[#48618f]' : 'text-slate-400'">
                                        <span class="inline-flex h-5 w-5 items-center justify-center rounded-full border"
                                            :class="item[1] ? 'border-[#b7d7ff] bg-white text-[#2563eb]' : 'border-slate-200 bg-slate-100 text-slate-300'">
                                            <svg class="h-3.5 w-3.5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.02 7.08a1 1 0 0 1-1.42.008L3.29 8.87a1 1 0 0 1 1.42-1.408l3.267 3.298 6.314-6.365a1 1 0 0 1 1.414-.006Z" clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                        <span x-text="item[0]"></span>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <div class="shrink-0 text-[13px] font-black" :class="score >= 5 ? 'text-emerald-600' : score >= 3 ? 'text-blue-600' : score >= 1 ? 'text-amber-500' : 'text-[#7286aa]'">
                            <span class="text-[#7286aa]">Strength:</span> <span x-text="strengthLabel"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <label for="update_password_password_confirmation" class="mb-2 block text-[12px] font-black tracking-tight text-[#243f79]">{{ __('Confirm Password') }}</label>
                <div class="relative">
                    <x-text-input id="update_password_password_confirmation" name="password_confirmation" x-bind:type="confirmVisible ? 'text' : 'password'" class="block w-full rounded-[14px] border-[#d9e5f7] bg-white py-3.5 pl-4 pr-[3.25rem] text-[14px] font-semibold text-[#1f376d] shadow-[0_8px_20px_rgba(15,23,42,0.04)] transition focus:border-[#90b7ff] focus:ring-[#dfeeff]" autocomplete="new-password" />
                    <button type="button" @click="confirmVisible = !confirmVisible" class="absolute inset-y-0 right-3 z-10 flex w-6 items-center justify-center text-[#5f7398] transition hover:text-[#274f96] focus:outline-none">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-6 10-6 10 6 10 6-3.5 6-10 6S2 12 2 12Z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>
                </div>
                @if ($errors->updatePassword->has('password_confirmation'))
                    <div class="mt-2 text-sm font-semibold text-rose-600">{{ $errors->updatePassword->first('password_confirmation') }}</div>
                @endif
            </div>

            <div class="flex items-center justify-end pt-1">
                <button type="submit" class="inline-flex h-12 items-center justify-center rounded-[12px] bg-[linear-gradient(135deg,#2563eb_0%,#1650df_100%)] px-8 text-[14px] font-black tracking-tight text-white shadow-[0_16px_32px_rgba(37,99,235,0.24)] transition duration-300 hover:-translate-y-0.5 hover:shadow-[0_20px_40px_rgba(37,99,235,0.32)] hover:ring-4 hover:ring-[#dbe8ff]">
                    {{ __('Update Password') }}
                </button>
            </div>
        </form>
    </div>
</section>
