<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController extends Controller
{
    public function googleLogin(Request $request)
    {
        $request->validate([
            'id_token' => 'required',
        ]);

        $idToken = $request->id_token;

        try {
            // Replace with your public key (or fetch it dynamically from Google's JWKs URL)
            $publicKey = "-----BEGIN PUBLIC KEY-----\nYOUR_PUBLIC_KEY_HERE\n-----END PUBLIC KEY-----";

            // Decode the token
            $decoded = JWT::decode($idToken, new Key($publicKey, 'RS256'));

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