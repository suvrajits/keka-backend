<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PlayerProgression;
use App\Models\Level;

class PlayerProgressionController extends Controller
{
    /**
     * Get the player's progression data.
     */
    public function getProgression(Request $request)
    {
        $playerId = $request->input('player_id');

        $progression = PlayerProgression::where('player_id', $playerId)->first();

        if (!$progression) {
            return response()->json(['status' => 0, 'message' => 'Player progression not found.'], 404);
        }

        return response()->json([
            'status' => 1,
            'level' => $progression->level,
            'current_xp' => $progression->current_xp,
            'tracks_unlocked' => $progression->tracks_unlocked ?? [],
            'skills_acquired' => $progression->skills_acquired ?? [],
        ]);
    }

    /**
     * Update player XP and track skill progression.
     */
    public function updateXP(Request $request)
    {
        $validated = $request->validate([
            'player_id' => 'required|exists:players,id',
            'xp_gained' => 'required|integer|min:0',
        ]);

        $progression = PlayerProgression::where('player_id', $validated['player_id'])->first();

        if (!$progression) {
            return response()->json(['status' => 0, 'message' => 'Player progression not found.'], 404);
        }

        $currentLevel = Level::where('level', $progression->level)->first();
        $adjustedXP = $validated['xp_gained'] * $currentLevel->xfactor;
        $progression->current_xp += (int)$adjustedXP;

        // Check for level-up
        while ($nextLevel = Level::where('level', $progression->level + 1)->first()) {
            if ($progression->current_xp >= $nextLevel->xp_required) {
                $progression->current_xp -= $nextLevel->xp_required;
                $progression->level = $nextLevel->level;
            } else {
                break;
            }
        }

        // Add new track/skill if provided
        if ($request->filled('new_track')) {
            $tracks = $progression->tracks_unlocked ?? [];
            if (!in_array($validated['new_track'], $tracks)) {
                $tracks[] = $validated['new_track'];
                $progression->tracks_unlocked = $tracks;
            }
        }

        if ($request->filled('new_skill')) {
            $skills = $progression->skills_acquired ?? [];
            if (!in_array($validated['new_skill'], $skills)) {
                $skills[] = $validated['new_skill'];
                $progression->skills_acquired = $skills;
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
        ]);
    }
}
