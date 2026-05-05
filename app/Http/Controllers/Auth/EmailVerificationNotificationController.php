<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        $status = 'verification-link-sent';

        try {
            $request->user()->sendEmailVerificationNotification();
        } catch (Throwable $e) {
            Log::warning('Email verification resend failed.', [
                'user_id' => $request->user()->id,
                'email' => $request->user()->email,
                'message' => $e->getMessage(),
            ]);

            $status = 'verification-link-fallback';
        }

        return back()->with('status', $status);
    }
}
