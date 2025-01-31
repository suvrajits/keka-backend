<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlayerProgression;
use App\Models\Level;

class PlayerProgressionSeeder extends Seeder
{
    public function run()
    {
        // Get all player progressions where tracks & skills are empty
        $progressionsToUpdate = PlayerProgression::whereJsonContains('tracks_unlocked', [])
            ->whereJsonContains('skills_acquired', [])
            ->get();

        // If no records need updates, exit
        if ($progressionsToUpdate->isEmpty()) {
            $this->command->info('No player progressions needed updates.');
            return;
        }

        foreach ($progressionsToUpdate as $progression) {
            // Get the player's current level
            $playerLevel = $progression->level;

            // Fetch track and skill for the current level
            $levelData = Level::where('level', $playerLevel)->first();

            if (!$levelData) {
                $this->command->warn("Level {$playerLevel} not found for player ID {$progression->player_id}. Skipping update.");
                continue;
            }

            // Extract track and skill based on level
            $trackUnlocked = $levelData->track_name ? [$levelData->track_name] : [];
            $skillsAcquired = $levelData->skill_name ? [$levelData->skill_name] : [];

            // Update the player's progression
            $progression->update([
                'tracks_unlocked' => json_encode($trackUnlocked),
                'skills_acquired' => json_encode($skillsAcquired),
            ]);

            $this->command->info("Updated player ID {$progression->player_id} at Level {$playerLevel}.");
        }
    }
}
