<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    public function index()
    {
        $users = User::query()
            ->withCount(['conversations', 'aiRequests'])
            ->orderByDesc('id')
            ->paginate(25);

        return view('admin.users.index', [
            'users' => $users,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'is_admin' => ['required', 'boolean'],
        ]);

        $user->update([
            'is_admin' => (bool) $validated['is_admin'],
        ]);

        return back();
    }
}
