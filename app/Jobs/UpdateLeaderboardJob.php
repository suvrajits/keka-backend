<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class UpdateLeaderboardJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle()
    {
        Log::info('UpdateLeaderboardJob started');

        $cutoff = now()->subHours(24);

        try {
            Log::info('Fetching leaderboard data');

            $leaderboardData = DB::table('scores')
                ->where('updated_at', '>=', $cutoff)
                ->select('user_id', DB::raw('SUM(score) as total_score'))
                ->groupBy('user_id')
                ->orderByDesc('total_score')
                ->get();

            Log::info('Leaderboard data fetched', ['count' => $leaderboardData->count()]);

            foreach ($leaderboardData as $entry) {
                 Log::info('Processing leaderboard entry', ['user_id' => $entry->user_id, 'total_score' => $entry->total_score]);

                 DB::table('leaderboards')->updateOrInsert(
                     ['user_id' => $entry->user_id],
                     ['score' => $entry->total_score, 'updated_at' => now()]
                 );

                 Log::info('Leaderboard entry updated', ['user_id' => $entry->user_id]);
            }

            Log::info('UpdateLeaderboardJob completed successfully');
        } catch (\Exception $e) {
            Log::error('UpdateLeaderboardJob failed', [
                'message' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);

            // Optionally, rethrow the exception to retry the job
            throw $e;
        }
    }

}
