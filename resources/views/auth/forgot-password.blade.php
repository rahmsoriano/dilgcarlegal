<x-guest-layout>
    @php
        $logo = asset('dilglogo.png');
    @endphp

    <style>
        :root {
            color-scheme: light;
        }

        html,
        body {
            min-height: 100%;
            margin: 0;
            background:
                radial-gradient(circle at top, rgba(198, 211, 246, 0.22), transparent 34%),
                linear-gradient(180deg, #fbfcfe 0%, #f3f6fb 100%);
            font-family: 'Poppins', 'Figtree', sans-serif;
        }

        .forgot-premium-page {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
            padding: 52px 20px 36px;
            color: #0e1f3d;
        }

        .forgot-premium-page::before {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 50% 34%, rgba(124, 111, 219, 0.1), transparent 28%),
                radial-gradient(circle at 15% 80%, rgba(52, 98, 212, 0.12), transparent 25%),
                radial-gradient(circle at 85% 76%, rgba(255, 255, 255, 0.9), transparent 30%);
        }

        .forgot-wave-left,
        .forgot-wave-right,
        .forgot-bottom-blob,
        .forgot-dots,
        .forgot-gold-line-left,
        .forgot-gold-line-right,
        .forgot-watermark {
            position: absolute;
            pointer-events: none;
        }

        .forgot-wave-left {
            left: -228px;
            top: 116px;
            width: 840px;
            height: 808px;
            border-radius: 44% 56% 40% 60% / 40% 34% 66% 60%;
            background:
                linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(255,255,255,0.62)),
                radial-gradient(circle at 26% 22%, rgba(255,255,255,1), rgba(255,255,255,0.34));
            box-shadow: inset -32px 0 54px rgba(255,255,255,0.84);
            transform: rotate(10deg);
            opacity: 0.96;
        }

        .forgot-wave-right {
            right: -176px;
            top: 44px;
            width: 498px;
            height: 808px;
            border-radius: 54% 46% 52% 48% / 42% 40% 60% 58%;
            background: linear-gradient(180deg, rgba(255,255,255,0.78), rgba(244, 247, 255, 0.42));
            box-shadow: inset 26px 0 46px rgba(255,255,255,0.82);
            transform: rotate(-10deg);
            opacity: 0.82;
        }

        .forgot-bottom-blob {
            left: -134px;
            bottom: -188px;
            width: 604px;
            height: 620px;
            border-radius: 46% 54% 0 0 / 54% 54% 0 0;
            background:
                radial-gradient(circle at 48% 32%, rgba(120, 150, 234, 0.34), rgba(14, 31, 61, 0.9) 68%);
            opacity: 0.95;
        }

        .forgot-dots {
            top: 82px;
            right: 10px;
            width: 150px;
            height: 230px;
            opacity: 0.22;
            background-image: radial-gradient(circle, rgba(228, 167, 56, 0.62) 1.1px, transparent 1.5px);
            background-size: 16px 16px;
        }

        .forgot-gold-line-left {
            left: -118px;
            top: 94px;
            width: 514px;
            height: 330px;
            border: 1.2px solid rgba(223, 179, 67, 0.4);
            border-radius: 48% 52% 54% 46% / 42% 34% 66% 58%;
            transform: rotate(12deg);
        }

        .forgot-gold-line-right {
            right: -52px;
            top: 42px;
            width: 338px;
            height: 736px;
            border: 1.2px solid rgba(223, 179, 67, 0.42);
            border-radius: 48%;
        }

        .forgot-watermark {
            left: 56px;
            bottom: 120px;
            width: 280px;
            height: 280px;
            opacity: 0.065;
            color: #ffffff;
        }

        .forgot-shell {
            position: relative;
            z-index: 2;
            width: min(100%, 1180px);
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .forgot-logo {
            width: 90px;
            height: 90px;
            margin: 0 auto 16px;
            display: block;
            object-fit: contain;
        }

        .forgot-title {
            margin: 0;
            font-size: clamp(2.4rem, 2rem + 1.2vw, 3.9rem);
            line-height: 1.05;
            font-weight: 800;
            letter-spacing: -0.05em;
            color: #102657;
        }

        .forgot-subtitle {
            margin: 12px auto 0;
            max-width: 700px;
            font-size: 1rem;
            line-height: 1.6;
            color: #60708f;
        }

        .forgot-card {
            width: min(100%, 548px);
            margin-top: 28px;
            padding: 32px 34px 38px;
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(255, 255, 255, 0.9);
            box-shadow:
                0 20px 56px rgba(110, 129, 171, 0.14),
                inset 0 1px 0 rgba(255,255,255,0.92);
            backdrop-filter: blur(18px);
            text-align: left;
        }

        .forgot-status {
            margin-bottom: 18px;
            border-radius: 18px;
            border: 1px solid rgba(16, 185, 129, 0.18);
            background: rgba(16, 185, 129, 0.09);
            padding: 14px 16px;
            font-size: 0.92rem;
            line-height: 1.55;
            color: #047857;
        }

        .forgot-mail-badge {
            width: 92px;
            height: 92px;
            margin: 0 auto 26px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: linear-gradient(180deg, rgba(225, 229, 255, 0.96), rgba(238, 241, 255, 0.9));
            box-shadow: 0 18px 38px rgba(146, 156, 231, 0.16);
            color: #1f46aa;
        }

        .forgot-label {
            display: block;
            margin-bottom: 10px;
            font-size: 0.95rem;
            font-weight: 700;
            color: #1f325f;
        }

        .forgot-control {
            display: flex;
            align-items: center;
            gap: 14px;
            min-height: 52px;
            padding: 0 18px;
            border-radius: 12px;
            border: 1.5px solid #dce3f0;
            background: linear-gradient(180deg, #fbfcff 0%, #f5f7fb 100%);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,0.98),
                0 10px 28px rgba(117, 132, 165, 0.06);
            transition: border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
        }

        .forgot-control:focus-within {
            transform: translateY(-1px);
            border-color: rgba(59, 130, 246, 0.36);
            box-shadow:
                0 0 0 5px rgba(59, 130, 246, 0.08),
                0 14px 34px rgba(59, 130, 246, 0.08);
        }

        .forgot-control.is-invalid {
            border-color: rgba(239, 68, 68, 0.36);
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.08);
        }

        .forgot-input {
            flex: 1;
            border: 0;
            background: transparent;
            padding: 0;
            font-size: 0.98rem;
            color: #162447;
            outline: none;
        }

        .forgot-input::placeholder {
            color: #9aa6bc;
        }

        .forgot-icon {
            width: 22px;
            height: 22px;
            flex: 0 0 auto;
            color: #b4bccb;
        }

        .forgot-error {
            margin-top: 10px;
            padding-left: 8px;
            font-size: 0.88rem;
            color: #dc2626;
        }

        .forgot-submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 14px;
            width: 100%;
            min-height: 48px;
            margin-top: 30px;
            border: 0;
            border-radius: 999px;
            background: linear-gradient(90deg, #123a97 0%, #5033a3 40%, #d14f2d 72%, #f0a11a 100%);
            box-shadow:
                0 18px 34px rgba(18, 58, 151, 0.2),
                0 16px 30px rgba(240, 161, 26, 0.18);
            color: #ffffff;
            font-size: 0.94rem;
            font-weight: 800;
            letter-spacing: 0.11em;
            text-transform: uppercase;
            transition: transform 180ms ease, box-shadow 180ms ease, filter 180ms ease;
        }

        .forgot-submit:hover {
            transform: translateY(-2px);
            filter: saturate(1.04);
            box-shadow:
                0 22px 42px rgba(18, 58, 151, 0.24),
                0 18px 34px rgba(240, 161, 26, 0.2);
        }

        .forgot-submit:focus-visible {
            outline: none;
            box-shadow:
                0 0 0 5px rgba(80, 51, 163, 0.12),
                0 22px 42px rgba(18, 58, 151, 0.24);
        }

        .forgot-submit svg {
            width: 20px;
            height: 20px;
        }

        .forgot-divider {
            width: min(100%, 220px);
            margin: 24px auto 0;
            display: flex;
            align-items: center;
            gap: 16px;
            color: #57667f;
            font-weight: 700;
        }

        .forgot-divider::before,
        .forgot-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(177, 187, 209, 0.9), transparent);
        }

        .forgot-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            min-width: 276px;
            min-height: 50px;
            margin-top: 18px;
            padding: 0 26px;
            border-radius: 999px;
            border: 1.6px solid rgba(146, 166, 221, 0.6);
            background: rgba(255, 255, 255, 0.62);
            color: #214099;
            font-size: 1rem;
            font-weight: 700;
            text-decoration: none;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.88);
            transition: transform 180ms ease, border-color 180ms ease, box-shadow 180ms ease;
        }

        .forgot-secondary:hover {
            transform: translateY(-1px);
            border-color: rgba(59, 130, 246, 0.52);
            box-shadow: 0 12px 26px rgba(110, 129, 171, 0.12);
        }

        .forgot-footer {
            margin-top: 38px;
            display: inline-flex;
            align-items: center;
            gap: 16px;
            padding: 10px 14px;
            border-radius: 18px;
            background: transparent;
            box-shadow: none;
            color: #677791;
            text-align: left;
        }

        .forgot-footer-badge {
            width: 56px;
            height: 56px;
            flex: 0 0 auto;
            display: grid;
            place-items: center;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.88);
            color: #2447a3;
            box-shadow: 0 12px 24px rgba(103, 119, 145, 0.12);
        }

        .forgot-footer-text {
            font-size: 0.95rem;
            line-height: 1.55;
        }

        @media (max-width: 900px) {
            .forgot-premium-page {
                padding-top: 36px;
            }

            .forgot-wave-left,
            .forgot-wave-right,
            .forgot-bottom-blob,
            .forgot-gold-line-left,
            .forgot-gold-line-right,
            .forgot-watermark,
            .forgot-dots {
                opacity: 0.45;
            }

            .forgot-card {
                padding: 28px 20px 28px;
            }

            .forgot-title {
                font-size: 2.5rem;
            }

            .forgot-subtitle {
                font-size: 0.96rem;
            }

            .forgot-footer {
                width: min(100%, 560px);
            }
        }

        @media (max-width: 640px) {
            .forgot-premium-page {
                padding: 26px 14px 24px;
            }

            .forgot-logo {
                width: 78px;
                height: 78px;
                margin-bottom: 14px;
            }

            .forgot-title {
                font-size: 2.15rem;
            }

            .forgot-subtitle {
                max-width: 95%;
                font-size: 0.94rem;
            }

            .forgot-card {
                border-radius: 22px;
            }

            .forgot-divider {
                width: 100%;
            }

            .forgot-secondary {
                width: 100%;
                min-width: 0;
            }

            .forgot-footer {
                flex-direction: column;
                align-items: flex-start;
                gap: 12px;
                width: 100%;
            }
        }
    </style>

    <div class="forgot-premium-page">
        <div class="forgot-wave-left" aria-hidden="true"></div>
        <div class="forgot-wave-right" aria-hidden="true"></div>
        <div class="forgot-bottom-blob" aria-hidden="true"></div>
        <div class="forgot-dots" aria-hidden="true"></div>
        <div class="forgot-gold-line-left" aria-hidden="true"></div>
        <div class="forgot-gold-line-right" aria-hidden="true"></div>

        <svg class="forgot-watermark" viewBox="0 0 320 320" fill="none" aria-hidden="true">
            <path d="M52 242H268" stroke="currentColor" stroke-width="7" stroke-linecap="round"/>
            <path d="M86 232V134M234 232V134M130 232V168M190 232V168" stroke="currentColor" stroke-width="7" stroke-linecap="round"/>
            <path d="M78 134H242" stroke="currentColor" stroke-width="7" stroke-linecap="round"/>
            <path d="M58 134L160 82L262 134" stroke="currentColor" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M44 272H164" stroke="currentColor" stroke-width="7" stroke-linecap="round"/>
            <path d="M58 272L80 224H150L164 272" stroke="currentColor" stroke-width="7" stroke-linejoin="round"/>
            <path d="M57 116C57 88 79 66 107 66C135 66 157 88 157 116" stroke="currentColor" stroke-width="7" stroke-linecap="round"/>
            <path d="M160 54V22" stroke="currentColor" stroke-width="7" stroke-linecap="round"/>
        </svg>

        <div class="forgot-shell">
            <img src="{{ $logo }}" alt="DILG Logo" class="forgot-logo">

            <h1 class="forgot-title">Forgot Password?</h1>
            <p class="forgot-subtitle">
                No problem. Just let us know your email address and we’ll email you a password reset link that will allow you to choose a new one.
            </p>

            <div class="forgot-card">
                <x-auth-session-status class="forgot-status" :status="session('status')" />

                <div class="forgot-mail-badge" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <path d="M4 7.75C4 6.23122 5.23122 5 6.75 5H17.25C18.7688 5 20 6.23122 20 7.75V16.25C20 17.7688 18.7688 19 17.25 19H6.75C5.23122 19 4 17.7688 4 16.25V7.75Z" stroke="currentColor" stroke-width="1.9"/>
                        <path d="M5 7L11.0593 11.5445C11.6184 11.9638 12.3816 11.9638 12.9407 11.5445L19 7" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                    </svg>
                </div>

                <form method="POST" action="{{ route('password.email') }}">
                    @csrf

                    <label for="email" class="forgot-label">Email</label>
                    <div @class(['forgot-control', 'is-invalid' => $errors->has('email')])>
                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            class="forgot-input"
                            placeholder="Enter your email address"
                        >

                        <svg class="forgot-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M4 7.75C4 6.23122 5.23122 5 6.75 5H17.25C18.7688 5 20 6.23122 20 7.75V16.25C20 17.7688 18.7688 19 17.25 19H6.75C5.23122 19 4 17.7688 4 16.25V7.75Z" stroke="currentColor" stroke-width="1.8"/>
                            <path d="M5 7L11.0593 11.5445C11.6184 11.9638 12.3816 11.9638 12.9407 11.5445L19 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                        </svg>
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="forgot-error" />

                    <button type="submit" class="forgot-submit">
                        <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M21.25 3.75L10.25 14.75" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                            <path d="M21.25 3.75L14.25 20.25L10.25 14.75L4.75 10.75L21.25 3.75Z" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/>
                        </svg>
                        <span>Email Password Reset Link</span>
                    </button>
                </form>
            </div>

            <div class="forgot-divider">or</div>

            <a href="{{ route('login') }}" class="forgot-secondary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span>Back to Login</span>
            </a>

            <div class="forgot-footer">
                <div class="forgot-footer-badge" aria-hidden="true">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                        <path d="M12 3L19 6V11C19 15.4183 16.134 19.4312 12 21C7.866 19.4312 5 15.4183 5 11V6L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        <path d="M9.75 11.5L11.3 13.05L14.65 9.7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>

                <div class="forgot-footer-text">
                    Your security is our priority. We will never share your information with anyone.
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
