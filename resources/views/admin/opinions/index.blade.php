<x-admin-layout>
    <x-slot name="title">DILG Opinions</x-slot>
    <x-slot name="subtitle">Search and maintain your corpus.</x-slot>
    <x-slot name="actions">
        <a href="{{ route('admin.opinions.create') }}" class="rounded-2xl bg-white/10 px-4 py-2 text-sm font-semibold text-white ring-1 ring-white/10 hover:bg-white/15">
            Add
        </a>
    </x-slot>

    <div class="rounded-3xl bg-white/5 p-5 ring-1 ring-white/10">
        <form method="GET" class="flex flex-col gap-3 sm:flex-row">
            <input type="text" name="q" value="{{ $q }}" placeholder="Search opinions…" class="flex-1 rounded-2xl border border-white/10 bg-white/5 px-4 py-3 text-sm text-white placeholder-white/40 focus:border-white/20 focus:ring-0">
            <button type="submit" class="rounded-2xl bg-white/10 px-4 py-3 text-sm font-semibold text-white ring-1 ring-white/10 hover:bg-white/15">
                Search
            </button>
        </form>

        <div class="mt-6 overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead>
                    <tr class="text-left text-white/60">
                        <th class="py-3 pr-4 font-medium">Title</th>
                        <th class="py-3 pr-4 font-medium">Reference</th>
                        <th class="py-3 pr-4 font-medium">Date</th>
                        <th class="py-3 pr-4 font-medium">Tags</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/10">
                    @foreach ($opinions as $op)
                        <tr id="opinion-{{ $op->id }}">
                            <td class="py-3 pr-4">{{ $op->title }}</td>
                            <td class="py-3 pr-4 text-white/80">{{ $op->reference_no }}</td>
                            <td class="py-3 pr-4 text-white/80">{{ optional($op->opinion_date)->format('Y-m-d') }}</td>
                            <td class="py-3 pr-4 text-white/80">{{ $op->tags }}</td>
                        </tr>
                    @endforeach
                    @if ($opinions->count() === 0)
                        <tr>
                            <td class="py-3 text-white/60" colspan="4">No opinions</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <div class="mt-6 text-white/80">{{ $opinions->links() }}</div>
    </div>
</x-admin-layout>
