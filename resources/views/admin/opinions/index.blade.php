<x-admin-layout>
    <div class="space-y-8">
        <div class="flex flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-3xl font-bold text-white tracking-tight">Gallery Library</h2>
                <p class="mt-2 text-slate-400">Manage legal opinions used as the chatbot knowledge base.</p>
            </div>
            <a href="{{ route('admin.opinions.create') }}" class="inline-flex items-center justify-center rounded-2xl bg-blue-600 px-6 py-3.5 text-sm font-bold text-white shadow-lg shadow-blue-600/20 hover:bg-blue-500 transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="mr-2 h-5 w-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Add Legal Opinion
            </a>
        </div>

        <div class="rounded-[2rem] bg-white/[0.02] backdrop-blur-xl p-6 ring-1 ring-white/10 shadow-xl">
            <form action="{{ route('admin.opinions.index') }}" method="GET" class="flex flex-col gap-4 md:flex-row md:items-end">
                <div class="flex-1">
                    <label class="block text-xs font-bold uppercase tracking-widest text-slate-500 mb-2">Search</label>
                    <input type="text" name="q" value="{{ $q }}" placeholder="Search by title, opinion number, year, or context..."
                        class="w-full rounded-2xl bg-white/[0.03] border-white/10 px-6 py-3.5 text-sm text-white placeholder:text-slate-500 focus:border-blue-500/50 focus:ring-blue-500/20 transition-all">
                </div>
                <button type="submit" class="h-[52px] px-8 flex items-center justify-center rounded-2xl bg-white/[0.05] text-white ring-1 ring-white/10 hover:bg-white/[0.1] transition-all text-sm font-bold">
                    Search
                </button>
            </form>
        </div>

        <div class="overflow-hidden rounded-[2rem] bg-white/[0.02] backdrop-blur-xl ring-1 ring-white/10 shadow-2xl">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-white/5 bg-white/[0.01]">
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Title</th>
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Opinion Number</th>
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500">Date</th>
                            <th class="px-8 py-5 text-[10px] font-bold uppercase tracking-widest text-slate-500 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse ($opinions as $opinion)
                            <tr class="group hover:bg-white/[0.02] transition-colors">
                                <td class="px-8 py-6">
                                    <div class="text-sm font-bold text-white">{{ $opinion->title }}</div>
                                    <div class="mt-1 text-xs font-medium text-slate-500">Updated {{ $opinion->updated_at->diffForHumans() }}</div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-sm font-semibold text-slate-200">{{ $opinion->opinion_number }}</div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-sm font-semibold text-slate-200">{{ optional($opinion->date)->format('Y-m-d') }}</div>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="{{ route('admin.opinions.show', $opinion) }}" class="p-2 rounded-xl bg-white/[0.05] text-slate-400 hover:text-white hover:bg-white/[0.1] transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.opinions.edit', $opinion) }}" class="p-2 rounded-xl bg-white/[0.05] text-slate-400 hover:text-blue-400 hover:bg-blue-500/10 transition-all">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-5 w-5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.opinions.destroy', $opinion) }}" onsubmit="return confirm('Delete this legal opinion?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl bg-rose-500/5 text-rose-400 ring-1 ring-rose-500/20 hover:bg-rose-500 hover:text-white transition-all">
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
                                <td class="px-8 py-10 text-center text-slate-500" colspan="4">No legal opinions found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($opinions->hasPages())
                <div class="border-t border-white/5 bg-white/[0.01] px-8 py-5">
                    {{ $opinions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-admin-layout>
