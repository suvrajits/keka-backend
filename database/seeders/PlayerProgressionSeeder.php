<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlayerProgression;
use App\Models\Level;

class PlayerProgressionSeeder extends Seeder
{
    public function run()
    {
        // Get all player progressions where tracks & skills are empty arrays
        $progressionsToUpdate = PlayerProgression::where('tracks_unlocked', '[]')
            ->where('skills_acquired', '[]')
            ->get();

        // If no records need updates, exit
        if ($progressionsToUpdate->isEmpty()) {
            $this->command->info('No player progressions needed updates.');
            return;
        }

        foreach ($progressionsToUpdate as $progression) {
            // Get the player's current level
            $playerLevel = $progression->level;

            // Fetch all levels up to and including the player's level
            $levels = Level::where('level', '<=', $playerLevel)->get();

            if ($levels->isEmpty()) {
                $this->command->warn("No level data found for player ID {$progression->player_id}. Skipping update.");
                continue;
            }

            // Accumulate all tracks and skills up to the player's level
            $tracksUnlocked = [];
            $skillsAcquired = [];

            foreach ($levels as $level) {
                if ($level->track_name && !in_array($level->track_name, $tracksUnlocked)) {
                    $tracksUnlocked[] = $level->track_name;
                }
                if ($level->skill_name && !in_array($level->skill_name, $skillsAcquired)) {
                    $skillsAcquired[] = $level->skill_name;
                }
            }

            // Update the player's progression
            $progression->update([
                'tracks_unlocked' => json_encode($tracksUnlocked),
                'skills_acquired' => json_encode($skillsAcquired),
            ]);

            $this->command->info("Updated player ID {$progression->player_id} at Level {$playerLevel} with tracks: " . json_encode($tracksUnlocked) . " and skills: " . json_encode($skillsAcquired));
        }
    }
}
