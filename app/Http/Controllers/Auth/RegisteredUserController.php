<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\ReviewerAccessRequestedNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

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
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'department' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'role' => ['nullable', 'string', 'in:innovator,reviewer'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $role = $request->input('role', 'innovator');
        $isReviewer = $role === 'reviewer';

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'department' => $request->department,
            'phone' => $request->phone,
            'reviewer_status' => $isReviewer ? 'pending' : null,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        if ($isReviewer) {
            // Send email notification to all portal administrators
            $admins = User::role('admin')->get();
            if ($admins->isNotEmpty()) {
                Notification::send($admins, new ReviewerAccessRequestedNotification($user));
            }

            // Zero access: user is NOT authenticated into the portal
            return redirect()->route('login')->with(
                'status',
                'Your registration as a Reviewer has been submitted for administrator approval. You will receive an email once approved.'
            );
        }

        // Standard staff innovator: assign role and log in immediately
        Role::findOrCreate('innovator');
        $user->assignRole('innovator');
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
