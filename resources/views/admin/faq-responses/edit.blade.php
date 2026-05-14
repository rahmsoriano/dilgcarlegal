<x-admin-layout>
    <div class="mx-auto max-w-5xl space-y-8">
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.faq-responses.index') }}" class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/70 text-slate-600 shadow-sm ring-1 ring-slate-900/10 transition-all duration-300 hover:bg-white hover:text-slate-900">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
                </svg>
            </a>
            <div>
                <div class="text-xs font-black uppercase tracking-[0.2em] text-slate-500">FAQ Response Manager</div>
                <h2 class="mt-2 text-3xl font-black tracking-tight text-slate-900">Edit Response</h2>
            </div>
        </div>

        <div class="overflow-hidden rounded-[2.5rem] bg-white/80 shadow-[0_24px_70px_rgba(15,23,42,0.08)] ring-1 ring-slate-900/5 backdrop-blur-xl">
            <form method="POST" action="{{ route('admin.faq-responses.update', $item) }}" class="space-y-8 p-10">
                @csrf
                @method('PUT')

                <div class="grid gap-6 lg:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Question</label>
                        <textarea name="inquiry" rows="6" required class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-6 py-4 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">{{ old('inquiry', $item->inquiry) }}</textarea>
                        @error('inquiry')
                            <div class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                    <div>
                        <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Response</label>
                        <textarea name="response" rows="6" required class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-6 py-4 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">{{ old('response', $item->response) }}</textarea>
                        @error('response')
                            <div class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Alternative Questions</label>
                    <textarea name="aliases" rows="4" class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-6 py-4 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">{{ old('aliases', $item->aliases) }}</textarea>
                    <div class="mt-2 text-xs font-medium text-slate-500">Optional. Add one alternate wording per line for better FAQ matching.</div>
                    @error('aliases')
                        <div class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('admin.faq-responses.index') }}" class="inline-flex h-11 items-center justify-center rounded-2xl bg-white/80 px-6 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-slate-900/10 transition hover:bg-white hover:text-slate-900">
                        Cancel
                    </a>
                    <button type="submit" class="h-11 rounded-2xl bg-blue-600 px-7 text-sm font-bold text-white shadow-lg shadow-blue-600/20 transition hover:bg-blue-500">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
