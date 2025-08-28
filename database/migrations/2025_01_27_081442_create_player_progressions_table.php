<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('player_progressions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('users')->onDelete('cascade'); 
            $table->integer('level')->default(1);
            $table->integer('current_xp')->default(0);
            $table->json('tracks_unlocked')->nullable();
            $table->json('skills_acquired')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('player_progressions');
    }
};

