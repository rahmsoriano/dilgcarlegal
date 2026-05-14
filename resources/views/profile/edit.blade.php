<x-app-layout>
    @php
        $initials = collect(explode(' ', $user->name))
            ->filter()
            ->take(2)
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->implode('') ?: 'AA';

        $isPasswordPanelActive = $errors->updatePassword->isNotEmpty() || $errors->userDeletion->isNotEmpty();
    @endphp

    <style>
        body {
            background:
                radial-gradient(circle at top left, rgba(71, 132, 255, 0.10), transparent 28%),
                linear-gradient(180deg, #f7fbff 0%, #eef5ff 100%);
        }

        body > div.min-h-screen.bg-gray-100 > nav {
            display: none !important;
        }

        body > div.min-h-screen.bg-gray-100 {
            background: transparent !important;
        }

        .settings-page {
            min-height: 100vh;
            color: #1d3160;
        }

        .settings-shell {
            max-width: 1520px;
            margin: 0 auto;
            padding: 0 36px 40px;
        }

        .settings-topbar {
            position: sticky;
            top: 0;
            z-index: 30;
            background:
                radial-gradient(circle at top left, rgba(255, 222, 21, 0.14), transparent 40%),
                radial-gradient(circle at bottom right, rgba(255, 255, 255, 0.08), transparent 45%),
                linear-gradient(90deg, #002C76 0%, #083B8F 45%, #002C76 100%);
            border-bottom: 1px solid rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(14px);
            box-shadow: 0 14px 34px rgba(0, 44, 118, 0.18);
        }

        .settings-topbar__inner {
            max-width: 1520px;
            margin: 0 auto;
            padding: 20px 36px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
        }

        .settings-brand {
            display: flex;
            align-items: center;
            gap: 16px;
            min-width: 0;
        }

        .settings-brand__logo {
            width: 54px;
            height: 54px;
            border-radius: 999px;
            background: #fff;
            border: 1px solid #d6e4fb;
            box-shadow: 0 12px 30px rgba(33, 84, 179, 0.10);
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            flex: 0 0 auto;
        }

        .settings-brand__title {
            display: block;
            font-size: 19px;
            font-weight: 800;
            line-height: 1.1;
            color: #ffffff;
        }

        .settings-brand__subtitle {
            display: block;
            margin-top: 4px;
            font-size: 11px;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8);
            letter-spacing: 0.01em;
        }

        .settings-topbar__nav {
            display: flex;
            align-items: center;
            gap: 22px;
            margin-left: auto;
        }

        .settings-nav-link {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            font-weight: 700;
            transition: color 160ms ease, transform 160ms ease;
        }

        .settings-nav-link:hover {
            color: #ffffff;
            transform: translateY(-1px);
        }

        .settings-nav-link svg,
        .settings-topbar__icon svg {
            width: 20px;
            height: 20px;
        }

        .settings-topbar__divider {
            width: 1px;
            height: 30px;
            background: #dde7f7;
        }

        .settings-topbar__actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .settings-topbar__icon {
            position: relative;
            width: 40px;
            height: 40px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #1c4fad;
            background: #fff;
            border: 1px solid #e2ebf9;
            box-shadow: 0 10px 24px rgba(30, 74, 146, 0.06);
        }

        .settings-topbar__icon-badge {
            position: absolute;
            top: 8px;
            right: 8px;
            width: 8px;
            height: 8px;
            border-radius: 999px;
            background: #2563eb;
            border: 2px solid #fff;
        }

        .settings-user {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            padding: 10px 14px;
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.14);
            background: rgba(255, 255, 255, 0.96);
            box-shadow: 0 12px 28px rgba(15, 23, 42, 0.12);
            transition: border-color 160ms ease, box-shadow 160ms ease, transform 160ms ease;
        }

        .settings-user:hover {
            transform: translateY(-1px);
            border-color: rgba(255, 255, 255, 0.28);
            box-shadow: 0 16px 34px rgba(15, 23, 42, 0.16);
        }

        .settings-user__avatar {
            position: relative;
            width: 52px;
            height: 52px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 21px;
            font-weight: 800;
            background: linear-gradient(135deg, #5c6cff 0%, #7b3ff2 100%);
            box-shadow: 0 16px 32px rgba(92, 108, 255, 0.24);
            flex: 0 0 auto;
        }

        .settings-user__status {
            position: absolute;
            right: 2px;
            bottom: 3px;
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: #22c55e;
            border: 2px solid #fff;
        }

        .settings-user__meta {
            min-width: 0;
        }

        .settings-user__name {
            display: block;
            font-size: 14px;
            font-weight: 700;
            color: #19376d;
            white-space: nowrap;
        }

        .settings-user__role {
            display: block;
            margin-top: 2px;
            font-size: 12px;
            color: #5f7398;
            white-space: nowrap;
        }

        .settings-user__chevron {
            color: #47608e;
        }

        .settings-user__menu {
            position: absolute;
            top: calc(100% + 12px);
            right: 0;
            width: 220px;
            overflow: hidden;
            border-radius: 18px;
            border: 1px solid #deebfb;
            background: rgba(255, 255, 255, 0.98);
            box-shadow: 0 22px 48px rgba(23, 58, 118, 0.14);
            backdrop-filter: blur(14px);
        }

        .settings-user__menu-link,
        .settings-user__menu-button {
            display: flex;
            width: 100%;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            font-size: 14px;
            font-weight: 700;
            color: #1c3d82;
            transition: background-color 160ms ease, color 160ms ease;
        }

        .settings-user__menu-link:hover,
        .settings-user__menu-button:hover {
            background: #f3f8ff;
            color: #154fd2;
        }

        .settings-user__menu-divider {
            height: 1px;
            background: #e4edf9;
        }

        .settings-content {
            padding-top: 20px;
        }

        .settings-hero,
        .settings-panel,
        .settings-summary-card,
        .settings-card {
            background: rgba(255, 255, 255, 0.96);
            border: 1px solid #deebfb;
            box-shadow: 0 18px 50px rgba(24, 54, 108, 0.08);
        }

        .settings-hero {
            position: relative;
            overflow: hidden;
            border-radius: 28px;
            padding: 22px 28px;
        }

        .settings-hero::before {
            content: "";
            position: absolute;
            inset: 0;
            background:
                radial-gradient(circle at 82% 50%, rgba(93, 141, 255, 0.10), transparent 20%),
                linear-gradient(90deg, rgba(255,255,255,0.2) 0%, rgba(244,248,255,0.7) 100%);
            pointer-events: none;
        }

        .settings-hero__inner {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .settings-hero__lead {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .settings-hero__icon {
            width: 74px;
            height: 74px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(180deg, #f8fbff 0%, #eef4ff 100%);
            border: 1px solid #e1ebfb;
            color: #2563eb;
            flex: 0 0 auto;
        }

        .settings-hero__icon svg {
            width: 34px;
            height: 34px;
        }

        .settings-hero__title {
            font-size: 24px;
            line-height: 1.1;
            font-weight: 800;
            color: #162c66;
        }

        .settings-hero__subtitle {
            margin-top: 8px;
            font-size: 14px;
            color: #4d638d;
        }

        .settings-hero__art {
            width: 240px;
            max-width: 100%;
            color: #1f5eff;
            flex: 0 0 auto;
        }

        .settings-grid {
            display: grid;
            grid-template-columns: 390px minmax(0, 1fr);
            gap: 34px;
            margin-top: 20px;
            align-items: start;
        }

        .settings-panel {
            border-radius: 26px;
            padding: 14px;
            height: fit-content;
            align-self: start;
        }

        .settings-tab-link {
            display: flex;
            align-items: center;
            gap: 16px;
            width: 100%;
            border-radius: 18px;
            padding: 16px 16px 16px 14px;
            border: 1px solid transparent;
            transition: background 160ms ease, border-color 160ms ease, box-shadow 160ms ease;
        }

        .settings-tab-link + .settings-tab-link {
            margin-top: 10px;
        }

        .settings-tab-link.is-active {
            background: linear-gradient(180deg, #f6f9ff 0%, #edf4ff 100%);
            border-color: #dbe8fb;
            box-shadow: inset 4px 0 0 #2563eb;
        }

        .settings-tab-link__icon {
            width: 56px;
            height: 56px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #fff;
            border: 1px solid #e1eaf9;
            color: #2563eb;
            flex: 0 0 auto;
            box-shadow: 0 8px 18px rgba(27, 67, 138, 0.06);
        }

        .settings-tab-link__icon svg {
            width: 24px;
            height: 24px;
        }

        .settings-tab-link__label {
            display: block;
            font-size: 15px;
            font-weight: 800;
            color: #1750c7;
        }

        .settings-tab-link__copy {
            display: block;
            margin-top: 4px;
            font-size: 13px;
            color: #57709b;
        }

        .settings-tab-link__arrow {
            margin-left: auto;
            color: #215ce4;
        }

        .settings-right {
            display: grid;
            gap: 18px;
        }

        .settings-summary-card,
        .settings-card {
            border-radius: 24px;
        }

        .settings-summary-card {
            padding: 28px 24px;
        }

        .settings-summary-card__inner {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .settings-summary-card__user {
            display: flex;
            align-items: center;
            gap: 18px;
            min-width: 0;
        }

        .settings-summary-card__avatar {
            width: 78px;
            height: 78px;
            border-radius: 999px;
            background: linear-gradient(135deg, #3b82f6 0%, #6d28d9 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 24px;
            font-weight: 800;
            flex: 0 0 auto;
            box-shadow: 0 18px 32px rgba(74, 98, 236, 0.22);
        }

        .settings-summary-card__name {
            font-size: 17px;
            font-weight: 800;
            color: #1c3168;
        }

        .settings-summary-card__email {
            margin-top: 4px;
            font-size: 14px;
            color: #51688f;
        }

        .settings-summary-card__button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            min-height: 46px;
            padding: 0 22px;
            border-radius: 14px;
            border: 1px solid #d4e2fa;
            color: #1654d4;
            background: #fff;
            font-size: 14px;
            font-weight: 700;
            box-shadow: 0 10px 24px rgba(24, 66, 139, 0.06);
            transition: border-color 160ms ease, transform 160ms ease, box-shadow 160ms ease;
        }

        .settings-summary-card__button:hover {
            transform: translateY(-1px);
            border-color: #c0d5fa;
            box-shadow: 0 14px 28px rgba(24, 66, 139, 0.10);
        }

        .settings-card {
            padding: 24px;
        }

        .settings-password-stack {
            display: grid;
            gap: 18px;
        }

        .settings-footer {
            margin-top: 26px;
            padding-bottom: 10px;
            text-align: center;
            color: #597098;
        }

        .settings-footer__brand {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 500;
        }

        .settings-footer__brand::before,
        .settings-footer__brand::after {
            content: "";
            width: 88px;
            height: 1px;
            background: #d5e2f6;
        }

        .settings-footer__copy {
            margin-top: 10px;
            font-size: 13px;
        }

        @media (max-width: 1280px) {
            .settings-grid {
                grid-template-columns: 300px minmax(0, 1fr);
                gap: 24px;
            }
        }

        @media (max-width: 1100px) {
            .settings-topbar__inner,
            .settings-shell {
                padding-left: 20px;
                padding-right: 20px;
            }

            .settings-topbar__inner {
                flex-wrap: wrap;
            }

            .settings-topbar__nav {
                order: 3;
                width: 100%;
                justify-content: flex-start;
                padding-top: 8px;
            }

            .settings-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 860px) {
            .settings-hero {
                padding: 20px 18px;
            }

            .settings-hero__inner,
            .settings-summary-card__inner {
                flex-direction: column;
                align-items: flex-start;
            }

            .settings-hero__lead {
                align-items: flex-start;
            }

            .settings-hero__art {
                width: 180px;
                align-self: center;
            }

            .settings-summary-card__button {
                width: 100%;
            }
        }

        @media (max-width: 640px) {
            .settings-topbar__inner,
            .settings-shell {
                padding-left: 14px;
                padding-right: 14px;
            }

            .settings-brand {
                align-items: flex-start;
            }

            .settings-topbar__actions {
                width: 100%;
                justify-content: space-between;
            }

            .settings-user {
                flex: 1 1 auto;
                justify-content: flex-end;
                padding-left: 0;
            }

            .settings-user__meta {
                display: none;
            }

            .settings-hero__lead {
                flex-direction: column;
                gap: 16px;
            }

            .settings-hero__title {
                font-size: 21px;
            }

            .settings-panel,
            .settings-summary-card,
            .settings-card {
                border-radius: 22px;
            }

            .settings-footer__brand::before,
            .settings-footer__brand::after {
                width: 34px;
            }
        }
    </style>

    <div class="settings-page" x-data="{
        activeTab: '{{ $isPasswordPanelActive ? 'security' : 'profile' }}',
        profileEditMode: {{ ($errors->has('name') || $errors->has('email')) ? 'true' : 'false' }},
        enableProfileEdit() {
            this.activeTab = 'profile';
            this.profileEditMode = true;
            this.$nextTick(() => this.$refs.profileNameInput?.focus());
        },
        cancelProfileEdit() {
            if (this.$refs.profileForm) {
                this.$refs.profileForm.reset();
            }
            this.profileEditMode = false;
        }
    }">
        <header class="settings-topbar">
            <div class="settings-topbar__inner">
                <a href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('chat.index') }}" class="settings-brand">
                    <span class="settings-brand__logo">
                        <img src="{{ asset('dilglogo.png') }}" alt="DILG Logo" class="h-11 w-11 object-contain">
                    </span>
                    <span class="min-w-0">
                        <span class="settings-brand__title">GABAY-Lex</span>
                        <span class="settings-brand__subtitle">Guidance and Advisory for Better Administration in Law</span>
                    </span>
                </a>

                <nav class="settings-topbar__nav" aria-label="Primary">
                    <a href="{{ auth()->user()->is_admin ? route('admin.legal.ai') : route('chat.index') }}" class="settings-nav-link">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 14h5" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 20 21 16V6a2 2 0 0 0-2-2H5A2 2 0 0 0 3 6v10a2 2 0 0 0 2 2h12Z" />
                        </svg>
                        <span>Chat</span>
                    </a>

                    @can('admin')
                        <a href="{{ route('admin.dashboard') }}" class="settings-nav-link">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                <circle cx="9" cy="7" r="4" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M20 8v6" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M23 11h-6" />
                            </svg>
                            <span>Admin</span>
                        </a>
                    @endcan
                </nav>

                <div class="settings-topbar__actions">
                    <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                        <button class="settings-user" type="button" @click="open = !open" :aria-expanded="open.toString()">
                            <span class="settings-user__avatar">
                                {{ $initials }}
                                <span class="settings-user__status"></span>
                            </span>
                            <span class="settings-user__meta">
                                <span class="settings-user__name">{{ Auth::user()->name }}</span>
                                <span class="settings-user__role">{{ Auth::user()->is_admin ? 'Administrator' : 'User' }}</span>
                            </span>
                            <span class="settings-user__chevron">
                                <svg x-show="!open" x-cloak class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 0 1 1.414 0L10 10.586l3.293-3.293a1 1 0 1 1 1.414 1.414l-4 4a1 1 0 0 1-1.414 0l-4-4a1 1 0 0 1 0-1.414Z" clip-rule="evenodd" />
                                </svg>
                                <svg x-show="open" x-cloak class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 0 1-1.414 0L10 9.414l-3.293 3.293a1 1 0 0 1-1.414-1.414l4-4a1 1 0 0 1 1.414 0l4 4a1 1 0 0 1 0 1.414Z" clip-rule="evenodd" />
                                </svg>
                            </span>
                        </button>

                        <div
                            x-cloak
                            x-show="open"
                            x-transition:enter="transition ease-out duration-180"
                            x-transition:enter-start="opacity-0 translate-y-2 scale-[0.98]"
                            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                            x-transition:leave="transition ease-in duration-140"
                            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                            x-transition:leave-end="opacity-0 translate-y-2 scale-[0.98]"
                            class="settings-user__menu"
                        >
                            <a href="{{ route('profile.edit') }}" class="settings-user__menu-link">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 20a7 7 0 0 1 14 0" />
                                </svg>
                                <span>{{ __('Profile') }}</span>
                            </a>

                            <div class="settings-user__menu-divider"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="settings-user__menu-button">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 12H9.75" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.25 8.25 3.75 3.75-3.75 3.75" />
                                    </svg>
                                    <span>{{ __('Log Out') }}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <main class="settings-shell settings-content">
            <section class="settings-hero">
                <div class="settings-hero__inner">
                    <div class="settings-hero__lead">
                        <div class="settings-hero__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 3v5c0 5-3.5 8.5-7 10-3.5-1.5-7-5-7-10V6l7-3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="m9.5 12 1.7 1.7L15 10" />
                            </svg>
                        </div>
                        <div>
                            <h1 class="settings-hero__title">Profile Settings</h1>
                            <p class="settings-hero__subtitle">Manage your account details, password, and security.</p>
                        </div>
                    </div>

                    <div class="settings-hero__art" aria-hidden="true">
                        <svg viewBox="0 0 320 160" fill="none">
                            <g opacity="0.35" fill="#9bbcff">
                                <circle cx="38" cy="38" r="2.5" />
                                <circle cx="62" cy="38" r="2.5" />
                                <circle cx="86" cy="38" r="2.5" />
                                <circle cx="110" cy="38" r="2.5" />
                                <circle cx="134" cy="38" r="2.5" />
                                <circle cx="158" cy="38" r="2.5" />
                                <circle cx="38" cy="62" r="2.5" />
                                <circle cx="62" cy="62" r="2.5" />
                                <circle cx="86" cy="62" r="2.5" />
                                <circle cx="110" cy="62" r="2.5" />
                                <circle cx="134" cy="62" r="2.5" />
                                <circle cx="158" cy="62" r="2.5" />
                            </g>
                            <path d="M222 14 264 31v31c0 32.5-21.6 55-42 66-20.4-11-42-33.5-42-66V31l42-17Z" fill="url(#heroShield)" />
                            <path d="m205 65 13 13 28-31" stroke="white" stroke-width="9" stroke-linecap="round" stroke-linejoin="round" />
                            <rect x="245" y="72" width="35" height="35" rx="10" fill="url(#heroLock)" />
                            <path d="M254 71v-7c0-7 5-12 12-12s12 5 12 12v7" stroke="#16306c" stroke-width="5" stroke-linecap="round" />
                            <rect x="258.8" y="82" width="10.5" height="14" rx="5.2" fill="white" />
                            <path d="M285 66c8-4 13-1 17 5" stroke="#d9e6ff" stroke-width="7" stroke-linecap="round" />
                            <path d="M304 71v20" stroke="#d9e6ff" stroke-width="7" stroke-linecap="round" />
                            <path d="M304 91c-3 8-8 11-16 12" stroke="#d9e6ff" stroke-width="7" stroke-linecap="round" />
                            <defs>
                                <linearGradient id="heroShield" x1="180" y1="14" x2="271" y2="122" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#2f79ff" />
                                    <stop offset="1" stop-color="#1e4ed8" />
                                </linearGradient>
                                <linearGradient id="heroLock" x1="245" y1="72" x2="280" y2="107" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#83a9ff" />
                                    <stop offset="1" stop-color="#4164ef" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
            </section>

            <section class="settings-grid">
                <aside class="settings-panel">
                    <a href="#profile-information-card"
                        class="settings-tab-link"
                        :class="{ 'is-active': activeTab === 'profile' }"
                        @click="activeTab = 'profile'">
                        <span class="settings-tab-link__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 1 0-4-4 4 4 0 0 0 4 4Z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 20a7 7 0 0 1 14 0" />
                            </svg>
                        </span>
                        <span>
                            <span class="settings-tab-link__label">Personal Information</span>
                            <span class="settings-tab-link__copy">Update your details</span>
                        </span>
                        <span class="settings-tab-link__arrow">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 0 1 1.06 0l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 1 1-1.06-1.06L11.19 10 7.22 6.03a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </a>

                    <a href="#password-security-card"
                        class="settings-tab-link"
                        :class="{ 'is-active': activeTab === 'security' }"
                        @click="activeTab = 'security'">
                        <span class="settings-tab-link__icon">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8 11V8a4 4 0 1 1 8 0v3" />
                                <rect x="5" y="11" width="14" height="10" rx="2.5" />
                            </svg>
                        </span>
                        <span>
                            <span class="settings-tab-link__label" style="color:#1d3160;">Password &amp; Security</span>
                            <span class="settings-tab-link__copy">Manage your password</span>
                        </span>
                        <span class="settings-tab-link__arrow">
                            <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M7.22 4.97a.75.75 0 0 1 1.06 0l4.5 4.5a.75.75 0 0 1 0 1.06l-4.5 4.5a.75.75 0 1 1-1.06-1.06L11.19 10 7.22 6.03a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                            </svg>
                        </span>
                    </a>
                </aside>

                <div class="settings-right">
                    <section class="settings-summary-card">
                        <div class="settings-summary-card__inner">
                            <div class="settings-summary-card__user">
                                <div class="settings-summary-card__avatar">{{ $initials }}</div>
                                <div class="min-w-0">
                                    <div class="settings-summary-card__name">{{ $user->name }}</div>
                                    <div class="settings-summary-card__email">{{ $user->email }}</div>
                                </div>
                            </div>

                            <button type="button" x-cloak x-show="!profileEditMode" x-transition.opacity @click="enableProfileEdit()" class="settings-summary-card__button">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 1 1 2.828 2.828l-8.88 8.88a2 2 0 0 1-.878.513l-2.608.783a.75.75 0 0 1-.932-.932l.783-2.608a2 2 0 0 1 .513-.878l8.88-8.88Z" />
                                </svg>
                                Edit Profile
                            </button>
                        </div>
                    </section>

                    <section id="profile-information-card" class="settings-card">
                        @include('profile.partials.update-profile-information-form')
                    </section>

                    <div class="settings-password-stack">
                        <section id="password-security-card" class="settings-card">
                            @include('profile.partials.update-password-form')
                        </section>

                        <section class="settings-card" style="border-color:#ffd4d4; box-shadow:0 18px 50px rgba(220, 38, 38, 0.08);">
                            @include('profile.partials.delete-user-form')
                        </section>
                    </div>
                </div>
            </section>

            <footer class="settings-footer">
                <div class="settings-footer__brand">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 19h16" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 19V8l6-3 6 3v11" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 11h4" />
                    </svg>
                    <span>Guidance and Advisory for Better Administration in Law</span>
                </div>
                <div class="settings-footer__copy">&copy; 2026 GABAY-Lex. All rights reserved.</div>
            </footer>
        </main>
    </div>
</x-app-layout>
