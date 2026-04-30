<nav x-data="{ open: false }" class="border-b border-white/10 bg-[radial-gradient(circle_at_top_left,rgba(255,222,21,0.14),transparent_40%),radial-gradient(circle_at_bottom_right,rgba(255,255,255,0.08),transparent_45%),linear-gradient(90deg,#002C76_0%,#083b8f_45%,#002C76_100%)]">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between py-3">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('chat.index') }}">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-white/90 ring-1 ring-white/30 shadow-sm overflow-hidden">
                                <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                            </div>
                            <div class="min-w-0">
                                <div class="text-[15px] font-black tracking-tight leading-tight text-white">{{ config('app.name', 'GABAY-Lex') }}</div>
                                <div class="text-[10px] font-semibold leading-tight tracking-wide text-white/80">Guidance and Advisory for Better Administration in Law</div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link class="text-white/85 hover:text-white hover:border-white/40 focus:text-white focus:border-white/60" :href="auth()->user()->is_admin ? route('admin.legal.ai') : route('chat.index')" :active="request()->routeIs('chat.*') || request()->routeIs('admin.legal.ai*')">
                        {{ __('Chat') }}
                    </x-nav-link>
                    @if(!auth()->user()->is_admin)
                        <x-nav-link class="text-white/85 hover:text-white hover:border-white/40 focus:text-white focus:border-white/60" :href="route('chat.saved')" :active="request()->routeIs('chat.saved')">
                            {{ __('Saved') }}
                        </x-nav-link>
                    @endif
                    @can('admin')
                        <x-nav-link class="text-white/85 hover:text-white hover:border-white/40 focus:text-white focus:border-white/60" :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                            {{ __('Admin') }}
                        </x-nav-link>
                    @endcan
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 rounded-2xl bg-white/10 px-3 py-2 text-sm font-bold text-white/90 ring-1 ring-white/15 hover:bg-white/15 hover:text-white focus:outline-none transition ease-in-out duration-150">
                            <div class="max-w-[160px] truncate">{{ Auth::user()->name }}</div>

                            <div class="ms-1">
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
            <div class="-me-2 flex items-center sm:hidden">
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
