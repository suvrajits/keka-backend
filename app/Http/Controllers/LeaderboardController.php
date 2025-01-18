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
        try {
            // ✅ Step 1: Validate the request
            $validated = $request->validate([
                'id_token' => 'required', // Required Google ID token
                'score' => 'required|integer|min:0',
            ]);
    
            // ✅ Step 2: Verify the Google ID token
            $jwkResponse = Http::get('https://www.googleapis.com/oauth2/v3/certs');
            if ($jwkResponse->failed()) {
                throw new \Exception('Failed to fetch Google public keys', 500);
            }
    
            $publicKeys = JWK::parseKeySet($jwkResponse->json());
            $decoded = JWT::decode($validated['id_token'], $publicKeys);
            $googleId = $decoded->sub; // Extract the Google user ID
    
            // ✅ Step 3: Check if the user exists
            $user = User::where('google_id', $googleId)->first();
            if (!$user) {
                throw new \Exception('User not found', 404);
            }
    
            // ✅ Step 4: Create a new score entry
            $score = Score::create([
                'user_id' => $user->id, // Use the ID from the validated user
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
    
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Handle validation errors
            return response()->json([
                'status' => 0,
                'error' => 'Validation error',
                'details' => $e->errors(), // Provide detailed validation errors
            ], 422);
        } catch (\Firebase\JWT\ExpiredException $e) {
            // Handle expired token error
            return response()->json([
                'status' => 0,
                'error' => 'ID token expired',
            ], 401);
        } catch (\Firebase\JWT\SignatureInvalidException $e) {
            // Handle invalid token signature error
            return response()->json([
                'status' => 0,
                'error' => 'Invalid ID token signature',
            ], 401);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json([
                'status' => 0,
                'error' => $e->getMessage(),
            ], $e->getCode() ?: 500); // Default to 500 if no code is set
        }
    }
    

    public function getLeaderboard(Request $request)
    {
        // Check Redis cache first
        $type = $request->query('type', 'accumulated'); // 'accumulated' or 'session'
        $cacheKey = "leaderboard:daily:{$type}";
        $leaderboard = \Cache::get($cacheKey);

        if (!$leaderboard) {
            // Fallback if cache is empty
            $leaderboard = Leaderboard::with('user:id,name,email')
                ->orderByDesc('score')
                ->get();

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
            return view('leaderboard', compact('leaderboard'));
        } catch (\Exception $e) {
            // Handle exceptions and show an error message
            return view('leaderboard', ['error' => $e->getMessage()]);
        }
    }

}
