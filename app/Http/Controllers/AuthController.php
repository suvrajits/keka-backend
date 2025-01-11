<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;

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
            // Fetch Google's public keys (JWKs)
            $jwkResponse = Http::get('https://www.googleapis.com/oauth2/v3/certs');
            $publicKeys = JWK::parseKeySet($jwkResponse->json());

            // Decode the ID token
            $decoded = JWT::decode($idToken, $publicKeys);

            // Return the decoded token
            return response()->json([
                'message' => 'Token is valid',
                'user' => $decoded,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
