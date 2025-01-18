<?php

namespace App\Http\Controllers;

use App\Models\Score;
use Illuminate\Http\Request;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use Illuminate\Support\Facades\Http;


class LeaderboardController extends Controller
{
    /**
     * Submit a user's score.
     */
    /*
    public function submitScore(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'score' => 'required|integer|min:0',
        ]);

        $score = Score::create([
            'user_id' => $validated['user_id'],
            'score' => $validated['score'],
            'updated_at' => now(),
            'track_id' => Str::uuid(),
        ]);

        return response()->json([
            'message' => 'Score updated successfully',
            'score' => $score,
        ], 201);
    }
    */
    public function submitScore(Request $request)
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




    /**
     * Fetch the leaderboard for the last 24 hours.
     */
    public function getLeaderboard(Request $request)
    {
        $type = $request->query('type', 'accumulated'); // 'accumulated' or 'session'
        $cutoff = now()->subHours(24);

        $leaderboard = ($type === 'session')
            ? Score::where('updated_at', '>=', $cutoff)
                ->select('user_id', 'score', 'updated_at', 'track_id')
                ->orderByDesc('score')
                ->with('user:id,name,email')
                ->get()
            : Score::where('updated_at', '>=', $cutoff)
                ->select('user_id', \DB::raw('SUM(score) as total_score'))
                ->groupBy('user_id')
                ->orderByDesc('total_score')
                ->with('user:id,name,email')
                ->get();

        return response()->json($leaderboard, 200);
    }
}
