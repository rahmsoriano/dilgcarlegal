<x-admin-layout>
    <div class="space-y-8">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-3xl font-bold tracking-tight text-slate-900">Edit AMICUS Section</h2>
                <p class="mt-2 text-slate-600">Update the section title, topic label, and legal text without affecting the rest of the knowledge base.</p>
            </div>
            <a href="{{ route('admin.amicus.index') }}" class="inline-flex items-center justify-center rounded-2xl bg-white/80 px-6 py-3 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-slate-900/10 transition-all hover:bg-white hover:text-slate-900">
                Back to Amicus
            </a>
        </div>

        <div class="rounded-[2rem] bg-white/80 p-6 shadow-[0_24px_70px_rgba(15,23,42,0.08)] ring-1 ring-slate-900/5 backdrop-blur-xl">
            <form method="POST" action="{{ route('admin.amicus.update', $amicus) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Section Title</label>
                    <input type="text" name="section_title" value="{{ old('section_title', $amicus->section_title) }}" required class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-6 py-4 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">
                    @error('section_title')
                        <div class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold uppercase tracking-widest text-slate-500">Section Content</label>
                    <textarea name="section_content" rows="14" required class="w-full rounded-2xl border border-slate-900/10 bg-white/80 px-6 py-4 text-sm text-slate-900 transition-all focus:border-blue-500/40 focus:ring-blue-500/15">{{ old('section_content', $amicus->section_content) }}</textarea>
                    @error('section_content')
                        <div class="mt-2 text-sm font-semibold text-rose-600">{{ $message }}</div>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3">
                    <a href="{{ route('admin.amicus.index') }}" class="h-11 rounded-2xl bg-white/80 px-6 py-3 text-sm font-bold text-slate-700 shadow-sm ring-1 ring-slate-900/10 transition hover:bg-white hover:text-slate-900">
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
