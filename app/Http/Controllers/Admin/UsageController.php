<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UsageController extends Controller
{
    public function index()
    {
        $since = now()->subDays(14);

        $daily = AiRequest::query()
            ->selectRaw('DATE(created_at) as day')
            ->selectRaw('COUNT(*) as requests')
            ->selectRaw('SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as errors', ['error'])
            ->selectRaw('SUM(COALESCE(total_tokens, 0)) as tokens')
            ->where('created_at', '>=', $since)
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('day')
            ->get();

        $topUsers = User::query()
            ->withCount(['aiRequests' => function ($query) {
                $query->where('created_at', '>=', now()->subDays(7));
            }])
            ->orderByDesc('ai_requests_count')
            ->limit(10)
            ->get(['id', 'name', 'email']);

        $recentErrors = AiRequest::query()
            ->where('status', 'error')
            ->orderByDesc('id')
            ->limit(50)
            ->get();

        return view('admin.usage.index', [
            'daily' => $daily,
            'topUsers' => $topUsers,
            'recentErrors' => $recentErrors,
        ]);
    }
}
