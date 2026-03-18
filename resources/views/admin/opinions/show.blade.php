<x-admin-layout>
    <div class="max-w-5xl mx-auto space-y-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-start gap-6">
                <a href="{{ route('admin.opinions.index') }}" class="mt-1 flex h-12 w-12 items-center justify-center rounded-2xl bg-white/[0.03] text-slate-400 ring-1 ring-white/10 hover:bg-white/[0.05] hover:text-white transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <div class="min-w-0">
                    <h2 class="text-3xl font-bold text-white tracking-tight">{{ $opinion->title }}</h2>
                    <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-slate-400">
                        <span class="inline-flex items-center rounded-full bg-indigo-500/10 px-4 py-1.5 text-[10px] font-black uppercase tracking-[0.2em] text-indigo-400 ring-1 ring-indigo-500/20">
                            {{ $opinion->opinion_number }}
                        </span>
                        <span class="text-xs font-semibold text-slate-500">Issued {{ optional($opinion->date)->format('Y-m-d') }}</span>
                        <span class="text-xs font-semibold text-slate-600">Updated {{ $opinion->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.opinions.edit', $opinion) }}" class="inline-flex items-center justify-center rounded-2xl bg-white/[0.05] px-6 py-3.5 text-sm font-bold text-white ring-1 ring-white/10 hover:bg-white/[0.1] transition-all">
                    Edit
                </a>
                <form method="POST" action="{{ route('admin.opinions.destroy', $opinion) }}" onsubmit="return confirm('Delete this legal opinion?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-rose-500/10 px-6 py-3.5 text-sm font-bold text-rose-300 ring-1 ring-rose-500/20 hover:bg-rose-500 hover:text-white transition-all">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        <div class="rounded-[2.5rem] bg-white/[0.02] backdrop-blur-xl p-10 ring-1 ring-white/10 shadow-2xl">
            <div class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-4">Context</div>
            <div class="whitespace-pre-wrap text-sm leading-relaxed text-slate-200">{{ $opinion->context }}</div>
        </div>
    </div>
</x-admin-layout>
