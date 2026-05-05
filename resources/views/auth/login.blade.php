<x-guest-layout>
    @php
        $logo = asset('dilglogo.png');
        $hero = asset('images/login-hero-exact.png');
    @endphp

    <style>
        :root {
            color-scheme: light;
        }

        html,
        body {
            min-height: 100%;
            margin: 0;
            background: #b8bcc4;
        }

        .login-refresh-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 18px;
            font-family: 'Figtree', sans-serif;
        }

        .login-refresh-shell {
            width: min(1260px, calc(100vw - 36px));
            min-height: min(860px, calc(100vh - 36px));
            display: grid;
            grid-template-columns: minmax(0, 1.16fr) minmax(460px, 0.84fr);
            overflow: hidden;
            border-radius: 42px;
            background: #ffffff;
            box-shadow: 0 38px 90px rgba(15, 23, 42, 0.12);
        }

        .login-refresh-brand {
            position: relative;
            overflow: hidden;
            padding: 40px 40px 34px;
            color: #ffffff;
            background:
                radial-gradient(circle at 18% 14%, rgba(255, 255, 255, 0.12), transparent 22%),
                linear-gradient(100deg, #33226e 0%, #3158c7 27%, #c44746 68%, #efc53d 100%);
        }

        .login-refresh-brand::before {
            content: '';
            position: absolute;
            left: 32px;
            right: 180px;
            bottom: 26px;
            height: 92px;
            opacity: 0.15;
            pointer-events: none;
            background:
                radial-gradient(120% 100% at 0% 100%, transparent 58%, rgba(255,255,255,0.16) 58.5%, transparent 59.6%),
                radial-gradient(116% 96% at 0% 100%, transparent 63%, rgba(255,255,255,0.14) 63.5%, transparent 64.6%),
                radial-gradient(112% 92% at 0% 100%, transparent 68%, rgba(255,255,255,0.12) 68.5%, transparent 69.6%);
        }

        .login-refresh-dots {
            position: absolute;
            right: 38px;
            top: 238px;
            width: 118px;
            height: 118px;
            opacity: 0.38;
            pointer-events: none;
            background-image: radial-gradient(circle, rgba(255,255,255,0.92) 1.3px, transparent 1.6px);
            background-size: 24px 24px;
        }

        .login-refresh-brand-top {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: flex-start;
            gap: 16px;
        }

        .login-refresh-brand-seal {
            width: 74px;
            height: 74px;
            flex: 0 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.14);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,0.28),
                0 16px 34px rgba(9, 19, 61, 0.2);
        }

        .login-refresh-brand-seal img,
        .login-refresh-panel-logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .login-refresh-brand-heading {
            max-width: 520px;
        }

        .login-refresh-brand-title {
            margin: 0;
            font-size: clamp(1.45rem, 1.25rem + 0.82vw, 2rem);
            line-height: 1.1;
            font-weight: 800;
            letter-spacing: -0.03em;
            text-transform: uppercase;
        }

        .login-refresh-brand-region {
            margin-top: 8px;
            font-size: 0.84rem;
            line-height: 1.5;
            font-weight: 600;
            letter-spacing: 0.36em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.88);
        }

        .login-refresh-brand-motto {
            margin-top: 8px;
            font-size: 0.9rem;
            line-height: 1.5;
            font-style: italic;
            color: rgba(255, 255, 255, 0.82);
        }

        .login-refresh-brand-main {
            position: relative;
            z-index: 2;
            height: calc(100% - 120px);
            display: flex;
            align-items: flex-end;
        }

        .login-refresh-brand-copy {
            width: min(360px, 54%);
            padding-top: 150px;
        }

        .login-refresh-brand-product {
            margin: 0;
            font-size: 0.98rem;
            line-height: 1.5;
            font-weight: 700;
            letter-spacing: 0.34em;
            text-transform: uppercase;
            color: #cce5ff;
        }

        .login-refresh-brand-product-copy {
            margin-top: 18px;
            font-size: 1rem;
            line-height: 1.7;
            color: rgba(255, 255, 255, 0.92);
        }

        .login-refresh-brand-headline {
            margin: 28px 0 0;
            font-size: clamp(2.35rem, 1.7rem + 1.55vw, 4.2rem);
            line-height: 1.06;
            font-weight: 800;
            letter-spacing: -0.05em;
            color: #ffffff;
            text-wrap: balance;
        }

        .login-refresh-brand-subtitle {
            margin: 26px 0 0;
            font-size: 1rem;
            line-height: 1.75;
            color: rgba(255, 255, 255, 0.92);
        }

        .login-refresh-hero {
            position: absolute;
            right: 0;
            bottom: 28px;
            width: min(410px, 55%);
            pointer-events: none;
            filter: drop-shadow(0 24px 40px rgba(15, 23, 42, 0.22));
        }

        .login-refresh-hero img {
            width: 100%;
            height: auto;
            display: block;
        }

        .login-refresh-panel {
            position: relative;
            background: linear-gradient(180deg, #ffffff 0%, #fdfdff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 34px 34px 28px;
        }

        .login-refresh-panel::before {
            content: '';
            position: absolute;
            inset: 22px;
            border-radius: 34px;
            border: 1px solid rgba(226, 232, 240, 0.82);
            pointer-events: none;
        }

        .login-refresh-close {
            position: absolute;
            top: 28px;
            right: 28px;
            z-index: 2;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 54px;
            height: 54px;
            border-radius: 999px;
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.9);
            box-shadow: 0 14px 28px rgba(15, 23, 42, 0.08);
            color: #6b7280;
            transition: transform 180ms ease, color 180ms ease, box-shadow 180ms ease;
        }

        .login-refresh-close:hover {
            transform: translateY(-1px);
            color: #111827;
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.12);
        }

        .login-refresh-card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 410px;
            padding: 20px 0 10px;
        }

        .login-refresh-panel-logo {
            width: 92px;
            height: 92px;
            margin: 0 auto;
            padding: 8px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        }

        .login-refresh-title {
            margin: 26px 0 0;
            text-align: center;
            font-size: clamp(2rem, 1.7rem + 0.7vw, 3rem);
            line-height: 1.06;
            font-weight: 800;
            letter-spacing: -0.04em;
            color: #162447;
        }

        .login-refresh-subtitle {
            margin: 14px auto 0;
            max-width: 360px;
            text-align: center;
            font-size: 1.02rem;
            line-height: 1.7;
            color: #6b7a93;
        }

        .login-refresh-status,
        .login-refresh-errors {
            margin-top: 24px;
            border-radius: 20px;
            padding: 14px 18px;
            font-size: 0.92rem;
            line-height: 1.55;
        }

        .login-refresh-status {
            border: 1px solid rgba(16, 185, 129, 0.2);
            background: rgba(16, 185, 129, 0.08);
            color: #047857;
        }

        .login-refresh-errors {
            border: 1px solid rgba(239, 68, 68, 0.18);
            background: rgba(239, 68, 68, 0.08);
            color: #b91c1c;
        }

        .login-refresh-errors ul {
            margin: 0;
            padding-left: 1.2rem;
        }

        .login-refresh-form {
            margin-top: 30px;
        }

        .login-refresh-field + .login-refresh-field {
            margin-top: 22px;
        }

        .login-refresh-label {
            display: block;
            margin-bottom: 10px;
            font-size: 0.96rem;
            font-weight: 700;
            color: #2f3f5d;
        }

        .login-refresh-control {
            display: flex;
            align-items: center;
            gap: 14px;
            min-height: 62px;
            padding: 0 22px;
            border-radius: 999px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            background: linear-gradient(180deg, #ffffff 0%, #f7f9fc 100%);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,0.94),
                0 10px 24px rgba(15, 23, 42, 0.04);
            transition: border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
        }

        .login-refresh-control:focus-within {
            transform: translateY(-1px);
            border-color: rgba(37, 99, 235, 0.3);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,0.98),
                0 0 0 4px rgba(37, 99, 235, 0.08),
                0 14px 28px rgba(15, 23, 42, 0.06);
        }

        .login-refresh-control.is-invalid {
            border-color: rgba(239, 68, 68, 0.34);
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.07);
        }

        .login-refresh-icon {
            width: 22px;
            height: 22px;
            flex: 0 0 auto;
            color: #94a3b8;
        }

        .login-refresh-input {
            width: 100%;
            border: 0;
            padding: 0;
            background: transparent;
            font-size: 0.98rem;
            line-height: 1.4;
            color: #0f172a;
            outline: none;
            box-shadow: none;
        }

        .login-refresh-input::placeholder {
            color: #94a3b8;
        }

        .login-refresh-error {
            margin-top: 10px;
            padding-left: 10px;
            font-size: 0.86rem;
            color: #dc2626;
        }

        .login-refresh-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-top: 22px;
            font-size: 0.94rem;
            color: #64748b;
        }

        .login-refresh-check {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-weight: 600;
            color: #475569;
        }

        .login-refresh-check input {
            appearance: none;
            width: 24px;
            height: 24px;
            margin: 0;
            border-radius: 8px;
            border: 1.5px solid #d8deea;
            background: linear-gradient(180deg, #ffffff 0%, #f6f8fc 100%);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,0.92),
                0 8px 18px rgba(148, 163, 184, 0.16);
            display: grid;
            place-items: center;
            cursor: pointer;
        }

        .login-refresh-check input::after {
            content: '';
            width: 12px;
            height: 12px;
            border-radius: 4px;
            background: linear-gradient(135deg, #2f4bd7 0%, #7f379c 45%, #ea5f28 78%, #f5c63d 100%);
            transform: scale(0);
            transition: transform 180ms ease;
        }

        .login-refresh-check input:checked::after {
            transform: scale(1);
        }

        .login-refresh-link {
            font-weight: 700;
            color: #5b6a88;
            transition: color 180ms ease;
        }

        .login-refresh-link:hover {
            color: #2248c7;
        }

        .login-refresh-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 58px;
            margin-top: 30px;
            border: 0;
            border-radius: 999px;
            background: linear-gradient(90deg, #2a2474 0%, #2d56c6 24%, #be3d48 58%, #df8625 82%, #efca43 100%);
            box-shadow:
                0 20px 40px rgba(46, 77, 200, 0.22),
                0 14px 26px rgba(239, 202, 67, 0.14);
            color: #ffffff;
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: 0.42em;
            text-transform: uppercase;
            transition: transform 180ms ease, box-shadow 180ms ease, filter 180ms ease;
        }

        .login-refresh-submit:hover {
            transform: translateY(-2px);
            filter: saturate(1.03);
            box-shadow:
                0 24px 48px rgba(46, 77, 200, 0.26),
                0 18px 30px rgba(239, 202, 67, 0.18);
        }

        .login-refresh-submit:focus-visible {
            outline: none;
            box-shadow:
                0 0 0 5px rgba(37, 99, 235, 0.12),
                0 24px 48px rgba(46, 77, 200, 0.26);
        }

        .login-refresh-footer {
            margin-top: 20px;
            text-align: center;
            font-size: 0.98rem;
            color: #64748b;
        }

        .login-refresh-footer a {
            font-weight: 800;
            color: #162447;
        }

        @media (max-width: 1180px) {
            .login-refresh-shell {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .login-refresh-brand {
                min-height: 620px;
            }

            .login-refresh-brand-main {
                height: auto;
                min-height: 500px;
            }

            .login-refresh-brand-copy {
                width: min(420px, 100%);
                padding-top: 92px;
            }

            .login-refresh-hero {
                width: min(360px, 54%);
            }

            .login-refresh-panel {
                padding: 90px 22px 30px;
            }
        }

        @media (max-width: 760px) {
            .login-refresh-page {
                padding: 10px;
            }

            .login-refresh-shell {
                width: calc(100vw - 20px);
                border-radius: 28px;
            }

            .login-refresh-brand {
                padding: 24px 20px 240px;
            }

            .login-refresh-brand-title {
                font-size: 1.12rem;
            }

            .login-refresh-brand-region {
                font-size: 0.72rem;
                letter-spacing: 0.24em;
            }

            .login-refresh-brand-motto {
                font-size: 0.84rem;
            }

            .login-refresh-brand-copy {
                width: 100%;
                padding-top: 48px;
            }

            .login-refresh-brand-product {
                font-size: 0.88rem;
                letter-spacing: 0.22em;
            }

            .login-refresh-brand-product-copy {
                font-size: 0.92rem;
            }

            .login-refresh-brand-headline {
                margin-top: 18px;
                font-size: 2.55rem;
            }

            .login-refresh-brand-subtitle {
                margin-top: 18px;
                font-size: 0.94rem;
            }

            .login-refresh-dots {
                top: 212px;
                right: 20px;
                transform: scale(0.82);
                transform-origin: top right;
            }

            .login-refresh-hero {
                right: 8px;
                width: min(290px, 74%);
            }

            .login-refresh-panel {
                padding: 82px 16px 24px;
            }

            .login-refresh-panel::before {
                inset: 12px;
                border-radius: 26px;
            }

            .login-refresh-close {
                top: 16px;
                right: 16px;
                width: 48px;
                height: 48px;
            }

            .login-refresh-card {
                max-width: 100%;
                padding: 0 8px 6px;
            }

            .login-refresh-panel-logo {
                width: 82px;
                height: 82px;
            }

            .login-refresh-title {
                margin-top: 22px;
                font-size: 2.2rem;
            }

            .login-refresh-subtitle {
                font-size: 0.96rem;
            }

            .login-refresh-control {
                min-height: 58px;
                padding: 0 18px;
            }

            .login-refresh-meta {
                flex-direction: column;
                align-items: flex-start;
            }

            .login-refresh-submit {
                min-height: 56px;
                letter-spacing: 0.3em;
                font-size: 0.94rem;
            }
        }
    </style>

    <div class="login-refresh-page">
        <div class="login-refresh-shell">
            <section class="login-refresh-brand">
                <div class="login-refresh-dots" aria-hidden="true"></div>

                <div class="login-refresh-brand-top">
                    <div class="login-refresh-brand-seal">
                        <img src="{{ $logo }}" alt="DILG Logo">
                    </div>

                    <div class="login-refresh-brand-heading">
                        <div class="login-refresh-brand-title">Department of the Interior and Local Government</div>
                        <div class="login-refresh-brand-region">Cordillera Administrative Region</div>
                        <div class="login-refresh-brand-motto">Matino. Mahusay. at Maaasahan.</div>
                    </div>
                </div>

                <div class="login-refresh-brand-main">
                    <div class="login-refresh-brand-copy">
                        <div class="login-refresh-brand-product">GABAY-LEX</div>
                        <div class="login-refresh-brand-product-copy">Guidance and Advisory for Better Administration in Law</div>

                        <h1 class="login-refresh-brand-headline">Smart legal support for efficient public service.</h1>
                        <p class="login-refresh-brand-subtitle">Instant help, document assistance, and reliable guidance all in one place.</p>
                    </div>

                    <div class="login-refresh-hero" aria-hidden="true">
                        <img src="{{ $hero }}" alt="">
                    </div>
                </div>
            </section>

            <section class="login-refresh-panel">
                <a href="{{ url('/') }}" class="login-refresh-close" aria-label="Close login">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                    </svg>
                </a>

                <div class="login-refresh-card">
                    <div class="login-refresh-panel-logo">
                        <img src="{{ $logo }}" alt="DILG Logo">
                    </div>

                    <h2 class="login-refresh-title">Welcome Back</h2>
                    <p class="login-refresh-subtitle">Sign in to access your saved legal conversations.</p>

                    <x-auth-session-status class="login-refresh-status" :status="session('status')" />

                    @if ($errors->any())
                        <div class="login-refresh-errors">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('login') }}" class="login-refresh-form">
                        @csrf

                        <div class="login-refresh-field">
                            <label for="email" class="login-refresh-label">Email address</label>
                            <div @class(['login-refresh-control', 'is-invalid' => $errors->has('email')])>
                                <svg class="login-refresh-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M4 7.75C4 6.23122 5.23122 5 6.75 5H17.25C18.7688 5 20 6.23122 20 7.75V16.25C20 17.7688 18.7688 19 17.25 19H6.75C5.23122 19 4 17.7688 4 16.25V7.75Z" stroke="currentColor" stroke-width="1.8"/>
                                    <path d="M5 7L11.0593 11.5445C11.6184 11.9638 12.3816 11.9638 12.9407 11.5445L19 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="login-refresh-input" placeholder="Enter your email">
                            </div>
                            <x-input-error :messages="$errors->get('email')" class="login-refresh-error" />
                        </div>

                        <div class="login-refresh-field">
                            <label for="password" class="login-refresh-label">Password</label>
                            <div @class(['login-refresh-control', 'is-invalid' => $errors->has('password')])>
                                <svg class="login-refresh-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M8 10V8.75C8 6.67893 9.67893 5 11.75 5H12.25C14.3211 5 16 6.67893 16 8.75V10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    <path d="M6.75 10H17.25C18.2165 10 19 10.7835 19 11.75V17.25C19 18.2165 18.2165 19 17.25 19H6.75C5.7835 19 5 18.2165 5 17.25V11.75C5 10.7835 5.7835 10 6.75 10Z" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                                <input id="password" name="password" type="password" required autocomplete="current-password" class="login-refresh-input" placeholder="Enter your password">
                            </div>
                            <x-input-error :messages="$errors->get('password')" class="login-refresh-error" />
                        </div>

                        <div class="login-refresh-meta">
                            <label class="login-refresh-check" for="remember_me">
                                <input id="remember_me" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                <span>Remember me</span>
                            </label>

                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="login-refresh-link">Forgot password?</a>
                            @endif
                        </div>

                        <button type="submit" class="login-refresh-submit">Log In</button>
                    </form>

                    @if (Route::has('register'))
                        <p class="login-refresh-footer">
                            Don’t have an account? <a href="{{ route('register') }}">Sign Up</a>
                        </p>
                    @endif
                </div>
            </section>
        </div>
    </div>
</x-guest-layout>
