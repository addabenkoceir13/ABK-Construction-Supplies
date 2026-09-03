<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Debt;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the authenticated user profile page with metrics and settings.
     */
    public function index()
    {
        $user = Auth::user();

        // System overview statistics for profile hero
        $stats = [
            'total_debts' => Debt::count(),
            'unpaid_debts' => Debt::where('status', 'unpaid')->count(),
            'vehicles_count' => Vehicle::count(),
            'categories_count' => Category::count(),
        ];

        return view('content.Profile.index', compact('user', 'stats'));
    }

    /**
     * Update the user profile information and avatar.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'fname' => ['nullable', 'string', 'max:100'],
            'lname' => ['nullable', 'string', 'max:100'],
            'username' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[a-zA-Z0-9_.-]+$/',
                Rule::unique('users', 'username')->ignore($user->id),
            ],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:3072'],
        ], [
            'username.unique' => __('اسم المستخدم مستخدم بالفعل، يرجى اختيار اسم آخر.'),
            'username.regex' => __('يجب أن يحتوي اسم المستخدم على أحرف وأرقام ورموز (- _ .) فقط بدون مسافات.'),
            'email.required' => __('البريد الإلكتروني مطلوب.'),
            'email.unique' => __('البريد الإلكتروني مسجل مسبقاً.'),
            'avatar.image' => __('يجب أن يكون الملف صورة.'),
            'avatar.max' => __('أقصى حجم للصورة هو 3 ميجابايت.'),
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar')) {
            $uploadPath = public_path('uploads/avatars');
            if (!File::isDirectory($uploadPath)) {
                File::makeDirectory($uploadPath, 0755, true, true);
            }

            // Delete old custom avatar if it exists
            if ($user->avatar && File::exists(public_path($user->avatar))) {
                File::delete(public_path($user->avatar));
            }

            $file = $request->file('avatar');
            $fileName = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadPath, $fileName);
            $user->avatar = 'uploads/avatars/' . $fileName;
        }

        $user->fname = $request->fname;
        $user->lname = $request->lname;
        $user->username = $request->username;
        $user->email = $request->email;
        $user->phone = $request->phone;

        // Synchronize main name attribute
        $fullName = trim(($request->fname ?? '') . ' ' . ($request->lname ?? ''));
        if (!empty($fullName)) {
            $user->name = $fullName;
        }

        $user->save();

        return redirect()->route('profile.index')->with('success', __('تم تحديث بيانات الملف الشخصي بنجاح!'));
    }

    /**
     * Remove custom avatar and reset to default template avatar.
     */
    public function removeAvatar()
    {
        $user = Auth::user();

        if ($user->avatar && File::exists(public_path($user->avatar))) {
            File::delete(public_path($user->avatar));
        }

        $user->avatar = null;
        $user->save();

        return redirect()->route('profile.index')->with('success', __('تمت استعادة الصورة الافتراضية بنجاح!'));
    }

    /**
     * Update the authenticated user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'current_password.required' => __('يرجى إدخال كلمة المرور الحالية.'),
            'password.required' => __('يرجى إدخال كلمة المرور الجديدة.'),
            'password.min' => __('يجب أن تحتوي كلمة المرور على 8 أحرف على الأقل.'),
            'password.confirmed' => __('تأكيد كلمة المرور غير متطابق.'),
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => __('كلمة المرور الحالية غير صحيحة!')])
                         ->with('active_tab', 'password');
        }

        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('profile.index')
                         ->with('active_tab', 'password')
                         ->with('success', __('تم تغيير كلمة المرور بنجاح!'));
    }
}
