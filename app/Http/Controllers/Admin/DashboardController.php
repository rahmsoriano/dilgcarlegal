<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AiRequest;
use App\Models\Conversation;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $since = now()->subDay();

        $stats = [
            'users_total' => User::count(),
            'conversations_total' => Conversation::count(),
            'requests_24h' => AiRequest::where('created_at', '>=', $since)->count(),
            'errors_24h' => AiRequest::where('created_at', '>=', $since)->where('status', 'error')->count(),
            'avg_duration_ms_24h' => (int) round((float) AiRequest::where('created_at', '>=', $since)->avg('duration_ms')),
            'tokens_24h' => (int) AiRequest::where('created_at', '>=', $since)->sum('total_tokens'),
        ];

        return view('admin.dashboard', [
            'stats' => $stats,
        ]);
    }
}
