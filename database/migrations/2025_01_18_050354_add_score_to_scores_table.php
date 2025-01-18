<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScoreToScoresTable extends Migration
{
    public function up()
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->integer('score')->default(0); // Add the score column with a default value of 0
        });
    }

    public function down()
    {
        Schema::table('scores', function (Blueprint $table) {
            $table->dropColumn('score');
        });
    }
};
