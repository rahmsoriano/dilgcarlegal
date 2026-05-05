<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;
use Throwable;

class UserManagementController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'role' => (string) $request->query('role', ''),
            'status' => (string) $request->query('status', ''),
            'verification' => (string) $request->query('verification', ''),
        ];

        $users = User::query()
            ->when($filters['search'] !== '', function ($query) use ($filters) {
                $query->where(function ($subQuery) use ($filters) {
                    $subQuery
                        ->where('name', 'like', '%'.$filters['search'].'%')
                        ->orWhere('first_name', 'like', '%'.$filters['search'].'%')
                        ->orWhere('last_name', 'like', '%'.$filters['search'].'%')
                        ->orWhere('email', 'like', '%'.$filters['search'].'%');
                });
            })
            ->when($filters['role'] !== '', fn ($query) => $query->where('role', $filters['role']))
            ->when($filters['status'] !== '', fn ($query) => $query->where('status', $filters['status']))
            ->when($filters['verification'] === 'verified', fn ($query) => $query->whereNotNull('email_verified_at'))
            ->when($filters['verification'] === 'not_verified', fn ($query) => $query->whereNull('email_verified_at'))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.users.index', compact('users', 'filters'));
    }

    public function create(): View
    {
        return view('admin.users.create', [
            'user' => new User([
                'role' => 'user',
                'status' => 'active',
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules(requirePassword: true));

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'birthday' => $validated['birthday'] ?? null,
            'email' => $validated['email'],
            'password' => $validated['password'],
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        try {
            $user->sendEmailVerificationNotification();
        } catch (Throwable $e) {
            Log::warning('Admin-created user verification send failed.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'message' => $e->getMessage(),
            ]);
        }

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User account created successfully.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate($this->rules(user: $user, requirePassword: false));
        $admin = $request->user();

        if ($admin->is($user) && $validated['role'] !== 'admin') {
            return back()
                ->withErrors(['role' => 'You cannot change your own role from admin.'])
                ->withInput();
        }

        if ($admin->is($user) && $validated['status'] !== 'active') {
            return back()
                ->withErrors(['status' => 'You cannot deactivate your own account.'])
                ->withInput();
        }

        $user->fill([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'birthday' => $validated['birthday'] ?? null,
            'email' => $validated['email'],
            'role' => $validated['role'],
            'status' => $validated['status'],
        ]);

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $emailChanged = $user->isDirty('email');

        if ($emailChanged) {
            $user->email_verified_at = null;
        }

        $user->save();

        if ($emailChanged) {
            try {
                $user->sendEmailVerificationNotification();
            } catch (Throwable $e) {
                Log::warning('Verification resend after admin email update failed.', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User account updated successfully.');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors(['delete' => 'You cannot delete your own account.']);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User account deleted successfully.');
    }

    public function toggleStatus(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->withErrors(['status' => 'You cannot deactivate your own account.']);
        }

        $user->update([
            'status' => $user->status === 'active' ? 'inactive' : 'active',
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('status', 'User status updated successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function rules(?User $user = null, bool $requirePassword = true): array
    {
        $passwordRules = $requirePassword
            ? ['required', 'confirmed', Password::defaults()]
            : ['nullable', 'confirmed', Password::defaults()];

        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'birthday' => ['nullable', 'date', 'before:today'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user?->id)],
            'password' => $passwordRules,
            'role' => ['required', Rule::in(['admin', 'staff', 'user'])],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ];
    }
}
