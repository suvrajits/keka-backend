<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScoresTable extends Migration
{
    public function up()
    {
        Schema::create('scores', function (Blueprint $table) {
            $table->id(); // Primary Key
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Link to users table
            $table->integer('score'); // Score value
            $table->timestamp('submitted_at')->nullable(); // Timestamp of score submission
            $table->string('track_id')->unique(); // Unique identifier for the score entry
            $table->timestamps(); // Created and Updated timestamps
        });
    }

    public function down()
    {
        Schema::dropIfExists('scores');
    }
}