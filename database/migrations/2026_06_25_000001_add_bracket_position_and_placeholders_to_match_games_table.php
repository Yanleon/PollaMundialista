<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('match_games', function (Blueprint $table) {
            $table->dropForeign(['home_team_id']);
            $table->dropForeign(['away_team_id']);
        });

        Schema::table('match_games', function (Blueprint $table) {
            $table->foreignId('home_team_id')->nullable()->change();
            $table->foreignId('away_team_id')->nullable()->change();
            $table->string('home_placeholder', 40)->nullable()->after('home_team_id');
            $table->string('away_placeholder', 40)->nullable()->after('away_team_id');
            $table->unsignedTinyInteger('bracket_position')->nullable()->after('group_name');

            $table->foreign('home_team_id')->references('id')->on('teams')->nullOnDelete();
            $table->foreign('away_team_id')->references('id')->on('teams')->nullOnDelete();
            $table->index(['phase', 'bracket_position'], 'match_games_phase_position_index');
        });
    }

    public function down(): void
    {
        Schema::table('match_games', function (Blueprint $table) {
            $table->dropIndex('match_games_phase_position_index');
            $table->dropForeign(['home_team_id']);
            $table->dropForeign(['away_team_id']);
            $table->dropColumn(['home_placeholder', 'away_placeholder', 'bracket_position']);
        });

        Schema::table('match_games', function (Blueprint $table) {
            $table->foreignId('home_team_id')->nullable(false)->change();
            $table->foreignId('away_team_id')->nullable(false)->change();

            $table->foreign('home_team_id')->references('id')->on('teams')->cascadeOnDelete();
            $table->foreign('away_team_id')->references('id')->on('teams')->cascadeOnDelete();
        });
    }
};
