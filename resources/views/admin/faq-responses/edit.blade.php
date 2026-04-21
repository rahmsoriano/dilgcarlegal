<x-admin-layout>
    <div class="max-w-5xl mx-auto space-y-8">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.faq-responses.index') }}" class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/70 text-slate-600 ring-1 ring-slate-900/10 hover:bg-white hover:text-slate-900 transition-all duration-300 shadow-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>
            <div>
                <div class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">FAQ Response Manager</div>
                <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900">Edit Response</h2>
            </div>
        </div>

        <div class="rounded-[2.5rem] bg-white/80 backdrop-blur-xl ring-1 ring-slate-900/5 shadow-[0_24px_70px_rgba(15,23,42,0.08)] overflow-hidden">
            <form method="POST" action="{{ route('admin.faq-responses.update', $item) }}" class="p-10 space-y-8">
                @csrf
                @method('PUT')

                <div class="grid gap-6 lg:grid-cols-2">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Inquiry</label>
                        <textarea name="inquiry" rows="6" required class="w-full rounded-2xl bg-white/80 border border-slate-900/10 px-6 py-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all resize-y">{{ old('inquiry', $item->inquiry) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Response</label>
                        <textarea name="response" rows="6" required class="w-full rounded-2xl bg-white/80 border border-slate-900/10 px-6 py-4 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500/40 focus:ring-blue-500/15 transition-all resize-y">{{ old('response', $item->response) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('admin.faq-responses.index') }}" class="h-11 px-6 inline-flex items-center justify-center rounded-2xl bg-white/80 text-slate-700 ring-1 ring-slate-900/10 hover:bg-white hover:text-slate-900 transition text-sm font-bold shadow-sm">
                        Cancel
                    </a>
                    <button type="submit" class="h-11 px-7 rounded-2xl bg-blue-600 text-white shadow-lg shadow-blue-600/20 hover:bg-blue-500 transition text-sm font-bold">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>

