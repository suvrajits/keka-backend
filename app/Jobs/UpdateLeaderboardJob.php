<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class UpdateLeaderboardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle()
    {
        $cutoff = now()->subHours(24);

        // Compute leaderboard data
        $leaderboardData = DB::table('scores')
            ->where('updated_at', '>=', $cutoff)
            ->select('user_id', DB::raw('SUM(score) as total_score'))
            ->groupBy('user_id')
            ->orderByDesc('total_score')
            ->get();

        // Update the leaderboards table
        foreach ($leaderboardData as $entry) {
            DB::table('leaderboards')->updateOrInsert(
                ['user_id' => $entry->user_id],
                ['total_score' => $entry->total_score, 'updated_at' => now()]
            );
        }

        // Refresh cache
        \Cache::put('leaderboard:daily:accumulated', $leaderboardData, now()->addMinutes(5));
    }
}
