<x-admin-layout>
    <div class="max-w-5xl mx-auto space-y-8">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.opinions.show', $opinion) }}" class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/80 text-slate-500 ring-1 ring-slate-900/5 hover:bg-white hover:text-slate-900 transition-all duration-300 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>
            <div>
                <h2 class="text-3xl font-bold text-slate-900 tracking-tight">Edit Legal Opinion</h2>
                <p class="mt-1 text-slate-600">Update an existing legal reference entry.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="rounded-2xl bg-rose-500/10 p-4 ring-1 ring-rose-500/20 text-rose-600 text-sm font-medium">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-[2.5rem] bg-white/80 backdrop-blur-xl p-10 ring-1 ring-slate-900/5 shadow-[0_24px_70px_rgba(15,23,42,0.08)]">
            <form action="{{ route('admin.opinions.update', $opinion) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 gap-8">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Title</label>
                        <input type="text" name="title" value="{{ old('title', $opinion->title) }}" required
                            class="w-full rounded-2xl bg-white border-slate-900/10 px-6 py-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all">
                    </div>

                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Opinion Number and Year</label>
                            <input type="text" name="opinion_number" value="{{ old('opinion_number', $opinion->opinion_number) }}" required
                                class="w-full rounded-2xl bg-white border-slate-900/10 px-6 py-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Date</label>
                            <input type="text" name="date" value="{{ old('date', optional($opinion->date)->format('m/d/Y')) }}" placeholder="mm/dd/yyyy" inputmode="numeric" pattern="[0-9]{1,2}/[0-9]{1,2}/[0-9]{4}" required
                                class="w-full rounded-2xl bg-white border-slate-900/10 px-6 py-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Context</label>
                        <textarea name="context" rows="14"
                            class="w-full rounded-2xl bg-white border-slate-900/10 px-6 py-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all resize-y">{{ old('context', $opinion->context) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-8 border-t border-slate-900/5">
                    <a href="{{ route('admin.opinions.show', $opinion) }}" class="px-8 py-4 text-sm font-bold text-slate-400 hover:text-slate-600 transition-colors">Cancel</a>
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-10 py-4 text-sm font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-500 transition-all duration-300">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
<script>
const d=document.querySelector('input[name="date"]');if(d){const f=v=>{const s=String(v||'').replace(/\D/g,'').slice(0,8);const a=[s.slice(0,2),s.slice(2,4),s.slice(4,8)].filter(Boolean);return a.join('/')};d.addEventListener('input',e=>{const v=e.target.value;e.target.value=f(v)});d.addEventListener('keypress',e=>{if(!/[0-9]/.test(e.key))e.preventDefault()});d.addEventListener('blur',e=>{e.target.value=f(e.target.value)})}
</script>
