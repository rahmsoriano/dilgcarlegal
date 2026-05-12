@php
    $authModalLogo = asset('dilglogo.png');
    $authModalHero = asset('images/login-hero-exact.png');
    $registerOnlyFields = ['first_name', 'last_name', 'birthday', 'password_confirmation'];
    $registerErrorFields = ['first_name', 'last_name', 'birthday', 'email', 'password', 'password_confirmation'];
    $loginErrorFields = ['email', 'password'];
    $hasRegisterValidationErrors = false;

    foreach ($registerOnlyFields as $field) {
        if ($errors->has($field)) {
            $hasRegisterValidationErrors = true;
            break;
        }
    }

    $authMode = session('auth_mode') ?? old('auth_mode') ?? ($hasRegisterValidationErrors ? 'register' : 'login');
    $loginErrors = [];
    $registerErrors = [];

    foreach ($loginErrorFields as $field) {
        $loginErrors = array_merge($loginErrors, $errors->get($field));
    }

    foreach ($registerErrorFields as $field) {
        $registerErrors = array_merge($registerErrors, $errors->get($field));
    }

    $loginErrors = array_values(array_unique($loginErrors));
    $registerErrors = array_values(array_unique($registerErrors));
    $shouldAutoOpenAuthModal = session()->has('auth_mode') || old('auth_mode') !== null || ! empty($loginErrors) || ! empty($registerErrors);
@endphp

