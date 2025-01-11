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

        try {
            // Send a GET request to Google's tokeninfo endpoint with timeout and retry
            $response = Http::retry(3, 100) // Retry 3 times, wait 100ms between retries
                ->timeout(5) // Wait for a maximum of 5 seconds
                ->get('https://oauth2.googleapis.com/tokeninfo', [
                    'id_token' => $idToken,
                ]);

            // Check if the response failed
            if ($response->failed()) {
                return response()->json(['error' => 'Invalid Google ID token'], 401);
            }

            // Extract user info from the response
            $googleUser = $response->json();

            // Return the response
            return response()->json([
                'message' => 'Token is valid',
                'user' => $googleUser,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
