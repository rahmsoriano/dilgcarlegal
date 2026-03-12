<x-admin-layout>
    <x-slot name="title">Add DILG Opinion</x-slot>
    <x-slot name="subtitle">Add a new item to the corpus for retrieval and citations.</x-slot>

    <div class="max-w-4xl">
        <div class="rounded-3xl bg-white/5 p-6 ring-1 ring-white/10">
            <form method="POST" action="{{ route('admin.opinions.store') }}" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-white/80">Title</label>
                    <input type="text" name="title" value="{{ old('title') }}" class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-white/40 focus:border-white/20 focus:ring-0">
                    @error('title')<div class="mt-2 text-sm text-rose-200">{{ $message }}</div>@enderror
                </div>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-white/80">Reference No.</label>
                        <input type="text" name="reference_no" value="{{ old('reference_no') }}" class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-white/40 focus:border-white/20 focus:ring-0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/80">Date</label>
                        <input type="date" name="opinion_date" value="{{ old('opinion_date') }}" class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white focus:border-white/20 focus:ring-0">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-white/80">Tags</label>
                        <input type="text" name="tags" value="{{ old('tags') }}" class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-white/40 focus:border-white/20 focus:ring-0" placeholder="comma,separated">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-white/80">Slug</label>
                    <input type="text" name="slug" value="{{ old('slug') }}" class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-white/40 focus:border-white/20 focus:ring-0" placeholder="auto-generated if empty">
                    @error('slug')<div class="mt-2 text-sm text-rose-200">{{ $message }}</div>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-white/80">Body</label>
                    <textarea name="body" rows="16" class="mt-2 block w-full rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-white/40 focus:border-white/20 focus:ring-0">{{ old('body') }}</textarea>
                    @error('body')<div class="mt-2 text-sm text-rose-200">{{ $message }}</div>@enderror
                </div>
                <div class="flex items-center justify-end gap-2">
                    <a href="{{ route('admin.opinions.index') }}" class="rounded-2xl bg-white/5 px-4 py-2 text-sm font-semibold text-white/80 ring-1 ring-white/10 hover:bg-white/10">
                        Cancel
                    </a>
                    <button type="submit" class="rounded-2xl bg-white/10 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/10 hover:bg-white/15">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-admin-layout>