<style>
    .exact-auth-modal {
        font-family: 'Figtree', sans-serif;
    }

    .exact-auth-modal .exact-auth-shell {
        width: min(1020px, calc(100vw - 120px));
        min-height: min(660px, calc(100vh - 120px));
        display: grid;
        grid-template-columns: minmax(0, 1.08fr) minmax(410px, 0.92fr);
        overflow: hidden;
        border-radius: 34px;
        background: #ffffff;
        box-shadow: 0 26px 60px rgba(15, 23, 42, 0.2);
    }

    .exact-auth-modal .auth-modal-panel {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .exact-auth-modal [data-auth-views-root] {
        position: relative;
    }

    .exact-auth-modal .auth-view[aria-hidden="false"] {
        position: relative !important;
        inset: auto !important;
        visibility: visible;
        pointer-events: auto;
    }

    .exact-auth-modal .auth-view[aria-hidden="true"] {
        position: absolute !important;
        inset: 0 !important;
        visibility: hidden;
        pointer-events: none;
    }

    .exact-auth-modal .exact-auth-left {
        position: relative;
        overflow: hidden;
        padding: 32px 28px 28px 30px;
        color: #ffffff;
        background: linear-gradient(180deg, #091d57 0%, #122866 100%);
    }

    .exact-auth-modal .exact-auth-left::before {
        content: '';
        position: absolute;
        top: -8%;
        right: -112px;
        width: 210px;
        height: 118%;
        border-radius: 50%;
        border: 5px solid #e1a637;
        opacity: 0.98;
        z-index: 1;
    }

    .exact-auth-modal .exact-auth-left::after {
        content: '';
        position: absolute;
        left: -18%;
        right: 10%;
        bottom: -27%;
        height: 250px;
        border-radius: 50%;
        background:
            radial-gradient(140% 100% at 50% 0%, rgba(255,255,255,0.24), rgba(255,255,255,0.03) 60%, transparent 61%),
            linear-gradient(180deg, rgba(255,255,255,0.97), rgba(240,244,251,0.98));
        z-index: 1;
    }

    .exact-auth-modal .exact-auth-left-inner,
    .exact-auth-modal .exact-auth-right-inner {
        position: relative;
        z-index: 2;
    }

    .exact-auth-modal .exact-auth-top {
        display: flex;
        align-items: flex-start;
        gap: 16px;
    }

    .exact-auth-modal .exact-auth-seal {
        width: 58px;
        height: 58px;
        flex: 0 0 auto;
    }

    .exact-auth-modal .exact-auth-seal img,
    .exact-auth-modal .exact-auth-brand-girl img,
    .exact-auth-modal .exact-auth-form-seal img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .exact-auth-modal .exact-auth-office {
        max-width: 510px;
        padding-top: 6px;
    }

    .exact-auth-modal .exact-auth-office-title {
        margin: 0;
        font-size: 12px;
        line-height: 1.28;
        font-weight: 800;
        text-transform: uppercase;
    }

    .exact-auth-modal .exact-auth-office-region {
        margin-top: 7px;
        font-size: 10px;
        line-height: 1.5;
        letter-spacing: 0.36em;
        text-transform: uppercase;
        color: rgba(255,255,255,0.9);
    }

    .exact-auth-modal .exact-auth-office-motto {
        margin-top: 6px;
        font-size: 11px;
        font-style: italic;
        color: rgba(255,255,255,0.84);
    }

    .exact-auth-modal .exact-auth-watermark {
        position: absolute;
        left: 18px;
        top: 146px;
        width: 290px;
        height: 220px;
        opacity: 0.055;
        z-index: 1;
    }

    .exact-auth-modal .exact-auth-dots {
        position: absolute;
        right: 26px;
        top: 196px;
        width: 86px;
        height: 96px;
        opacity: 0.22;
        z-index: 1;
        background-image: radial-gradient(circle, rgba(255,255,255,0.95) 1.25px, transparent 1.6px);
        background-size: 18px 18px;
    }

    .exact-auth-modal .exact-auth-copy {
        position: relative;
        z-index: 2;
        max-width: 290px;
        padding-top: 88px;
        padding-bottom: 138px;
    }

    .exact-auth-modal .exact-auth-product {
        margin: 0;
        font-size: 28px;
        line-height: 1;
        font-weight: 800;
        letter-spacing: 0.08em;
        color: #ffde15;
    }

    .exact-auth-modal .exact-auth-rule {
        width: 56px;
        height: 4px;
        margin: 10px 0 12px;
        border-radius: 999px;
        background: #ffde15;
    }

    .exact-auth-modal .exact-auth-copy p {
        margin: 0;
    }

    .exact-auth-modal .exact-auth-copy-small {
        font-size: 12px;
        line-height: 1.45;
        color: rgba(255,255,255,0.92);
    }

    .exact-auth-modal .exact-auth-headline {
        margin: 28px 0 0;
        font-size: 20px;
        line-height: 1.18;
        font-weight: 800;
        letter-spacing: -0.04em;
        color: #ffffff;
    }

    .exact-auth-modal .exact-auth-subrule {
        width: 48px;
        height: 3px;
        margin: 18px 0 18px;
        border-radius: 999px;
        background: rgba(240, 187, 76, 0.55);
    }

    .exact-auth-modal .exact-auth-copy-body {
        max-width: 388px;
        font-size: 12px;
        line-height: 1.55;
        color: rgba(255,255,255,0.92);
    }

    .exact-auth-modal .exact-auth-brand-girl {
        position: absolute;
        right: -8px;
        bottom: 86px;
        width: min(220px, 35%);
        height: auto;
        z-index: 2;
        filter: drop-shadow(0 24px 36px rgba(0,0,0,0.24));
    }

    .exact-auth-modal .exact-auth-right {
        position: relative;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px 22px;
        background: linear-gradient(180deg, #ffffff 0%, #fbfdff 100%);
    }

    .exact-auth-modal .exact-auth-right::before {
        content: '';
        position: absolute;
        left: -118px;
        top: -8%;
        width: 180px;
        height: 120%;
        border-radius: 50%;
        background: linear-gradient(180deg, rgba(255,255,255,0), rgba(15,23,42,0.03));
        box-shadow: inset -16px 0 24px rgba(255,255,255,0.85);
        z-index: 1;
    }

    .exact-auth-modal .exact-auth-close {
        position: absolute;
        top: 18px;
        right: 18px;
        z-index: 5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        border-radius: 999px;
        background: #ffffff;
        border: 1px solid rgba(226,232,240,0.9);
        box-shadow: 0 16px 34px rgba(15,23,42,0.1);
        color: #1a264f;
    }

    .exact-auth-modal .exact-auth-right-inner {
        position: relative;
        width: 100%;
        max-width: 400px;
        padding: 18px 18px 20px;
        border: 1px solid rgba(188, 212, 246, 0.72);
        border-radius: 28px;
        background: rgba(255, 255, 255, 0.82);
        backdrop-filter: blur(14px);
        box-shadow:
            0 24px 54px rgba(20, 43, 111, 0.08),
            inset 0 1px 0 rgba(255, 255, 255, 0.96);
    }

    .exact-auth-modal .exact-auth-right-inner::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 110px;
        height: 110px;
        border-radius: 0 28px 0 100px;
        background:
            linear-gradient(135deg, rgba(20, 43, 111, 0.12), rgba(101, 167, 255, 0.04) 64%, transparent 65%);
        pointer-events: none;
    }

    .exact-auth-modal .exact-auth-form-seal {
        width: 70px;
        height: 70px;
        margin: 0 auto;
    }

    .exact-auth-modal .exact-auth-title {
        margin: 12px 0 0;
        text-align: center;
        font-size: 24px;
        line-height: 1.08;
        font-weight: 800;
        letter-spacing: -0.04em;
        color: #142b6f;
    }

    .exact-auth-modal .exact-auth-subtitle {
        margin: 8px auto 0;
        max-width: 320px;
        text-align: center;
        font-size: 12px;
        line-height: 1.48;
        color: #6b7893;
    }

    .exact-auth-modal .exact-auth-form {
        margin-top: 16px;
    }

    .exact-auth-modal .exact-auth-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 11px 14px;
    }

    .exact-auth-modal .exact-auth-field {
        min-width: 0;
    }

    .exact-auth-modal .exact-auth-field.is-span-2 {
        grid-column: 1 / -1;
    }

    .exact-auth-modal .exact-auth-label {
        display: block;
        margin-bottom: 8px;
        font-size: 11px;
        font-weight: 700;
        color: #1c3474;
    }

    .exact-auth-modal .exact-auth-control {
        display: flex;
        align-items: center;
        gap: 12px;
        min-height: 48px;
        padding: 0 16px;
        border-radius: 20px;
        border: 1.5px solid #d9e6f7;
        background: #ffffff;
        box-shadow: 0 12px 22px rgba(20, 43, 111, 0.045);
        transition: border-color 180ms ease, box-shadow 180ms ease;
    }

    .exact-auth-modal .exact-auth-control:focus-within {
        border-color: rgba(20, 43, 111, 0.32);
        box-shadow:
            0 0 0 4px rgba(117, 170, 255, 0.14),
            0 14px 24px rgba(20, 43, 111, 0.07);
    }

    .exact-auth-modal .exact-auth-control input {
        width: 100%;
        min-width: 0;
        border: 0;
        background: transparent;
        padding: 0;
        font-size: 11px;
        color: #1f2d59;
        outline: none;
        box-shadow: none;
    }

    .exact-auth-modal .exact-auth-control input::placeholder {
        color: #94a1b6;
    }

    .exact-auth-modal .exact-auth-icon {
        width: 16px;
        height: 16px;
        flex: 0 0 auto;
        color: #142b6f;
    }

    .exact-auth-modal .exact-auth-trailing {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 22px;
        height: 22px;
        border: 0;
        border-radius: 999px;
        background: rgba(20, 43, 111, 0.05);
        color: #486295;
        cursor: pointer;
        padding: 0;
        transition: background-color 180ms ease, color 180ms ease;
    }

    .exact-auth-modal .exact-auth-trailing:hover {
        background: rgba(20, 43, 111, 0.1);
        color: #142b6f;
    }

    .exact-auth-modal .exact-auth-password-guide {
        margin-top: 2px;
        padding: 14px 16px;
        border: 1px solid rgba(184, 213, 248, 0.88);
        border-radius: 14px;
        background: linear-gradient(180deg, #f8fbff 0%, #eef5ff 100%);
        box-shadow: 0 14px 26px rgba(20, 43, 111, 0.05);
        max-height: 0;
        padding-top: 0;
        padding-bottom: 0;
        border-width: 0;
        opacity: 0;
        overflow: hidden;
        pointer-events: none;
        box-shadow: none;
        transition: max-height 220ms ease, opacity 180ms ease, padding 180ms ease, border-color 180ms ease, border-width 180ms ease, transform 180ms ease, box-shadow 180ms ease, background 180ms ease;
    }

    .exact-auth-modal .exact-auth-password-guide.is-visible {
        max-height: 420px;
        padding-top: 14px;
        padding-bottom: 14px;
        border-width: 1px;
        opacity: 1;
        pointer-events: auto;
        box-shadow: 0 14px 26px rgba(20, 43, 111, 0.05);
    }

    .exact-auth-modal .exact-auth-password-guide:hover,
    .exact-auth-modal .exact-auth-password-guide.is-focused {
        transform: translateY(-1px);
        border-color: rgba(112, 166, 244, 0.84);
        box-shadow: 0 18px 32px rgba(20, 43, 111, 0.08);
        background: linear-gradient(180deg, #f7faff 0%, #edf4ff 100%);
    }

    .exact-auth-modal .exact-auth-password-guide-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
    }

    .exact-auth-modal .exact-auth-password-guide-heading {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        flex: 1;
    }

    .exact-auth-modal .exact-auth-password-guide-badge {
        width: 38px;
        height: 38px;
        flex: 0 0 auto;
        display: grid;
        place-items: center;
        border-radius: 12px;
        background: linear-gradient(180deg, #2447a3 0%, #1b377f 100%);
        box-shadow: 0 10px 18px rgba(37, 71, 163, 0.2);
        color: #ffffff;
    }

    .exact-auth-modal .exact-auth-password-guide-title {
        margin: 0;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.01em;
        color: #172554;
    }

    .exact-auth-modal .exact-auth-password-guide-copy {
        margin: 3px 0 0;
        font-size: 10px;
        line-height: 1.45;
        color: #64748b;
    }

    .exact-auth-modal .exact-auth-password-guide-counter {
        min-width: 52px;
        padding: 7px 10px;
        border: 1px solid rgba(184, 213, 248, 0.88);
        border-radius: 14px;
        background: rgba(255, 255, 255, 0.9);
        text-align: center;
    }

    .exact-auth-modal .exact-auth-password-guide-counter-label {
        display: block;
        font-size: 8px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #7b8dad;
    }

    .exact-auth-modal .exact-auth-password-guide-counter-value {
        display: block;
        margin-top: 2px;
        font-size: 12px;
        font-weight: 800;
        color: #142b6f;
    }

    .exact-auth-modal .exact-auth-password-guide-list {
        display: grid;
        gap: 8px;
        margin: 12px 0 0;
        padding: 0;
        list-style: none;
    }

    .exact-auth-modal .exact-auth-password-guide-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 10.5px;
        font-weight: 600;
        line-height: 1.4;
        color: #7c879b;
        transition: color 180ms ease, background-color 180ms ease;
        border-radius: 12px;
        padding: 3px 4px;
    }

    .exact-auth-modal .exact-auth-password-guide-item[data-valid="true"] {
        color: #15803d;
        background: rgba(220, 252, 231, 0.5);
    }

    .exact-auth-modal .exact-auth-password-guide-item[data-valid="true"] .exact-auth-password-guide-indicator {
        border-color: rgba(34, 197, 94, 0.32);
        background: rgba(220, 252, 231, 0.95);
        color: #16a34a;
        transform: scale(1.02);
    }

    .exact-auth-modal .exact-auth-password-guide-item[data-valid="true"] .exact-auth-password-guide-icon-check {
        opacity: 1;
        transform: scale(1);
    }

    .exact-auth-modal .exact-auth-password-guide-item[data-valid="true"] .exact-auth-password-guide-icon-circle {
        opacity: 0;
        transform: scale(0.65);
    }

    .exact-auth-modal .exact-auth-password-guide-indicator {
        position: relative;
        width: 18px;
        height: 18px;
        flex: 0 0 auto;
        border: 1px solid rgba(148, 163, 184, 0.38);
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.85);
        color: #94a3b8;
        transition: border-color 180ms ease, background-color 180ms ease, color 180ms ease, transform 180ms ease;
    }

    .exact-auth-modal .exact-auth-password-guide-indicator svg {
        position: absolute;
        inset: 0;
        margin: auto;
        transition: opacity 180ms ease, transform 180ms ease;
    }

    .exact-auth-modal .exact-auth-password-guide-icon-check {
        opacity: 0;
        transform: scale(0.65);
    }

    .exact-auth-modal .exact-auth-password-guide-icon-circle {
        opacity: 1;
        transform: scale(1);
    }

    .exact-auth-modal .exact-auth-submit {
        display: flex;
        align-items: center;
        width: 100%;
        min-height: 50px;
        margin-top: 14px;
        padding: 0 22px;
        border: 0;
        border-radius: 999px;
        background: linear-gradient(90deg, #163b9f 0%, #1e4fd1 52%, #3b82f6 100%);
        box-shadow: 0 22px 42px rgba(34, 57, 159, 0.2), 0 12px 22px rgba(59, 130, 246, 0.18);
        color: #ffffff;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.3em;
        text-transform: uppercase;
    }

    .exact-auth-modal .exact-auth-submit span:first-child {
        flex: 1;
        text-align: center;
        transform: translateX(16px);
    }

    .exact-auth-modal .exact-auth-footer {
        margin-top: 10px;
        text-align: center;
        font-size: 11px;
        color: #56657f;
    }

    .exact-auth-modal .exact-auth-footer button,
    .exact-auth-modal .exact-auth-footer a {
        color: #1f49cb;
        font-weight: 800;
        background: transparent;
        border: 0;
        padding: 0;
    }

    @media (max-width: 1180px) {
        .exact-auth-modal .exact-auth-shell {
            grid-template-columns: 1fr;
            min-height: auto;
        }

        .exact-auth-modal .exact-auth-left {
            min-height: 640px;
        }
    }

    @media (max-width: 860px) {
        .exact-auth-modal .exact-auth-shell {
            width: calc(100vw - 16px);
            border-radius: 28px;
        }

        .exact-auth-modal .exact-auth-left {
            padding: 24px 20px 26px;
            min-height: auto;
        }

        .exact-auth-modal .exact-auth-left::before,
        .exact-auth-modal .exact-auth-left::after,
        .exact-auth-modal .exact-auth-watermark,
        .exact-auth-modal .exact-auth-dots,
        .exact-auth-modal .exact-auth-brand-girl {
            display: none;
        }

        .exact-auth-modal .exact-auth-copy {
            max-width: none;
            padding-top: 42px;
            padding-bottom: 174px;
        }

        .exact-auth-modal .exact-auth-right {
            padding: 76px 14px 24px;
        }

        .exact-auth-modal .exact-auth-close {
            top: 16px;
            right: 16px;
            width: 48px;
            height: 48px;
        }

        .exact-auth-modal .exact-auth-grid {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .exact-auth-modal .exact-auth-password-guide {
            padding: 14px;
        }

        .exact-auth-modal .exact-auth-password-guide-head {
            flex-direction: column;
        }
    }
</style>

<div id="auth-login-modal" class="auth-modal exact-auth-modal hidden fixed inset-0 z-[2147483100]" aria-hidden="true" data-auto-open="{{ $shouldAutoOpenAuthModal ? 'true' : 'false' }}">
    <div class="auth-modal-backdrop absolute inset-0 bg-slate-900/30 backdrop-blur-[3px]" data-auth-modal-close></div>
    <div class="absolute inset-0 flex items-center justify-center px-2 py-2 sm:px-4 sm:py-4">
        <div class="auth-modal-panel relative w-full max-w-none">
            <div class="exact-auth-shell">
                <section class="exact-auth-left">
                    <div class="exact-auth-left-inner">
                        <div class="exact-auth-top">
                            <div class="exact-auth-seal">
                                <img src="{{ $authModalLogo }}" alt="DILG Seal">
                            </div>

                            <div class="exact-auth-office">
                                <p class="exact-auth-office-title">Department of the Interior and Local Government</p>
                                <div class="exact-auth-office-region">Cordillera Administrative Region</div>
                                <div class="exact-auth-office-motto">Matino. Mahusay. at Maaasahan.</div>
                            </div>
                        </div>

                        <svg class="exact-auth-watermark" viewBox="0 0 420 320" fill="none" aria-hidden="true">
                            <path d="M55 257H365" stroke="white" stroke-width="8" stroke-linecap="round"/>
                            <path d="M88 248V126M332 248V126M142 248V158M278 248V158" stroke="white" stroke-width="8" stroke-linecap="round"/>
                            <path d="M78 126H342" stroke="white" stroke-width="8" stroke-linecap="round"/>
                            <path d="M52 126L210 64L368 126" stroke="white" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M210 52V16" stroke="white" stroke-width="8" stroke-linecap="round"/>
                        </svg>

                        <div class="exact-auth-dots" aria-hidden="true"></div>

                        <div class="exact-auth-copy">
                            <h2 class="exact-auth-product">GABAY-LEX</h2>
                            <div class="exact-auth-rule"></div>
                            <p class="exact-auth-copy-small">Guidance and Advisory for Better Administration in Law</p>

                            <h3 class="exact-auth-headline">Smart legal support for efficient public service.</h3>
                            <div class="exact-auth-subrule"></div>
                            <p class="exact-auth-copy-body">Instant help, document assistance, and reliable guidance all in one place.</p>
                        </div>

                        <div class="exact-auth-brand-girl" aria-hidden="true">
                            <img src="{{ $authModalHero }}" alt="">
                        </div>

                    </div>
                </section>

                <section class="exact-auth-right">
                    <button type="button" class="auth-modal-close exact-auth-close" aria-label="Close" data-auth-modal-close>
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                            <path d="M6 6L18 18M18 6L6 18" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
                        </svg>
                    </button>

                    <div class="exact-auth-right-inner">
                        <div data-auth-views-root data-initial-mode="{{ $authMode }}" class="relative">
                            <div class="auth-view" data-view="login" aria-hidden="{{ $authMode === 'login' ? 'false' : 'true' }}">
                                <div class="exact-auth-form-seal">
                                    <img src="{{ $authModalLogo }}" alt="DILG Seal">
                                </div>

                                <h2 id="auth-modal-title" class="exact-auth-title">Welcome Back</h2>
                                <p class="exact-auth-subtitle">Sign in to access your saved legal conversations.</p>

                                <x-auth-session-status class="mt-6 rounded-2xl border border-emerald-400/20 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-700" :status="session('status')" />

                                @if (! empty($loginErrors))
                                    <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                                        <ul class="list-disc pl-5">
                                            @foreach ($loginErrors as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('login') }}" class="exact-auth-form">
                                    @csrf
                                    <input type="hidden" name="auth_mode" value="login">

                                    <div class="exact-auth-field">
                                        <label class="exact-auth-label">Email Address</label>
                                        <div class="exact-auth-control">
                                            <svg class="exact-auth-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M4 7.75C4 6.23122 5.23122 5 6.75 5H17.25C18.7688 5 20 6.23122 20 7.75V16.25C20 17.7688 18.7688 19 17.25 19H6.75C5.23122 19 4 17.7688 4 16.25V7.75Z" stroke="currentColor" stroke-width="1.8"/>
                                                <path d="M5 7L11.0593 11.5445C11.6184 11.9638 12.3816 11.9638 12.9407 11.5445L19 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                            </svg>
                                            <input name="email" type="email" required autocomplete="username" value="{{ old('email') }}" placeholder="Email address">
                                        </div>
                                    </div>

                                    <div class="exact-auth-field" style="margin-top: 20px;">
                                        <label class="exact-auth-label">Password</label>
                                        <div class="exact-auth-control">
                                            <svg class="exact-auth-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                <path d="M8 10V8C8 5.79086 9.79086 4 12 4C14.2091 4 16 5.79086 16 8V10" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                                <path d="M6 10H18C19.1046 10 20 10.8954 20 12V18C20 19.1046 19.1046 20 18 20H6C4.89543 20 4 19.1046 4 18V12C4 10.8954 4.89543 10 6 10Z" stroke="currentColor" stroke-width="1.9"/>
                                            </svg>
                                            <input id="modal_login_password" name="password" type="password" required autocomplete="current-password" placeholder="Password">
                                            <button type="button" class="exact-auth-trailing" data-auth-password-toggle="modal_login_password" aria-label="Show password">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M2 12C3.8 8.5 7.3 6 12 6C16.7 6 20.2 8.5 22 12C20.2 15.5 16.7 18 12 18C7.3 18 3.8 15.5 2 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                    <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="1.8"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mt-5 flex items-center justify-between text-sm text-slate-600">
                                        <label class="inline-flex items-center gap-2">
                                            <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900/20">
                                            <span>Remember me</span>
                                        </label>

                                        @if (Route::has('password.request'))
                                            <a href="{{ route('password.request') }}" class="font-semibold text-slate-500 hover:underline">Forgot password?</a>
                                        @endif
                                    </div>

                                    <button type="submit" class="exact-auth-submit">
                                        <span>Log In</span>
                                        <svg width="28" height="22" viewBox="0 0 30 22" fill="none" aria-hidden="true">
                                            <path d="M18 2L27 11L18 20" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M27 11H3" stroke="currentColor" stroke-width="2.8" stroke-linecap="round"/>
                                        </svg>
                                    </button>
                                </form>

                                <p class="exact-auth-footer">
                                    Don’t have an account? <button type="button" data-auth-switch="register">Sign Up</button>
                                </p>
                            </div>

                            <div class="auth-view absolute inset-0" data-view="register" aria-hidden="{{ $authMode === 'register' ? 'false' : 'true' }}">
                                <div class="exact-auth-form-seal">
                                    <img src="{{ $authModalLogo }}" alt="DILG Seal">
                                </div>

                                <h2 class="exact-auth-title">Create Account</h2>
                                <p class="exact-auth-subtitle">Sign up to start your legal research workspace.</p>

                                @if (! empty($registerErrors))
                                    <div class="mt-4 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                                        <ul class="list-disc pl-5">
                                            @foreach ($registerErrors as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('register') }}" class="exact-auth-form">
                                    @csrf
                                    <input type="hidden" name="auth_mode" value="register">

                                    <div class="exact-auth-grid">
                                        <div class="exact-auth-field">
                                            <label class="exact-auth-label">First Name</label>
                                            <div class="exact-auth-control">
                                                <svg class="exact-auth-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="currentColor" stroke-width="1.9"/>
                                                    <path d="M4 20C4 16.6863 7.58172 14 12 14C16.4183 14 20 16.6863 20 20" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                                </svg>
                                                <input name="first_name" type="text" value="{{ old('first_name') }}" required autocomplete="given-name" placeholder="First name">
                                            </div>
                                        </div>

                                        <div class="exact-auth-field">
                                            <label class="exact-auth-label">Last Name</label>
                                            <div class="exact-auth-control">
                                                <svg class="exact-auth-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M12 12C14.7614 12 17 9.76142 17 7C17 4.23858 14.7614 2 12 2C9.23858 2 7 4.23858 7 7C7 9.76142 9.23858 12 12 12Z" stroke="currentColor" stroke-width="1.9"/>
                                                    <path d="M4 20C4 16.6863 7.58172 14 12 14C16.4183 14 20 16.6863 20 20" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                                </svg>
                                                <input name="last_name" type="text" value="{{ old('last_name') }}" required autocomplete="family-name" placeholder="Last name">
                                            </div>
                                        </div>

                                        <div class="exact-auth-field">
                                            <label class="exact-auth-label">Birthday</label>
                                            <div class="exact-auth-control">
                                                <svg class="exact-auth-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M8 2V5M16 2V5M3 9H21" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                                    <path d="M5 5H19C20.1046 5 21 5.89543 21 7V19C21 20.1046 20.1046 21 19 21H5C3.89543 21 3 20.1046 3 19V7C3 5.89543 3.89543 5 5 5Z" stroke="currentColor" stroke-width="1.9"/>
                                                </svg>
                                                <input name="birthday" type="date" value="{{ old('birthday') }}" required>
                                            </div>
                                        </div>

                                        <div class="exact-auth-field">
                                            <label class="exact-auth-label">Email Address</label>
                                            <div class="exact-auth-control">
                                                <svg class="exact-auth-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M4 7.75C4 6.23122 5.23122 5 6.75 5H17.25C18.7688 5 20 6.23122 20 7.75V16.25C20 17.7688 18.7688 19 17.25 19H6.75C5.23122 19 4 17.7688 4 16.25V7.75Z" stroke="currentColor" stroke-width="1.8"/>
                                                    <path d="M5 7L11.0593 11.5445C11.6184 11.9638 12.3816 11.9638 12.9407 11.5445L19 7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/>
                                                </svg>
                                                <input name="email" type="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Email address">
                                            </div>
                                        </div>

                                        <div class="exact-auth-field">
                                            <label class="exact-auth-label">Password</label>
                                            <div class="exact-auth-control">
                                                <svg class="exact-auth-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M8 10V8C8 5.79086 9.79086 4 12 4C14.2091 4 16 5.79086 16 8V10" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                                    <path d="M6 10H18C19.1046 10 20 10.8954 20 12V18C20 19.1046 19.1046 20 18 20H6C4.89543 20 4 19.1046 4 18V12C4 10.8954 4.89543 10 6 10Z" stroke="currentColor" stroke-width="1.9"/>
                                                </svg>
                                                <input id="modal_register_password" name="password" type="password" required autocomplete="new-password" placeholder="Password" aria-describedby="modal_register_password_requirements">
                                                <button type="button" class="exact-auth-trailing" data-auth-password-toggle="modal_register_password" aria-label="Show password">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M2 12C3.8 8.5 7.3 6 12 6C16.7 6 20.2 8.5 22 12C20.2 15.5 16.7 18 12 18C7.3 18 3.8 15.5 2 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                        <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="1.8"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="exact-auth-field">
                                            <label class="exact-auth-label">Confirm Password</label>
                                            <div class="exact-auth-control">
                                                <svg class="exact-auth-icon" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                    <path d="M8 10V8C8 5.79086 9.79086 4 12 4C14.2091 4 16 5.79086 16 8V10" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                                    <path d="M6 10H18C19.1046 10 20 10.8954 20 12V18C20 19.1046 19.1046 20 18 20H6C4.89543 20 4 19.1046 4 18V12C4 10.8954 4.89543 10 6 10Z" stroke="currentColor" stroke-width="1.9"/>
                                                </svg>
                                                <input id="modal_register_password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" placeholder="Confirm password">
                                                <button type="button" class="exact-auth-trailing" data-auth-password-toggle="modal_register_password_confirmation" aria-label="Show confirm password">
                                                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                                        <path d="M2 12C3.8 8.5 7.3 6 12 6C16.7 6 20.2 8.5 22 12C20.2 15.5 16.7 18 12 18C7.3 18 3.8 15.5 2 12Z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/>
                                                        <path d="M12 15C13.6569 15 15 13.6569 15 12C15 10.3431 13.6569 9 12 9C10.3431 9 9 10.3431 9 12C9 13.6569 10.3431 15 12 15Z" stroke="currentColor" stroke-width="1.8"/>
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="exact-auth-field is-span-2">
                                            <div id="modal_register_password_requirements" @class(['exact-auth-password-guide', 'is-visible' => $errors->has('password') || $errors->has('password_confirmation')]) data-password-guide>
                                                <div class="exact-auth-password-guide-head">
                                                    <div class="exact-auth-password-guide-heading">
                                                        <div class="exact-auth-password-guide-badge" aria-hidden="true">
                                                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none">
                                                                <path d="M12 3L19 6V11C19 15.4183 16.134 19.4312 12 21C7.866 19.4312 5 15.4183 5 11V6L12 3Z" stroke="currentColor" stroke-width="1.9" stroke-linejoin="round"/>
                                                                <path d="M9 11V9.75C9 8.09315 10.3431 6.75 12 6.75C13.6569 6.75 15 8.09315 15 9.75V11" stroke="currentColor" stroke-width="1.9" stroke-linecap="round"/>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            <p class="exact-auth-password-guide-title">Password Requirements</p>
                                                            <p class="exact-auth-password-guide-copy">Use a strong password that helps keep your account secure.</p>
                                                        </div>
                                                    </div>
                                                    <div class="exact-auth-password-guide-counter" aria-live="polite">
                                                        <span class="exact-auth-password-guide-counter-label">Length</span>
                                                        <span class="exact-auth-password-guide-counter-value" data-password-count>0+</span>
                                                    </div>
                                                </div>

                                                <ul class="exact-auth-password-guide-list" aria-live="polite">
                                                    <li class="exact-auth-password-guide-item" data-password-rule="length" data-valid="false">
                                                        <span class="exact-auth-password-guide-indicator" aria-hidden="true">
                                                            <svg class="exact-auth-password-guide-icon-circle" width="8" height="8" viewBox="0 0 10 10" fill="currentColor">
                                                                <circle cx="5" cy="5" r="3.5"></circle>
                                                            </svg>
                                                            <svg class="exact-auth-password-guide-icon-check" width="12" height="12" viewBox="0 0 20 20" fill="none">
                                                                <path d="M5 10.5L8.25 13.75L15 7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                            </svg>
                                                        </span>
                                                        <span>Must be 8 characters or more</span>
                                                    </li>
                                                    <li class="exact-auth-password-guide-item" data-password-rule="uppercase" data-valid="false">
                                                        <span class="exact-auth-password-guide-indicator" aria-hidden="true">
                                                            <svg class="exact-auth-password-guide-icon-circle" width="8" height="8" viewBox="0 0 10 10" fill="currentColor">
                                                                <circle cx="5" cy="5" r="3.5"></circle>
                                                            </svg>
                                                            <svg class="exact-auth-password-guide-icon-check" width="12" height="12" viewBox="0 0 20 20" fill="none">
                                                                <path d="M5 10.5L8.25 13.75L15 7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                            </svg>
                                                        </span>
                                                        <span>Must contain at least one uppercase letter</span>
                                                    </li>
                                                    <li class="exact-auth-password-guide-item" data-password-rule="lowercase" data-valid="false">
                                                        <span class="exact-auth-password-guide-indicator" aria-hidden="true">
                                                            <svg class="exact-auth-password-guide-icon-circle" width="8" height="8" viewBox="0 0 10 10" fill="currentColor">
                                                                <circle cx="5" cy="5" r="3.5"></circle>
                                                            </svg>
                                                            <svg class="exact-auth-password-guide-icon-check" width="12" height="12" viewBox="0 0 20 20" fill="none">
                                                                <path d="M5 10.5L8.25 13.75L15 7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                            </svg>
                                                        </span>
                                                        <span>Must contain at least one lowercase letter</span>
                                                    </li>
                                                    <li class="exact-auth-password-guide-item" data-password-rule="number" data-valid="false">
                                                        <span class="exact-auth-password-guide-indicator" aria-hidden="true">
                                                            <svg class="exact-auth-password-guide-icon-circle" width="8" height="8" viewBox="0 0 10 10" fill="currentColor">
                                                                <circle cx="5" cy="5" r="3.5"></circle>
                                                            </svg>
                                                            <svg class="exact-auth-password-guide-icon-check" width="12" height="12" viewBox="0 0 20 20" fill="none">
                                                                <path d="M5 10.5L8.25 13.75L15 7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                            </svg>
                                                        </span>
                                                        <span>Must contain at least one number</span>
                                                    </li>
                                                    <li class="exact-auth-password-guide-item" data-password-rule="special" data-valid="false">
                                                        <span class="exact-auth-password-guide-indicator" aria-hidden="true">
                                                            <svg class="exact-auth-password-guide-icon-circle" width="8" height="8" viewBox="0 0 10 10" fill="currentColor">
                                                                <circle cx="5" cy="5" r="3.5"></circle>
                                                            </svg>
                                                            <svg class="exact-auth-password-guide-icon-check" width="12" height="12" viewBox="0 0 20 20" fill="none">
                                                                <path d="M5 10.5L8.25 13.75L15 7" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"></path>
                                                            </svg>
                                                        </span>
                                                        <span>Must contain at least one special character</span>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="submit" class="exact-auth-submit">
                                        <span>Sign Up</span>
                                        <svg width="28" height="22" viewBox="0 0 30 22" fill="none" aria-hidden="true">
                                            <path d="M18 2L27 11L18 20" stroke="currentColor" stroke-width="2.8" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M27 11H3" stroke="currentColor" stroke-width="2.8" stroke-linecap="round"/>
                                        </svg>
                                    </button>
                                </form>

                                <p class="exact-auth-footer">
                                    Already have an account? <button type="button" data-auth-switch="login">Log In</button>
                                </p>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('click', function (event) {
        var toggle = event.target.closest('[data-auth-password-toggle]');

        if (!toggle) {
            return;
        }

        var input = document.getElementById(toggle.getAttribute('data-auth-password-toggle'));

        if (!input) {
            return;
        }

        input.type = input.type === 'password' ? 'text' : 'password';
    });

    (function () {
        var passwordInput = document.getElementById('modal_register_password');
        var confirmationInput = document.getElementById('modal_register_password_confirmation');
        var guide = document.getElementById('modal_register_password_requirements');
        var count = guide ? guide.querySelector('[data-password-count]') : null;

        if (!passwordInput || !guide) {
            return;
        }

        var rules = {
            length: function (value) { return value.length >= 8; },
            uppercase: function (value) { return /[A-Z]/.test(value); },
            lowercase: function (value) { return /[a-z]/.test(value); },
            number: function (value) { return /[0-9]/.test(value); },
            special: function (value) { return /[@$!%*#?&]/.test(value); }
        };

        function syncGuide() {
            var password = passwordInput.value;

            if (count) {
                count.textContent = password.length + '+';
            }

            Object.keys(rules).forEach(function (key) {
                var row = guide.querySelector('[data-password-rule="' + key + '"]');

                if (!row) {
                    return;
                }

                row.setAttribute('data-valid', rules[key](password) ? 'true' : 'false');
            });
        }

        function syncGuideVisibility() {
            var activeElement = document.activeElement;
            var shouldShow = passwordInput.value.length > 0
                || (confirmationInput && confirmationInput.value.length > 0)
                || activeElement === passwordInput
                || activeElement === confirmationInput
                || guide.classList.contains('is-focused');

            guide.classList.toggle('is-visible', shouldShow);
        }

        function setGuideFocus(isFocused) {
            guide.classList.toggle('is-focused', isFocused);
            syncGuideVisibility();
        }

        passwordInput.addEventListener('input', function () {
            syncGuide();
            syncGuideVisibility();
        });
        passwordInput.addEventListener('focus', function () { setGuideFocus(true); });
        passwordInput.addEventListener('blur', function () {
            window.setTimeout(function () { setGuideFocus(false); }, 0);
        });

        if (confirmationInput) {
            confirmationInput.addEventListener('input', syncGuideVisibility);
            confirmationInput.addEventListener('focus', function () { setGuideFocus(true); });
            confirmationInput.addEventListener('blur', function () {
                window.setTimeout(function () { setGuideFocus(false); }, 0);
            });
        }

        syncGuide();
        syncGuideVisibility();
    }());
</script>
