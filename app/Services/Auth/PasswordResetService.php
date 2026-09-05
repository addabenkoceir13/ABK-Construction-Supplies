<?php

namespace App\Services\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class PasswordResetService
{
    const OTP_EXPIRY_MINUTES = 10;
    const TOKEN_EXPIRY_MINUTES = 15;
    const MAX_VERIFICATION_ATTEMPTS = 5;

    /**
     * Find user by channel and identifier.
     *
     * @param string $channel ('email' or 'sms')
     * @param string $identifier
     * @return User|null
     */
    public function findUser(string $channel, string $identifier): ?User
    {
        $identifier = trim($identifier);

        if ($channel === 'email') {
            return User::where('email', $identifier)->first();
        }

        // SMS / Phone search: normalize phone numbers
        $cleanedPhone = preg_replace('/[^0-9]/', '', $identifier);

        return User::where('phone', $identifier)
            ->orWhere('phone', $cleanedPhone)
            ->orWhereRaw("REPLACE(REPLACE(REPLACE(phone, ' ', ''), '-', ''), '+', '') = ?", [$cleanedPhone])
            ->first();
    }

    /**
     * Request a password reset code.
     *
     * @param string $channel
     * @param string $identifier
     * @return array
     */
    public function requestReset(string $channel, string $identifier): array
    {
        $user = $this->findUser($channel, $identifier);

        if (!$user) {
            return [
                'success' => false,
                'message' => $channel === 'email' 
                    ? __('لم يتم العثور على حساب مرتبط بهذا البريد الإلكتروني') 
                    : __('لم يتم العثور على حساب مرتبط برقم الهاتف هذا'),
            ];
        }

        // Generate 6-digit numeric OTP
        $otp = random_int(100000, 999999);
        $cacheKey = $this->getOtpCacheKey($user->id);

        $payload = [
            'user_id' => $user->id,
            'channel' => $channel,
            'identifier' => $identifier,
            'otp' => (string) $otp,
            'attempts' => 0,
            'created_at' => now()->timestamp,
            'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES)->timestamp,
        ];

        Cache::put($cacheKey, $payload, now()->addMinutes(self::OTP_EXPIRY_MINUTES));

        // Dispatch or Log OTP notification
        if ($channel === 'email') {
            Log::info("Password Reset OTP for Email [{$user->email}]: {$otp}");
        } else {
            Log::info("Password Reset SMS OTP for Phone [{$user->phone}]: {$otp}");
        }

        $maskedIdentifier = $this->maskIdentifier($channel, $channel === 'email' ? $user->email : $user->phone);

        return [
            'success' => true,
            'message' => $channel === 'email'
                ? __('تم إرسال رمز التحقق إلى بريدك الإلكتروني')
                : __('تم إرسال رمز التحقق في رسالة نصية SMS إلى هاتفك'),
            'channel' => $channel,
            'masked_identifier' => $maskedIdentifier,
            'identifier' => $identifier,
            'expires_in_seconds' => self::OTP_EXPIRY_MINUTES * 60,
            'demo_otp' => (app()->environment('local', 'testing') || config('app.debug')) ? (string) $otp : null,
        ];
    }

