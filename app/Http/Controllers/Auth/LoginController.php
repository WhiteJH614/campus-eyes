<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Handle login form submission.
     */
    public function authenticate(Request $request)
    {
        // Validate the request
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Attempt to log in
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors([
                    'email' => 'The provided credentials do not match our records.',
                ])
                ->onlyInput('email');
        }

        // Regenerate session to prevent fixation
        $request->session()->regenerate();

        $user = Auth::user();

        // Role-based redirect
        switch ($user->role) {
            case 'Technician':
                return redirect()->to('/technician/dashboard');
            case 'Admin':
                return redirect()->to('/admin/dashboard');
            case 'Reporter':
            default:
                return redirect()->to('/reporter/dashboard');
        }
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        // Invalidate session & regenerate CSRF token
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Redirect back to home or login
        return redirect()->to('/');
    }
}
