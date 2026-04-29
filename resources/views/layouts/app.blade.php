<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'GABAY-Lex') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
        <div id="global-loading-overlay" class="hidden fixed inset-0 z-[2147483200]" aria-hidden="true">
            <div class="global-loading-backdrop absolute inset-0"></div>
            <div class="absolute inset-0 flex items-center justify-center">
                <div class="global-loading-card">
                    <div class="global-loading-spinner" role="status" aria-label="Loading"></div>
                    <div class="global-loading-text">Loading</div>
                </div>
            </div>
        </div>
        <div id="auth-login-modal" class="auth-modal hidden fixed inset-0 z-[2147483100]" aria-hidden="true">
            <div class="auth-modal-backdrop absolute inset-0" data-auth-modal-close></div>
            <div class="absolute inset-0 flex items-center justify-center px-4 py-6 sm:px-6 lg:px-8">
                <div class="auth-modal-panel relative w-full max-w-5xl">
                    <button type="button" class="auth-modal-close" aria-label="Close" data-auth-modal-close>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="h-5 w-5">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                    </button>

                    <div class="auth-login-card overflow-hidden rounded-[36px] bg-white">
                        <div class="grid h-[min(640px,calc(100vh-3.5rem))] lg:grid-cols-[1.08fr_0.92fr]">
                            <section class="auth-grid relative hidden h-full overflow-hidden px-8 py-8 text-white lg:block">
                                <div class="relative flex h-full flex-col">
                                    <div class="flex items-start gap-4">
                                        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-full bg-white shadow-[0_18px_36px_rgba(0,0,0,0.18)]">
                                            <img
                                                src="https://upload.wikimedia.org/wikipedia/commons/c/c9/Department_of_the_Interior_and_Local_Government_%28DILG%29_Seal_-_Logo.svg"
                                                alt="DILG Seal"
                                                class="h-full w-full object-contain"
                                            >
                                        </div>

                                        <div class="min-w-0">
                                            <div class="text-sm font-black uppercase tracking-wide text-white">
                                                Department of the Interior and Local Government
                                            </div>
                                            <div class="mt-1 text-xs font-semibold uppercase tracking-[0.22em] text-white/80">
                                                Cordillera Administrative Region
                                            </div>
                                            <div class="mt-1 text-xs italic text-white/80">
                                                Matino. Mahusay.at Maaasahan.
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-1 flex-col justify-center">
                                        <div class="max-w-sm">
                                            <p class="text-sm font-semibold uppercase tracking-[0.38em] text-cyan-200/90">
                                                <span>GABAY-Lex</span>
                                                <span class="mt-2 block text-[11px] font-semibold normal-case tracking-wide text-white/80">Guidance and Advisory for Better Administration in Law</span>
                                            </p>
                                            <h2 class="mt-5 text-4xl font-semibold leading-tight text-white">
                                                Smart legal support for efficient public service.
                                            </h2>
                                            <p class="mt-4 text-base leading-7 text-blue-100/78">
                                                Instant help, document assistance, and reliable guidance all in one place.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="flex h-full items-center bg-white px-6 py-6 sm:px-10 lg:px-12 overflow-y-auto">
                                <div class="mx-auto w-full max-w-md text-slate-900">
                                    <div data-auth-views-root data-initial-mode="login" class="relative">
                                        <div class="auth-view" data-view="login" aria-hidden="false">
                                            <div class="mx-auto flex h-16 w-16 items-center justify-center overflow-hidden rounded-full bg-white shadow-[0_18px_36px_rgba(59,130,246,0.22)]">
                                                <img
                                                    src="https://upload.wikimedia.org/wikipedia/commons/c/c9/Department_of_the_Interior_and_Local_Government_%28DILG%29_Seal_-_Logo.svg"
                                                    alt="DILG Seal"
                                                    class="h-full w-full object-contain"
                                                >
                                            </div>

                                            <h2 id="auth-modal-title" class="mt-6 text-center text-2xl font-semibold tracking-tight">Welcome Back</h2>
                                            <p class="mt-2 text-center text-sm leading-6 text-slate-500">
                                                Sign in to access your saved legal conversations.
                                            </p>

                                            <x-auth-session-status class="mt-6 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-700" :status="session('status')" />

                                            <form method="POST" action="{{ route('login') }}" class="mt-6 space-y-3.5">
                                                @csrf

                                                <div>
                                                    <label class="mb-2 block text-xs font-medium text-slate-700">Email address</label>
                                                    <div class="auth-input flex items-center gap-3 rounded-full px-5 py-3 transition focus-within:ring-2 focus-within:ring-slate-900/10">
                                                        <svg class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="none">
                                                            <path d="M3.333 5.833A1.667 1.667 0 0 1 5 4.167h10A1.667 1.667 0 0 1 16.667 5.833v8.334A1.667 1.667 0 0 1 15 15.833H5a1.667 1.667 0 0 1-1.667-1.666V5.833Z" stroke="currentColor" stroke-width="1.5"/>
                                                            <path d="m4.167 6.25 5.284 4.224a.833.833 0 0 0 1.04 0l5.276-4.224" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                        </svg>
                                                        <input name="email" type="email" required autocomplete="username" class="min-w-0 w-full border-0 bg-transparent p-0 text-xs text-slate-900 outline-none focus:ring-0" placeholder="Enter your email">
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="mb-2 block text-xs font-medium text-slate-700">Password</label>
                                                    <div class="auth-input flex items-center gap-3 rounded-full px-5 py-3 transition focus-within:ring-2 focus-within:ring-slate-900/10">
                                                        <svg class="h-5 w-5 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="none">
                                                            <path d="M6.667 8.333V6.667a3.333 3.333 0 1 1 6.666 0v1.666" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                            <path d="M5.833 8.333h8.334c.92 0 1.666.746 1.666 1.667v5A1.667 1.667 0 0 1 14.167 16.667H5.833A1.667 1.667 0 0 1 4.167 15v-5c0-.92.746-1.667 1.666-1.667Z" stroke="currentColor" stroke-width="1.5"/>
                                                        </svg>
                                                        <input name="password" type="password" required autocomplete="current-password" class="min-w-0 w-full border-0 bg-transparent p-0 text-xs text-slate-900 outline-none focus:ring-0" placeholder="Enter your password">
                                                    </div>
                                                </div>

                                                <div class="flex items-center justify-between text-xs font-semibold text-slate-600 py-3">
                                                    <label class="inline-flex items-center gap-2">
                                                        <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900/20">
                                                        <span>Remember me</span>
                                                    </label>

                                                    @if (Route::has('password.request'))
                                                        <a href="{{ route('password.request') }}" class="text-slate-500 hover:underline">
                                                            Forgot password?
                                                        </a>
                                                    @endif
                                                </div>

                                                <button type="submit" class="auth-login-button mt-4 w-full rounded-full py-3 text-[12px] font-black uppercase tracking-[0.22em] text-white transition duration-200 focus:outline-none focus:ring-4 focus:ring-slate-900/10">
                                                    Log In
                                                </button>
                                            </form>

                                            <p class="mt-4 text-center text-xs font-semibold text-slate-600">
                                                Don’t have an account?
                                                <button type="button" data-auth-switch="register" class="text-slate-900 hover:underline">Sign Up</button>
                                            </p>
                                        </div>

                                        <div class="auth-view absolute inset-0" data-view="register" aria-hidden="true">
                                            <div class="mx-auto flex h-16 w-16 items-center justify-center overflow-hidden rounded-full bg-white shadow-[0_18px_36px_rgba(59,130,246,0.22)]">
                                                <img
                                                    src="https://upload.wikimedia.org/wikipedia/commons/c/c9/Department_of_the_Interior_and_Local_Government_%28DILG%29_Seal_-_Logo.svg"
                                                    alt="DILG Seal"
                                                    class="h-full w-full object-contain"
                                                >
                                            </div>

                                            <h2 class="mt-5 text-center text-xl font-semibold tracking-tight">Create Account</h2>
                                            <p class="mt-1.5 text-center text-xs leading-5 text-slate-500">
                                                Sign up to start your legal research workspace.
                                            </p>

                                            <form method="POST" action="{{ route('register') }}" class="mt-5 space-y-3">
                                                @csrf

                                                <div class="flex flex-col gap-3 sm:flex-row">
                                                    <div class="sm:flex-1">
                                                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">First Name</label>
                                                        <div class="auth-input flex items-center gap-2.5 rounded-full px-5 py-3 transition focus-within:ring-2 focus-within:ring-slate-900/10">
                                                            <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="none">
                                                                <path d="M10 10a3.5 3.5 0 1 0-3.5-3.5A3.5 3.5 0 0 0 10 10Z" stroke="currentColor" stroke-width="1.5"/>
                                                                <path d="M3.333 16.667c0-2.577 3.134-4.667 6.667-4.667s6.667 2.09 6.667 4.667" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                            </svg>
                                                            <input name="first_name" type="text" value="{{ old('first_name') }}" required autocomplete="given-name" class="min-w-0 w-full border-0 bg-transparent p-0 text-xs text-slate-900 outline-none focus:ring-0" placeholder="First name">
                                                        </div>
                                                    </div>

                                                    <div class="sm:flex-1">
                                                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Last Name</label>
                                                        <div class="auth-input flex items-center gap-2.5 rounded-full px-5 py-3 transition focus-within:ring-2 focus-within:ring-slate-900/10">
                                                            <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="none">
                                                                <path d="M10 10a3.5 3.5 0 1 0-3.5-3.5A3.5 3.5 0 0 0 10 10Z" stroke="currentColor" stroke-width="1.5"/>
                                                                <path d="M3.333 16.667c0-2.577 3.134-4.667 6.667-4.667s6.667 2.09 6.667 4.667" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                            </svg>
                                                            <input name="last_name" type="text" value="{{ old('last_name') }}" required autocomplete="family-name" class="min-w-0 w-full border-0 bg-transparent p-0 text-xs text-slate-900 outline-none focus:ring-0" placeholder="Last name">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="flex flex-col gap-3 sm:flex-row">
                                                    <div class="sm:flex-[0.85]">
                                                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Birthday</label>
                                                        <div class="auth-input flex items-center gap-2.5 rounded-full px-5 py-3 transition focus-within:ring-2 focus-within:ring-slate-900/10">
                                                            <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="none">
                                                                <path d="M6.667 3.333v2.5M13.333 3.333v2.5" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                                <path d="M3.333 7.5h13.334" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                                <path d="M4.167 5.833h11.666c.92 0 1.667.746 1.667 1.667v8.333c0 .92-.747 1.667-1.667 1.667H4.167c-.92 0-1.667-.747-1.667-1.667V7.5c0-.92.747-1.667 1.667-1.667Z" stroke="currentColor" stroke-width="1.5"/>
                                                            </svg>
                                                            <input name="birthday" type="date" value="{{ old('birthday') }}" required class="min-w-0 w-full border-0 bg-transparent p-0 text-xs text-slate-900 outline-none focus:ring-0">
                                                        </div>
                                                    </div>

                                                    <div class="sm:flex-1">
                                                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Email address</label>
                                                        <div class="auth-input flex items-center gap-2.5 rounded-full px-5 py-3 transition focus-within:ring-2 focus-within:ring-slate-900/10">
                                                            <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="none">
                                                                <path d="M3.333 5.833A1.667 1.667 0 0 1 5 4.167h10A1.667 1.667 0 0 1 16.667 5.833v8.334A1.667 1.667 0 0 1 15 15.833H5a1.667 1.667 0 0 1-1.667-1.666V5.833Z" stroke="currentColor" stroke-width="1.5"/>
                                                                <path d="m4.167 6.25 5.284 4.224a.833.833 0 0 0 1.04 0l5.276-4.224" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                            </svg>
                                                            <input name="email" type="email" value="{{ old('email') }}" required autocomplete="username" class="min-w-0 w-full border-0 bg-transparent p-0 text-xs text-slate-900 outline-none focus:ring-0" placeholder="Email address">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="flex flex-col gap-3 sm:flex-row">
                                                    <div class="sm:flex-1">
                                                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Password</label>
                                                        <div class="auth-input flex items-center gap-2.5 rounded-full px-5 py-3 transition focus-within:ring-2 focus-within:ring-slate-900/10">
                                                            <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="none">
                                                                <path d="M6.667 8.333V6.667a3.333 3.333 0 1 1 6.666 0v1.666" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                                <path d="M5.833 8.333h8.334c.92 0 1.666.746 1.666 1.667v5A1.667 1.667 0 0 1 14.167 16.667H5.833A1.667 1.667 0 0 1 4.167 15v-5c0-.92.746-1.667 1.666-1.667Z" stroke="currentColor" stroke-width="1.5"/>
                                                            </svg>
                                                            <input name="password" type="password" required autocomplete="new-password" class="min-w-0 w-full border-0 bg-transparent p-0 text-xs text-slate-900 outline-none focus:ring-0" placeholder="Password">
                                                        </div>
                                                    </div>

                                                    <div class="sm:flex-1">
                                                        <label class="mb-1.5 block text-xs font-semibold text-slate-700">Confirm Password</label>
                                                        <div class="auth-input flex items-center gap-2.5 rounded-full px-5 py-3 transition focus-within:ring-2 focus-within:ring-slate-900/10">
                                                            <svg class="h-4 w-4 shrink-0 text-slate-400" viewBox="0 0 20 20" fill="none">
                                                                <path d="M6.667 8.333V6.667a3.333 3.333 0 1 1 6.666 0v1.666" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                                                                <path d="M5.833 8.333h8.334c.92 0 1.666.746 1.666 1.667v5A1.667 1.667 0 0 1 14.167 16.667H5.833A1.667 1.667 0 0 1 4.167 15v-5c0-.92.746-1.667 1.666-1.667Z" stroke="currentColor" stroke-width="1.5"/>
                                                            </svg>
                                                            <input name="password_confirmation" type="password" required autocomplete="new-password" class="min-w-0 w-full border-0 bg-transparent p-0 text-xs text-slate-900 outline-none focus:ring-0" placeholder="Confirm">
                                                        </div>
                                                    </div>
                                                </div>

                                                <button type="submit" class="auth-login-button mt-3.5 w-full rounded-full py-3 text-[11px] font-black uppercase tracking-[0.22em] text-white transition duration-200 focus:outline-none focus:ring-4 focus:ring-slate-900/10">
                                                    Sign Up
                                                </button>
                                            </form>

                                            <p class="mt-3.5 text-center text-xs font-semibold text-slate-600 py-3">
                                                Already have an account?
                                                <button type="button" data-auth-switch="login" class="text-slate-900 hover:underline">Log In</button>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
