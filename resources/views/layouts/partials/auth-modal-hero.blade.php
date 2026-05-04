@php
    $authHeroLogo = asset('dilglogo.png');
    $authHeroExact = asset('images/login-hero-exact.png');
@endphp

<div class="relative h-full overflow-hidden" style="width:500px;">
    <div style="position: absolute; inset: 0; pointer-events: none; background:
        radial-gradient(circle at 16% 16%, rgba(255,255,255,0.14), transparent 24%),
        radial-gradient(circle at 58% 40%, rgba(255,255,255,0.09), transparent 28%),
        radial-gradient(circle at 84% 74%, rgba(255,255,255,0.15), transparent 22%);
        opacity: 0.95;"></div>

    <div class="relative z-[2] flex items-start gap-4">
        <div class="h-14 w-14 shrink-0 overflow-hidden rounded-full bg-white shadow-[0_18px_36px_rgba(0,0,0,0.18)]">
            <img
                src="{{ $authHeroLogo }}"
                alt="DILG Seal"
                class="h-full w-full object-contain"
            >
        </div>

        <div class="min-w-0">
            <div class="text-sm font-black uppercase tracking-wide text-white">
                Department of the Interior and Local Government
            </div>
            <div class="mt-1 text-xs font-semibold uppercase tracking-[0.22em] text-white/80">
                Cordillera Administrative Region
            </div>
            <div class="mt-1 text-xs italic text-white/80">
                Matino. Mahusay. at Maaasahan.
            </div>
        </div>
    </div>


    <div style="position: absolute; left: 0; top: 258px; width: 310px; z-index: 2;">
        <p class="text-sm font-semibold uppercase tracking-[0.38em] text-cyan-200/90">
            <span>GABAY-Lex</span>
            <span class="mt-2 block text-[11px] font-semibold normal-case tracking-wide text-white/80">Guidance and Advisory for Better Administration in Law</span>
        </p>
        <h2 class="mt-5 text-[46px] font-semibold leading-[1.08] tracking-[-0.04em] text-white">
            Smart legal support for efficient public service.
        </h2>
        <p class="mt-4 text-base leading-7 text-blue-100/78">
            Instant help, document assistance, and reliable guidance all in one place.
        </p>
    </div>

    <div aria-hidden="true" style="position: absolute; right: 0px; bottom: 18px; width: 230px; height: 448px; z-index: 2; pointer-events: none; overflow: visible;">
        <div style="position: absolute; right: 8px; top: 22px; width: 92px; height: 92px; opacity: 0.32; background-image: radial-gradient(circle, rgba(255,255,255,0.95) 1.25px, transparent 1.5px); background-size: 20px 20px;"></div>
        <div style="position: absolute; left: 50%; bottom: -2px; width: 172px; height: 22px; transform: translateX(-50%); border-radius: 999px; background: radial-gradient(circle, rgba(15,23,42,0.45) 0%, rgba(15,23,42,0.18) 48%, rgba(15,23,42,0) 78%); filter: blur(10px);"></div>
        <img
            src="{{ $authHeroExact }}"
            alt=""
            style="position: absolute; right: 0; bottom: 10px; width: 198px; height: auto; filter: drop-shadow(0 26px 34px rgba(15, 23, 42, 0.28));"
        >
    </div>

    <div style="position: absolute; left: -18px; right: 120px; bottom: -6px; height: 136px; opacity: 0.2; pointer-events: none; background:
        radial-gradient(120% 100% at 0% 100%, transparent 58%, rgba(255,255,255,0.16) 58.5%, transparent 59.6%),
        radial-gradient(116% 96% at 0% 100%, transparent 63%, rgba(255,255,255,0.14) 63.5%, transparent 64.6%),
        radial-gradient(112% 92% at 0% 100%, transparent 68%, rgba(255,255,255,0.12) 68.5%, transparent 69.6%),
        radial-gradient(108% 88% at 0% 100%, transparent 73%, rgba(255,255,255,0.10) 73.5%, transparent 74.6%);"></div>
</div>
