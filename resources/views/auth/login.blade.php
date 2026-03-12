<x-guest-layout>
    <style>
        .auth-login-shell {
            background:
                radial-gradient(circle at top left, rgba(125, 211, 252, 0.34), transparent 24%),
                radial-gradient(circle at top right, rgba(59, 130, 246, 0.26), transparent 28%),
                linear-gradient(135deg, #4cbaf0 0%, #2d6fe8 100%);
        }

        .auth-login-card {
            box-shadow: 0 36px 120px rgba(9, 15, 55, 0.28);
        }

        .auth-grid {
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.06) 1px, transparent 1px);
            background-size: 26px 26px;
        }

        .bot-glow {
            box-shadow:
                0 0 0 10px rgba(96, 165, 250, 0.08),
                0 0 0 22px rgba(96, 165, 250, 0.05),
                0 28px 80px rgba(34, 211, 238, 0.18);
        }

        .ring-orbit {
            border: 4px solid rgba(99, 102, 241, 0.85);
            box-shadow: 0 0 30px rgba(99, 102, 241, 0.12);
        }

        .auth-input {
            background: rgba(34, 41, 84, 0.96);
            border: 1px solid rgba(96, 165, 250, 0.14);
        }

        .auth-input::placeholder {
            color: rgba(191, 219, 254, 0.55);
        }
    </style>

    <div class="auth-login-shell flex min-h-screen items-center justify-center px-4 py-8 sm:px-6 lg:px-8">
        <div class="w-full max-w-6xl">
            <div class="mb-6 text-center">
                <a href="/" class="inline-flex items-center justify-center">
                    <x-application-logo class="h-16 w-16 fill-current text-white/95 drop-shadow-[0_10px_24px_rgba(15,23,42,0.25)]" />
                </a>
                <h1 class="mt-5 text-4xl font-semibold tracking-tight text-white sm:text-5xl">Log In</h1>
                <p class="mt-3 text-base text-blue-100/90">Secure access to your legal AI workspace.</p>
            </div>

            <div class="auth-login-card overflow-hidden rounded-[36px] bg-[#0e1547]">
                <div class="grid min-h-[720px] lg:grid-cols-[1.08fr_0.92fr]">
                    <section class="auth-grid relative hidden overflow-hidden px-8 py-10 text-white lg:block xl:px-12 xl:py-12">
                        <div class="absolute -left-10 top-16 h-48 w-48 rounded-full ring-orbit"></div>
                        <div class="absolute left-12 top-48 h-28 w-28 rounded-full ring-orbit"></div>
                        <div class="absolute left-44 top-28 h-16 w-16 rounded-full bg-cyan-300/80 blur-[2px]"></div>
                        <div class="absolute left-52 top-40 h-12 w-12 rounded-full bg-sky-200/70 blur-[1px]"></div>

                        <div class="relative flex h-full flex-col justify-between">
                            <div class="max-w-sm">
                                <p class="text-sm font-semibold uppercase tracking-[0.38em] text-cyan-200/90">DILG Legal AI</p>
                                <h2 class="mt-6 text-5xl font-semibold leading-tight text-white">Professional legal support, presented like a product.</h2>
                                <p class="mt-5 text-lg leading-8 text-blue-100/78">A cleaner sign-in experience for your chat, research, and opinion retrieval workspace.</p>
                            </div>

                            <div class="relative mx-auto mt-8 flex w-full max-w-xl items-end justify-center">
                                <div class="bot-glow relative flex h-[360px] w-[280px] flex-col items-center justify-center rounded-[42px] bg-gradient-to-b from-slate-100 to-sky-100">
                                    <div class="absolute -top-16 h-24 w-24 rounded-full bg-gradient-to-b from-sky-200 to-blue-400 shadow-[0_18px_36px_rgba(96,165,250,0.4)]"></div>
                                    <div class="absolute -top-9 h-12 w-12 rounded-full border-[7px] border-cyan-100"></div>

                                    <div class="absolute left-[-42px] top-[126px] h-28 w-16 rotate-[24deg] rounded-[30px] bg-gradient-to-b from-slate-100 to-sky-300"></div>
                                    <div class="absolute right-[-42px] top-[126px] h-28 w-16 -rotate-[24deg] rounded-[30px] bg-gradient-to-b from-slate-100 to-sky-300"></div>
                                    <div class="absolute bottom-[-34px] left-[58px] h-28 w-16 rotate-[12deg] rounded-[30px] bg-gradient-to-b from-slate-100 to-sky-300"></div>
                                    <div class="absolute bottom-[-34px] right-[58px] h-28 w-16 -rotate-[12deg] rounded-[30px] bg-gradient-to-b from-slate-100 to-sky-300"></div>

                                    <div class="relative z-10 flex h-40 w-[210px] items-center justify-center rounded-[34px] bg-gradient-to-b from-slate-100 to-slate-300 shadow-[0_24px_64px_rgba(15,23,42,0.28)]">
                                        <div class="flex h-[116px] w-[168px] items-center justify-center rounded-[26px] bg-gradient-to-b from-slate-950 to-slate-800">
                                            <div class="flex items-center gap-8">
                                                <span class="h-5 w-10 rounded-full border-b-[6px] border-cyan-200"></span>
                                                <span class="h-5 w-10 rounded-full border-b-[6px] border-cyan-200"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="absolute bottom-10 z-0 h-40 w-44 rounded-[38px] bg-gradient-to-b from-sky-100 to-sky-300"></div>
                                    <div class="absolute bottom-16 h-16 w-16 rounded-full bg-cyan-200 shadow-[0_0_0_10px_rgba(103,232,249,0.25)]"></div>
                                </div>

                                <div class="absolute -left-4 bottom-28 rounded-2xl bg-sky-400 px-5 py-3 text-base text-white shadow-[0_16px_34px_rgba(56,189,248,0.32)]">
                                    Hello, can you help me?
                                </div>

                                <div class="absolute -right-2 bottom-2 max-w-[230px] rounded-2xl bg-[#123ea6] px-5 py-4 text-sm text-blue-50 shadow-[0_18px_32px_rgba(16,54,145,0.38)]">
                                    <div class="font-semibold text-cyan-300">Buddy!</div>
                                    <div class="mt-2 text-[15px] leading-6">Sure, I am ready to help you research and respond.</div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="flex items-center bg-[#121843] px-6 py-8 sm:px-10 lg:px-12 xl:px-14">
                        <div class="mx-auto w-full max-w-md text-white">
                            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-gradient-to-b from-sky-300 to-blue-500 shadow-[0_18px_36px_rgba(59,130,246,0.35)]">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-[#0f1647] text-sm font-bold uppercase tracking-[0.22em] text-cyan-200">AI</div>
                            </div>

                            <h2 class="mt-8 text-center text-3xl font-semibold tracking-tight">Welcome Back</h2>
                            <p class="mt-3 text-center text-base leading-7 text-blue-100/72">Sign in to continue with Buddy and access your saved legal conversations.</p>

                            <x-auth-session-status class="mt-6 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100" :status="session('status')" />

                            <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-4">
                                @csrf

                                <div>
                                    <label for="email" class="mb-2 block text-sm font-medium text-blue-100/88">Email address</label>
                                    <div class="auth-input flex items-center gap-3 rounded-2xl px-4 py-3 shadow-[inset_0_1px_0_rgba(255,255,255,0.04)]">
                                        <svg class="h-5 w-5 shrink-0 text-cyan-300/85" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M3.333 5.833A1.667 1.667 0 0 1 5 4.167h10A1.667 1.667 0 0 1 16.667 5.833v8.334A1.667 1.667 0 0 1 15 15.833H5a1.667 1.667 0 0 1-1.667-1.666V5.833Z" stroke="currentColor" stroke-width="1.5"/>
                                            <path d="m4.167 6.25 5.284 4.224a.833.833 0 0 0 1.04 0l5.276-4.224" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                        </svg>
                                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="w-full border-0 bg-transparent p-0 text-base text-white placeholder:text-blue-100/55 focus:ring-0" placeholder="Enter your email">
                                    </div>
                                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-rose-300" />
                                </div>

                                <div>
                                    <label for="password" class="mb-2 block text-sm font-medium text-blue-100/88">Password</label>
                                    <div class="auth-input flex items-center gap-3 rounded-2xl px-4 py-3 shadow-[inset_0_1px_0_rgba(255,255,255,0.04)]">
                                        <svg class="h-5 w-5 shrink-0 text-cyan-300/85" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M6.667 8.333V6.667a3.333 3.333 0 1 1 6.666 0v1.666" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                            <path d="M5.833 8.333h8.334c.92 0 1.666.746 1.666 1.667v5A1.667 1.667 0 0 1 14.167 16.667H5.833A1.667 1.667 0 0 1 4.167 15v-5c0-.92.746-1.667 1.666-1.667Z" stroke="currentColor" stroke-width="1.5"/>
                                        </svg>
                                        <input id="password" name="password" type="password" required autocomplete="current-password" class="w-full border-0 bg-transparent p-0 text-base text-white placeholder:text-blue-100/55 focus:ring-0" placeholder="Enter your password">
                                    </div>
                                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-rose-300" />
                                </div>

                                <div class="flex flex-col gap-3 pt-1 sm:flex-row sm:items-center sm:justify-between">
                                    <label for="remember_me" class="inline-flex items-center gap-3 text-sm text-blue-100/78">
                                        <input id="remember_me" type="checkbox" class="rounded border-white/20 bg-transparent text-sky-400 shadow-sm focus:ring-sky-400" name="remember">
                                        <span>Remember me</span>
                                    </label>

                                    @if (Route::has('password.request'))
                                        <a class="text-sm font-medium text-cyan-300 transition hover:text-cyan-200" href="{{ route('password.request') }}">
                                            Forgot password?
                                        </a>
                                    @endif
                                </div>

                                <button type="submit" class="mt-2 inline-flex w-full items-center justify-center rounded-2xl bg-gradient-to-r from-sky-400 to-blue-500 px-4 py-3.5 text-sm font-semibold text-white shadow-[0_18px_34px_rgba(59,130,246,0.34)] transition hover:-translate-y-0.5 hover:from-sky-300 hover:to-blue-400">
                                    Log In
                                </button>
                            </form>

                            @if (Route::has('register'))
                                <p class="mt-6 text-center text-sm text-blue-100/65">
                                    Don’t have an account?
                                    <a href="{{ route('register') }}" class="font-semibold text-cyan-300 transition hover:text-cyan-200">Create one</a>
                                </p>
                            @endif

                            <div class="mt-8 flex items-center gap-4 text-blue-100/45">
                                <div class="h-px flex-1 bg-white/10"></div>
                                <span class="text-xs uppercase tracking-[0.28em]">Secure access</span>
                                <div class="h-px flex-1 bg-white/10"></div>
                            </div>

                            <div class="mt-8 flex items-center justify-between text-xs font-medium text-blue-100/58">
                                <span>Terms of Service</span>
                                <span>Privacy Policy</span>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
