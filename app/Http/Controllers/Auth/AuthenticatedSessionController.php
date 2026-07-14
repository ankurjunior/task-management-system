<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $request->user()->update([
            'last_login_at' => now(),
        ]);

        $this->recordLoginLog($request, 'login', 'success');

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $this->recordLoginLog($request, 'logout', 'success');

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function recordLoginLog(Request $request, string $eventType, string $status): void
    {
        $user = $request->user();

        DB::table('login_logs')->insert([
            'user_id' => $user?->id,
            'username' => $user?->username,
            'event_type' => $eventType,
            'status' => $status,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'failure_reason' => null,
            'logged_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
