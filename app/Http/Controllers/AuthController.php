<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
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
            // Fetch Google's public keys (JWKs)
            $jwkResponse = Http::get('https://www.googleapis.com/oauth2/v3/certs');
            $publicKeys = JWK::parseKeySet($jwkResponse->json());

            // Decode the ID token
            $decoded = JWT::decode($idToken, $publicKeys);

            // Extract user info from the decoded token
            $googleId = $decoded->sub;
            $email = $decoded->email ?? null;
            $name = $decoded->name ?? null;
            $avatar = $decoded->picture ?? null;

            // Check if user already exists
            $user = User::where('google_id', $googleId)->first();

            if (!$user) {
                // Create a new user if they don't exist
                $user = User::create([
                    'google_id' => $googleId,
                    'name' => $name,
                    'email' => $email,
                    'avatar' => $avatar,
                    'password' => bcrypt('default_password'),
                ]);
            }

            // Generate a token for the user
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return the token and user details
            return response()->json([
                'access_token' => $token,
                'user' => $user,
            ]);

        } catch (\Firebase\JWT\ExpiredException $e) {
            return response()->json(['error' => 'Token has expired'], 401);
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return response()->json(['error' => 'Invalid token signature'], 401);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function handleInstagramCallback(Request $request)
    {
        // ✅ Step 1: Check if there's an error in the callback
        if ($request->has('error')) {
            return response()->json(['error' => $request->get('error_description')], 400);
        }

        // ✅ Step 2: Get the "code" from the request (sent by Instagram)
        $authCode = $request->get('code');

        if (!$authCode) {
            return response()->json(['error' => 'Authorization code not found'], 400);
        }

        // ✅ Step 3: Exchange the code for an access token
        $response = Http::asForm()->post('https://api.instagram.com/oauth/access_token', [
            'client_id' => config('services.instagram.client_id'),
            'client_secret' => config('services.instagram.client_secret'),
            'grant_type' => 'authorization_code',
            'redirect_uri' => config('services.instagram.redirect_uri'),
            'code' => $authCode,
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'Failed to get access token'], 400);
        }

        // ✅ Step 4: Get the access token and user info
        $data = $response->json();
        $accessToken = $data['access_token'];
        $userId = $data['user_id'];

        return response()->json([
            'message' => 'Instagram authentication successful',
            'access_token' => $accessToken,
            'user_id' => $userId,
        ]);
    }

}