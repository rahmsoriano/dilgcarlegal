<x-admin-layout>
    <div class="max-w-4xl mx-auto space-y-8">
        <!-- Header -->
        <div class="flex items-center gap-6">
            <a href="{{ route('admin.laws.index') }}" class="flex h-12 w-12 items-center justify-center rounded-2xl bg-white/[0.03] text-slate-400 ring-1 ring-white/10 hover:bg-white/[0.05] hover:text-white transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                </svg>
            </a>
            <div>
                <h2 class="text-3xl font-bold text-white tracking-tight">Edit Law</h2>
                <p class="mt-1 text-slate-400">Update law metadata or replace the document file.</p>
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

        <!-- Form Card -->
        <div class="rounded-[2.5rem] bg-white/[0.02] backdrop-blur-xl p-10 ring-1 ring-white/10 shadow-2xl">
            <form action="{{ route('admin.laws.update', $law) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-1 gap-8 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Law Title</label>
                        <input type="text" name="title" value="{{ old('title', $law->title) }}" required
                            class="w-full rounded-2xl bg-white/[0.03] border-white/10 px-6 py-4 text-sm text-white focus:border-blue-500/50 focus:ring-blue-500/20 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Law Number</label>
                        <input type="text" name="law_number" value="{{ old('law_number', $law->law_number) }}" required
                            class="w-full rounded-2xl bg-white/[0.03] border-white/10 px-6 py-4 text-sm text-white focus:border-blue-500/50 focus:ring-blue-500/20 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Category</label>
                        <input type="text" name="category" value="{{ old('category', $law->category) }}"
                            class="w-full rounded-2xl bg-white/[0.03] border-white/10 px-6 py-4 text-sm text-white focus:border-blue-500/50 focus:ring-blue-500/20 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Year</label>
                        <input type="number" name="year" value="{{ old('year', $law->year) }}" required
                            class="w-full rounded-2xl bg-white/[0.03] border-white/10 px-6 py-4 text-sm text-white focus:border-blue-500/50 focus:ring-blue-500/20 transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Replace Document (Optional)</label>
                        <input type="file" name="file"
                            class="w-full text-sm text-slate-400 file:mr-4 file:py-4 file:px-8 file:rounded-2xl file:border-0 file:text-sm file:font-bold file:bg-white/[0.05] file:text-white file:hover:bg-white/[0.1] transition-all cursor-pointer">
                        <div class="mt-2 flex items-center gap-2">
                            <span class="text-[10px] text-slate-600 uppercase tracking-widest font-bold">Current file:</span>
                            <span class="text-[10px] text-blue-400 font-bold">{{ basename($law->file_path) }}</span>
                        </div>
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-3">Brief Description</label>
                        <textarea name="description" rows="4"
                            class="w-full rounded-2xl bg-white/[0.03] border-white/10 px-6 py-4 text-sm text-white focus:border-blue-500/50 focus:ring-blue-500/20 transition-all resize-none">{{ old('description', $law->description) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-4 pt-6 border-t border-white/5">
                    <a href="{{ route('admin.laws.index') }}" class="px-8 py-4 text-sm font-bold text-slate-400 hover:text-white transition-colors">Cancel</a>
                    <button type="submit" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-10 py-4 text-sm font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-500 transition-all duration-300">
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
