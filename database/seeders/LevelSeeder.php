<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Level;


class LevelSeeder extends Seeder
{
    public function run()
    {
        Level::insert([
            ['level' => 1, 'xp_required' => 0, 'xfactor' => 1.0, 'track_name' => 'Track 1', 'skill_name' => null],
            ['level' => 2, 'xp_required' => 1000, 'xfactor' => 1.1, 'track_name' => null, 'skill_name' => null],
            ['level' => 3, 'xp_required' => 3000, 'xfactor' => 1.2, 'track_name' => 'Track 2', 'skill_name' => null],
            ['level' => 4, 'xp_required' => 6000, 'xfactor' => 1.3, 'track_name' => null, 'skill_name' => null],
            ['level' => 5, 'xp_required' => 10000, 'xfactor' => 1.4, 'track_name' => 'Track 3', 'skill_name' => null],
            ['level' => 6, 'xp_required' => 15000, 'xfactor' => 1.5, 'track_name' => null, 'skill_name' => 'Double Jump'],
            ['level' => 7, 'xp_required' => 21000, 'xfactor' => 1.6, 'track_name' => null, 'skill_name' => null],
            ['level' => 8, 'xp_required' => 28000, 'xfactor' => 1.7, 'track_name' => null, 'skill_name' => 'Climbing'],
            ['level' => 9, 'xp_required' => 36000, 'xfactor' => 1.8, 'track_name' => null, 'skill_name' => null],
            ['level' => 10, 'xp_required' => 45000, 'xfactor' => 1.9, 'track_name' => 'Track 4', 'skill_name' => null],
            ['level' => 11, 'xp_required' => 55000, 'xfactor' => 2.0, 'track_name' => null, 'skill_name' => 'Swing'],
            ['level' => 12, 'xp_required' => 66000, 'xfactor' => 2.1, 'track_name' => null, 'skill_name' => null],
            ['level' => 13, 'xp_required' => 78000, 'xfactor' => 2.2, 'track_name' => 'Track 5', 'skill_name' => null],
            ['level' => 14, 'xp_required' => 91000, 'xfactor' => 2.3, 'track_name' => null, 'skill_name' => null],
            ['level' => 15, 'xp_required' => 105000, 'xfactor' => 2.4, 'track_name' => null, 'skill_name' => null],
            ['level' => 16, 'xp_required' => 120000, 'xfactor' => 2.5, 'track_name' => 'Track 6', 'skill_name' => null],
            ['level' => 17, 'xp_required' => 136000, 'xfactor' => 2.6, 'track_name' => null, 'skill_name' => null],
            ['level' => 18, 'xp_required' => 153000, 'xfactor' => 2.7, 'track_name' => null, 'skill_name' => null],
            ['level' => 19, 'xp_required' => 171000, 'xfactor' => 2.8, 'track_name' => 'Track 7', 'skill_name' => null],
            ['level' => 20, 'xp_required' => 190000, 'xfactor' => 2.9, 'track_name' => null, 'skill_name' => null],
            ['level' => 21, 'xp_required' => 210000, 'xfactor' => 3.0, 'track_name' => null, 'skill_name' => null],
            ['level' => 22, 'xp_required' => 231000, 'xfactor' => 3.1, 'track_name' => 'Track 8', 'skill_name' => null],
            ['level' => 23, 'xp_required' => 253000, 'xfactor' => 3.2, 'track_name' => null, 'skill_name' => null],
            ['level' => 24, 'xp_required' => 276000, 'xfactor' => 3.3, 'track_name' => null, 'skill_name' => null],
            ['level' => 25, 'xp_required' => 300000, 'xfactor' => 3.4, 'track_name' => 'Track 9', 'skill_name' => null],
        ]);
    }
}