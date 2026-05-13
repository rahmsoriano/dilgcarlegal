<nav x-data="{ open: false }" class="border-b border-white/10 shadow-[0_10px_30px_rgba(0,44,118,0.22)]" style="background:
    radial-gradient(circle at top left, rgba(255, 222, 21, 0.14), transparent 40%),
    radial-gradient(circle at bottom right, rgba(255, 255, 255, 0.08), transparent 45%),
    linear-gradient(90deg, #002C76 0%, #083B8F 45%, #002C76 100%);">
    <!-- Primary Navigation Menu -->
    <div class="max-w-none px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-[auto_1fr_auto] items-center gap-6 py-3">
            <div class="flex items-center">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('chat.index') }}">
                        <div class="flex items-center gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-white/95 ring-1 ring-white/30 shadow-sm overflow-hidden">
                                <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                            </div>
                            <div class="min-w-0">
                                <div class="text-[18px] font-black tracking-tight leading-tight text-white">{{ config('app.name', 'GABAY-Lex') }}</div>
                                <div class="text-[10px] font-semibold leading-tight tracking-wide text-white/80">Guidance and Advisory for Better Administration in Law</div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <div class="hidden items-center justify-center gap-12 sm:flex">
                <a href="{{ auth()->user()->is_admin ? route('admin.legal.ai') : route('chat.index') }}" class="inline-flex items-center gap-2 text-[16px] font-semibold text-white/90 transition hover:text-white">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 14h5" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20 21 16V6a2 2 0 0 0-2-2H5A2 2 0 0 0 3 6v10a2 2 0 0 0 2 2h12Z" />
                    </svg>
                    <span>Chat</span>
                </a>
                @can('admin')
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center gap-2 text-[16px] font-semibold text-white/90 transition hover:text-white">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                            <circle cx="9" cy="7" r="4" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 8v6" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M23 11h-6" />
                        </svg>
                        <span>Admin</span>
                    </a>
                @endcan
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:justify-end">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-4 rounded-[1.6rem] bg-[#335fb8]/60 px-6 py-3 text-white/90 ring-1 ring-white/10 shadow-[0_10px_24px_rgba(15,23,42,0.12)] hover:bg-[#3b67c0]/70 hover:text-white focus:outline-none transition ease-in-out duration-150">
                            <div class="text-right leading-tight">
                                <div class="max-w-[180px] truncate text-[15px] font-black tracking-tight text-white">{{ Auth::user()->name }}</div>
                                <div class="mt-1 text-[11px] font-semibold text-white/75">{{ Auth::user()->is_admin ? 'Administrator' : 'User' }}</div>
                            </div>

                            <div class="relative flex h-14 w-14 items-center justify-center rounded-full bg-[linear-gradient(135deg,#8b5cf6_0%,#6366f1_100%)] text-xl font-black text-white shadow-[0_10px_24px_rgba(99,102,241,0.25)]">
                                {{ collect(explode(' ', Auth::user()->name))->filter()->take(2)->map(fn ($part) => strtoupper(substr($part, 0, 1)))->implode('') ?: 'AA' }}
                                <span class="absolute bottom-1 right-1 h-3.5 w-3.5 rounded-full border-2 border-[#2f55b4] bg-lime-400"></span>
                            </div>

                            <div class="pl-1">
                                <svg class="fill-current h-4 w-4 text-white/80" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center justify-end sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-2xl bg-white/10 p-2 text-white/85 ring-1 ring-white/15 hover:bg-white/15 hover:text-white focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-[#002C76]/40 backdrop-blur-xl border-t border-white/10">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link class="text-white/90 hover:text-white hover:bg-white/10" :href="auth()->user()->is_admin ? route('admin.legal.ai') : route('chat.index')" :active="request()->routeIs('chat.*') || request()->routeIs('admin.legal.ai*')">
                {{ __('Chat') }}
            </x-responsive-nav-link>
            @if(!auth()->user()->is_admin)
                <x-responsive-nav-link class="text-white/90 hover:text-white hover:bg-white/10" :href="route('chat.saved')" :active="request()->routeIs('chat.saved')">
                    {{ __('Saved') }}
                </x-responsive-nav-link>
            @endif
            @can('admin')
                <x-responsive-nav-link class="text-white/90 hover:text-white hover:bg-white/10" :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Admin') }}
                </x-responsive-nav-link>
            @endcan
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-white/10">
            <div class="px-4">
                <div class="font-bold text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-semibold text-sm text-white/75">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link class="text-white/90 hover:text-white hover:bg-white/10" :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link class="text-white/90 hover:text-white hover:bg-white/10" :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
