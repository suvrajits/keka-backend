<?php

namespace App\Http\Controllers;

use App\Models\Score;
use Illuminate\Http\Request;
use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\JWK;
use Illuminate\Support\Facades\Http;
use App\Models\Leaderboard; 


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
        try {
            // ✅ Step 1: Validate the request
            $validated = $request->validate([
                'id_token' => 'required', // Required Google ID token
                'score' => 'required|integer|min:0',
            ]);

            // ✅ Step 2: If the score is zero, return a success response with no score submission
            if ($validated['score'] == 0) {
                return response()->json([
                    'status' => 1,
                    'message' => 'No score submitted.',
                ], 200);
            }

            // ✅ Step 3: Verify the Google ID token
            $jwkResponse = Http::get('https://www.googleapis.com/oauth2/v3/certs');
            if ($jwkResponse->failed()) {
                throw new \Exception('Failed to fetch Google public keys', 500);
            }

            $publicKeys = JWK::parseKeySet($jwkResponse->json());
            $decoded = JWT::decode($validated['id_token'], $publicKeys);
            $googleId = $decoded->sub; // Extract the Google user ID

            // ✅ Step 4: Check if the user exists
            $user = User::where('google_id', $googleId)->first();
            if (!$user) {
                throw new \Exception('User not found', 404);
            }

            // ✅ Step 5: Create a new score entry
            $score = Score::create([
                'user_id' => $user->id, // Use the ID from the validated user
                'score' => $validated['score'],
                'updated_at' => now(),
            ]);

            // ✅ Step 6: Return a success response
            return response()->json([
                'status' => 1, // Success status
                'message' => 'Score submitted successfully',
                'user_id' => $user->id, // Include the user ID
                'score' => $score, // Include the score object
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 1, // Always return status 1
                'message' => 'No score submitted.',
                'error' => $e->getMessage(),
            ], 200);
        }
    }

    public function submitScoreIOS(Request $request)
    {
        try {
            // ✅ Step 1: Validate the request
            $validated = $request->validate([
                'google_id' => 'required', // Directly validate Google ID instead of ID token
                'score' => 'required|integer|min:0',
            ]);

            // ✅ Step 2: If the score is zero, return a success response with no score submission
            if ($validated['score'] == 0) {
                return response()->json([
                    'status' => 1,
                    'message' => 'No score submitted.',
                ], 200);
            }

            // ✅ Step 3: Check if the user exists using `google_id`
            $user = User::where('google_id', $validated['google_id'])->first();
            if (!$user) {
                return response()->json([
                    'status' => 1,
                    'message' => 'No score submitted. User not found.',
                ], 200);
            }

            // ✅ Step 4: Create a new score entry
            $score = Score::create([
                'user_id' => $user->id, // Use the user ID from the validated user
                'score' => $validated['score'],
                'updated_at' => now(),
            ]);

            // ✅ Step 5: Return a success response
            return response()->json([
                'status' => 1, // Success status
                'message' => 'Score submitted successfully',
                'user_id' => $user->id, // Include the user ID
                'score' => $score, // Include the score object
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 1, // Always return status 1
                'message' => 'No score submitted due to an error.',
            ], 200);
        }
    }

    

    public function getLeaderboard(Request $request)
    {
        // Check Redis cache first
        $type = $request->query('type', 'accumulated'); // 'accumulated' or 'session'
        $cacheKey = "leaderboard:weekly:{$type}";
        $leaderboard = \Cache::get($cacheKey);

        if (!$leaderboard) {
            // Get scores accumulated over the last 7 days
            $oneWeekAgo = now()->subDays(7);

            $leaderboard = Leaderboard::where('created_at', '>=', $oneWeekAgo)
                ->with('user:id,name,email,avatar')
                ->selectRaw('user_id, SUM(score) as total_score')
                ->groupBy('user_id')
                ->orderByDesc('total_score')
                ->get()
                ->map(function ($item, $index) {
                    return [
                        'user_id' => $item->user_id,
                        'name' => $item->user->name,
                        'email' => $item->user->email,
                        'avatar' => $item->user->avatar ?? 'https://example.com/default-avatar.png',
                        'score' => $item->total_score,
                        'rank' => $index + 1,
                    ];
                });

            // Cache the result for 5 minutes
            \Cache::put($cacheKey, $leaderboard, now()->addMinutes(5));
        }

        return response()->json($leaderboard, 200);
    }




    

    public function showLeaderboard()
    {
        $apiUrl = 'https://kekagame.com/api/leaderboard';
        try {
            // Fetch leaderboard data from API
            $response = Http::get($apiUrl);

            if ($response->failed()) {
                throw new \Exception('Failed to fetch leaderboard data');
            }

            $leaderboard = $response->json();

            // Return the leaderboard view with the fetched data
            return view('admin.leaderboard', compact('leaderboard'));
        } catch (\Exception $e) {
            // Handle exceptions and show an error message
            return view('admin.leaderboard', ['error' => $e->getMessage()]);
        }
    }

}
