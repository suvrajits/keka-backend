<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Score;
use App\Models\Leaderboard;

class UpdateLeaderboardCommand extends Command
{
    protected $signature = 'leaderboard:update';
    protected $description = 'Update the leaderboard based on scores from the last 7 days';

    public function handle()
    {
        $cutoff = now()->subDays(7);

        $scores = Score::where('updated_at', '>=', $cutoff)
            ->select('user_id', \DB::raw('SUM(score) as total_score'))
            ->groupBy('user_id')
            ->orderByDesc('total_score')
            ->get();

        \DB::transaction(function () use ($scores) {
            Leaderboard::truncate();

            foreach ($scores as $score) {
                Leaderboard::create([
                    'user_id' => $score->user_id,
                    'score' => $score->total_score,
                ]);
            }
        });

        $this->info('Leaderboard updated successfully.');
    }
}
