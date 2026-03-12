<x-admin-layout>
    <x-slot name="title">Usage</x-slot>
    <x-slot name="subtitle">Request volume, tokens, and errors.</x-slot>

    <div class="space-y-6">
        <div class="rounded-3xl bg-white/5 p-5 ring-1 ring-white/10">
            <div class="text-sm font-semibold">Daily (last 14 days)</div>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-white/60">
                            <th class="py-3 pr-4 font-medium">Day</th>
                            <th class="py-3 pr-4 font-medium">Requests</th>
                            <th class="py-3 pr-4 font-medium">Errors</th>
                            <th class="py-3 pr-4 font-medium">Tokens</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach ($daily as $row)
                            <tr>
                                <td class="py-3 pr-4 text-white/80">{{ $row->day }}</td>
                                <td class="py-3 pr-4">{{ number_format($row->requests) }}</td>
                                <td class="py-3 pr-4">{{ number_format($row->errors) }}</td>
                                <td class="py-3 pr-4">{{ number_format($row->tokens) }}</td>
                            </tr>
                        @endforeach
                        @if ($daily->count() === 0)
                            <tr>
                                <td class="py-3 text-white/60" colspan="4">No data yet.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-3xl bg-white/5 p-5 ring-1 ring-white/10">
            <div class="text-sm font-semibold">Top users (last 7 days)</div>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-white/60">
                            <th class="py-3 pr-4 font-medium">User</th>
                            <th class="py-3 pr-4 font-medium">Email</th>
                            <th class="py-3 pr-4 font-medium">Requests</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach ($topUsers as $user)
                            <tr>
                                <td class="py-3 pr-4">{{ $user->name }}</td>
                                <td class="py-3 pr-4 text-white/80">{{ $user->email }}</td>
                                <td class="py-3 pr-4">{{ number_format($user->ai_requests_count) }}</td>
                            </tr>
                        @endforeach
                        @if ($topUsers->count() === 0)
                            <tr>
                                <td class="py-3 text-white/60" colspan="3">No data yet.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-3xl bg-white/5 p-5 ring-1 ring-white/10">
            <div class="text-sm font-semibold">Recent errors</div>
            <div class="mt-4 overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead>
                        <tr class="text-left text-white/60">
                            <th class="py-3 pr-4 font-medium">Time</th>
                            <th class="py-3 pr-4 font-medium">User</th>
                            <th class="py-3 pr-4 font-medium">HTTP</th>
                            <th class="py-3 pr-4 font-medium">Type</th>
                            <th class="py-3 pr-4 font-medium">Code</th>
                            <th class="py-3 pr-4 font-medium">Model</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/10">
                        @foreach ($recentErrors as $err)
                            <tr>
                                <td class="py-3 pr-4 text-white/80">{{ $err->created_at->format('Y-m-d H:i') }}</td>
                                <td class="py-3 pr-4 text-white/80">#{{ $err->user_id }}</td>
                                <td class="py-3 pr-4">{{ $err->http_status }}</td>
                                <td class="py-3 pr-4">{{ $err->error_type }}</td>
                                <td class="py-3 pr-4">{{ $err->error_code }}</td>
                                <td class="py-3 pr-4 text-white/80">{{ $err->model }}</td>
                            </tr>
                        @endforeach
                        @if ($recentErrors->count() === 0)
                            <tr>
                                <td class="py-3 text-white/60" colspan="6">No errors.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-admin-layout>
