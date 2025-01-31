<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PlayerProgression;
use App\Models\Level;

class PlayerProgressionSeeder extends Seeder
{
    public function run()
    {
        // Fetch player progressions where tracks & skills are empty JSON arrays
        $progressionsToUpdate = PlayerProgression::whereRaw("tracks_unlocked::text = '[]'::text")
            ->whereRaw("skills_acquired::text = '[]'::text")
            ->get();

        if ($progressionsToUpdate->isEmpty()) {
            $this->command->info('No player progressions needed updates.');
            return;
        }

        foreach ($progressionsToUpdate as $progression) {
            $playerLevel = $progression->level;

            // Fetch all levels up to and including the player's level
            $levels = Level::where('level', '<=', $playerLevel)->get();

            if ($levels->isEmpty()) {
                $this->command->warn("No level data found for player ID {$progression->player_id}. Skipping update.");
                continue;
            }

            // Accumulate tracks & skills
            $tracksUnlocked = [];
            $skillsAcquired = [];

            foreach ($levels as $level) {
                if (!empty($level->track_name) && !in_array($level->track_name, $tracksUnlocked)) {
                    $tracksUnlocked[] = $level->track_name;
                }
                if (!empty($level->skill_name) && !in_array($level->skill_name, $skillsAcquired)) {
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
