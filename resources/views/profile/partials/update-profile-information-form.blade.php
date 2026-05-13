<section>
    <header class="flex items-start justify-between gap-5">
        <div class="flex gap-4">
            <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-full border border-[#dfebfb] bg-[#f5f9ff] text-[#2563eb]">
                <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 20a7 7 0 0 1 14 0" />
                </svg>
            </div>
            <div>
                <h2 class="text-[18px] font-black tracking-tight text-[#182f69]">{{ __('Profile Information') }}</h2>
                <p class="mt-1 text-[14px] font-medium text-[#5c7197]">{{ __("Update your account's profile information and email address.") }}</p>
            </div>
        </div>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form x-ref="profileForm" method="post" action="{{ route('profile.update') }}" class="mt-7 space-y-6">
        @csrf
        @method('patch')

        <div class="grid gap-5 lg:grid-cols-2">
            <div>
                <label for="name" class="mb-2.5 block text-[12px] font-black tracking-tight text-[#425a86]">{{ __('Full Name') }}</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#5f77a1]">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 20a7 7 0 0 1 14 0" />
                        </svg>
                    </span>
                    <x-text-input x-ref="profileNameInput" id="name" name="name" type="text" x-bind:disabled="!profileEditMode" x-bind:readonly="!profileEditMode" class="block w-full rounded-2xl border-[#d7e5f8] bg-white py-3.5 pl-12 pr-4 text-[14px] font-semibold text-[#1f376d] shadow-[0_8px_20px_rgba(15,23,42,0.04)] transition focus:border-[#90b7ff] focus:ring-[#dfeeff] disabled:cursor-default disabled:border-[#e1eaf7] disabled:bg-[#f8fbff] disabled:text-[#6d81a6] disabled:shadow-none" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                </div>
                @if ($errors->has('name'))
                    <div class="mt-2 text-sm font-semibold text-rose-600">{{ $errors->first('name') }}</div>
                @endif
            </div>

            <div>
                <label for="email" class="mb-2.5 block text-[12px] font-black tracking-tight text-[#425a86]">{{ __('Email Address') }}</label>
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-4 flex items-center text-[#5f77a1]">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7.5 12 13l8-5.5" />
                            <rect x="3" y="5" width="18" height="14" rx="2.5" />
                        </svg>
                    </span>
                    <x-text-input id="email" name="email" type="email" x-bind:disabled="!profileEditMode" x-bind:readonly="!profileEditMode" class="block w-full rounded-2xl border-[#d7e5f8] bg-white py-3.5 pl-12 pr-4 text-[14px] font-semibold text-[#1f376d] shadow-[0_8px_20px_rgba(15,23,42,0.04)] transition focus:border-[#90b7ff] focus:ring-[#dfeeff] disabled:cursor-default disabled:border-[#e1eaf7] disabled:bg-[#f8fbff] disabled:text-[#6d81a6] disabled:shadow-none" :value="old('email', $user->email)" required autocomplete="username" />
                </div>
                @if ($errors->has('email'))
                    <div class="mt-2 text-sm font-semibold text-rose-600">{{ $errors->first('email') }}</div>
                @endif
            </div>
        </div>

        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="rounded-[1.35rem] border border-[#cfe0ff] bg-[linear-gradient(180deg,#f8fbff_0%,#f3f7ff_100%)] px-5 py-5">
                <div class="flex gap-4">
                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full bg-[#eaf2ff] text-[#2563eb]">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 7.5 12 13l8-5.5" />
                            <rect x="3" y="5" width="18" height="14" rx="2.5" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-[15px] font-black tracking-tight text-[#182f69]">Unverified Email</div>
                        <div class="mt-1 text-[14px] font-medium text-[#5c7197]">
                            {{ __('Your email address is unverified.') }}
                            <button form="send-verification" type="submit" class="font-black text-[#1557e5] underline underline-offset-4 hover:text-[#0f46bc]">
                                {{ __('Re-send verification email') }}
                            </button>
                        </div>
                        @if (session('status') === 'verification-link-sent')
                            <div class="mt-2 text-sm font-semibold text-emerald-700">
                                {{ __('A new verification link has been sent to your email address.') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif

        <div x-cloak x-show="profileEditMode" x-transition.opacity class="flex flex-col items-stretch justify-end gap-3 sm:flex-row sm:items-center">
            <button type="button" @click="cancelProfileEdit()" class="inline-flex h-12 items-center justify-center rounded-2xl border border-[#d7e1f0] bg-white px-6 text-[14px] font-black tracking-tight text-[#5f7398] shadow-[0_8px_20px_rgba(15,23,42,0.04)] transition hover:bg-[#f8fbff] hover:text-[#425a86]">
                Cancel
            </button>
            <button type="submit" class="inline-flex h-12 min-w-[170px] items-center justify-center gap-2 rounded-2xl border border-[#2563eb] px-7 text-[14px] font-black tracking-tight text-white shadow-[0_18px_36px_rgba(37,99,235,0.20)] transition hover:-translate-y-0.5 hover:shadow-[0_20px_40px_rgba(37,99,235,0.24)]" style="background: linear-gradient(135deg, #2563eb 0%, #346dff 100%); color: #ffffff;">
                <svg class="h-4.5 w-4.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 0 1 .006 1.414l-7.02 7.08a1 1 0 0 1-1.42.008L3.29 8.87a1 1 0 0 1 1.42-1.408l3.267 3.298 6.314-6.365a1 1 0 0 1 1.414-.006Z" clip-rule="evenodd" />
                </svg>
                {{ __('Save Changes') }}
            </button>
        </div>
    </form>
</section>
