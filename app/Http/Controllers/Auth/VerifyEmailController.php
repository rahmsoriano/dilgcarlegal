<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VerifyEmailController extends Controller
{
    /**
     * Mark the signed user's email address as verified and complete login when needed.
     */
    public function __invoke(Request $request): RedirectResponse
    {
        $user = User::query()->findOrFail((int) $request->route('id'));

        if (! hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            abort(403, 'This action is unauthorized.');
        }

        if (! $user->hasVerifiedEmail() && $user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        if ($user->status !== 'active') {
            return redirect()->route('login')->with('status', 'Your email has been verified. Your account is inactive. Please contact administrator.');
        }

        if (! $request->user() || (int) $request->user()->getKey() !== (int) $user->getKey()) {
            Auth::guard('web')->login($user);
            $request->session()->regenerate();
        }

        return redirect()->route(($user->role === 'admin' || $user->is_admin) ? 'admin.legal.ai' : 'chat.index', [
            'verified' => 1,
        ]);
    }
}
