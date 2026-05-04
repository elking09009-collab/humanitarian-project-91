<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Notifications\NewAccountPendingNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    // Register
    public function register(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'password' => 'required|min:6',
            'phone'    => 'nullable|string',
            'role'     => 'required|in:volunteer,organization'
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'role'     => $request->role,
            'status'   => 'pending',
            'password' => Hash::make($request->password),
        ]);

        // Lightweight notification to admins via mail log/mail driver.
        $adminEmails = User::where('can_review_accounts', true)
            ->orWhere('role', 'admin')
            ->pluck('email')
            ->filter()
            ->all();

        if (!empty($adminEmails)) {
            Mail::raw("طلب تسجيل جديد بحاجة للمراجعة: {$user->email}", function ($message) use ($adminEmails) {
                $message->to($adminEmails)->subject('طلب تسجيل جديد - HTR');
            });
        }

        // إشعار داخلي لكل المشرفين
        $admins = User::where('role', 'admin')
            ->orWhere('can_review_accounts', true)
            ->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewAccountPendingNotification($user));
        }

        return response()->json([
            'message' => 'تم إرسال طلب إنشاء الحساب إلى الأدمن للمراجعة'
        ], 201);
    }

    // Login
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['بيانات الدخول غير صحيحة'],
            ]);
        }

        if ($user->role !== 'admin' && ($user->status ?? 'approved') === 'rejected') {
            $reason = $user->rejection_reason ? (' سبب الرفض: ' . $user->rejection_reason) : '';
            throw ValidationException::withMessages([
                'email' => ['تم رفض طلب الحساب.' . $reason],
            ]);
        }

        if ($user->role !== 'admin' && ($user->status ?? 'approved') !== 'approved') {
            throw ValidationException::withMessages([
                'email' => ['الحساب قيد المراجعة من الأدمن حالياً'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user'  => $user,
            'token' => $token
        ]);
    }

    // Logout
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'تم تسجيل الخروج بنجاح'
        ]);
    }

    public function registrationStatus(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => ['لا يوجد طلب مسجل بهذا البريد الإلكتروني'],
            ]);
        }

        if ($user->role === 'admin') {
            return response()->json([
                'status' => 'approved',
                'message' => 'حساب أدمن مفعل',
            ]);
        }

        return response()->json([
            'status' => $user->status ?? 'pending',
            'reason' => $user->rejection_reason,
        ]);
    }
}
