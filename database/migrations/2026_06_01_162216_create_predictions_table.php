<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('match_game_id')->constrained('match_games')->cascadeOnDelete();
            $table->unsignedTinyInteger('predicted_home_score');
            $table->unsignedTinyInteger('predicted_away_score');
            $table->unsignedTinyInteger('points')->default(0);
            $table->boolean('is_exact_score')->default(false);
            $table->boolean('is_correct_result')->default(false);
            $table->timestamps();

            $table->unique(['user_id', 'match_game_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('predictions');
    }
};