    /**
     * Verify the 6-digit OTP code.
     *
     * @param string $channel
     * @param string $identifier
     * @param string $otp
     * @return array
     */
    public function verifyOtp(string $channel, string $identifier, string $otp): array
    {
        $user = $this->findUser($channel, $identifier);

        if (!$user) {
            return [
                'success' => false,
                'message' => __('المستخدم غير موجود'),
            ];
        }

        $cacheKey = $this->getOtpCacheKey($user->id);
        $payload = Cache::get($cacheKey);

        if (!$payload) {
            return [
                'success' => false,
                'message' => __('انتهت صلاحية رمز التحقق، يرجى طلب رمز جديد'),
            ];
        }

        // Increment and verify attempt count
        $payload['attempts'] = ($payload['attempts'] ?? 0) + 1;
        Cache::put($cacheKey, $payload, now()->addMinutes(self::OTP_EXPIRY_MINUTES));

        if ($payload['attempts'] > self::MAX_VERIFICATION_ATTEMPTS) {
            Cache::forget($cacheKey);
            return [
                'success' => false,
                'message' => __('تم تجاوز الحد الأقصى للمحاولات. يرجى طلب رمز جديد'),
            ];
        }

        if (trim($payload['otp']) !== trim($otp)) {
            $remaining = self::MAX_VERIFICATION_ATTEMPTS - $payload['attempts'];
            return [
                'success' => false,
                'message' => __('رمز التحقق غير صحيح. المحاولات المتبقية: :remaining', ['remaining' => $remaining]),
            ];
        }

        // Generate single-use reset authorization token
        $resetToken = Str::random(64);
        $tokenCacheKey = $this->getTokenCacheKey($user->id);

        Cache::put($tokenCacheKey, [
            'user_id' => $user->id,
            'reset_token' => $resetToken,
            'created_at' => now()->timestamp,
        ], now()->addMinutes(self::TOKEN_EXPIRY_MINUTES));

        // Invalidate OTP so it cannot be reused
        Cache::forget($cacheKey);

        return [
            'success' => true,
            'message' => __('تم التحقق من الرمز بنجاح'),
            'reset_token' => $resetToken,
            'channel' => $channel,
            'identifier' => $identifier,
        ];
    }

    /**
     * Reset the user's password using the verified reset token.
     *
     * @param string $channel
     * @param string $identifier
     * @param string $resetToken
     * @param string $newPassword
     * @return array
     */
    public function resetPassword(string $channel, string $identifier, string $resetToken, string $newPassword): array
    {
        $user = $this->findUser($channel, $identifier);

        if (!$user) {
            return [
                'success' => false,
                'message' => __('المستخدم غير موجود'),
            ];
        }

        $tokenCacheKey = $this->getTokenCacheKey($user->id);
        $cachedTokenData = Cache::get($tokenCacheKey);

        if (!$cachedTokenData || ($cachedTokenData['reset_token'] ?? '') !== $resetToken) {
            return [
                'success' => false,
                'message' => __('انتهت صلاحية جلسة تغيير كلمة المرور، يرجى إعادة المحاولة من البداية'),
            ];
        }

        // Update password
        $user->password = Hash::make($newPassword);
        $user->setRememberToken(Str::random(60));
        $user->save();

        // Invalidate token
        Cache::forget($tokenCacheKey);

        Log::info("Password successfully reset for User ID [{$user->id}], via Channel [{$channel}]");

        return [
            'success' => true,
            'message' => __('تم تحديث كلمة المرور بنجاح! يمكنك الآن تسجيل الدخول بكلمة المرور الجديدة'),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ];
    }

    /**
     * Mask identifier for privacy.
     *
     * @param string $channel
     * @param string|null $value
     * @return string
     */
    protected function maskIdentifier(string $channel, ?string $value): string
    {
        if (empty($value)) {
            return '***';
        }

        if ($channel === 'email') {
            $parts = explode('@', $value);
            if (count($parts) === 2) {
                $name = $parts[0];
                $domain = $parts[1];
                $visibleStart = substr($name, 0, 1);
                $visibleEnd = strlen($name) > 2 ? substr($name, -1) : '';
                return $visibleStart . '***' . $visibleEnd . '@' . $domain;
            }
            return substr($value, 0, 2) . '***';
        }

        // Phone masking (e.g., 0655443322 -> 06*****322)
        $len = strlen($value);
        if ($len > 5) {
            return substr($value, 0, 2) . str_repeat('*', $len - 5) . substr($value, -3);
        }

        return substr($value, 0, 2) . '***';
    }

    protected function getOtpCacheKey(int $userId): string
    {
        return "pwd_reset_otp_{$userId}";
    }

    protected function getTokenCacheKey(int $userId): string
    {
        return "pwd_reset_token_{$userId}";
    }
}
