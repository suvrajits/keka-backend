<?php

namespace App\Http\Controllers;

use App\Models\AdminUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\AdminVerificationMail;

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
        try {
            // Validate the input
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:admin_users,email',
                'password' => 'required|min:8|confirmed',
            ], [
                'email.unique' => 'The email has already been registered. Please try logging in or reset your password.',
                'password.min' => 'Password must be at least 8 characters long.',
                'password.confirmed' => 'Password confirmation does not match.',
            ]);

            // Generate a 6-digit verification code
            $verificationCode = rand(100000, 999999);

            // Create the admin user
            $admin = AdminUser::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'verification_code' => $verificationCode,
            ]);

            // Send verification email
            Mail::to($admin->email)->send(new AdminVerificationMail($admin));

            return redirect()->route('admin.verify')
                ->with('success', 'A verification code has been sent to your email.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Return validation errors with input data
            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            // Handle unexpected errors
            return redirect()->back()
                ->withErrors(['error' => 'An unexpected error occurred. Please try again later.'])
                ->withInput();
        }
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