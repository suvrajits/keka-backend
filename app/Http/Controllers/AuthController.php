<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function googleLogin(Request $request)
    {
        // Validate the request
        $request->validate([
            'id_token' => 'required',
        ]);

        $idToken = $request->id_token;

        // Verify the token with Google's API
        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $idToken,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Invalid Google ID token'], 401);
        }

        // Extract user information from the response
        $googleUser = $response->json();
        $googleId = $googleUser['sub'];
        $email = $googleUser['email'];
        $name = $googleUser['name'] ?? null;
        $avatar = $googleUser['picture'] ?? null;

        // Check if user already exists
        $user = User::where('google_id', $googleId)->first();

        if (!$user) {
            // Create a new user if not exists
            $user = User::create([
                'google_id' => $googleId,
                'name' => $name,
                'email' => $email,
                'avatar' => $avatar,
            ]);
        }

        // Generate a token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return the token and user details
        return response()->json([
            'access_token' => $token,
            'user' => $user,
        ]);
    }
}
