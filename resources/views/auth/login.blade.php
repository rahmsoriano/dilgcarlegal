<x-guest-layout>
    @php
        $logo = asset('dilglogo.png');
        $hero = asset('images/login-hero-exact.png');
        $isRegisterMode = ($initialMode ?? 'login') === 'register';
    @endphp

    <style>
        :root {
            color-scheme: light;
        }

        html,
        body {
            min-height: 100%;
            margin: 0;
            background: linear-gradient(180deg, #c4c7ce 0%, #aeb2bc 100%);
        }

        .auth-exact-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            font-family: 'Figtree', sans-serif;
        }

        .auth-exact-shell {
            width: min(1440px, calc(100vw - 48px));
            min-height: min(1000px, calc(100vh - 48px));
            display: grid;
            grid-template-columns: minmax(0, 1.18fr) minmax(520px, 0.82fr);
            overflow: hidden;
            border-radius: 40px;
            background: #fff;
            box-shadow: 0 36px 90px rgba(15, 23, 42, 0.16);
        }

        .auth-exact-brand {
            position: relative;
            overflow: hidden;
            padding: 46px 40px 36px 42px;
            background:
                radial-gradient(circle at 78% 50%, rgba(255, 255, 255, 0.08) 0, rgba(255, 255, 255, 0) 32%),
                linear-gradient(180deg, #0a1e56 0%, #122968 100%);
            color: #fff;
        }

        .auth-exact-brand::before {
            content: '';
            position: absolute;
            top: -10%;
            right: -130px;
            width: 330px;
            height: 120%;
            border-radius: 50%;
            border: 6px solid #e4a62f;
            opacity: 0.95;
        }

        .auth-exact-brand::after {
            content: '';
            position: absolute;
            left: -16%;
            right: 8%;
            bottom: -28%;
            height: 360px;
            border-radius: 50%;
            background:
                radial-gradient(140% 100% at 50% 0%, rgba(255, 255, 255, 0.22), rgba(255, 255, 255, 0.02) 60%, transparent 61%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.96), rgba(239, 243, 252, 0.98));
            box-shadow: inset 0 24px 50px rgba(255, 255, 255, 0.72);
        }

        .auth-exact-brand-inner {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            min-height: 100%;
        }

        .auth-exact-brand-top {
            display: flex;
            align-items: flex-start;
            gap: 18px;
        }

        .auth-exact-seal {
            width: 88px;
            height: 88px;
            flex: 0 0 auto;
        }

        .auth-exact-seal img,
        .auth-exact-panel-logo img,
        .auth-exact-hero img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .auth-exact-agency {
            max-width: 520px;
            padding-top: 8px;
        }

        .auth-exact-agency-title {
            margin: 0;
            font-size: clamp(1.55rem, 1.3rem + 0.55vw, 2.15rem);
            line-height: 1.18;
            font-weight: 800;
            text-transform: uppercase;
        }

        .auth-exact-agency-region {
            margin-top: 10px;
            font-size: 0.9rem;
            line-height: 1.5;
            font-weight: 500;
            letter-spacing: 0.4em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.9);
        }

        .auth-exact-agency-motto {
            margin-top: 10px;
            font-size: 0.95rem;
            font-style: italic;
            color: rgba(255, 255, 255, 0.88);
        }

        .auth-exact-watermark {
            position: absolute;
            left: 28px;
            top: 160px;
            width: 360px;
            height: 290px;
            opacity: 0.06;
        }

        .auth-exact-dots {
            position: absolute;
            right: 46px;
            top: 232px;
            width: 124px;
            height: 150px;
            opacity: 0.22;
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.95) 1.4px, transparent 1.7px);
            background-size: 18px 18px;
        }

        .auth-exact-brand-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 18px;
            flex: 1;
            padding-top: 26px;
        }

        .auth-exact-copy {
            width: min(430px, 54%);
            padding: 110px 0 170px;
        }

        .auth-exact-product {
            margin: 0;
            font-size: clamp(2.7rem, 2.1rem + 1.2vw, 4rem);
            line-height: 1;
            font-weight: 800;
            letter-spacing: 0.08em;
            color: #f3bc49;
        }

        .auth-exact-product-line {
            width: 86px;
            height: 5px;
            margin: 18px 0 16px;
            border-radius: 999px;
            background: #f3bc49;
        }

        .auth-exact-product-copy {
            margin: 0;
            max-width: 360px;
            font-size: 1rem;
            line-height: 1.45;
            color: rgba(255, 255, 255, 0.92);
        }

        .auth-exact-headline {
            margin: 42px 0 0;
            max-width: 520px;
            font-size: clamp(2.5rem, 1.92rem + 1.15vw, 3.8rem);
            line-height: 1.12;
            font-weight: 800;
            letter-spacing: -0.04em;
            text-wrap: balance;
        }

        .auth-exact-headline-line {
            width: 72px;
            height: 4px;
            margin: 24px 0 24px;
            border-radius: 999px;
            background: rgba(243, 188, 73, 0.55);
        }

        .auth-exact-subtitle {
            margin: 0;
            max-width: 430px;
            font-size: 1rem;
            line-height: 1.55;
            color: rgba(255, 255, 255, 0.92);
        }

        .auth-exact-hero {
            position: absolute;
            right: 10px;
            bottom: 206px;
            width: min(380px, 44%);
            z-index: 2;
            filter: drop-shadow(0 24px 36px rgba(0, 0, 0, 0.28));
        }

        .auth-exact-features {
            position: absolute;
            left: 54px;
            bottom: 108px;
            z-index: 3;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            width: min(420px, calc(100% - 108px));
            overflow: hidden;
            border-radius: 28px;
            border: 1px solid rgba(255, 255, 255, 0.12);
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(14px);
            box-shadow: 0 20px 38px rgba(6, 19, 54, 0.24);
        }

        .auth-exact-feature {
            padding: 22px 18px 20px;
            text-align: center;
        }

        .auth-exact-feature + .auth-exact-feature {
            border-left: 1px solid rgba(255, 255, 255, 0.14);
        }

        .auth-exact-feature-icon {
            width: 64px;
            height: 64px;
            margin: 0 auto 14px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.92);
            color: #2d3f93;
        }

        .auth-exact-feature-icon.is-gold {
            color: #c59025;
            background: rgba(255, 247, 227, 0.98);
        }

        .auth-exact-feature-title {
            margin: 0;
            font-size: 0.92rem;
            font-weight: 700;
        }

        .auth-exact-feature-copy {
            margin: 6px 0 0;
            font-size: 0.86rem;
            line-height: 1.35;
            color: rgba(255, 255, 255, 0.86);
        }

        .auth-exact-panel {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 38px 34px;
            background:
                radial-gradient(circle at 84% 12%, rgba(29, 78, 216, 0.06), transparent 25%),
                linear-gradient(180deg, #ffffff 0%, #fefeff 100%);
        }

        .auth-exact-panel::before {
            content: '';
            position: absolute;
            left: -110px;
            top: -8%;
            width: 260px;
            height: 118%;
            border-radius: 50%;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0), rgba(16, 24, 40, 0.03));
            box-shadow: inset -16px 0 26px rgba(255, 255, 255, 0.85);
        }

        .auth-exact-close {
            position: absolute;
            top: 28px;
            right: 28px;
            z-index: 3;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 58px;
            height: 58px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid rgba(226, 232, 240, 0.95);
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.1);
            color: #15244d;
            transition: transform 180ms ease, box-shadow 180ms ease;
        }

        .auth-exact-close:hover {
            transform: translateY(-1px);
            box-shadow: 0 22px 40px rgba(15, 23, 42, 0.14);
        }

        .auth-exact-card {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 520px;
            padding: 42px 8px 12px;
        }

        .auth-exact-panel-logo {
            width: 92px;
            height: 92px;
            margin: 0 auto;
        }

        .auth-exact-title {
            margin: 20px 0 0;
            text-align: center;
            font-size: clamp(2.25rem, 1.95rem + 0.62vw, 3.05rem);
            line-height: 1.05;
            font-weight: 800;
            letter-spacing: -0.05em;
            color: #162857;
        }

        .auth-exact-subcopy {
            margin: 14px auto 0;
            max-width: 410px;
            text-align: center;
            font-size: 1rem;
            line-height: 1.5;
            color: #6f7b94;
        }

        .auth-exact-status,
        .auth-exact-errors {
            margin-top: 24px;
            border-radius: 18px;
            padding: 14px 16px;
            font-size: 0.92rem;
            line-height: 1.55;
        }

        .auth-exact-status {
            border: 1px solid rgba(16, 185, 129, 0.2);
            background: rgba(16, 185, 129, 0.08);
            color: #047857;
        }

        .auth-exact-errors {
            border: 1px solid rgba(239, 68, 68, 0.18);
            background: rgba(239, 68, 68, 0.08);
            color: #b91c1c;
        }

        .auth-exact-errors ul {
            margin: 0;
            padding-left: 1.2rem;
        }

        .auth-exact-form {
            margin-top: 30px;
        }

        .auth-exact-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px 24px;
        }

        .auth-exact-field {
            min-width: 0;
        }

        .auth-exact-field.is-span-2 {
            grid-column: 1 / -1;
        }

        .auth-exact-label {
            display: block;
            margin-bottom: 10px;
            font-size: 0.94rem;
            font-weight: 700;
            color: #172554;
        }

        .auth-exact-control {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 54px;
            padding: 0 18px;
            border-radius: 999px;
            border: 1.5px solid #d8dee9;
            background: #fff;
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.95),
                0 12px 24px rgba(15, 23, 42, 0.04);
            transition: border-color 180ms ease, box-shadow 180ms ease;
        }

        .auth-exact-control:focus-within {
            border-color: rgba(37, 99, 235, 0.35);
            box-shadow:
                0 0 0 4px rgba(37, 99, 235, 0.08),
                0 12px 24px rgba(15, 23, 42, 0.05);
        }

        .auth-exact-control.is-invalid {
            border-color: rgba(239, 68, 68, 0.36);
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.07);
        }

        .auth-exact-control.is-trailing {
            padding-right: 12px;
        }

        .auth-exact-icon {
            width: 22px;
            height: 22px;
            flex: 0 0 auto;
            color: #7f8aa2;
        }

        .auth-exact-input {
            width: 100%;
            border: 0;
            padding: 0;
            background: transparent;
            font-size: 0.98rem;
            color: #1f2d59;
            outline: none;
        }

        .auth-exact-input::placeholder {
            color: #95a0b5;
        }

        .auth-exact-input[type="date"] {
            appearance: none;
        }

        .auth-exact-trailing {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border: 0;
            border-radius: 999px;
            background: transparent;
            color: #64748b;
            cursor: pointer;
            transition: background-color 180ms ease, color 180ms ease;
        }

        .auth-exact-trailing:hover {
            background: rgba(148, 163, 184, 0.12);
            color: #0f172a;
        }

        .auth-exact-error {
            margin-top: 8px;
            padding-left: 10px;
            font-size: 0.84rem;
            color: #dc2626;
        }

        .auth-exact-privacy {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-top: 26px;
            padding: 18px 20px;
            border-radius: 16px;
            border: 1.5px solid rgba(96, 132, 216, 0.32);
            background: linear-gradient(180deg, #f5f8ff 0%, #eef3fd 100%);
            color: #4f5e82;
        }

        .auth-exact-privacy-icon {
            width: 74px;
            height: 74px;
            flex: 0 0 auto;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: linear-gradient(180deg, #2447a3 0%, #1f3d8c 100%);
            color: #fff;
        }

        .auth-exact-privacy-copy {
            margin: 0;
            font-size: 0.95rem;
            line-height: 1.45;
        }

        .auth-exact-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            width: 100%;
            min-height: 66px;
            margin-top: 28px;
            border: 0;
            border-radius: 999px;
            background: linear-gradient(90deg, #2d2477 0%, #243eaa 38%, #d01d2a 70%, #efba35 100%);
            box-shadow:
                0 22px 42px rgba(34, 57, 159, 0.22),
                0 12px 24px rgba(239, 186, 53, 0.18);
            color: #fff;
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: 0.34em;
            text-transform: uppercase;
            transition: transform 180ms ease, box-shadow 180ms ease;
        }

        .auth-exact-submit:hover {
            transform: translateY(-2px);
            box-shadow:
                0 28px 48px rgba(34, 57, 159, 0.26),
                0 16px 28px rgba(239, 186, 53, 0.2);
        }

        .auth-exact-submit-arrow {
            margin-left: auto;
            display: inline-flex;
            align-items: center;
        }

        .auth-exact-footer {
            margin-top: 26px;
            text-align: center;
            font-size: 0.98rem;
            color: #56657f;
        }

        .auth-exact-footer a {
            font-weight: 800;
            color: #1b45cc;
            text-decoration: none;
        }

        .auth-exact-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-top: 22px;
            font-size: 0.94rem;
            color: #64748b;
        }

        .auth-exact-check {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            color: #475569;
        }

        .auth-exact-check input {
            width: 18px;
            height: 18px;
        }

        .auth-exact-link {
            font-weight: 700;
            color: #5b6a88;
            text-decoration: none;
        }

        @media (max-width: 1240px) {
            .auth-exact-shell {
                grid-template-columns: 1fr;
                min-height: auto;
            }

            .auth-exact-brand {
                min-height: 700px;
            }

            .auth-exact-copy {
                width: min(480px, 100%);
                padding-bottom: 190px;
            }

            .auth-exact-hero {
                right: 30px;
                width: min(320px, 44%);
            }
        }

        @media (max-width: 860px) {
            .auth-exact-page {
                padding: 12px;
            }

            .auth-exact-shell {
                width: calc(100vw - 24px);
                border-radius: 28px;
            }

            .auth-exact-brand {
                padding: 24px 20px 26px;
                min-height: auto;
            }

            .auth-exact-brand::before,
            .auth-exact-brand::after,
            .auth-exact-watermark,
            .auth-exact-dots,
            .auth-exact-hero {
                display: none;
            }

            .auth-exact-brand-content {
                display: block;
            }

            .auth-exact-copy {
                width: 100%;
                padding: 42px 0 180px;
            }

            .auth-exact-features {
                left: 20px;
                right: 20px;
                bottom: 24px;
                width: auto;
            }

            .auth-exact-panel {
                padding: 74px 16px 26px;
            }

            .auth-exact-close {
                top: 16px;
                right: 16px;
                width: 48px;
                height: 48px;
            }

            .auth-exact-card {
                max-width: 100%;
                padding-top: 18px;
            }

            .auth-exact-grid {
                grid-template-columns: 1fr;
                gap: 16px;
            }

            .auth-exact-privacy {
                flex-direction: column;
                text-align: center;
            }

            .auth-exact-meta {
                flex-direction: column;
                align-items: flex-start;
            }

            .auth-exact-submit {
                min-height: 58px;
                letter-spacing: 0.22em;
            }
        }
    </style>

    <div class="auth-exact-page">
        <div class="auth-exact-shell">
            <section class="auth-exact-brand">
                <div class="auth-exact-brand-inner">
                    <div class="auth-exact-brand-top">
                        <div class="auth-exact-seal">
                            <img src="{{ $logo }}" alt="DILG Logo">
                        </div>

                        <div class="auth-exact-agency">
                            <h1 class="auth-exact-agency-title">Department of the Interior and Local Government</h1>
                            <div class="auth-exact-agency-region">Cordillera Administrative Region</div>
                            <div class="auth-exact-agency-motto">Matino. Mahusay. at Maaasahan.</div>
                        </div>
                    </div>

                    <svg class="auth-exact-watermark" viewBox="0 0 420 320" fill="none" aria-hidden="true">
                        <path d="M55 257H365" stroke="white" stroke-width="8" stroke-linecap="round"/>
                        <path d="M88 248V126M332 248V126M142 248V158M278 248V158" stroke="white" stroke-width="8" stroke-linecap="round"/>
                        <path d="M78 126H342" stroke="white" stroke-width="8" stroke-linecap="round"/>
                        <path d="M52 126L210 64L368 126" stroke="white" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M121 78C121 111.689 93.6888 139 60 139C26.3112 139 -1 111.689 -1 78" transform="translate(48 18)" stroke="white" stroke-width="8" stroke-linecap="round"/>
                        <path d="M121 78C121 111.689 93.6888 139 60 139C26.3112 139 -1 111.689 -1 78" transform="translate(250 18)" stroke="white" stroke-width="8" stroke-linecap="round"/>
                        <path d="M210 52V16" stroke="white" stroke-width="8" stroke-linecap="round"/>
                        <path d="M180 52H240" stroke="white" stroke-width="8" stroke-linecap="round"/>
                    </svg>

                    <div class="auth-exact-dots" aria-hidden="true"></div>

                    <div class="auth-exact-brand-content">
                        <div class="auth-exact-copy">
                            <div class="auth-exact-product">GABAY-LEX</div>
                            <div class="auth-exact-product-line"></div>
                            <p class="auth-exact-product-copy">Guidance and Advisory for Better Administration in Law</p>

                            <h2 class="auth-exact-headline">Smart legal support for efficient public service.</h2>
                            <div class="auth-exact-headline-line"></div>
                            <p class="auth-exact-subtitle">Instant help, document assistance, and reliable guidance all in one place.</p>
                        </div>
                    </div>

                    <div class="auth-exact-hero" aria-hidden="true">
                        <img src="{{ $hero }}" alt="">
                    </div>

                    <div class="auth-exact-features">
                        <div class="auth-exact-feature">
                            <div class="auth-exact-feature-icon">
                                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M12 3L19 6V11C19 15.4183 16.134 19.4312 12 21C7.866 19.4312 5 15.4183 5 11V6L12 3Z" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/>
                                    <path d="M9.5 11.8L11.2 13.5L14.8 9.7" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <p class="auth-exact-feature-title">Trusted</p>
                            <p class="auth-exact-feature-copy">Secure and confidential</p>
                        </div>

                        <div class="auth-exact-feature">
                            <div class="auth-exact-feature-icon">
                                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M8 3.5H16L20 7.5V20.5H8V3.5Z" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/>
                                    <path d="M16 3.5V7.5H20" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/>
                                    <path d="M11 11.5H17M11 15.5H17" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                </svg>
                            </div>
                            <p class="auth-exact-feature-title">Reliable</p>
                            <p class="auth-exact-feature-copy">Accurate legal information</p>
                        </div>

                        <div class="auth-exact-feature">
                            <div class="auth-exact-feature-icon is-gold">
                                <svg width="30" height="30" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M13.5 2.5L6.5 13H11L10.5 21.5L17.5 11H13L13.5 2.5Z" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <p class="auth-exact-feature-title">Accessible</p>
                            <p class="auth-exact-feature-copy">Anytime, anywhere</p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="auth-exact-panel">
                <a href="{{ url('/') }}" class="auth-exact-close" aria-label="Close">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                    </svg>
                </a>

                <div class="auth-exact-card">
                    <div class="auth-exact-panel-logo">
                        <img src="{{ $logo }}" alt="DILG Logo">
                    </div>

                    @if ($isRegisterMode)
                        <h2 class="auth-exact-title">Create Account</h2>
                        <p class="auth-exact-subcopy">Sign up to start your legal research workspace.</p>
                    @else
                        <h2 class="auth-exact-title">Welcome!</h2>
                        <p class="auth-exact-subcopy">Sign in to access your saved legal conversations.</p>
                    @endif

                    <x-auth-session-status class="auth-exact-status" :status="session('status')" />

                    @if ($errors->any())
                        <div class="auth-exact-errors">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if ($isRegisterMode)
                        <form method="POST" action="{{ route('register') }}" class="auth-exact-form">
                            @csrf

                            <div class="auth-exact-grid">
                                <div class="auth-exact-field">
                                    <label for="first_name" class="auth-exact-label">First Name</label>
                                    <div @class(['auth-exact-control', 'is-invalid' => $errors->has('first_name')])>
                                        <svg class="auth-exact-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="currentColor" stroke-width="1.9"/>
                                            <path d="M4 20C4 16.6863 7.58172 14 12 14C16.4183 14 20 16.6863 20 20" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                        </svg>
                                        <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" required autocomplete="given-name" class="auth-exact-input" placeholder="First name">
                                    </div>
                                    <x-input-error :messages="$errors->get('first_name')" class="auth-exact-error" />
                                </div>

                                <div class="auth-exact-field">
                                    <label for="last_name" class="auth-exact-label">Last Name</label>
                                    <div @class(['auth-exact-control', 'is-invalid' => $errors->has('last_name')])>
                                        <svg class="auth-exact-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="currentColor" stroke-width="1.9"/>
                                            <path d="M4 20C4 16.6863 7.58172 14 12 14C16.4183 14 20 16.6863 20 20" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                        </svg>
                                        <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" required autocomplete="family-name" class="auth-exact-input" placeholder="Last name">
                                    </div>
                                    <x-input-error :messages="$errors->get('last_name')" class="auth-exact-error" />
                                </div>

                                <div class="auth-exact-field">
                                    <label for="birthday" class="auth-exact-label">Birthday</label>
                                    <div @class(['auth-exact-control auth-exact-control is-trailing', 'is-invalid' => $errors->has('birthday')])>
                                        <svg class="auth-exact-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M8 2V5M16 2V5M3 9H21" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                            <path d="M5 5H19C20.1046 5 21 5.89543 21 7V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V7C3 5.89543 3.89543 5 5 5Z" stroke="currentColor" stroke-width="1.9"/>
                                        </svg>
                                        <input id="birthday" name="birthday" type="date" value="{{ old('birthday') }}" required class="auth-exact-input" placeholder="dd/mm/yyyy">
                                        <span class="auth-exact-trailing" aria-hidden="true">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                                <path d="M8 2V5M16 2V5M3 9H21" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                                <path d="M5 5H19C20.1046 5 21 5.89543 21 7V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V7C3 5.89543 3.89543 5 5 5Z" stroke="currentColor" stroke-width="1.9"/>
                                            </svg>
                                        </span>
                                    </div>
                                    <x-input-error :messages="$errors->get('birthday')" class="auth-exact-error" />
                                </div>

                                <div class="auth-exact-field">
                                    <label for="email" class="auth-exact-label">Email Address</label>
                                    <div @class(['auth-exact-control', 'is-invalid' => $errors->has('email')])>
                                        <svg class="auth-exact-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M4 7.75C4 6.23122 5.23122 5 6.75 5H17.25C18.7688 5 20 6.23122 20 7.75V16.25C20 17.7688 18.7688 19 17.25 19H6.75C5.23122 19 4 17.7688 4 16.25V7.75Z" stroke="currentColor" stroke-width="1.8"/>
                                            <path d="M5 7L11.0593 11.5445C11.6184 11.9638 12.3816 11.9638 12.9407 11.5445L19 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        </svg>
                                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" class="auth-exact-input" placeholder="Email address">
                                    </div>
                                    <x-input-error :messages="$errors->get('email')" class="auth-exact-error" />
                                </div>

                                <div class="auth-exact-field">
                                    <label for="password" class="auth-exact-label">Password</label>
                                    <div @class(['auth-exact-control auth-exact-control is-trailing', 'is-invalid' => $errors->has('password')])>
                                        <svg class="auth-exact-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M8 10V8C8 5.79086 9.79086 4 12 4C14.2091 4 16 5.79086 16 8V10" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                            <path d="M6 10H18C19.1046 10 20 10.8954 20 12V18C20 19.1046 19.1046 20 18 20H6C4.89543 20 4 19.1046 4 18V12C4 10.8954 4.89543 10 6 10Z" stroke="currentColor" stroke-width="1.9"/>
                                        </svg>
                                        <input id="password" name="password" type="password" required autocomplete="new-password" class="auth-exact-input" placeholder="Password">
                                        <button type="button" class="auth-exact-trailing" data-password-toggle="password" aria-label="Show password">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M2 12C3.8 8.5 7.3 6 12 6C16.7 6 20.2 8.5 22 12C20.2 15.5 16.7 18 12 18C7.3 18 3.8 15.5 2 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="1.8"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <x-input-error :messages="$errors->get('password')" class="auth-exact-error" />
                                </div>

                                <div class="auth-exact-field">
                                    <label for="password_confirmation" class="auth-exact-label">Confirm Password</label>
                                    <div class="auth-exact-control auth-exact-control is-trailing">
                                        <svg class="auth-exact-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M8 10V8C8 5.79086 9.79086 4 12 4C14.2091 4 16 5.79086 16 8V10" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                            <path d="M6 10H18C19.1046 10 20 10.8954 20 12V18C20 19.1046 19.1046 20 18 20H6C4.89543 20 4 19.1046 4 18V12C4 10.8954 4.89543 10 6 10Z" stroke="currentColor" stroke-width="1.9"/>
                                        </svg>
                                        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="auth-exact-input" placeholder="Confirm password">
                                        <button type="button" class="auth-exact-trailing" data-password-toggle="password_confirmation" aria-label="Show confirm password">
                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M2 12C3.8 8.5 7.3 6 12 6C16.7 6 20.2 8.5 22 12C20.2 15.5 16.7 18 12 18C7.3 18 3.8 15.5 2 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="1.8"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="auth-exact-privacy">
                                <div class="auth-exact-privacy-icon" aria-hidden="true">
                                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none">
                                        <path d="M12 3L19 6V11C19 15.4183 16.134 19.4312 12 21C7.866 19.4312 5 15.4183 5 11V6L12 3Z" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/>
                                        <path d="M9.5 11.8L11.2 13.5L14.8 9.7" stroke="currentColor" stroke-width="1.9" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </div>
                                <p class="auth-exact-privacy-copy">We respect your privacy. Your information is secure and will only be used to enhance your legal research experience.</p>
                            </div>

                            <button type="submit" class="auth-exact-submit">
                                <span style="margin-left: auto;">Sign Up</span>
                                <span class="auth-exact-submit-arrow" aria-hidden="true">
                                    <svg width="30" height="22" viewBox="0 0 30 22" fill="none">
                                        <path d="M18 2L27 11L18 20" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M27 11H3" stroke="currentColor" stroke-width="2.8" stroke-linecap="round"/>
                                    </svg>
                                </span>
                            </button>
                        </form>

                        <p class="auth-exact-footer">
                            Already have an account? <a href="{{ route('login') }}">Log In</a>
                        </p>
                    @else
                        <form method="POST" action="{{ route('login') }}" class="auth-exact-form">
                            @csrf

                            <div class="auth-exact-field is-span-2">
                                <label for="login_email" class="auth-exact-label">Email address</label>
                                <div @class(['auth-exact-control', 'is-invalid' => $errors->has('email')])>
                                    <svg class="auth-exact-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M4 7.75C4 6.23122 5.23122 5 6.75 5H17.25C18.7688 5 20 6.23122 20 7.75V16.25C20 17.7688 18.7688 19 17.25 19H6.75C5.23122 19 4 17.7688 4 16.25V7.75Z" stroke="currentColor" stroke-width="1.8"/>
                                        <path d="M5 7L11.0593 11.5445C11.6184 11.9638 12.3816 11.9638 12.9407 11.5445L19 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                    </svg>
                                    <input id="login_email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="auth-exact-input" placeholder="Enter your email">
                                </div>
                                <x-input-error :messages="$errors->get('email')" class="auth-exact-error" />
                            </div>

                            <div class="auth-exact-field is-span-2" style="margin-top: 20px;">
                                <label for="login_password" class="auth-exact-label">Password</label>
                                <div @class(['auth-exact-control auth-exact-control is-trailing', 'is-invalid' => $errors->has('password')])>
                                    <svg class="auth-exact-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <path d="M8 10V8C8 5.79086 9.79086 4 12 4C14.2091 4 16 5.79086 16 8V10" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                        <path d="M6 10H18C19.1046 10 20 10.8954 20 12V18C20 19.1046 19.1046 20 18 20H6C4.89543 20 4 19.1046 4 18V12C4 10.8954 4.89543 10 6 10Z" stroke="currentColor" stroke-width="1.9"/>
                                    </svg>
                                    <input id="login_password" name="password" type="password" required autocomplete="current-password" class="auth-exact-input" placeholder="Enter your password">
                                    <button type="button" class="auth-exact-trailing" data-password-toggle="login_password" aria-label="Show password">
                                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M2 12C3.8 8.5 7.3 6 12 6C16.7 6 20.2 8.5 22 12C20.2 15.5 16.7 18 12 18C7.3 18 3.8 15.5 2 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                            <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="1.8"/>
                                        </svg>
                                    </button>
                                </div>
                                <x-input-error :messages="$errors->get('password')" class="auth-exact-error" />
                            </div>

                            <div class="auth-exact-meta">
                                <label class="auth-exact-check" for="remember_me">
                                    <input id="remember_me" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <span>Remember me</span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="auth-exact-link">Forgot password?</a>
                                @endif
                            </div>

                            <button type="submit" class="auth-exact-submit">
                                <span style="margin-left: auto;">Log In</span>
                                <span class="auth-exact-submit-arrow" aria-hidden="true">
                                    <svg width="30" height="22" viewBox="0 0 30 22" fill="none">
                                        <path d="M18 2L27 11L18 20" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"/>
                                        <path d="M27 11H3" stroke="currentColor" stroke-width="2.8" stroke-linecap="round"/>
                                    </svg>
                                </span>
                            </button>
                        </form>

                        @if (Route::has('register'))
                            <p class="auth-exact-footer">
                                Don’t have an account? <a href="{{ route('register') }}">Sign Up</a>
                            </p>
                        @endif
                    @endif
                </div>
            </section>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
            button.addEventListener('click', function () {
                var input = document.getElementById(button.getAttribute('data-password-toggle'));

                if (!input) {
                    return;
                }

                input.type = input.type === 'password' ? 'text' : 'password';
            });
        });
    </script>
</x-guest-layout>
