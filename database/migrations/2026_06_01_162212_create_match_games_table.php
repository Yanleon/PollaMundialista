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
        Schema::create('match_games', function (Blueprint $table) {
            $table->id();
            $table->string('phase')->index();
            $table->string('group_name')->nullable()->index();
            $table->foreignId('home_team_id')->constrained('teams')->cascadeOnDelete();
            $table->foreignId('away_team_id')->constrained('teams')->cascadeOnDelete();
            $table->dateTime('match_date')->index();
            $table->unsignedTinyInteger('home_score')->nullable();
            $table->unsignedTinyInteger('away_score')->nullable();
            $table->enum('status', ['scheduled', 'live', 'finished', 'cancelled'])->default('scheduled')->index();
            $table->dateTime('prediction_deadline')->index();
            $table->timestamps();

            $table->unique(['home_team_id', 'away_team_id', 'match_date'], 'match_games_unique_fixture');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('match_games');
    }
};
