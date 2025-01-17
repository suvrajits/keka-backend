<?php

namespace App\Http\Controllers;

use App\Models\Score;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LeaderboardController extends Controller
{
    /**
     * Submit a user's score.
     */
    public function submitScore(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'score' => 'required|integer|min:0',
        ]);

        $score = Score::create([
            'user_id' => $validated['user_id'],
            'score' => $validated['score'],
            'submitted_at' => now(),
            'track_id' => Str::uuid(),
        ]);

        return response()->json([
            'message' => 'Score submitted successfully',
            'score' => $score,
        ], 201);
    }

    /**
     * Fetch the leaderboard for the last 24 hours.
     */
    public function getLeaderboard(Request $request)
    {
        $type = $request->query('type', 'accumulated'); // 'accumulated' or 'session'
        $cutoff = now()->subHours(24);

        $leaderboard = ($type === 'session')
            ? Score::where('submitted_at', '>=', $cutoff)
                ->select('user_id', 'score', 'submitted_at', 'track_id')
                ->orderByDesc('score')
                ->with('user:id,name,email')
                ->get()
            : Score::where('submitted_at', '>=', $cutoff)
                ->select('user_id', \DB::raw('SUM(score) as total_score'))
                ->groupBy('user_id')
                ->orderByDesc('total_score')
                ->with('user:id,name,email')
                ->get();

        return response()->json($leaderboard, 200);
    }
}
