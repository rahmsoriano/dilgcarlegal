<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birthday' => ['required', 'date', 'before:today'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/[A-Z]/',
                'regex:/[a-z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*#?&]/',
                'confirmed',
            ],
        ], [
            'password.min' => 'Password must be 8 characters or more and include uppercase, lowercase, number, and special character.',
            'password.regex' => 'Password must be 8 characters or more and include uppercase, lowercase, number, and special character.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('auth_mode', 'register');
        }

        $user = User::create([
            'name' => trim($request->first_name.' '.$request->last_name),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'birthday' => $request->birthday,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'user',
            'status' => 'active',
        ]);

        $status = 'verification-link-sent';

        try {
            $user->sendEmailVerificationNotification();
        } catch (Throwable $e) {
            Log::warning('Email verification send failed during registration.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'message' => $e->getMessage(),
            ]);

            $status = 'verification-link-fallback';
        }

        Auth::login($user);

        return redirect()->route('verification.notice')->with('status', $status);
    }
}
