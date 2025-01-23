<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminProfileController extends Controller
{
    /**
     * Display the admin profile page.
     */
    public function index()
    {
        // Ensure admin is authenticated before accessing profile
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login')->with('error', 'Please log in to access your profile.');
        }

        $admin = Auth::guard('admin')->user();

        return view('admin.profile', compact('admin'));
    }
}
