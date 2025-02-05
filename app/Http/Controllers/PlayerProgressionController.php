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

        $isLevelup = 0; // Default is no level-up
        $newTrack = null;
        $newSkill = null;

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
            'next_level_xp' => $nextLevelXP, // Always pass XP required for next level
        ]);
    }
}
