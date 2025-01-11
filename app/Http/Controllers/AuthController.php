<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;

class AuthController extends Controller
{
    public function googleLogin(Request $request)
    {
        $request->validate([
            'id_token' => 'required',
        ]);

        $idToken = $request->id_token;

        try {
            // Fetch Google's public keys
            $keys = json_decode(file_get_contents('https://www.googleapis.com/oauth2/v3/certs'), true);

            // Decode and validate the JWT
            $decodedToken = JWT::decode($idToken, JWK::parseKeySet($keys), ['RS256']);

            // Extract user info
            $googleUser = (array) $decodedToken;

            return response()->json([
                'message' => 'Token is valid',
                'user' => $googleUser,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }
    }
}