<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'GABAY-Lex') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

        @if (file_exists(public_path('hot')) || file_exists(public_path('build/manifest.json')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        <style>
            :root {
                color-scheme: light;
            }

            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: 'Figtree', sans-serif;
                color: #0f172a;
                background: linear-gradient(135deg, #eef6ff, #f8fbff, #eaf2ff);
            }

            .verify-page {
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 48px 20px 36px;
                position: relative;
                overflow: hidden;
                isolation: isolate;
            }

            .verify-page::before {
                content: '';
                position: absolute;
                left: -240px;
                bottom: -220px;
                width: 620px;
                height: 620px;
                border-radius: 50%;
                background: radial-gradient(circle at 40% 35%, rgba(96, 165, 250, 0.28), rgba(147, 197, 253, 0.15) 48%, rgba(191, 219, 254, 0) 72%);
                z-index: -2;
            }

            .verify-page::after {
                content: '';
                position: absolute;
                top: 94px;
                right: -82px;
                width: 272px;
                height: 272px;
                border-radius: 50%;
                border: 26px solid rgba(96, 165, 250, 0.14);
                z-index: -2;
            }

            .verify-dots {
                position: absolute;
                width: 128px;
                height: 128px;
                background-image: radial-gradient(circle, rgba(96, 165, 250, 0.36) 2px, transparent 2.2px);
                background-size: 27px 27px;
                z-index: -1;
                pointer-events: none;
            }

            .verify-dots--left {
                top: 195px;
                left: 52px;
            }

            .verify-dots--right {
                right: 92px;
                bottom: 118px;
            }

            .verify-stage {
                width: 100%;
                max-width: 500px;
                animation: verifyFadeIn 0.7s ease-out both;
            }

            .verify-card {
                width: 100%;
                padding: 40px;
                border-radius: 28px;
                background: rgba(255, 255, 255, 0.95);
                border: 1px solid rgba(255, 255, 255, 0.8);
                box-shadow: 0 30px 80px rgba(15, 23, 42, 0.15);
                backdrop-filter: blur(12px);
            }

            .verify-logo-shell {
                width: 92px;
                height: 92px;
                margin: 0 auto 18px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                background: rgba(255, 255, 255, 0.92);
                box-shadow: 0 20px 46px rgba(15, 23, 42, 0.12);
            }

            .verify-logo {
                width: 60px;
                height: 60px;
                border-radius: 999px;
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                box-shadow: 0 10px 24px rgba(15, 23, 42, 0.12);
            }

            .verify-logo img {
                width: 100%;
                height: 100%;
                object-fit: contain;
                display: block;
            }

            .verify-title {
                margin: 0;
                text-align: center;
                font-size: 40px;
                font-weight: 900;
                line-height: 1.06;
                letter-spacing: -0.045em;
                color: #0f172a;
            }

            .verify-divider {
                margin: 26px 0 28px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 16px;
            }

            .verify-divider-line {
                flex: 1;
                height: 1px;
                background: #d7dfeb;
            }

            .verify-divider-icon {
                width: 42px;
                height: 42px;
                border-radius: 999px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: #2563eb;
                background: #eff6ff;
                border: 1px solid #dbeafe;
                box-shadow: 0 10px 24px rgba(37, 99, 235, 0.12);
                flex-shrink: 0;
            }

            .verify-description {
                margin: 0 0 28px;
                text-align: center;
                color: #64748b;
                font-size: 16px;
                line-height: 1.65;
            }

            .verify-success {
                margin-bottom: 34px;
                display: flex;
                align-items: flex-start;
                gap: 16px;
                padding: 16px 18px;
                border-radius: 14px;
                background: #ecfdf5;
                border: 1px solid #86efac;
                color: #047857;
                font-size: 16px;
                line-height: 1.55;
            }

            .verify-success-icon {
                width: 34px;
                height: 34px;
                border-radius: 999px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                flex-shrink: 0;
                background: #10b981;
                color: #ffffff;
                box-shadow: 0 10px 24px rgba(16, 185, 129, 0.2);
            }

            .verify-actions {
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 12px;
                flex-wrap: wrap;
            }

            .verify-form {
                margin: 0;
            }

            .verify-btn {
                min-height: 56px;
                border-radius: 999px;
                padding: 0 32px;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                gap: 12px;
                font-size: 15px;
                font-weight: 800;
                letter-spacing: 2px;
                text-transform: uppercase;
                transition: transform 180ms ease, box-shadow 180ms ease, background 180ms ease, border-color 180ms ease, color 180ms ease;
                cursor: pointer;
                text-decoration: none;
            }

            .verify-btn:hover {
                transform: translateY(-2px);
            }

            .verify-btn--primary {
                border: none;
                color: #ffffff;
                background: linear-gradient(135deg, #2563eb, #1d4ed8);
                box-shadow: 0 10px 25px rgba(37, 99, 235, 0.4);
            }

            .verify-btn--secondary {
                border: 1px solid #dbe3ef;
                color: #334155;
                background: #ffffff;
                box-shadow: 0 8px 20px rgba(15, 23, 42, 0.05);
            }

            .verify-btn--secondary:hover {
                box-shadow: 0 12px 24px rgba(15, 23, 42, 0.08);
            }

            .verify-footer {
                margin-top: 26px;
                text-align: center;
                color: #64748b;
            }

            .verify-footer-lock {
                width: 34px;
                height: 34px;
                margin: 0 auto 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 999px;
                background: #eff6ff;
                border: 1px solid #dbeafe;
                color: #2563eb;
                box-shadow: 0 8px 18px rgba(37, 99, 235, 0.1);
            }

            .verify-footer-copy {
                margin: 0;
                font-size: 14px;
                line-height: 1.6;
            }

            .verify-footer-brand {
                margin: 8px 0 0;
                font-size: 16px;
                font-weight: 900;
                color: #0f172a;
                letter-spacing: 0.02em;
            }

            @keyframes verifyFadeIn {
                from {
                    opacity: 0;
                    transform: translateY(18px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @media (max-width: 640px) {
                .verify-page {
                    padding: 24px 14px 30px;
                }

                .verify-card {
                    padding: 30px 22px;
                    border-radius: 24px;
                }

                .verify-title {
                    font-size: 33px;
                }

                .verify-actions {
                    flex-direction: column;
                }

                .verify-form,
                .verify-btn {
                    width: 100%;
                }

                .verify-dots--left {
                    top: 118px;
                    left: 18px;
                }

                .verify-dots--right {
                    right: 18px;
                    bottom: 82px;
                }
            }
        </style>
    </head>
    <body>
        @php
            $logo = asset('dilglogo.png');
        @endphp

        <div class="verify-page">
            <div class="verify-dots verify-dots--left" aria-hidden="true"></div>
            <div class="verify-dots verify-dots--right" aria-hidden="true"></div>

            <div class="verify-stage">
                <div class="verify-card">
                    <div class="verify-logo-shell">
                        <div class="verify-logo">
                            <img src="{{ $logo }}" alt="DILG Logo">
                        </div>
                    </div>

                    <h1 class="verify-title">Verify Your Email</h1>

                    <div class="verify-divider" aria-hidden="true">
                        <div class="verify-divider-line"></div>
                        <div class="verify-divider-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none">
                                <path d="M4 7.75C4 6.23122 5.23122 5 6.75 5H17.25C18.7688 5 20 6.23122 20 7.75V16.25C20 17.7688 18.7688 19 17.25 19H6.75C5.23122 19 4 17.7688 4 16.25V7.75Z" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M5 7L11.0593 11.5445C11.6184 11.9638 12.3816 11.9638 12.9407 11.5445L19 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div class="verify-divider-line"></div>
                    </div>

                    <p class="verify-description">
                        Please check your inbox and click the verification link we sent to your email address before logging in to the system.
                    </p>

                    @if (session('status') === 'verification-link-sent')
                        <div class="verify-success">
                            <div class="verify-success-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.414l-7.25 7.25a1 1 0 01-1.414 0l-3.25-3.25a1 1 0 111.414-1.414l2.543 2.543 6.543-6.543a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>A new verification link has been sent to your email address.</div>
                        </div>
                    @endif

                    <div class="verify-actions">
                        <form class="verify-form" method="POST" action="{{ route('verification.send') }}">
                            @csrf
                            <button type="submit" class="verify-btn verify-btn--primary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M3.478 2.405a.75.75 0 01.81-.163l18 8.25a.75.75 0 010 1.362l-18 8.25A.75.75 0 013 19.5v-6.764a.75.75 0 01.553-.724L12 9.75 3.553 7.488A.75.75 0 013 6.764V3a.75.75 0 01.478-.595Z"/>
                                </svg>
                                Resend Verification
                            </button>
                        </form>

                        <form class="verify-form" method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="verify-btn verify-btn--secondary">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none">
                                    <path d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-7.5a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 006 21h7.5a2.25 2.25 0 002.25-2.25V15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M18 12H9.75" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M15 9L18 12L15 15" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                Log Out
                            </button>
                        </form>
                    </div>
                </div>

                <div class="verify-footer">
                    <div class="verify-footer-lock">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none">
                            <path d="M16.5 10.5V8.25A4.5 4.5 0 007.5 8.25V10.5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            <path d="M6.75 10.5H17.25C18.0784 10.5 18.75 11.1716 18.75 12V18C18.75 18.8284 18.0784 19.5 17.25 19.5H6.75C5.92157 19.5 5.25 18.8284 5.25 18V12C5.25 11.1716 5.92157 10.5 6.75 10.5Z" stroke="currentColor" stroke-width="1.8"/>
                        </svg>
                    </div>
                    <p class="verify-footer-copy">Securing access. Protecting communities. Upholding integrity.</p>
                    <p class="verify-footer-brand">DILG GABAY-LEX SYSTEM</p>
                </div>
            </div>
        </div>
    </body>
</html>
