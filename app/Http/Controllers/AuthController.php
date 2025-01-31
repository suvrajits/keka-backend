<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Level;
use App\Models\PlayerProgression;
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

        try {
            // Authenticate user and retrieve user data
            $user = $this->authenticateGoogleUser($request->id_token);

            // Ensure player progression exists
            $progression = $this->getOrCreatePlayerProgression($user->id);

            // Generate auth token
            $token = $user->createToken('auth_token')->plainTextToken;

            // Return the response
            return response()->json([
                'status' => 1,
                'access_token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'google_id' => $user->google_id,
                ],
                'player_progression' => [
                    'level' => $progression->level,
                    'current_xp' => $progression->current_xp,
                    'tracks_unlocked' => json_decode($progression->tracks_unlocked),
                    'skills_acquired' => json_decode($progression->skills_acquired),
                ],
            ]);
        } catch (\Firebase\JWT\ExpiredException $e) {
            return response()->json(['status' => 0, 'error' => 'Token has expired'], 401);
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            return response()->json(['status' => 0, 'error' => 'Invalid token signature'], 401);
        } catch (\Exception $e) {
            return response()->json(['status' => 0, 'error' => $e->getMessage()], 400);
        }
    }

    /**
     * Authenticate user with Google ID token and retrieve/create the user.
     */
    private function authenticateGoogleUser($idToken)
    {
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

        // Check if the user already exists
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

        return $user;
    }

    /**
     * Get or create player progression data for a user.
     */
    private function getOrCreatePlayerProgression($playerId)
    {
        $progression = PlayerProgression::where('player_id', $playerId)->first();
    
        if (!$progression) {
            // Get Level 1 details
            $levelOne = Level::where('level', 1)->first();
    
            // Extract default track and skill from Level 1
            $defaultTrack = $levelOne->track_name ? [$levelOne->track_name] : [];
            $defaultSkill = $levelOne->skill_name ? [$levelOne->skill_name] : [];
    
            // Create a new progression entry with Level 1 defaults
            $progression = PlayerProgression::create([
                'player_id' => $playerId,
                'level' => 1,
                'current_xp' => 0,
                'tracks_unlocked' => json_encode($defaultTrack), // Set default track(s) if available
                'skills_acquired' => json_encode($defaultSkill), // Set default skill(s) if available
            ]);
        }
    
        return $progression;
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