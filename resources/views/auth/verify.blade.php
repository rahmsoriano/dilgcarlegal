<x-guest-layout>
    @php
        $logo = asset('dilglogo.png');
    @endphp

    <div class="min-h-screen bg-[radial-gradient(circle_at_top_left,rgba(59,130,246,0.18),transparent_34%),radial-gradient(circle_at_bottom_right,rgba(251,191,36,0.18),transparent_34%),linear-gradient(180deg,#e8edf7_0%,#d7dee9_100%)] px-4 py-8">
        <div class="mx-auto flex min-h-[calc(100vh-4rem)] max-w-3xl items-center justify-center">
            <div class="w-full rounded-[2.5rem] border border-white/70 bg-white/85 p-8 shadow-[0_40px_120px_rgba(15,23,42,0.18)] backdrop-blur-xl sm:p-10">
                <div class="mx-auto flex h-24 w-24 items-center justify-center rounded-full bg-white shadow-[0_20px_40px_rgba(15,23,42,0.08)]">
                    <img src="{{ $logo }}" alt="DILG Logo" class="h-16 w-16 object-contain">
                </div>

                <h1 class="mt-6 text-center text-4xl font-extrabold tracking-tight text-slate-900">Verify Your Email</h1>
                <p class="mx-auto mt-4 max-w-2xl text-center text-base leading-7 text-slate-600">
                    Please check your inbox and click the verification link we sent to your email address before logging in to the system.
                </p>

                @if (app()->environment('local') && in_array(config('mail.default'), ['log', 'failover'], true))
                    <div class="mt-6 rounded-[1.5rem] border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800">
                        Local testing mode is active. If no real SMTP is connected, the verification email may be written to <code>storage/logs/laravel.log</code>.
                    </div>
                @endif

                @if (session('status') === 'verification-link-sent')
                    <div class="mt-6 rounded-[1.5rem] border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-semibold text-emerald-700">
                        A new verification link has been sent to your email address.
                    </div>
                @endif

                @if (session('status') === 'verification-link-fallback')
                    <div class="mt-6 rounded-[1.5rem] border border-amber-200 bg-amber-50 px-5 py-4 text-sm font-semibold text-amber-800">
                        Email sending failed, but your account was created. You can continue testing from this page and check <code>storage/logs/laravel.log</code> if needed.
                    </div>
                @endif

                <div class="mt-8 flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:justify-center">
                    <form method="POST" action="{{ route('verification.send') }}">
                        @csrf
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-blue-600 px-8 py-3 text-sm font-black uppercase tracking-[0.22em] text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-500">
                            Resend Verification
                        </button>
                    </form>

                    @if (app()->environment('local'))
                        <form method="POST" action="{{ route('verification.local') }}">
                            @csrf
                            <button
                                type="submit"
                                class="inline-flex w-full items-center justify-center rounded-full px-8 py-3 text-sm font-black uppercase tracking-[0.18em] text-white shadow-lg transition"
                                style="background: linear-gradient(135deg, #059669 0%, #10b981 100%); border: 1px solid #34d399; box-shadow: 0 18px 38px rgba(16, 185, 129, 0.24);"
                            >
                                Verify Locally
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="inline-flex w-full items-center justify-center rounded-full bg-white px-8 py-3 text-sm font-black uppercase tracking-[0.22em] text-slate-700 shadow-sm ring-1 ring-slate-900/10 transition hover:bg-slate-50 hover:text-slate-900">
                            Log Out
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
