<x-guest-layout>
    @php
        $logo = asset('dilglogo.png');
        $lexCharacter = asset('images/isay.png');
    @endphp

    <style>
        :root {
            --gov-blue: #0f4dd8;
            --gov-deep-blue: #0a2f8f;
            --gov-red: #d81f3f;
            --gov-orange: #f47d20;
            --gov-yellow: #ffd533;
            --gov-ink: #14213d;
            --gov-panel: rgba(255, 255, 255, 0.94);
            --gov-panel-line: rgba(210, 219, 236, 0.75);
            --gov-field-bg: linear-gradient(180deg, rgba(248, 250, 255, 0.96), rgba(241, 245, 252, 0.96));
            --gov-muted: #657392;
        }

        html,
        body {
            min-height: 100%;
            margin: 0;
            background:
                radial-gradient(900px 600px at 12% 18%, rgba(59, 130, 246, 0.18), transparent 58%),
                radial-gradient(760px 540px at 84% 24%, rgba(244, 114, 182, 0.14), transparent 56%),
                linear-gradient(180deg, #e8edf7 0%, #d7dee9 100%);
            background-attachment: fixed;
        }

        .gov-login-page {
            position: relative;
            min-height: 100vh;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            font-family: 'Figtree', sans-serif;
        }

        .gov-login-page::before,
        .gov-login-page::after {
            content: '';
            position: fixed;
            inset: auto;
            width: 30rem;
            height: 30rem;
            border-radius: 999px;
            filter: blur(90px);
            pointer-events: none;
            opacity: 0.45;
        }

        .gov-login-page::before {
            top: -9rem;
            left: -8rem;
            background: rgba(59, 130, 246, 0.35);
        }

        .gov-login-page::after {
            right: -10rem;
            bottom: -10rem;
            background: rgba(251, 191, 36, 0.34);
        }

        .gov-login-shell {
            position: relative;
            width: min(1460px, 100%);
            min-height: min(980px, calc(100vh - 40px));
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.7);
            border-radius: 40px;
            background: rgba(255, 255, 255, 0.2);
            box-shadow:
                0 40px 120px rgba(15, 23, 42, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.35);
            backdrop-filter: blur(18px);
        }

        .gov-login-grid {
            display: grid;
            grid-template-columns: minmax(0, 1.16fr) minmax(420px, 0.84fr);
            min-height: min(980px, calc(100vh - 40px));
        }

        .gov-brand {
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            padding: 44px 42px 38px 42px;
            color: #fff;
            background:
                radial-gradient(circle at 14% 16%, rgba(255, 255, 255, 0.14), transparent 22%),
                radial-gradient(circle at 56% 42%, rgba(255, 255, 255, 0.09), transparent 26%),
                radial-gradient(circle at 84% 72%, rgba(255, 255, 255, 0.16), transparent 20%),
                linear-gradient(125deg,
                    #0e4acb 0%,
                    #2142b6 17%,
                    #5130a1 33%,
                    #b51f58 53%,
                    #eb4d20 74%,
                    #f8bb1f 90%,
                    #ffd73d 100%);
        }

        .gov-brand::before {
            content: '';
            position: absolute;
            inset: auto auto -14% -10%;
            width: 110%;
            height: 44%;
            background:
                repeating-radial-gradient(circle at 0 100%, rgba(255, 255, 255, 0.1) 0 2px, transparent 2px 16px);
            opacity: 0.18;
            transform: rotate(-4deg);
            pointer-events: none;
        }

        .gov-brand::after {
            content: '';
            position: absolute;
            right: -8%;
            top: -8%;
            width: 42%;
            height: 42%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.18), transparent 62%);
            filter: blur(12px);
            pointer-events: none;
        }

        .gov-brand__dots,
        .gov-brand__waves {
            position: absolute;
            pointer-events: none;
            opacity: 0.32;
        }

        .gov-brand__dots {
            top: 155px;
            right: 58px;
            width: 106px;
            height: 106px;
            background-image: radial-gradient(circle, rgba(255, 255, 255, 0.9) 1.2px, transparent 1.4px);
            background-size: 18px 18px;
        }

        .gov-brand__waves {
            left: -24px;
            bottom: 42px;
            width: 55%;
            height: 160px;
            background:
                radial-gradient(120% 100% at 0% 100%, transparent 58%, rgba(255, 255, 255, 0.16) 58.5%, transparent 59.6%),
                radial-gradient(116% 96% at 0% 100%, transparent 63%, rgba(255, 255, 255, 0.14) 63.5%, transparent 64.6%),
                radial-gradient(112% 92% at 0% 100%, transparent 68%, rgba(255, 255, 255, 0.12) 68.5%, transparent 69.6%),
                radial-gradient(108% 88% at 0% 100%, transparent 73%, rgba(255, 255, 255, 0.1) 73.5%, transparent 74.6%);
        }

        .gov-brand__top {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: flex-start;
            gap: 18px;
        }

        .gov-brand__seal {
            width: 82px;
            height: 82px;
            flex: 0 0 auto;
            border-radius: 999px;
            padding: 8px;
            background: rgba(255, 255, 255, 0.16);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.35),
                0 18px 42px rgba(5, 17, 55, 0.22);
            backdrop-filter: blur(14px);
        }

        .gov-brand__seal img,
        .gov-panel__logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            display: block;
        }

        .gov-brand__agency {
            max-width: 430px;
        }

        .gov-brand__agency-title {
            font-size: clamp(1.5rem, 1.2rem + 1vw, 2rem);
            font-weight: 800;
            line-height: 1.08;
            letter-spacing: -0.03em;
            text-transform: uppercase;
        }

        .gov-brand__agency-subtitle {
            margin-top: 10px;
            font-size: 0.95rem;
            font-weight: 600;
            letter-spacing: 0.38em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, 0.88);
        }

        .gov-brand__agency-motto {
            margin-top: 8px;
            font-size: 1rem;
            font-style: italic;
            color: rgba(255, 255, 255, 0.82);
        }

        .gov-brand__content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            max-width: 420px;
        }

        .gov-brand__main {
            position: relative;
            z-index: 2;
            flex: 1;
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(300px, 390px);
            align-items: end;
            gap: 14px;
            padding-top: 76px;
        }

        .gov-brand__product {
            display: inline-flex;
            flex-direction: column;
            gap: 12px;
            padding: 18px 22px;
            width: fit-content;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            background: rgba(255, 255, 255, 0.09);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.22),
                0 20px 44px rgba(9, 19, 61, 0.16);
            backdrop-filter: blur(18px);
        }

        .gov-brand__product-name {
            font-size: 0.92rem;
            font-weight: 800;
            letter-spacing: 0.32em;
            text-transform: uppercase;
            color: #cde4ff;
        }

        .gov-brand__product-copy {
            max-width: 260px;
            font-size: 0.98rem;
            line-height: 1.65;
            color: rgba(255, 255, 255, 0.9);
        }

        .gov-brand__headline {
            margin-top: 28px;
            font-size: clamp(3rem, 2rem + 2vw, 4.5rem);
            font-weight: 800;
            line-height: 1.04;
            letter-spacing: -0.05em;
            text-wrap: balance;
        }

        .gov-brand__subtitle {
            margin-top: 20px;
            max-width: 390px;
            font-size: 1.22rem;
            line-height: 1.65;
            color: rgba(255, 255, 255, 0.9);
        }

        .gov-hero {
            position: relative;
            z-index: 2;
            width: 100%;
            max-width: 390px;
            justify-self: end;
            align-self: end;
            margin-right: -8px;
            pointer-events: none;
            animation: gov-hero-float 5.4s ease-in-out infinite;
        }

        .gov-hero__glow {
            position: absolute;
            left: 50%;
            bottom: 12px;
            width: 68%;
            height: 30px;
            border-radius: 999px;
            background: radial-gradient(circle, rgba(15, 23, 42, 0.42) 0%, rgba(15, 23, 42, 0.14) 46%, rgba(15, 23, 42, 0) 78%);
            transform: translateX(-50%);
            filter: blur(10px);
        }

        .gov-hero__stage {
            position: relative;
            width: 100%;
            height: 620px;|
        }

        .gov-hero__character {
            position: absolute;
            left: 0;
            bottom: 22px;
            display: block;
            width: 270px;
            height: auto;
            z-index: 2;
            filter: drop-shadow(0 30px 40px rgba(15, 23, 42, 0.26;))
            /* filter: drop-shadow(0 30px 40px rgba(15, 23, 42, 0.26;)) */
            
        }

        .gov-hero__scale {
            position: absolute;
            right: 0;
            top: 180px;
            width: 190px;
            height: auto;
            z-index: 3;
            filter: drop-shadow(0 18px 28px rgba(77, 28, 5, 0.24));
        }

        .gov-panel {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 34px;
            background:
                radial-gradient(circle at 16% 14%, rgba(30, 64, 175, 0.06), transparent 26%),
                radial-gradient(circle at 84% 78%, rgba(251, 191, 36, 0.08), transparent 22%),
                linear-gradient(180deg, rgba(255, 255, 255, 0.98) 0%, rgba(249, 251, 255, 0.94) 100%);
        }

        .gov-panel::before {
            content: '';
            position: absolute;
            inset: 20px;
            border-radius: 34px;
            border: 1px solid rgba(222, 229, 241, 0.55);
            pointer-events: none;
        }

        .gov-panel__close {
            position: absolute;
            top: 26px;
            right: 26px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 64px;
            height: 64px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(222, 229, 241, 0.8);
            box-shadow:
                0 18px 32px rgba(15, 23, 42, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.8);
            color: #6b7280;
            backdrop-filter: blur(10px);
            transition: transform 180ms ease, box-shadow 180ms ease, color 180ms ease;
        }

        .gov-panel__close:hover {
            color: #1f2937;
            transform: translateY(-1px);
            box-shadow:
                0 22px 38px rgba(15, 23, 42, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.88);
        }

        .gov-panel__card {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 510px;
            padding: 22px 28px 18px;
        }

        .gov-panel__logo {
            width: 108px;
            height: 108px;
            margin: 0 auto;
            padding: 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.84);
            box-shadow:
                0 24px 48px rgba(15, 23, 42, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.9);
        }

        .gov-panel__title {
            margin-top: 24px;
            text-align: center;
            font-size: clamp(2.5rem, 2rem + 1vw, 3.6rem);
            font-weight: 800;
            line-height: 1.05;
            letter-spacing: -0.05em;
            color: var(--gov-ink);
        }

        .gov-panel__subtitle {
            margin: 14px auto 0;
            max-width: 430px;
            text-align: center;
            font-size: 1.15rem;
            line-height: 1.7;
            color: var(--gov-muted);
        }

        .gov-feedback {
            margin-top: 26px;
            border-radius: 22px;
            padding: 14px 18px;
            font-size: 0.94rem;
            line-height: 1.6;
        }

        .gov-feedback--status {
            border: 1px solid rgba(16, 185, 129, 0.18);
            background: rgba(16, 185, 129, 0.08);
            color: #047857;
        }

        .gov-feedback--error {
            border: 1px solid rgba(239, 68, 68, 0.16);
            background: rgba(239, 68, 68, 0.08);
            color: #b91c1c;
        }

        .gov-login-form {
            margin-top: 34px;
        }

        .gov-form-grid {
            display: grid;
            gap: 26px;
        }

        .gov-field__label {
            display: block;
            margin-bottom: 12px;
            font-size: 0.96rem;
            font-weight: 700;
            color: #334155;
        }

        .gov-field__control {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 0 22px;
            min-height: 72px;
            border-radius: 999px;
            border: 1px solid rgba(226, 232, 240, 0.92);
            background: var(--gov-field-bg);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.94),
                inset 0 -10px 22px rgba(15, 23, 42, 0.02),
                0 14px 26px rgba(15, 23, 42, 0.04);
            transition: border-color 180ms ease, box-shadow 180ms ease, transform 180ms ease;
        }

        .gov-field__control:focus-within {
            border-color: rgba(37, 99, 235, 0.32);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.98),
                0 0 0 5px rgba(37, 99, 235, 0.08),
                0 16px 30px rgba(15, 23, 42, 0.06);
            transform: translateY(-1px);
        }

        .gov-field__control.is-invalid {
            border-color: rgba(239, 68, 68, 0.36);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.94),
                0 0 0 5px rgba(239, 68, 68, 0.08);
        }

        .gov-field__icon {
            width: 24px;
            height: 24px;
            flex: 0 0 auto;
            color: #94a3b8;
        }

        .gov-field__input {
            width: 100%;
            border: 0;
            background: transparent;
            padding: 0;
            font-size: 1.02rem;
            line-height: 1.4;
            color: #0f172a;
            outline: none;
            box-shadow: none;
        }

        .gov-field__input::placeholder {
            color: #94a3b8;
        }

        .gov-field__error {
            margin-top: 10px;
            padding-left: 10px;
            font-size: 0.85rem;
            color: #dc2626;
        }

        .gov-login-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            margin-top: 22px;
            color: var(--gov-muted);
        }

        .gov-check {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            font-size: 0.98rem;
            font-weight: 600;
            color: #475569;
        }

        .gov-check input {
            appearance: none;
            width: 24px;
            height: 24px;
            margin: 0;
            border-radius: 8px;
            border: 1.5px solid #d1d9e7;
            background: linear-gradient(180deg, #ffffff 0%, #f6f8fc 100%);
            box-shadow:
                inset 0 1px 0 rgba(255, 255, 255, 0.9),
                0 8px 18px rgba(148, 163, 184, 0.18);
            display: grid;
            place-items: center;
            cursor: pointer;
        }

        .gov-check input::after {
            content: '';
            width: 12px;
            height: 12px;
            border-radius: 4px;
            background: linear-gradient(135deg, var(--gov-blue), var(--gov-red), var(--gov-yellow));
            transform: scale(0);
            transition: transform 180ms ease;
        }

        .gov-check input:checked::after {
            transform: scale(1);
        }

        .gov-login-link {
            font-size: 0.98rem;
            font-weight: 700;
            color: #5b6a88;
            transition: color 180ms ease;
        }

        .gov-login-link:hover {
            color: #1e40af;
        }

        .gov-login-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            min-height: 70px;
            margin-top: 32px;
            border: 0;
            border-radius: 999px;
            background: linear-gradient(90deg, #1249c9 0%, #254fcb 20%, #8c2d96 47%, #e2332b 68%, #f49c1a 86%, #ffd239 100%);
            box-shadow:
                0 20px 40px rgba(28, 78, 216, 0.22),
                0 16px 24px rgba(248, 187, 28, 0.12),
                inset 0 1px 0 rgba(255, 255, 255, 0.18);
            color: #ffffff;
            font-size: 1.18rem;
            font-weight: 800;
            letter-spacing: 0.45em;
            text-transform: uppercase;
            transition: transform 180ms ease, box-shadow 180ms ease, filter 180ms ease;
        }

        .gov-login-button:hover {
            transform: translateY(-2px);
            box-shadow:
                0 26px 48px rgba(28, 78, 216, 0.28),
                0 18px 28px rgba(248, 187, 28, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.24);
            filter: saturate(1.03);
        }

        .gov-login-button:focus-visible {
            outline: none;
            box-shadow:
                0 0 0 6px rgba(37, 99, 235, 0.14),
                0 26px 48px rgba(28, 78, 216, 0.28);
        }

        .gov-panel__footer {
            margin-top: 28px;
            text-align: center;
            font-size: 1rem;
            color: var(--gov-muted);
        }

        .gov-panel__footer a {
            font-weight: 800;
            color: #0f172a;
        }

        @keyframes gov-hero-float {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }

        @media (max-width: 1180px) {
            .gov-login-grid {
                grid-template-columns: 1fr;
            }

            .gov-brand {
                min-height: 760px;
            }

            .gov-brand__main {
                grid-template-columns: 1fr;
                justify-items: center;
                padding-top: 52px;
                gap: 28px;
            }

            .gov-brand__content {
                max-width: 560px;
                justify-self: start;
            }

            .gov-hero {
                width: min(440px, 80vw);
                max-width: none;
                margin-right: 0;
                justify-self: center;
            }

            .gov-hero__stage {
                height: 620px;
                
            }

            .gov-panel {
                padding-top: 96px;
            }
        }

        @media (max-width: 760px) {
            .gov-login-page {
                padding: 12px;
            }

            .gov-login-shell,
            .gov-login-grid {
                min-height: calc(100vh - 24px);
                border-radius: 30px;
            }

            .gov-brand {
                padding: 28px 22px 280px;
            }

            .gov-brand__top {
                gap: 14px;
            }

            .gov-brand__seal {
                width: 68px;
                height: 68px;
            }

            .gov-brand__agency-title {
                font-size: 1.12rem;
            }

            .gov-brand__agency-subtitle {
                font-size: 0.72rem;
                letter-spacing: 0.26em;
            }

            .gov-brand__agency-motto {
                font-size: 0.88rem;
            }

            .gov-brand__content {
                max-width: 100%;
            }

            .gov-brand__product {
                padding: 14px 16px;
            }

            .gov-brand__headline {
                margin-top: 20px;
                font-size: 2.45rem;
            }

            .gov-brand__subtitle {
                font-size: 1rem;
            }

            .gov-brand__dots {
                top: 128px;
                right: 22px;
                transform: scale(0.78);
                transform-origin: top right;
            }

            .gov-brand__waves {
                width: 78%;
                bottom: 28px;
            }

            .gov-brand__main {
                padding-top: 38px;
                gap: 20px;
            }

            .gov-hero {
                width: min(360px, 84vw);
            }

            .gov-hero__stage {
                height: 560px;
                
            }

            .gov-hero__character {
                width: min(248px, 68vw);
                left: 8px;
                bottom: 20px;
               
            }

            .gov-hero__scale {
                width: min(168px, 43vw);
                right: 2px;
                top: 165px;
            }

            .gov-panel {
                padding: 84px 18px 32px;
            }

            .gov-panel__close {
                top: 18px;
                right: 18px;
                width: 54px;
                height: 54px;
            }

            .gov-panel__card {
                padding: 10px 4px 0;
            }

            .gov-panel__logo {
                width: 84px;
                height: 84px;
            }

            .gov-panel__subtitle {
                font-size: 1rem;
            }

            .gov-field__control {
                min-height: 64px;
                padding: 0 18px;
            }

            .gov-login-meta {
                flex-direction: column;
                align-items: flex-start;
            }

            .gov-login-button {
                min-height: 62px;
                font-size: 1rem;
                letter-spacing: 0.32em;
            }
        }
    </style>

    <div class="gov-login-page">
        <div class="gov-login-shell">
            <div class="gov-login-grid">
                <section class="gov-brand">
                    <div class="gov-brand__dots" aria-hidden="true"></div>
                    <div class="gov-brand__waves" aria-hidden="true"></div>

                    <div class="gov-brand__top">
                        <div class="gov-brand__seal">
                            <img src="{{ $logo }}" alt="DILG Logo">
                        </div>

                        <div class="gov-brand__agency">
                            <div class="gov-brand__agency-title">Department of the Interior and Local Government</div>
                            <div class="gov-brand__agency-subtitle">Cordillera Administrative Region</div>
                            <div class="gov-brand__agency-motto">Matino. Mahusay. at Maaasahan.</div>
                        </div>
                    </div>

                    <div class="gov-brand__main">
                        <div class="gov-brand__content">
                            <div class="gov-brand__product">
                                <div class="gov-brand__product-name">GABAY-Lex</div>
                                <div class="gov-brand__product-copy">Guidance and Advisory for Better Administration in Law</div>
                            </div>

                            <h1 class="gov-brand__headline">Smart legal support for efficient public service.</h1>
                            <p class="gov-brand__subtitle">Instant help, document assistance, and reliable guidance all in one place.</p>
                        </div>

                        <div class="gov-hero" aria-hidden="true">
                            <div class="gov-hero__glow"></div>
                            <div class="gov-hero__stage">
                                <img src="{{ $lexCharacter }}" alt="" class="gov-hero__character">
                                <svg class="gov-hero__scale" viewBox="0 0 220 250" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <defs>
                                        <linearGradient id="scaleGold" x1="110" y1="18" x2="110" y2="228" gradientUnits="userSpaceOnUse">
                                            <stop stop-color="#FFD76B"/>
                                            <stop offset="0.32" stop-color="#F8B83B"/>
                                            <stop offset="0.66" stop-color="#D68A14"/>
                                            <stop offset="1" stop-color="#B66C0A"/>
                                        </linearGradient>
                                    </defs>
                                    <path d="M109 28C118 28 125 35 125 44C125 50 121 55 116 58V79L161 108C165 111 168 116 168 121V179H175C181 179 186 184 186 190C186 196 181 201 175 201H130L143 214C147 218 147 225 143 229C139 233 132 233 128 229L109 210L90 229C86 233 79 233 75 229C71 225 71 218 75 214L88 201H43C37 201 32 196 32 190C32 184 37 179 43 179H50V121C50 116 53 111 57 108L102 79V58C97 55 93 50 93 44C93 35 100 28 109 28Z" fill="url(#scaleGold)"/>
                                    <path d="M109 7C117 7 124 14 124 22C124 30 117 37 109 37C101 37 94 30 94 22C94 14 101 7 109 7Z" fill="url(#scaleGold)"/>
                                    <path d="M109 48V184" stroke="#8A4E06" stroke-width="4" stroke-linecap="round"/>
                                    <path d="M59 108H159" stroke="#8A4E06" stroke-width="4" stroke-linecap="round"/>
                                    <path d="M77 108L53 146" stroke="#8A4E06" stroke-width="4" stroke-linecap="round"/>
                                    <path d="M141 108L165 146" stroke="#8A4E06" stroke-width="4" stroke-linecap="round"/>
                                    <path d="M42 146H88C88 166 78 178 65 178C52 178 42 166 42 146Z" fill="url(#scaleGold)" stroke="#8A4E06" stroke-width="4"/>
                                    <path d="M130 146H176C176 166 166 178 153 178C140 178 130 166 130 146Z" fill="url(#scaleGold)" stroke="#8A4E06" stroke-width="4"/>
                                    <path d="M109 184C127 184 142 192 145 201H73C76 192 91 184 109 184Z" fill="url(#scaleGold)" stroke="#8A4E06" stroke-width="4"/>
                                    <path d="M85 201H133V212H85V201Z" fill="#B66C0A"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="gov-panel">
                    <a href="{{ url('/') }}" class="gov-panel__close" aria-label="Close login">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                        </svg>
                    </a>

                    <div class="gov-panel__card">
                        <div class="gov-panel__logo">
                            <img src="{{ $logo }}" alt="DILG Logo">
                        </div>

                        <h2 class="gov-panel__title">Welcome Back</h2>
                        <p class="gov-panel__subtitle">Sign in to access your saved legal conversations.</p>

                        <x-auth-session-status class="gov-feedback gov-feedback--status" :status="session('status')" />

                        @if ($errors->any())
                            <div class="gov-feedback gov-feedback--error">
                                <ul class="list-disc pl-5">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form method="POST" action="{{ route('login') }}" class="gov-login-form">
                            @csrf

                            <div class="gov-form-grid">
                                <div class="gov-field">
                                    <label for="email" class="gov-field__label">Email address</label>
                                    <div @class(['gov-field__control', 'is-invalid' => $errors->has('email')])>
                                        <svg class="gov-field__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M4 7.75C4 6.23122 5.23122 5 6.75 5H17.25C18.7688 5 20 6.23122 20 7.75V16.25C20 17.7688 18.7688 19 17.25 19H6.75C5.23122 19 4 17.7688 4 16.25V7.75Z" stroke="currentColor" stroke-width="1.8"/>
                                            <path d="M5 7L11.0593 11.5445C11.6184 11.9638 12.3816 11.9638 12.9407 11.5445L19 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                        </svg>
                                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username" class="gov-field__input" placeholder="Enter your email">
                                    </div>
                                    <x-input-error :messages="$errors->get('email')" class="gov-field__error" />
                                </div>

                                <div class="gov-field">
                                    <label for="password" class="gov-field__label">Password</label>
                                    <div @class(['gov-field__control', 'is-invalid' => $errors->has('password')])>
                                        <svg class="gov-field__icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <path d="M8 10V8.75C8 6.67893 9.67893 5 11.75 5H12.25C14.3211 5 16 6.67893 16 8.75V10" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            <path d="M6.75 10H17.25C18.2165 10 19 10.7835 19 11.75V17.25C19 18.2165 18.2165 19 17.25 19H6.75C5.7835 19 5 18.2165 5 17.25V11.75C5 10.7835 5.7835 10 6.75 10Z" stroke="currentColor" stroke-width="1.8"/>
                                        </svg>
                                        <input id="password" name="password" type="password" required autocomplete="current-password" class="gov-field__input" placeholder="Enter your password">
                                    </div>
                                    <x-input-error :messages="$errors->get('password')" class="gov-field__error" />
                                </div>
                            </div>

                            <div class="gov-login-meta">
                                <label class="gov-check" for="remember_me">
                                    <input id="remember_me" type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <span>Remember me</span>
                                </label>

                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="gov-login-link">Forgot password?</a>
                                @endif
                            </div>

                            <button type="submit" class="gov-login-button">Log In</button>
                        </form>

                        @if (Route::has('register'))
                            <p class="gov-panel__footer">
                                Don't have an account? <a href="{{ route('register') }}">Sign Up</a>
                            </p>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-guest-layout>
