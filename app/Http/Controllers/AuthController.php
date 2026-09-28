<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function show()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $throttleKey = 'login:' . $request->ip() . '|' . strtolower($request->input('email'));

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'email' => "هەوڵی زۆر دراوە. تکایە دوای {$seconds} چرکە دووبارە هەوڵ بدەرەوە.",
            ]);
        }

        if (Auth::attempt(array_merge($credentials, ['active' => true]), $request->boolean('remember'))) {
            RateLimiter::clear($throttleKey);
            $request->session()->regenerate();

            // Audit log
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'user_login',
                'model_type' => Auth::user()::class,
                'model_id' => Auth::id(),
                'performed_at' => now(),
                'details' => [
                    'ip' => $request->ip(),
                    'user_agent' => substr($request->userAgent() ?? '', 0, 255),
                ],
            ]);

            return redirect()->intended('/dashboard');
        }

        RateLimiter::hit($throttleKey, 60);

        return back()->withErrors([
            'email' => 'ئیمەیڵ یان وشەی نهێنی هەڵەیە، یان هەژمارەکەت ناچالاک کراوە.',
        ])->withInput($request->only('email', 'remember'));
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'user_logout',
                'model_type' => Auth::user()::class,
                'model_id' => Auth::id(),
                'performed_at' => now(),
                'details' => [
                    'ip' => $request->ip(),
                ],
            ]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
