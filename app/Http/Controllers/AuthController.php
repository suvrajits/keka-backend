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
    public function updateXP(Request $request)
    {
        $validated = $request->validate([
            'player_id' => 'required|exists:users,id',
            'xp_gained' => 'required|integer|min:0',
        ]);

        $progression = PlayerProgression::where('player_id', $validated['player_id'])->first();

        if (!$progression) {
            return response()->json(['status' => 0, 'message' => 'Player progression not found.'], 404);
        }

        $currentLevel = Level::where('level', $progression->level)->first();
        $adjustedXP = $validated['xp_gained'] * $currentLevel->xfactor;
        $progression->current_xp += (int)$adjustedXP;

        $isLevelup = 0; // Default: No level-up
        $newTrack = null;
        $newSkill = null;
        $newXFactor = null; // XFactor of the new level

        // Fetch next level XP requirement (whether level-up happens or not)
        $nextLevelXP = Level::where('level', $progression->level + 1)->value('xp_required');

        // Check for level-up
        while ($nextLevel = Level::where('level', $progression->level + 1)->first()) {
            if ($progression->current_xp >= $nextLevel->xp_required) {
                $progression->current_xp -= $nextLevel->xp_required;
                $progression->level = $nextLevel->level;
                $isLevelup = 1; // Level-up happened

                // Update next level XP for the next possible level-up
                $nextLevelXP = Level::where('level', $progression->level + 1)->value('xp_required');

                // Store new XFactor of the level-up
                $newXFactor = $nextLevel->xfactor;

                // Unlock new track if available
                if (!empty($nextLevel->track_name)) {
                    $tracks = $progression->tracks_unlocked ?? [];
                    if (!in_array($nextLevel->track_name, $tracks)) {
                        $tracks[] = $nextLevel->track_name;
                        $progression->tracks_unlocked = $tracks;
                        $newTrack = $nextLevel->track_name;
                    }
                }

                // Unlock new skill if available
                if (!empty($nextLevel->skill_name)) {
                    $skills = $progression->skills_acquired ?? [];
                    if (!in_array($nextLevel->skill_name, $skills)) {
                        $skills[] = $nextLevel->skill_name;
                        $progression->skills_acquired = $skills;
                        $newSkill = $nextLevel->skill_name;
                    }
                }
            } else {
                break;
            }
        }

        $progression->save();

        return response()->json([
            'status' => 1,
            'message' => 'XP updated successfully.',
            'level' => $progression->level,
            'current_xp' => $progression->current_xp,
            'tracks_unlocked' => $progression->tracks_unlocked,
            'skills_acquired' => $progression->skills_acquired,
            'isLevelup' => $isLevelup, // New parameter
            'new_track' => $isLevelup ? $newTrack : null, // New track only if level-up
            'new_skill' => $isLevelup ? $newSkill : null, // New skill only if level-up
            'new_xfactor' => $isLevelup ? $newXFactor : null, // XFactor only if level-up
            'next_level_xp' => $nextLevelXP, // Always pass XP required for next level
        ]);
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