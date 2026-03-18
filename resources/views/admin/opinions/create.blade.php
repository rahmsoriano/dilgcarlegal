<x-admin-layout>
    <div class="max-w-5xl mx-auto space-y-8">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.opinions.index') }}" class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/[0.03] text-slate-400 ring-1 ring-white/10 hover:bg-white/[0.05] hover:text-white transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>
            <div>
                <h2 class="text-3xl font-bold text-white tracking-tight">Add Legal Opinion</h2>
                <p class="mt-1 text-slate-400">Create a new legal reference entry for the chatbot knowledge base.</p>
            </div>
        </div>

        @if($errors->any())
            <div class="rounded-2xl bg-rose-500/10 p-4 ring-1 ring-rose-500/20 text-rose-400 text-sm font-medium">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-[2.5rem] bg-white/[0.02] backdrop-blur-xl p-10 ring-1 ring-white/10 shadow-2xl">
            <form action="{{ route('admin.opinions.store') }}" method="POST" class="space-y-8">
                @csrf

                <div class="grid grid-cols-1 gap-8">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Title</label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                            class="w-full rounded-2xl bg-white/[0.03] border-white/10 px-6 py-4 text-sm text-white placeholder:text-slate-600 focus:border-blue-500/50 focus:ring-blue-500/20 transition-all">
                    </div>

                    <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Opinion Number and Year</label>
                            <input type="text" name="opinion_number" value="{{ old('opinion_number') }}" placeholder="Opinion No. 06, s. 2003" required
                                class="w-full rounded-2xl bg-white/[0.03] border-white/10 px-6 py-4 text-sm text-white placeholder:text-slate-600 focus:border-blue-500/50 focus:ring-blue-500/20 transition-all">
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Date</label>
                            <input type="date" name="date" value="{{ old('date') }}" required
                                class="w-full rounded-2xl bg-white/[0.03] border-white/10 px-6 py-4 text-sm text-white placeholder:text-slate-600 focus:border-blue-500/50 focus:ring-blue-500/20 transition-all">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Context</label>
                        <textarea name="context" rows="14" required
                            class="w-full rounded-2xl bg-white/[0.03] border-white/10 px-6 py-4 text-sm text-white placeholder:text-slate-600 focus:border-blue-500/50 focus:ring-blue-500/20 transition-all resize-y">{{ old('context') }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-6 border-t border-white/5">
                    <a href="{{ route('admin.opinions.index') }}" class="px-8 py-4 text-sm font-bold text-slate-400 hover:text-white transition-colors">Cancel</a>
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-10 py-4 text-sm font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-500 transition-all duration-300">
                        Save Opinion
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
