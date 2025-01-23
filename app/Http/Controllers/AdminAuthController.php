<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    /**
     * Show the admin login form.
     */
    public function showLoginForm()
    {
        return view('admin.auth.login');  // Ensure the view exists at resources/views/admin/auth/login.blade.php
    }

    /**
     * Handle admin login attempt.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::guard('admin')->attempt($credentials)) {
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }

    /**
     * Handle admin logout.
     */
    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }

    public function showRegistrationForm()
    {
        return view('admin.auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:admin_users,email',
            'password' => 'required|min:8|confirmed',
        ]);

        $verificationCode = rand(100000, 999999);

        $admin = AdminUser::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'verification_code' => $verificationCode,
        ]);

        Mail::to($admin->email)->send(new AdminVerificationMail($admin));

        return redirect()->route('admin.verify')->with('success', 'A verification code has been sent to your email.');
    }

    public function showVerificationForm()
    {
        return view('admin.auth.verify');
    }

    public function verify(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'verification_code' => 'required|numeric'
        ]);

        $admin = AdminUser::where('email', $request->email)
            ->where('verification_code', $request->verification_code)
            ->first();

        if ($admin) {
            $admin->update(['is_verified' => true, 'verification_code' => null]);
            return redirect()->route('admin.login')->with('success', 'Verification successful. Await admin approval.');
        }

        return back()->withErrors(['verification_code' => 'Invalid verification code.']);
    }

    public function resendVerificationCode(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:admin_users,email',
        ]);

        $admin = AdminUser::where('email', $request->email)->first();

        if ($admin && !$admin->is_verified) {
            $admin->verification_code = rand(100000, 999999);
            $admin->save();

            Mail::to($admin->email)->send(new AdminVerificationMail($admin));

            return back()->with('success', 'A new verification code has been sent to your email.');
        }

        return back()->withErrors(['email' => 'Invalid email or user already verified.']);
    }


}