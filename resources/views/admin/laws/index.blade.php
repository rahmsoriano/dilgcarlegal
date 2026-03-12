<x-admin-layout>
    <div class="space-y-8">
        <!-- Header Actions -->
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-3xl font-bold text-white tracking-tight">Law Knowledge Base</h2>
                <p class="mt-2 text-slate-400">Manage and search through uploaded legal documents and statutes.</p>
            </div>
            <a href="{{ route('admin.laws.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-500 transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="mr-2 h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Upload New Law
            </a>
        </div>

        <!-- Filters -->
        <div class="rounded-[2rem] bg-white/[0.02] backdrop-blur-xl p-6 ring-1 ring-white/10 shadow-xl">
            <form action="{{ route('admin.laws.index') }}" method="GET" class="grid grid-cols-1 gap-6 md:grid-cols-4">
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Search</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by title, number, or content..." 
                            class="w-full rounded-2xl bg-white/[0.03] border-white/10 px-12 py-3.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/50 focus:ring-blue-500/20 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="absolute left-4 top-1/2 -translate-y-1/2 h-5 w-5 text-slate-500">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Year</label>
                    <select name="year" class="w-full rounded-2xl bg-white/[0.03] border-white/10 px-4 py-3.5 text-sm text-white focus:border-blue-500/50 focus:ring-blue-500/20 transition-all">
                        <option value="">All Years</option>
                        @foreach($years as $year)
                            <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <div class="flex-1">
                        <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Category</label>
                        <select name="category" class="w-full rounded-2xl bg-white/[0.03] border-white/10 px-4 py-3.5 text-sm text-white focus:border-blue-500/50 focus:ring-blue-500/20 transition-all">
                            <option value="">All Categories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="h-[52px] w-[52px] flex items-center justify-center rounded-2xl bg-white/[0.05] text-white ring-1 ring-white/10 hover:bg-white/[0.1] transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3c2.755 0 5.455.232 8.083.678.533.09.917.556.917 1.096v1.044a2.25 2.25 0 01-.659 1.591l-5.432 5.432a2.25 2.25 0 00-.659 1.591v2.927a2.25 2.25 0 01-1.244 2.013L9.75 21v-6.568a2.25 2.25 0 00-.659-1.591L3.659 7.409A2.25 2.25 0 013 5.818V4.774c0-.54.384-1.006.917-1.096A48.32 48.32 0 0112 3z" />
                        </svg>
                    </button>
                </div>
            </form>
        </div>

        @if(session('success'))
            <div class="rounded-2xl bg-emerald-500/10 p-4 ring-1 ring-emerald-500/20 text-emerald-400 text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <!-- Laws Table -->
        <div class="overflow-hidden rounded-[2rem] bg-white/[0.02] backdrop-blur-xl ring-1 ring-white/10 shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/5 bg-white/[0.01]">
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Law Info</th>
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Category & Year</th>
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Status</th>
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($laws as $law)
                            <tr class="group hover:bg-white/[0.02] transition-colors">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-500/10 text-blue-400 ring-1 ring-blue-500/20">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-6 w-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <div class="text-sm font-bold text-white truncate">{{ $law->title }}</div>
                                            <div class="mt-1 text-xs font-medium text-slate-500">{{ $law->law_number }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex flex-col gap-1.5">
                                        <span class="inline-flex w-fit items-center rounded-lg bg-indigo-500/10 px-2 py-1 text-[10px] font-bold uppercase tracking-widest text-indigo-400 ring-1 ring-indigo-500/20">
                                            {{ $law->category ?? 'General' }}
                                        </span>
                                        <span class="text-xs font-medium text-slate-500">Year {{ $law->year }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-2">
                                        <div class="h-1.5 w-1.5 rounded-full {{ $law->content_text ? 'bg-emerald-500' : 'bg-rose-500' }}"></div>
                                        <span class="text-xs font-medium {{ $law->content_text ? 'text-emerald-400' : 'text-rose-400' }}">
                                            {{ $law->content_text ? 'Indexed' : 'Text Missing' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ Storage::url($law->file_path) }}" target="_blank" class="p-2 rounded-xl bg-white/[0.05] text-slate-400 hover:text-white hover:bg-white/[0.1] transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.laws.edit', $law) }}" class="p-2 rounded-xl bg-white/[0.05] text-slate-400 hover:text-blue-400 hover:bg-blue-500/10 transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.laws.destroy', $law) }}" method="POST" onsubmit="return confirm('Delete this law?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl bg-white/[0.05] text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition-all">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-1.806A2.25 2.25 0 0013.813 1.5h-3.626a2.25 2.25 0 00-2.25 2.25V3m7.5 0H9" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="flex h-20 w-20 items-center justify-center rounded-[2rem] bg-white/[0.02] text-slate-600 ring-1 ring-white/5 mb-6">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-10 w-10">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-xl font-bold text-white tracking-tight">No laws found</h3>
                                        <p class="mt-2 text-slate-500">Start building your knowledge base by uploading legal documents.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($laws->hasPages())
                <div class="border-t border-white/5 bg-white/[0.01] px-8 py-5">
                    {{ $laws->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
