<section x-data="{
    showCurrent: false,
    showNew: false,
    showConfirm: false,
    password: '',
    checks() {
        return {
            length: this.password.length >= 8,
            upper: /[A-Z]/.test(this.password),
            lower: /[a-z]/.test(this.password),
            number: /[0-9]/.test(this.password),
            special: /[^A-Za-z0-9]/.test(this.password),
        };
    },
    strength() {
        const values = Object.values(this.checks());
        return values.filter(Boolean).length;
    },
    strengthLabel() {
        return ['Weak', 'Weak', 'Fair', 'Good', 'Strong', 'Strong'][this.strength()];
    }
}">
    <div class="flex items-start justify-between gap-4">
        <button
            type="button"
            @click="securityOpen = securityOpen === 'password' ? null : 'password'"
            class="flex min-w-0 flex-1 items-start gap-4 text-left"
            :aria-expanded="(securityOpen === 'password').toString()"
            aria-controls="update-password-panel"
        >
            <div class="flex h-14 w-14 items-center justify-center rounded-full bg-[#edf4ff] text-[#2563eb] shadow-[inset_0_1px_0_rgba(255,255,255,0.85)]">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8 10V8a4 4 0 118 0v2M6 10h12v10H6V10z" />
                </svg>
            </div>
            <div class="min-w-0">
                <h2 class="text-[1rem] font-black text-[#14214d]">{{ __('Update Password') }}</h2>
                <p class="mt-1 text-[13px] font-medium leading-6 text-[#58698d]">{{ __('Manage and update your password securely') }}</p>
            </div>
        </button>

        <div class="flex items-center gap-3">
            @if (session('status') === 'password-updated')
                <div class="rounded-full bg-emerald-50 px-3 py-1.5 text-[11px] font-black uppercase tracking-[0.16em] text-emerald-700 ring-1 ring-emerald-200">
                    Saved
                </div>
            @endif
            <button
                type="button"
                @click="securityOpen = securityOpen === 'password' ? null : 'password'"
                class="flex h-10 w-10 items-center justify-center rounded-full border border-[#dbe6fb] bg-white text-[#5b6b8f] shadow-sm transition hover:bg-[#f5f9ff]"
                :aria-expanded="(securityOpen === 'password').toString()"
                aria-controls="update-password-panel"
            >
                <svg class="h-4 w-4 transition duration-300" :class="securityOpen === 'password' ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                </svg>
            </button>
        </div>
    </div>

    <div
        id="update-password-panel"
        x-ref="panel"
        class="overflow-hidden transition-all duration-300 ease-out"
        :style="securityOpen === 'password' ? `max-height:${$refs.panel.scrollHeight}px;opacity:1;transform:translateY(0)` : 'max-height:0px;opacity:0;transform:translateY(-6px)'"
    >
    <form method="post" action="{{ route('password.update') }}" class="mt-5 space-y-5">
        @csrf
        @method('put')

        <div class="grid gap-4 xl:grid-cols-[1fr_1fr_0.72fr] xl:items-start">
            <div>
                <label for="update_password_current_password" class="mb-2.5 block text-[13px] font-medium text-[#44557d]">{{ __('Current Password') }}</label>
                <div class="relative">
                    <x-text-input id="update_password_current_password" name="current_password" x-bind:type="showCurrent ? 'text' : 'password'" class="block h-[44px] w-full rounded-xl border-[#d5e2fb] bg-white px-4 pr-12 text-[14px] font-semibold text-[#15224a] shadow-sm focus:border-[#8fb3ff] focus:ring-4 focus:ring-[#dbe8ff]" autocomplete="current-password" />
                    <button type="button" @click="showCurrent = !showCurrent" class="absolute inset-y-0 right-0 flex items-center pr-5 text-[#7c8db0] transition hover:text-[#255de8]">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>
                </div>
                @if ($errors->updatePassword->has('current_password'))
                    <div class="mt-2 text-sm font-semibold text-rose-600">{{ $errors->updatePassword->first('current_password') }}</div>
                @endif
            </div>

            <div class="space-y-3">
                <label for="update_password_password" class="mb-2.5 block text-[13px] font-medium text-[#44557d]">{{ __('New Password') }}</label>
                <div class="relative">
                    <x-text-input id="update_password_password" name="password" x-model="password" x-bind:type="showNew ? 'text' : 'password'" class="block h-[44px] w-full rounded-xl border-[#d5e2fb] bg-white px-4 pr-12 text-[14px] font-semibold text-[#15224a] shadow-sm focus:border-[#8fb3ff] focus:ring-4 focus:ring-[#dbe8ff]" autocomplete="new-password" />
                    <button type="button" @click="showNew = !showNew" class="absolute inset-y-0 right-0 flex items-center pr-5 text-[#7c8db0] transition hover:text-[#255de8]">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                    </button>
                </div>
                @if ($errors->updatePassword->has('password'))
                    <div class="mt-2 text-sm font-semibold text-rose-600">{{ $errors->updatePassword->first('password') }}</div>
                @endif

                <div class="flex flex-wrap items-center gap-x-3 gap-y-2 px-1">
                    <div class="flex items-center gap-2">
                        <div class="h-1.5 w-9 rounded-full transition" :class="strength() >= 1 ? 'bg-[#2563eb]' : 'bg-[#e5ebf7]'"></div>
                        <div class="h-1.5 w-9 rounded-full transition" :class="strength() >= 2 ? 'bg-[#4f8cff]' : 'bg-[#e5ebf7]'"></div>
                        <div class="h-1.5 w-9 rounded-full transition" :class="strength() >= 3 ? 'bg-[#9ac0ff]' : 'bg-[#e5ebf7]'"></div>
                        <div class="h-1.5 w-9 rounded-full transition" :class="strength() >= 4 ? 'bg-[#d7dfef]' : 'bg-[#e5ebf7]'"></div>
                    </div>
                    <div class="text-[11px] font-bold leading-4 text-[#16a34a]" x-show="password.length">
                        Strength:
                        <span x-text="strengthLabel()"></span>
                    </div>
                </div>
            </div>

            <div class="pt-[1.7rem]">
                <div class="space-y-2.5">
                    <div class="flex items-center gap-2 text-[12px] font-medium text-[#33476e]">
                        <span class="text-[15px]" :class="checks().length ? 'text-emerald-500' : 'text-[#9ca9c2]'">&#10003;</span>
                        <span>8 characters or more</span>
                    </div>
                    <div class="flex items-center gap-2 text-[12px] font-medium text-[#33476e]">
                        <span class="text-[15px]" :class="checks().upper ? 'text-emerald-500' : 'text-[#9ca9c2]'">&#10003;</span>
                        <span>Uppercase letter</span>
                    </div>
                    <div class="flex items-center gap-2 text-[12px] font-medium text-[#33476e]">
                        <span class="text-[15px]" :class="checks().lower ? 'text-emerald-500' : 'text-[#9ca9c2]'">&#10003;</span>
                        <span>Lowercase letter</span>
                    </div>
                    <div class="flex items-center gap-2 text-[12px] font-medium text-[#33476e]">
                        <span class="text-[15px]" :class="checks().number ? 'text-emerald-500' : 'text-[#9ca9c2]'">&#10003;</span>
                        <span>Number</span>
                    </div>
                    <div class="flex items-center gap-2 text-[12px] font-medium text-[#33476e]">
                        <span class="text-[15px]" :class="checks().special ? 'text-emerald-500' : 'text-[#9ca9c2]'">&#10003;</span>
                        <span>Special character</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-1">
            <label for="update_password_password_confirmation" class="mb-2.5 block text-[13px] font-medium text-[#44557d]">{{ __('Confirm Password') }}</label>
            <div class="relative">
                <x-text-input id="update_password_password_confirmation" name="password_confirmation" x-bind:type="showConfirm ? 'text' : 'password'" class="block h-[44px] w-full rounded-xl border-[#d5e2fb] bg-white px-4 pr-12 text-[14px] font-semibold text-[#15224a] shadow-sm focus:border-[#8fb3ff] focus:ring-4 focus:ring-[#dbe8ff]" autocomplete="new-password" />
                <button type="button" @click="showConfirm = !showConfirm" class="absolute inset-y-0 right-0 flex items-center pr-5 text-[#7384a5] transition hover:text-[#255de8]">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6z" />
                        <circle cx="12" cy="12" r="3" />
                    </svg>
                </button>
            </div>
            @if ($errors->updatePassword->has('password_confirmation'))
                <div class="mt-2 text-sm font-semibold text-rose-600">{{ $errors->updatePassword->first('password_confirmation') }}</div>
            @endif
        </div>

        <div class="flex justify-end pt-1">
            <button type="submit" class="inline-flex h-[44px] items-center justify-center gap-2 rounded-[0.9rem] bg-[#1f5ff0] px-8 text-[14px] font-bold text-white shadow-[0_14px_26px_rgba(37,99,235,0.22)] transition hover:-translate-y-0.5 hover:bg-[#184fd0]">
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-8 8a1 1 0 01-1.415 0l-4-4a1 1 0 111.415-1.42l3.292 3.29 7.292-7.29a1 1 0 011.416 0z" clip-rule="evenodd" />
                </svg>
                {{ __('Save Changes') }}
            </button>
        </div>
    </form>
    </div>
</section>
