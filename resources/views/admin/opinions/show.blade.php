<x-admin-layout>
    <div class="max-w-5xl mx-auto space-y-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-start sm:justify-between">
            <div class="flex items-start gap-6">
                <a href="{{ route('admin.opinions.index') }}" class="mt-1 flex h-12 w-12 items-center justify-center rounded-2xl bg-white/80 text-slate-500 ring-1 ring-slate-900/5 hover:bg-white hover:text-slate-900 transition-all duration-300 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </a>
                <div class="min-w-0">
                    <h2 class="text-3xl font-bold text-slate-900 tracking-tight">{{ $opinion->title }}</h2>
                    <div class="mt-2 flex flex-wrap items-center gap-3 text-sm text-slate-600">
                        <span class="inline-flex items-center rounded-full bg-indigo-500/10 px-4 py-1.5 text-[10px] font-black uppercase tracking-[0.2em] text-indigo-600 ring-1 ring-indigo-500/20">
                            {{ $opinion->opinion_number }}
                        </span>
                        <span class="text-xs font-semibold text-slate-500">Issued {{ optional($opinion->date)->format('m/d/Y') }}</span>
                        <span class="text-xs font-semibold text-slate-400">Updated {{ $opinion->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('admin.opinions.edit', $opinion) }}" class="inline-flex items-center justify-center rounded-2xl bg-white/80 px-6 py-3.5 text-sm font-bold text-slate-700 ring-1 ring-slate-900/10 hover:bg-white hover:text-slate-900 transition-all shadow-sm">
                    Edit
                </a>
                <form method="POST" action="{{ route('admin.opinions.destroy', $opinion) }}" data-confirm="delete" data-confirm-title="Delete Legal Opinion" data-confirm-message="Delete this legal opinion?">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-rose-500/5 px-6 py-3.5 text-sm font-bold text-rose-600 ring-1 ring-rose-500/20 hover:bg-rose-600 hover:text-white transition-all">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        @if (session('success'))
            <div id="opinion-success" class="rounded-2xl bg-emerald-500/10 p-4 ring-1 ring-emerald-500/20 text-emerald-700 text-sm font-bold transition-opacity duration-500">
                {{ session('success') }}
            </div>
        @endif

        <div class="rounded-[2.5rem] bg-white/80 backdrop-blur-xl p-10 ring-1 ring-slate-900/5 shadow-[0_24px_70px_rgba(15,23,42,0.08)]">
            <div class="text-xs font-bold uppercase tracking-widest text-slate-500 mb-6">Context</div>
            <div class="whitespace-pre-wrap text-sm leading-relaxed text-slate-700">{{ $opinion->context }}</div>
        </div>
    </div>
</x-admin-layout>
<script>
const s=document.getElementById('opinion-success');if(s){setTimeout(()=>{s.classList.add('opacity-0')},5000);setTimeout(()=>{s.remove()},5600)}
</script>
