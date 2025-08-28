<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Score;
use Illuminate\Support\Facades\Cache;

class ResetLeaderboard extends Command
{
    protected $signature = 'leaderboard:reset';
    protected $description = 'Reset the weekly leaderboard and clear cached results';

    public function handle()
    {
        // Clear cached leaderboard
        Cache::forget('leaderboard:weekly:accumulated');

        // Optionally, archive or delete old scores if needed
        Score::where('created_at', '<', now()->subDays(7))->delete();

        $this->info('Leaderboard has been reset successfully.');
    }
}
