<nav x-data="{ open: false }" class="border-b border-white/10 shadow-[0_12px_36px_rgba(0,44,118,0.28)]" style="background:
    radial-gradient(circle at 0% 50%, rgba(30,64,175,0.42), transparent 26%),
    radial-gradient(circle at 72% 85%, rgba(96,165,250,0.18), transparent 22%),
    linear-gradient(90deg, #081a73 0%, #0b2787 38%, #0a3da0 100%);">
    <div class="mx-auto max-w-[1540px] px-4 sm:px-6 lg:px-8">
        <div class="flex min-h-[80px] items-center justify-between gap-4">
            <div class="flex min-w-0 items-center gap-4">
                <a href="{{ auth()->user()->is_admin ? route('admin.dashboard') : route('chat.index') }}" class="flex min-w-0 items-center gap-3">
                    <div class="flex h-[54px] w-[54px] items-center justify-center overflow-hidden rounded-full bg-white shadow-[0_6px_20px_rgba(255,255,255,0.18)]">
                        <x-application-logo class="block h-[48px] w-auto fill-current text-gray-800" />
                    </div>
                    <div class="min-w-0">
                        <div class="text-[14px] font-black leading-tight text-white sm:text-[15px]">{{ config('app.name', 'GABAY-Lex') }}</div>
                        <div class="truncate text-[9px] font-semibold leading-tight text-blue-100/95 sm:text-[10px]">Guidance and Advisory for Better Administration in Law</div>
                    </div>
                </a>
            </div>

            <div class="hidden items-center justify-center gap-10 lg:flex">
                <x-nav-link class="inline-flex items-center gap-2 border-none px-0 py-0 text-[14px] font-bold text-white/95 hover:text-white focus:text-white focus:outline-none" :href="auth()->user()->is_admin ? route('admin.legal.ai') : route('chat.index')" :active="request()->routeIs('chat.*') || request()->routeIs('admin.legal.ai*')">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10h8M8 14h4M7 4h10a3 3 0 013 3v6a3 3 0 01-3 3h-3l-4 4v-4H7a3 3 0 01-3-3V7a3 3 0 013-3z" />
                    </svg>
                    {{ __('Chat') }}
                </x-nav-link>

                @can('admin')
                    <x-nav-link class="inline-flex items-center gap-2 border-none px-0 py-0 text-[14px] font-bold text-white/95 hover:text-white focus:text-white focus:outline-none" :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2" />
                            <circle cx="9.5" cy="7" r="3.5" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 8h4M19 6v4" />
                        </svg>
                        {{ __('Admin') }}
                    </x-nav-link>
                @endcan
            </div>

            <div class="hidden sm:flex sm:items-center">
                <x-dropdown align="right" width="48" contentClasses="overflow-hidden rounded-[1.25rem] border border-[#dbe6fb] bg-white p-2 shadow-[0_18px_45px_rgba(15,23,42,0.18)]">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-3 rounded-[1.1rem] bg-white/10 px-4 py-2 text-white transition hover:bg-white/15 focus:outline-none">
                            <div class="text-right">
                                <div class="max-w-[140px] truncate text-[14px] font-bold leading-tight">{{ Auth::user()->name }}</div>
                                <div class="text-[11px] font-medium leading-tight text-blue-100/90">{{ auth()->user()->is_admin ? 'Administrator' : 'User' }}</div>
                            </div>
                            <div class="relative flex h-[42px] w-[42px] items-center justify-center rounded-full bg-gradient-to-br from-[#7c8dff] to-[#5f4af3] text-[0.95rem] font-black text-white">
                                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                <span class="absolute bottom-0.5 right-0.5 h-3 w-3 rounded-full border-2 border-[#1840a7] bg-[#67dd4f]"></span>
                            </div>
                            <svg class="h-4 w-4 text-white/90" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="rounded-xl px-3 py-2.5 font-semibold text-slate-700 hover:bg-blue-50 hover:text-blue-700">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')" class="rounded-xl px-3 py-2.5 font-semibold text-slate-700 hover:bg-rose-50 hover:text-rose-700"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center rounded-xl bg-white/10 p-2.5 text-white transition hover:bg-white/15 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{'block': open, 'hidden': ! open}" class="hidden border-t border-white/10 bg-[#0b2f8f] sm:hidden">
        <div class="space-y-1 px-4 py-3">
            <x-responsive-nav-link class="rounded-xl border-l-0 px-4 py-3 text-white/95 hover:bg-white/10 hover:text-white" :href="auth()->user()->is_admin ? route('admin.legal.ai') : route('chat.index')" :active="request()->routeIs('chat.*') || request()->routeIs('admin.legal.ai*')">
                {{ __('Chat') }}
            </x-responsive-nav-link>
            @can('admin')
                <x-responsive-nav-link class="rounded-xl border-l-0 px-4 py-3 text-white/95 hover:bg-white/10 hover:text-white" :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')">
                    {{ __('Admin') }}
                </x-responsive-nav-link>
            @endcan
            <x-responsive-nav-link class="rounded-xl border-l-0 px-4 py-3 text-white/95 hover:bg-white/10 hover:text-white" :href="route('profile.edit')">
                {{ __('Profile') }}
            </x-responsive-nav-link>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-responsive-nav-link class="rounded-xl border-l-0 px-4 py-3 text-white/95 hover:bg-white/10 hover:text-white" :href="route('logout')"
                    onclick="event.preventDefault(); this.closest('form').submit();">
                    {{ __('Log Out') }}
                </x-responsive-nav-link>
            </form>
        </div>
    </div>
</nav>
