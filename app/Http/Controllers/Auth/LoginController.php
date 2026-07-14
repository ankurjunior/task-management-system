<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }



    /**
     * Logout from system
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('login');
    }



    /**
     * Login Function 
     * Using Laravel Auth Facade
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $credentials = [
            'username' => $request->username,
            'password' => $request->password,
            'is_active' => 1
        ];

        if (Auth::attempt($credentials, $request->remember)) {
            $request->session()->regenerate();

            auth()->user()->update([
                'last_login_at' => now()
            ]);

            return redirect()->route('dashboard');
        }

        return back()
            ->withInput()
            ->withErrors([
                'username' => 'Invalid credentials'
            ]);
    }
}
