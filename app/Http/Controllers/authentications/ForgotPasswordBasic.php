<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use App\Services\Auth\PasswordResetService;
use Illuminate\Http\Request;

class ForgotPasswordBasic extends Controller
{
    protected PasswordResetService $passwordResetService;

    public function __construct(PasswordResetService $passwordResetService)
    {
        $this->passwordResetService = $passwordResetService;
    }

    /**
     * Display the redesigned reset password view.
     */
    public function index()
    {
        return view('content.authentications.auth-forgot-password-basic');
    }

    /**
     * Request an OTP / reset verification code via Email or SMS.
     */
    public function sendCode(Request $request)
    {
        $request->validate([
            'channel' => 'required|in:email,sms',
            'identifier' => 'required|string|min:3|max:100',
        ], [
            'channel.required' => __('يرجى تحديد وسيلة الاستعادة'),
            'channel.in' => __('وسيلة الاستعادة المحددة غير صالحة'),
            'identifier.required' => __('يرجى إدخال البريد الإلكتروني أو رقم الهاتف'),
        ]);

        $channel = $request->input('channel');
        $identifier = $request->input('identifier');

        // Additional format validation
        if ($channel === 'email' && !filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => __('يرجى إدخال بريد إلكتروني صالح بالصيغة الصحيحة'),
                ], 422);
            }
            return back()->withErrors(['identifier' => __('يرجى إدخال بريد إلكتروني صالح')])->withInput();
        }

        $result = $this->passwordResetService->requestReset($channel, $identifier);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if (!$result['success']) {
            return back()->with('error', $result['message'])->withInput();
        }

        return back()->with('success', $result['message'])
                     ->with('step', 2)
                     ->with('channel', $channel)
                     ->with('identifier', $identifier)
                     ->with('masked_identifier', $result['masked_identifier'] ?? $identifier);
    }

    /**
     * Verify the 6-digit OTP code.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'channel' => 'required|in:email,sms',
            'identifier' => 'required|string',
            'otp' => 'required|digits:6',
        ], [
            'otp.required' => __('يرجى إدخال رمز التحقق المكون من 6 أرقام'),
            'otp.digits' => __('يجب أن يتكون رمز التحقق من 6 أرقام دقيقة'),
        ]);

        $result = $this->passwordResetService->verifyOtp(
            $request->input('channel'),
            $request->input('identifier'),
            $request->input('otp')
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if (!$result['success']) {
            return back()->with('error', $result['message'])->withInput();
        }

        return back()->with('success', $result['message'])
                     ->with('step', 3)
                     ->with('channel', $request->input('channel'))
                     ->with('identifier', $request->input('identifier'))
                     ->with('reset_token', $result['reset_token']);
    }

    /**
     * Reset the user password with a new one.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'channel' => 'required|in:email,sms',
            'identifier' => 'required|string',
            'reset_token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.required' => __('يرجى إدخال كلمة المرور الجديدة'),
            'password.min' => __('يجب ألا تقل كلمة المرور عن 8 أحرف'),
            'password.confirmed' => __('تأكيد كلمة المرور غير متطابق'),
        ]);

        $result = $this->passwordResetService->resetPassword(
            $request->input('channel'),
            $request->input('identifier'),
            $request->input('reset_token'),
            $request->input('password')
        );

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if (!$result['success']) {
            return back()->with('error', $result['message'])->withInput();
        }

        return redirect()->route('login')->with('success', $result['message']);
    }
}
