<x-app-layout>
    <div class="bg-[#f4f7fd] py-6 sm:py-8" x-data="{ activeSection: 'personal-information', profileEditing: false, securityOpen: null }">
        <div class="mx-auto max-w-[1460px] px-4 sm:px-6 lg:px-8">
            <section class="relative overflow-hidden rounded-[2rem] border border-[#dbe6fb] bg-[linear-gradient(180deg,#ffffff_0%,#f7faff_100%)] px-5 py-5 shadow-[0_18px_48px_rgba(29,78,216,0.08)] sm:px-6 sm:py-6">
                <div class="absolute inset-x-0 bottom-0 h-24 bg-[radial-gradient(circle_at_80%_0%,rgba(59,130,246,0.12),transparent_26%),radial-gradient(circle_at_92%_15%,rgba(96,165,250,0.16),transparent_14%)]"></div>
                <div class="absolute right-8 top-5 hidden h-8 w-8 rounded-full bg-blue-100/80 lg:block"></div>
                <div class="absolute right-52 top-7 hidden h-3 w-3 rounded-full bg-blue-200 lg:block"></div>
                <div class="absolute right-40 top-14 hidden h-2 w-2 rounded-full bg-blue-300 lg:block"></div>
                <div class="absolute right-[18rem] top-20 hidden h-2 w-2 rounded-full bg-blue-200 lg:block"></div>

                <div class="relative flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-start gap-4">
                        <div class="flex h-[68px] w-[68px] items-center justify-center rounded-[1.25rem] border border-[#dce7fb] bg-white shadow-[0_14px_34px_rgba(29,78,216,0.08)]">
                            <svg class="h-8 w-8 text-[#2563eb]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3l7 3v5c0 5-3.5 8.5-7 10-3.5-1.5-7-5-7-10V6l7-3z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.5 12.5l1.7 1.7 3.3-4" />
                            </svg>
                        </div>
                        <div class="pt-0.5">
                            <h1 class="text-[1.5rem] font-black tracking-tight text-[#14214d] sm:text-[1.65rem]">Profile Settings</h1>
                            <p class="mt-2 text-[13px] font-medium text-[#4b5d84]">Manage your account details, password, and security.</p>
                        </div>
                    </div>

                    <div class="hidden lg:flex lg:items-end">
                        <svg viewBox="0 0 340 150" class="h-auto w-[320px]">
                            <defs>
                                <linearGradient id="heroShield" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" stop-color="#1d4ed8" />
                                    <stop offset="100%" stop-color="#60a5fa" />
                                </linearGradient>
                            </defs>
                            <path d="M0 150c24-21 45-34 71-34 21 0 30 9 49 9 30 0 43-42 82-42 26 0 43 18 60 18 17 0 28-9 43-20v69H0z" fill="#eaf1ff" />
                            <path d="M170 12l42 17v34c0 34-19 56-42 68-23-12-42-34-42-68V29l42-17z" fill="url(#heroShield)" />
                            <path d="M152 61l12 12 26-29" stroke="#fff" stroke-width="10" stroke-linecap="round" stroke-linejoin="round" fill="none" />
                            <rect x="196" y="74" width="42" height="34" rx="8" fill="#4f7ef5" />
                            <path d="M208 74V66c0-6 4-10 9-10 6 0 10 4 10 10v8" stroke="#fff" stroke-width="6" stroke-linecap="round" />
                            <circle cx="218" cy="90" r="4" fill="#fff" />
                            <rect x="214" y="91" width="8" height="10" rx="4" fill="#fff" />
                            <circle cx="258" cy="36" r="4" fill="#bfd3ff" />
                            <circle cx="286" cy="90" r="4" fill="#bfd3ff" />
                            <circle cx="120" cy="28" r="5" fill="#cfe0ff" />
                            <circle cx="110" cy="56" r="3" fill="#bfd3ff" />
                            <circle cx="76" cy="48" r="4" fill="#cfe0ff" />
                        </svg>
                    </div>
                </div>
            </section>

            <div class="mt-6 grid gap-6 lg:grid-cols-12">
                <aside class="lg:col-span-3">
                    <div class="rounded-[1.7rem] border border-[#dbe6fb] bg-white p-4 shadow-[0_18px_45px_rgba(29,78,216,0.08)]">
                        <div class="space-y-2.5">
                            <a href="#personal-information" @click="activeSection = 'personal-information'" :class="activeSection === 'personal-information' ? 'bg-[#edf4ff] text-[#1555e5]' : 'hover:bg-[#f7faff] text-[#1a2750]'" class="flex items-center gap-4 rounded-[1.15rem] px-4 py-4 transition">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full border border-[#d7e5ff] bg-white text-[#2563eb] shadow-sm">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 12a4 4 0 100-8 4 4 0 000 8zM5 20a7 7 0 0114 0" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-[14px] font-bold">Personal Information</div>
                                    <div class="mt-1 text-[13px] font-medium text-[#5b6b8f]">Update your details</div>
                                </div>
                            </a>

                            <a href="#password-security" @click="activeSection = 'password-security'" :class="activeSection === 'password-security' ? 'bg-[#edf4ff] text-[#1555e5]' : 'hover:bg-[#f7faff] text-[#1a2750]'" class="flex items-center gap-4 rounded-[1.15rem] px-4 py-4 transition">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full border border-[#d7e5ff] bg-[#f7faff] text-[#1f325f] shadow-sm">
                                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 10V8a4 4 0 118 0v2M6 10h12v10H6V10z" />
                                    </svg>
                                </div>
                                <div>
                                    <div class="text-[14px] font-bold">Password &amp; Security</div>
                                    <div class="mt-1 text-[13px] font-medium text-[#5b6b8f]">Manage your password</div>
                                </div>
                            </a>

                        </div>

                        <div class="my-5 h-px bg-[#e8eefb]"></div>

                        <div
                            x-data="{
                                open: false,
                                showAllDevelopers: false,
                                developers: [
                                    { name: 'Kevin Aquino', role: 'Full Stack Developer', photo: 'https://ui-avatars.com/api/?name=Kevin+Aquino&background=111827&color=ffffff&size=128' },
                                    { name: 'Shaquille L. Buraga', role: 'Project Lead / Backend Developer', photo: 'https://ui-avatars.com/api/?name=Shaquille+L.+Buraga&background=dbeafe&color=1e3a8a&size=128' },
                                    { name: 'John Paul Gomez', role: 'UI/UX Designer', photo: 'https://ui-avatars.com/api/?name=John+Paul+Gomez&background=0f172a&color=ffffff&size=128' },
                                    { name: 'Mark Louie Abalos', role: 'AI Engineer', photo: 'https://ui-avatars.com/api/?name=Mark+Louie+Abalos&background=bfdbfe&color=1d4ed8&size=128' }
                                ]
                            }"
                            class="rounded-[1.25rem] border border-[#d9e6ff] bg-[linear-gradient(180deg,#f8fbff_0%,#f3f7ff_100%)] p-4 shadow-[0_10px_28px_rgba(37,99,235,0.06)]"
                        >
                            <button type="button" @click="open = !open" class="flex w-full items-start justify-between gap-3 text-left">
                                <div class="flex items-start gap-3">
                                    <div class="flex h-11 w-11 items-center justify-center rounded-full bg-white text-[#2563eb] shadow-sm ring-1 ring-[#dbe6fb]">
                                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2" />
                                            <circle cx="9" cy="7" r="4" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M23 21v-2a4 4 0 00-3-3.87" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 3.13a4 4 0 010 7.75" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-[14px] font-bold text-[#16244c]">AI Development Team</div>
                                        <p class="mt-1 text-[12px] font-medium leading-5 text-[#5b6b8f]">The amazing people behind GABAY-Lex AI System.</p>
                                    </div>
                                </div>
                                <svg class="mt-1 h-4 w-4 shrink-0 text-[#4b6fd8] transition" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                                </svg>
                            </button>

                            <div x-show="open" x-transition.opacity.duration.200ms class="mt-4 space-y-3" style="display: none;">
                                <template x-for="developer in developers" :key="developer.name">
                                    <div class="flex items-center gap-3 rounded-2xl bg-white/70 px-3 py-2.5">
                                        <img :src="developer.photo" :alt="developer.name" class="h-12 w-12 rounded-full object-cover ring-1 ring-[#dbe6fb] shadow-sm">
                                        <div class="min-w-0">
                                            <div class="truncate text-[13px] font-bold text-[#16244c]" x-text="developer.name"></div>
                                            <div class="text-[12px] font-medium text-[#6a7ca2]" x-text="developer.role"></div>
                                        </div>
                                    </div>
                                </template>

                                <button type="button" @click="showAllDevelopers = true" class="mt-1 inline-flex w-full items-center justify-center rounded-xl border border-[#cfe0ff] bg-white px-4 py-3 text-[13px] font-bold text-[#2563eb] transition hover:bg-[#f5f9ff]">
                                    View All Developers
                                </button>
                            </div>

                            <div x-cloak x-show="showAllDevelopers" x-transition.opacity class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/30 p-4" style="display: none;">
                                <div @click.outside="showAllDevelopers = false" class="w-full max-w-lg rounded-[1.5rem] border border-[#d9e6ff] bg-white p-5 shadow-[0_24px_60px_rgba(15,23,42,0.18)]">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h3 class="text-[1rem] font-black text-[#16244c]">AI Development Team</h3>
                                            <p class="mt-1 text-[13px] font-medium text-[#5b6b8f]">Meet the people behind GABAY-Lex AI System.</p>
                                        </div>
                                        <button type="button" @click="showAllDevelopers = false" class="flex h-9 w-9 items-center justify-center rounded-full border border-[#dbe6fb] bg-white text-[#5b6b8f] transition hover:bg-[#f5f9ff]">
                                            <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div class="mt-4 space-y-3">
                                        <template x-for="developer in developers" :key="`${developer.name}-modal`">
                                            <div class="flex items-center gap-3 rounded-2xl border border-[#e3ebfb] bg-[#f8fbff] px-4 py-3">
                                                <img :src="developer.photo" :alt="developer.name" class="h-14 w-14 rounded-full object-cover ring-1 ring-[#dbe6fb] shadow-sm">
                                                <div class="min-w-0">
                                                    <div class="truncate text-[14px] font-bold text-[#16244c]" x-text="developer.name"></div>
                                                    <div class="text-[12px] font-medium text-[#6a7ca2]" x-text="developer.role"></div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </aside>

                <div class="space-y-5 lg:col-span-9">
                    <section id="account-activity" class="rounded-[1.55rem] border border-[#dbe6fb] bg-white px-6 py-5 shadow-[0_18px_45px_rgba(29,78,216,0.08)]">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-5">
                                <div class="flex h-[70px] w-[70px] items-center justify-center rounded-full bg-gradient-to-br from-[#5d6cf8] to-[#5437e9] text-[1.8rem] font-black text-white shadow-[0_12px_28px_rgba(91,91,255,0.28)]">
                                    {{ strtoupper(substr($user->name ?? auth()->user()?->name ?? 'U', 0, 2)) }}
                                </div>
                                <div class="min-w-0">
                                    <h2 class="truncate text-[0.95rem] font-black text-[#14214d]">{{ $user->name ?? auth()->user()?->name }}</h2>
                                    <p class="mt-1 truncate text-[13px] font-medium text-[#58698d]">{{ $user->email ?? auth()->user()?->email }}</p>
                                </div>
                            </div>

                            <a href="#personal-information" @click="activeSection = 'personal-information'; profileEditing = true; $nextTick(() => document.getElementById('name')?.focus())" class="inline-flex h-11 items-center justify-center gap-2 rounded-xl border border-[#bcd1fe] bg-[#fafdff] px-6 text-[14px] font-bold text-[#2060ef] transition hover:bg-[#f2f7ff]">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-8.9 8.9a2 2 0 01-.878.51l-2.54.726a.75.75 0 01-.927-.927l.726-2.54a2 2 0 01.51-.878l8.9-8.9z" />
                                </svg>
                                Edit Profile
                            </a>
                        </div>
                    </section>

                    <div id="personal-information" class="rounded-[1.55rem] border border-[#dbe6fb] bg-white px-6 py-5 shadow-[0_18px_45px_rgba(29,78,216,0.08)]">
                        @include('profile.partials.update-profile-information-form')
                    </div>

                    <div class="grid gap-5 xl:grid-cols-[1.15fr_0.85fr] xl:items-start">
                        <div id="password-security" class="self-start rounded-[1.55rem] border border-[#dbe6fb] bg-white px-6 py-5 shadow-[0_18px_45px_rgba(29,78,216,0.08)]">
                            @include('profile.partials.update-password-form')
                        </div>

                        <div class="h-fit self-start rounded-[1.55rem] border border-[#ffd4d8] bg-white px-6 py-5 shadow-[0_18px_45px_rgba(239,68,68,0.06)]">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>

                    <p class="pb-2 text-center text-[13px] font-medium text-[#66789c]">&copy; 2026 GABAY-Lex. All rights reserved.</p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
