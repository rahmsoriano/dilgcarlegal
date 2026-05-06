<x-guest-layout>
    @php
        $logo = asset('dilglogo.png');
        $prefilledEmail = old('email', $request->email);
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
                radial-gradient(circle at top, rgba(196, 212, 248, 0.24), transparent 32%),
                linear-gradient(180deg, #fbfcfe 0%, #f2f5fb 100%);
            font-family: 'Poppins', 'Figtree', sans-serif;
        }

        .reset-premium-page {
            position: relative;
            min-height: 100vh;
            overflow: hidden;
            padding: 28px 20px 32px;
            color: #0e1f3d;
        }

        .reset-premium-page::before {
            content: '';
            position: absolute;
            inset: 0;
            pointer-events: none;
            background:
                radial-gradient(circle at 24% 16%, rgba(255, 255, 255, 0.94), transparent 16%),
                radial-gradient(circle at 70% 8%, rgba(255, 248, 231, 0.8), transparent 10%),
                radial-gradient(circle at 26% 62%, rgba(255, 242, 205, 0.34), transparent 8%),
                radial-gradient(circle at 72% 58%, rgba(255, 255, 255, 0.84), transparent 8%);
        }

        .reset-wave-left,
        .reset-wave-right,
        .reset-bottom-blob,
        .reset-dots-left,
        .reset-dots-right,
        .reset-gold-line-left,
        .reset-gold-line-right,
        .reset-watermark-left,
        .reset-watermark-right,
        .reset-stars {
            position: absolute;
            pointer-events: none;
        }

        .reset-wave-left {
            left: -188px;
            top: -18px;
            width: 760px;
            height: 690px;
            border-radius: 48% 52% 42% 58% / 52% 34% 66% 48%;
            background:
                linear-gradient(180deg, rgba(255,255,255,0.96), rgba(248, 250, 255, 0.58)),
                radial-gradient(circle at 22% 18%, rgba(255,255,255,0.98), rgba(255,255,255,0.38));
            box-shadow: inset -30px 0 54px rgba(255,255,255,0.82);
            transform: rotate(8deg);
            opacity: 0.96;
        }

        .reset-wave-right {
            right: -154px;
            top: -22px;
            width: 630px;
            height: 860px;
            border-radius: 54% 46% 52% 48% / 42% 40% 60% 58%;
            background: linear-gradient(180deg, rgba(255,255,255,0.78), rgba(244,247,255,0.36));
            box-shadow: inset 26px 0 42px rgba(255,255,255,0.82);
            transform: rotate(-10deg);
            opacity: 0.82;
        }

        .reset-bottom-blob {
            left: -138px;
            bottom: -194px;
            width: 610px;
            height: 660px;
            border-radius: 46% 54% 0 0 / 54% 54% 0 0;
            background:
                radial-gradient(circle at 52% 30%, rgba(101, 135, 230, 0.36), rgba(14, 31, 61, 0.94) 68%);
            opacity: 0.98;
        }

        .reset-dots-left {
            top: 18px;
            left: 12px;
            width: 128px;
            height: 92px;
            opacity: 0.38;
            background-image: radial-gradient(circle, rgba(255,255,255,0.92) 1.6px, transparent 1.9px);
            background-size: 18px 18px;
        }

        .reset-dots-right {
            top: 18px;
            right: 12px;
            width: 156px;
            height: 112px;
            opacity: 0.34;
            background-image: radial-gradient(circle, rgba(228, 167, 56, 0.72) 1.2px, transparent 1.5px);
            background-size: 16px 16px;
        }

        .reset-gold-line-left {
            left: 92px;
            top: 22px;
            width: 332px;
            height: 420px;
            border: 1.2px solid rgba(223, 179, 67, 0.5);
            border-radius: 58% 42% 52% 48% / 46% 44% 56% 54%;
            transform: rotate(16deg);
        }

        .reset-gold-line-right {
            right: 102px;
            top: 162px;
            width: 306px;
            height: 514px;
            border: 1.1px solid rgba(223, 179, 67, 0.42);
            border-radius: 50%;
        }

        .reset-watermark-left {
            left: 28px;
            bottom: 112px;
            width: 276px;
            height: 276px;
            color: rgba(255, 255, 255, 0.08);
        }

        .reset-watermark-right {
            right: 22px;
            bottom: 160px;
            width: 356px;
            height: 356px;
            color: rgba(190, 203, 234, 0.22);
        }

        .reset-stars {
            inset: 0;
            background:
                radial-gradient(circle at 22% 24%, rgba(255,255,255,0.9) 0 2px, transparent 3px),
                radial-gradient(circle at 14% 63%, rgba(255,241,191,0.86) 0 1.8px, transparent 3px),
                radial-gradient(circle at 78% 56%, rgba(255,255,255,0.84) 0 1.8px, transparent 3px),
                radial-gradient(circle at 66% 8%, rgba(255,248,227,0.94) 0 2px, transparent 3px),
                radial-gradient(circle at 24% 90%, rgba(255,241,191,0.84) 0 1.6px, transparent 3px);
            opacity: 0.72;
        }

        .reset-shell {
            position: relative;
            z-index: 2;
            width: min(100%, 1180px);
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .reset-logo {
            width: 98px;
            height: 98px;
            margin: 0 auto 8px;
            display: block;
            object-fit: contain;
        }

        .reset-kicker {
            margin: 0;
            font-size: 1rem;
            line-height: 1.45;
            color: #4f607c;
            letter-spacing: 0.04em;
        }

        .reset-kicker strong {
            color: #234bb5;
            font-weight: 800;
            letter-spacing: 0.08em;
        }

        .reset-title {
            margin: 12px 0 0;
            font-size: clamp(2.8rem, 2.35rem + 1vw, 4rem);
            line-height: 1.06;
            font-weight: 800;
            letter-spacing: -0.05em;
            color: #102657;
        }

        .reset-subtitle {
            margin: 12px auto 0;
            max-width: 710px;
            font-size: 1rem;
            line-height: 1.6;
            color: #576987;
        }

        .reset-card {
            width: min(100%, 628px);
            margin-top: 22px;
            padding: 26px 28px 22px;
            border-radius: 28px;
            background: rgba(255,255,255,0.8);
            border: 1px solid rgba(255,255,255,0.9);
            box-shadow:
                0 22px 60px rgba(112, 128, 167, 0.16),
                inset 0 1px 0 rgba(255,255,255,0.96);
            backdrop-filter: blur(18px);
            text-align: left;
        }

        .reset-field + .reset-field {
            margin-top: 14px;
        }

        .reset-label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.95rem;
            font-weight: 700;
            color: #1a2e5b;
        }

        .reset-row {
            display: grid;
            grid-template-columns: 52px minmax(0, 1fr);
            gap: 8px;
            align-items: center;
        }

        .reset-leading {
            min-height: 48px;
            border-radius: 14px;
            display: grid;
            place-items: center;
            background: linear-gradient(180deg, rgba(234, 239, 255, 0.96), rgba(245, 247, 255, 0.92));
            color: #8397e6;
            box-shadow: 0 10px 24px rgba(125, 147, 225, 0.12);
        }

        .reset-control {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 48px;
            padding: 0 16px;
            border-radius: 14px;
            border: 1.5px solid #dce3f1;
            background: linear-gradient(180deg, #fcfdff 0%, #f5f8fd 100%);
            box-shadow:
                inset 0 1px 0 rgba(255,255,255,0.98),
                0 8px 18px rgba(117, 132, 165, 0.05);
            transition: border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
        }

        .reset-control:focus-within {
            transform: translateY(-1px);
            border-color: rgba(83, 137, 255, 0.38);
            box-shadow:
                0 0 0 5px rgba(83, 137, 255, 0.08),
                0 14px 26px rgba(83, 137, 255, 0.08);
        }

        .reset-control.is-invalid {
            border-color: rgba(239, 68, 68, 0.36);
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.08);
        }

        .reset-control.is-email {
            background: linear-gradient(180deg, #edf2ff 0%, #e7edfb 100%);
        }

        .reset-input {
            flex: 1;
            border: 0;
            background: transparent;
            padding: 0;
            font-size: 0.98rem;
            color: #152447;
            outline: none;
        }

        .reset-input::placeholder {
            color: #93a1ba;
        }

        .reset-icon {
            width: 22px;
            height: 22px;
            flex: 0 0 auto;
            color: #a5b0c5;
        }

        .reset-toggle {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 26px;
            height: 26px;
            border: 0;
            padding: 0;
            background: transparent;
            color: #a5b0c5;
            cursor: pointer;
        }

        .reset-meta {
            margin: 8px 0 0 76px;
        }

        .reset-strength-text,
        .reset-match {
            font-size: 0.88rem;
            font-weight: 600;
            color: #28a95f;
        }

        .reset-strength-text {
            margin-bottom: 6px;
        }

        .reset-strength-text.is-weak {
            color: #dc2626;
        }

        .reset-strength-text.is-fair {
            color: #d97706;
        }

        .reset-strength-text.is-good {
            color: #2563eb;
        }

        .reset-match.is-empty {
            color: #8a97b0;
        }

        .reset-match.is-mismatch {
            color: #dc2626;
        }

        .reset-strength-bar {
            display: grid;
            grid-template-columns: repeat(6, minmax(0, 1fr));
            gap: 4px;
            margin-top: 6px;
        }

        .reset-strength-bar span {
            display: block;
            height: 4px;
            border-radius: 999px;
            background: #dfe6f0;
        }

        .reset-strength-bar span.is-active {
            background: linear-gradient(90deg, #45bb73, #38a860);
        }

        .reset-strength-bar span.is-weak {
            background: linear-gradient(90deg, #ef4444, #dc2626);
        }

        .reset-strength-bar span.is-fair {
            background: linear-gradient(90deg, #f59e0b, #d97706);
        }

        .reset-strength-bar span.is-good {
            background: linear-gradient(90deg, #3b82f6, #2563eb);
        }

        .reset-match {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .reset-match.is-mismatch {
            color: #dc2626;
        }

        .reset-match svg {
            width: 14px;
            height: 14px;
        }

        .reset-rules {
            margin-top: 18px;
            padding: 16px 18px;
            display: grid;
            grid-template-columns: 62px minmax(0, 1fr);
            gap: 14px;
            border-radius: 18px;
            background: linear-gradient(180deg, rgba(243, 246, 253, 0.96), rgba(235, 240, 249, 0.9));
            border: 1px solid rgba(220, 227, 241, 0.88);
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.94);
        }

        .reset-rules-badge {
            width: 54px;
            height: 54px;
            display: grid;
            place-items: center;
            border-radius: 999px;
            background: linear-gradient(180deg, #e5ebff 0%, #eff3ff 100%);
            color: #1e46a8;
            box-shadow: 0 12px 26px rgba(123, 143, 214, 0.12);
        }

        .reset-rules-title {
            margin: 0 0 10px;
            font-size: 0.94rem;
            font-weight: 700;
            color: #1a2e5b;
        }

        .reset-checks {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px 18px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .reset-checks li {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-size: 0.86rem;
            color: #3e4f6d;
            white-space: nowrap;
        }

        .reset-checks li.is-pending {
            color: #7a879e;
        }

        .reset-checks li.is-pending svg {
            color: #c7d0de;
        }

        .reset-checks svg {
            width: 15px;
            height: 15px;
            flex: 0 0 auto;
            color: #31b164;
        }

        .reset-submit {
            display: flex;
            align-items: center;
            width: 100%;
            min-height: 58px;
            margin-top: 12px;
            padding: 0 22px;
            border: 0;
            border-radius: 999px;
            background: linear-gradient(90deg, #11369a 0%, #2147b3 22%, #5d2ea4 46%, #d44745 72%, #efb83a 100%);
            box-shadow:
                0 20px 38px rgba(24, 58, 150, 0.22),
                0 14px 28px rgba(239, 184, 58, 0.18);
            color: #ffffff;
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            transition: transform 180ms ease, box-shadow 180ms ease, filter 180ms ease;
        }

        .reset-submit:hover {
            transform: translateY(-2px);
            filter: saturate(1.04);
        }

        .reset-submit:disabled {
            cursor: not-allowed;
            filter: grayscale(0.08);
            opacity: 0.75;
            transform: none;
            box-shadow:
                0 14px 24px rgba(148, 163, 184, 0.16),
                0 8px 16px rgba(148, 163, 184, 0.1);
        }

        .reset-submit-main {
            flex: 1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .reset-submit-arrow {
            margin-left: auto;
            display: inline-flex;
            align-items: center;
        }

        .reset-error {
            margin-top: 8px;
            padding-left: 76px;
            font-size: 0.86rem;
            color: #dc2626;
        }

        .reset-divider {
            width: min(100%, 340px);
            margin: 18px auto 0;
            display: flex;
            align-items: center;
            gap: 16px;
            color: #57667f;
            font-weight: 700;
        }

        .reset-divider::before,
        .reset-divider::after {
            content: '';
            flex: 1;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(177, 187, 209, 0.9), transparent);
        }

        .reset-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            min-width: 336px;
            min-height: 52px;
            margin-top: 14px;
            padding: 0 28px;
            border-radius: 999px;
            border: 1.6px solid rgba(59, 94, 201, 0.72);
            background: rgba(255, 255, 255, 0.72);
            color: #2043a4;
            font-size: 1rem;
            font-weight: 700;
            text-decoration: none;
            box-shadow: inset 0 1px 0 rgba(255,255,255,0.92);
            transition: transform 180ms ease, border-color 180ms ease, box-shadow 180ms ease;
        }

        .reset-footer {
            margin-top: 18px;
            display: inline-flex;
            align-items: center;
            gap: 14px;
            padding: 10px 12px;
            color: #667894;
        }

        .reset-footer-badge {
            width: 46px;
            height: 46px;
            display: grid;
            place-items: center;
            border-radius: 15px;
            background: rgba(255,255,255,0.86);
            box-shadow: 0 10px 22px rgba(102, 120, 148, 0.12);
            color: #2348a8;
        }

        .reset-footer-text {
            font-size: 0.95rem;
            line-height: 1.55;
        }

        @media (max-width: 980px) {
            .reset-checks {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 700px) {
            .reset-premium-page {
                padding: 26px 14px 24px;
            }

            .reset-wave-left,
            .reset-wave-right,
            .reset-bottom-blob,
            .reset-gold-line-left,
            .reset-gold-line-right,
            .reset-watermark-left,
            .reset-watermark-right {
                opacity: 0.45;
            }

            .reset-card {
                padding: 22px 16px 18px;
            }

            .reset-row {
                grid-template-columns: 44px minmax(0, 1fr);
            }

            .reset-meta,
            .reset-error {
                margin-left: 0;
                padding-left: 0;
            }

            .reset-rules {
                grid-template-columns: 1fr;
            }

            .reset-checks {
                grid-template-columns: 1fr;
            }

            .reset-secondary {
                width: 100%;
                min-width: 0;
            }

            .reset-footer {
                align-items: flex-start;
                width: 100%;
            }
        }
    </style>

    <div class="reset-premium-page">
        <div class="reset-wave-left" aria-hidden="true"></div>
        <div class="reset-wave-right" aria-hidden="true"></div>
        <div class="reset-bottom-blob" aria-hidden="true"></div>
        <div class="reset-stars" aria-hidden="true"></div>
        <div class="reset-dots-left" aria-hidden="true"></div>
        <div class="reset-dots-right" aria-hidden="true"></div>
        <div class="reset-gold-line-left" aria-hidden="true"></div>
        <div class="reset-gold-line-right" aria-hidden="true"></div>

        <svg class="reset-watermark-left" viewBox="0 0 320 320" fill="none" aria-hidden="true">
            <path d="M52 242H268" stroke="currentColor" stroke-width="7" stroke-linecap="round"/>
            <path d="M86 232V134M234 232V134" stroke="currentColor" stroke-width="7" stroke-linecap="round"/>
            <path d="M78 134H242" stroke="currentColor" stroke-width="7" stroke-linecap="round"/>
            <path d="M58 134L160 82L262 134" stroke="currentColor" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M44 272H164" stroke="currentColor" stroke-width="7" stroke-linecap="round"/>
            <path d="M58 272L80 224H150L164 272" stroke="currentColor" stroke-width="7" stroke-linejoin="round"/>
            <path d="M57 116C57 88 79 66 107 66C135 66 157 88 157 116" stroke="currentColor" stroke-width="7" stroke-linecap="round"/>
        </svg>

        <svg class="reset-watermark-right" viewBox="0 0 360 360" fill="none" aria-hidden="true">
            <path d="M84 286H276" stroke="currentColor" stroke-width="8" stroke-linecap="round"/>
            <path d="M110 286L126 224H234L250 286" stroke="currentColor" stroke-width="8" stroke-linejoin="round"/>
            <path d="M144 220L214 258" stroke="currentColor" stroke-width="8" stroke-linecap="round"/>
            <path d="M112 84H248" stroke="currentColor" stroke-width="8" stroke-linecap="round"/>
            <path d="M96 84L180 38L264 84" stroke="currentColor" stroke-width="8" stroke-linejoin="round"/>
            <path d="M124 84V212M180 84V212M236 84V212" stroke="currentColor" stroke-width="8" stroke-linecap="round"/>
        </svg>

        <div class="reset-shell">
            <img src="{{ $logo }}" alt="DILG Logo" class="reset-logo">

            <p class="reset-kicker"><strong>GABAY-LEX</strong> Legal Security Portal</p>
            <h1 class="reset-title">Reset Your Password</h1>
            <p class="reset-subtitle">Create a strong new password to secure your legal research workspace and account.</p>

            <form method="POST" action="{{ route('password.store') }}" class="reset-card" id="reset-password-form">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="reset-field">
                    <label for="email" class="reset-label">Email Address</label>
                    <div class="reset-row">
                        <div class="reset-leading" aria-hidden="true">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                <path d="M4 7.75C4 6.23122 5.23122 5 6.75 5H17.25C18.7688 5 20 6.23122 20 7.75V16.25C20 17.7688 18.7688 19 17.25 19H6.75C5.23122 19 4 17.7688 4 16.25V7.75Z" stroke="currentColor" stroke-width="1.8"/>
                                <path d="M5 7L11.0593 11.5445C11.6184 11.9638 12.3816 11.9638 12.9407 11.5445L19 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                            </svg>
                        </div>
                        <div @class(['reset-control is-email', 'is-invalid' => $errors->has('email')])>
                            <input id="email" type="email" name="email" value="{{ $prefilledEmail }}" required autofocus autocomplete="username" class="reset-input" />
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="reset-error" />
                </div>

                <div class="reset-field">
                    <label for="password" class="reset-label">New Password</label>
                    <div class="reset-row">
                        <div class="reset-leading" aria-hidden="true">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                <path d="M8 10V8C8 5.79086 9.79086 4 12 4C14.2091 4 16 5.79086 16 8V10" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                <path d="M6 10H18C19.1046 10 20 10.8954 20 12V18C20 19.1046 19.1046 20 18 20H6C4.89543 20 4 19.1046 4 18V12C4 10.8954 4.89543 10 6 10Z" stroke="currentColor" stroke-width="1.9"/>
                            </svg>
                        </div>
                        <div @class(['reset-control', 'is-invalid' => $errors->has('password')])>
                            <input id="password" type="password" name="password" required autocomplete="new-password" class="reset-input" value="{{ old('password') }}" />
                            <button type="button" class="reset-toggle" data-password-toggle="password" aria-label="Show password">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M2 12C3.8 8.5 7.3 6 12 6C16.7 6 20.2 8.5 22 12C20.2 15.5 16.7 18 12 18C7.3 18 3.8 15.5 2 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="reset-meta">
                        <div class="reset-strength-text" id="password-strength-text">Password Strength: Strong</div>
                        <div class="reset-strength-bar" id="password-strength-bar" aria-hidden="true">
                            <span class="is-active"></span>
                            <span class="is-active"></span>
                            <span class="is-active"></span>
                            <span class="is-active"></span>
                            <span class="is-active"></span>
                            <span></span>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="reset-error" />
                </div>

                <div class="reset-field">
                    <label for="password_confirmation" class="reset-label">Confirm New Password</label>
                    <div class="reset-row">
                        <div class="reset-leading" aria-hidden="true">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none">
                                <path d="M8 10V8C8 5.79086 9.79086 4 12 4C14.2091 4 16 5.79086 16 8V10" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                <path d="M6 10H18C19.1046 10 20 10.8954 20 12V18C20 19.1046 19.1046 20 18 20H6C4.89543 20 4 19.1046 4 18V12C4 10.8954 4.89543 10 6 10Z" stroke="currentColor" stroke-width="1.9"/>
                            </svg>
                        </div>
                        <div class="reset-control">
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="reset-input" value="{{ old('password_confirmation') }}" />
                            <button type="button" class="reset-toggle" data-password-toggle="password_confirmation" aria-label="Show confirm password">
                                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M2 12C3.8 8.5 7.3 6 12 6C16.7 6 20.2 8.5 22 12C20.2 15.5 16.7 18 12 18C7.3 18 3.8 15.5 2 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="1.8"/>
                                </svg>
                            </button>
                        </div>
                    </div>
                    <div class="reset-meta">
                        <div class="reset-match" id="password-match-indicator">
                            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <circle cx="12" cy="12" r="10" fill="currentColor" opacity="0.16"></circle>
                                <path d="M8.5 12.2L10.8 14.5L15.7 9.6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span id="password-match-text">Passwords match</span>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="reset-error" />
                </div>

                <div class="reset-rules">
                    <div class="reset-rules-badge" aria-hidden="true">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none">
                            <path d="M12 3L19 6V11C19 15.4183 16.134 19.4312 12 21C7.866 19.4312 5 15.4183 5 11V6L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <path d="M9.75 11.5L11.3 13.05L14.65 9.7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>

                    <div>
                        <p class="reset-rules-title">Password must contain:</p>
                        <ul class="reset-checks">
                            <li class="is-pending" data-rule="length"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="currentColor" opacity="0.16"></circle><path d="M8.5 12.2L10.8 14.5L15.7 9.6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span>At least 12 characters</span></li>
                            <li class="is-pending" data-rule="uppercase"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="currentColor" opacity="0.16"></circle><path d="M8.5 12.2L10.8 14.5L15.7 9.6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Uppercase letter</span></li>
                            <li class="is-pending" data-rule="lowercase"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="currentColor" opacity="0.16"></circle><path d="M8.5 12.2L10.8 14.5L15.7 9.6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Lowercase letter</span></li>
                            <li class="is-pending" data-rule="number"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="currentColor" opacity="0.16"></circle><path d="M8.5 12.2L10.8 14.5L15.7 9.6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Number</span></li>
                            <li class="is-pending" data-rule="special"><svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" fill="currentColor" opacity="0.16"></circle><path d="M8.5 12.2L10.8 14.5L15.7 9.6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg><span>Special character (!@#$%^&*)</span></li>
                        </ul>
                    </div>
                </div>

                <button type="submit" class="reset-submit" id="reset-submit-button" disabled>
                    <span class="reset-submit-main">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M12 3L19 6V11C19 15.4183 16.134 19.4312 12 21C7.866 19.4312 5 15.4183 5 11V6L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                            <path d="M9.75 11.5L11.3 13.05L14.65 9.7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span>Reset Password</span>
                    </span>
                    <span class="reset-submit-arrow" aria-hidden="true">
                        <svg width="28" height="22" viewBox="0 0 30 22" fill="none">
                            <path d="M18 2L27 11L18 20" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M27 11H3" stroke="currentColor" stroke-width="2.8" stroke-linecap="round"/>
                        </svg>
                    </span>
                </button>
            </form>

            <div class="reset-divider">or</div>

            <a href="{{ route('login') }}" class="reset-secondary">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M10 12H21" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <span>Back to Login</span>
            </a>

            <div class="reset-footer">
                <div class="reset-footer-badge" aria-hidden="true">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                        <path d="M12 3L19 6V11C19 15.4183 16.134 19.4312 12 21C7.866 19.4312 5 15.4183 5 11V6L12 3Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                        <path d="M9.75 11.5L11.3 13.05L14.65 9.7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="reset-footer-text">Your account security is protected with encrypted authentication.</div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var passwordInput = document.getElementById('password');
            var confirmInput = document.getElementById('password_confirmation');
            var strengthText = document.getElementById('password-strength-text');
            var strengthBar = document.getElementById('password-strength-bar');
            var matchIndicator = document.getElementById('password-match-indicator');
            var matchText = document.getElementById('password-match-text');
            var ruleItems = Array.from(document.querySelectorAll('[data-rule]'));
            var submitButton = document.getElementById('reset-submit-button');

            document.querySelectorAll('[data-password-toggle]').forEach(function (button) {
                button.addEventListener('click', function () {
                    var input = document.getElementById(button.getAttribute('data-password-toggle'));

                    if (!input) {
                        return;
                    }

                    input.type = input.type === 'password' ? 'text' : 'password';
                });
            });

            if (!passwordInput || !confirmInput || !strengthText || !strengthBar || !matchIndicator || !matchText || !submitButton) {
                return;
            }

            var strengthSegments = Array.from(strengthBar.querySelectorAll('span'));

            function evaluatePassword(password) {
                return {
                    length: password.length >= 12,
                    uppercase: /[A-Z]/.test(password),
                    lowercase: /[a-z]/.test(password),
                    number: /\d/.test(password),
                    special: /[^A-Za-z0-9]/.test(password),
                };
            }

            function getStrength(password, checks) {
                if (!password) {
                    return { label: 'Required', level: 0, tone: 'weak' };
                }

                var passed = Object.values(checks).filter(Boolean).length;

                if (passed <= 2) {
                    return { label: 'Weak', level: Math.max(1, passed), tone: 'weak' };
                }

                if (passed === 3) {
                    return { label: 'Fair', level: 3, tone: 'fair' };
                }

                if (passed === 4) {
                    return { label: 'Good', level: 4, tone: 'good' };
                }

                return { label: 'Strong', level: 5, tone: 'strong' };
            }

            function updateStrengthUI(password) {
                var checks = evaluatePassword(password);
                var strength = getStrength(password, checks);

                strengthText.textContent = 'Password Strength: ' + strength.label;
                strengthText.classList.remove('is-weak', 'is-fair', 'is-good');

                if (strength.tone === 'weak') {
                    strengthText.classList.add('is-weak');
                } else if (strength.tone === 'fair') {
                    strengthText.classList.add('is-fair');
                } else if (strength.tone === 'good') {
                    strengthText.classList.add('is-good');
                }

                strengthSegments.forEach(function (segment, index) {
                    segment.classList.remove('is-active', 'is-weak', 'is-fair', 'is-good');

                    if (index < strength.level) {
                        if (strength.tone === 'weak') {
                            segment.classList.add('is-weak');
                        } else if (strength.tone === 'fair') {
                            segment.classList.add('is-fair');
                        } else if (strength.tone === 'good') {
                            segment.classList.add('is-good');
                        } else {
                            segment.classList.add('is-active');
                        }
                    }
                });

                return checks;
            }

            function updateRuleUI(password) {
                var checks = evaluatePassword(password);

                ruleItems.forEach(function (item) {
                    var key = item.getAttribute('data-rule');
                    var passed = Boolean(checks[key]);
                    item.classList.toggle('is-pending', !passed);
                });

                return checks;
            }

            function updateMatchUI() {
                var password = passwordInput.value;
                var confirmation = confirmInput.value;
                var hasConfirmation = confirmation.length > 0;
                var matches = hasConfirmation && password === confirmation;

                matchIndicator.classList.remove('is-empty', 'is-mismatch');

                if (!hasConfirmation) {
                    matchIndicator.classList.add('is-empty');
                    matchText.textContent = 'Confirm your password';
                    return;
                }

                if (matches) {
                    matchText.textContent = 'Passwords match';
                    return;
                }

                matchIndicator.classList.add('is-mismatch');
                matchText.textContent = 'Passwords do not match';
                return false;
            }

            function areAllRulesPassing(checks) {
                return Object.values(checks).every(Boolean);
            }

            function updateSubmitState() {
                var checks = evaluatePassword(passwordInput.value);
                var rulesPass = areAllRulesPassing(checks);
                var passwordsMatch = passwordInput.value.length > 0 && passwordInput.value === confirmInput.value;
                submitButton.disabled = !(rulesPass && passwordsMatch);
            }

            function syncPasswordUI() {
                updateStrengthUI(passwordInput.value);
                updateRuleUI(passwordInput.value);
                updateMatchUI();
                updateSubmitState();
            }

            passwordInput.addEventListener('input', syncPasswordUI);
            confirmInput.addEventListener('input', function () {
                updateMatchUI();
                updateSubmitState();
            });

            syncPasswordUI();
        })();
    </script>
</x-guest-layout>
