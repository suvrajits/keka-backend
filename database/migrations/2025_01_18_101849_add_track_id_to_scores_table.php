<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddTrackIdToScoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('scores', function (Blueprint $table) {
            // Add the column as nullable temporarily
            $table->uuid('track_id')->nullable()->after('score');
        });

        // Populate the track_id for existing rows
        \DB::table('scores')->whereNull('track_id')->update([
            'track_id' => \DB::raw("(uuid_generate_v4())"), // PostgreSQL function for UUID
        ]);

        // Set the column to NOT NULL after populating existing rows
        Schema::table('scores', function (Blueprint $table) {
            $table->uuid('track_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropColumn('track_id');
        });
    }
}
