<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use App\Mail\NewLoginAlertMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class LoginBasic extends Controller
{
    /**
     * Display the login page.
     */
    public function index(Request $request)
    {
        $lockoutSeconds = 0;

        // Check if session or IP currently has an active lockout
        if (session()->has('lockout_until')) {
            $remaining = session('lockout_until') - now()->timestamp;
            if ($remaining > 0) {
                $lockoutSeconds = $remaining;
            } else {
                session()->forget(['lockout_until', 'is_locked_out']);
            }
        }

        return view('content.authentications.auth-login-basic', compact('lockoutSeconds'));
    }

    /**
     * Handle login authentication with rate limiting and security email alert.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $throttleKey = $this->throttleKey($request);
        $maxAttempts = $this->maxAttempts();
        $decaySeconds = $this->decaySeconds();

        // 1. Check if user is currently locked out
        if (RateLimiter::tooManyAttempts($throttleKey, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            session(['lockout_until' => now()->timestamp + $seconds, 'is_locked_out' => true]);

            return redirect("/auth/login-basic")
                ->withInput($request->only('email'))
                ->with('lockout', true)
                ->with('lockout_seconds', $seconds)
                ->with('error', __('تم حظر تسجيل الدخول مؤقتاً لحماية حسابك لمدة :seconds ثانية بسبب تجاوز عدد المحاولات (:max محاولات).', [
                    'seconds' => $seconds,
                    'max' => $maxAttempts,
                ]));
        }

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        // 2. Attempt Authentication
        if (Auth::attempt($credentials, $remember)) {
            // Clear rate limiting counter upon success
            RateLimiter::clear($throttleKey);
            session()->forget(['lockout_until', 'is_locked_out']);
            if ($request->hasSession()) {
                $request->session()->regenerate();
            }

            $user = Auth::user();

            // 3. Send Security Login Alert Email
            if ($user && $user->email) {
                try {
                    Mail::to($user->email)->send(new NewLoginAlertMail($user, [
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent() ?? 'Web Browser',
                        'time' => now()->format('Y-m-d H:i:s'),
                    ]));
                    Log::info("Security Login Alert email successfully dispatched to {$user->email}");
                } catch (\Exception $e) {
                    Log::warning("Could not send security login alert email to {$user->email}: " . $e->getMessage());
                }
            }

            return redirect()->intended('/')
                        ->withSuccess(__('Signed in'));
        }

        // 3. Failed Attempt - Increment RateLimiter
        RateLimiter::hit($throttleKey, $decaySeconds);
        $attempts = RateLimiter::attempts($throttleKey);
        $remainingAttempts = max(0, $maxAttempts - $attempts);

        // If threshold reached, apply lockout
        if ($remainingAttempts === 0) {
            $seconds = RateLimiter::availableIn($throttleKey);
            if ($seconds <= 0) {
                $seconds = $decaySeconds;
            }
            session(['lockout_until' => now()->timestamp + $seconds, 'is_locked_out' => true]);

            return redirect("/auth/login-basic")
                ->withInput($request->only('email'))
                ->with('lockout', true)
                ->with('lockout_seconds', $seconds)
                ->with('error', __('تم استنفاد محاولات تسجيل الدخول (:max محاولات). تم حظر المحاولات مؤقتاً لمدة :seconds ثانية.', [
                    'seconds' => $seconds,
                    'max' => $maxAttempts,
                ]));
        }

        // Still has remaining attempts
        return redirect("/auth/login-basic")
                  ->withInput($request->only('email'))
                  ->with('remaining_attempts', $remainingAttempts)
                  ->with('error', __('بيانات تسجيل الدخول غير صالحة. لديك :remaining محاولة متبقية قبل الحظر المؤقت.', [
                      'remaining' => $remainingAttempts,
                  ]));
    }

    /**
     * Get the rate limiting throttle key for the given request.
     */
    protected function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower($request->input('email')) . '|' . $request->ip());
    }

    /**
     * Get maximum login attempts before lockout.
     */
    protected function maxAttempts(): int
    {
        return (int) config('auth.login_throttle.max_attempts', env('LOGIN_MAX_ATTEMPTS', 3));
    }

    /**
     * Get lockout duration in seconds.
     */
    protected function decaySeconds(): int
    {
        return (int) config('auth.login_throttle.decay_seconds', env('LOGIN_DECAY_SECONDS', 90));
    }
}
