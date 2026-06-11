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
        Schema::create('bonus_predictions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->foreignId('champion_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->foreignId('runner_up_team_id')->nullable()->constrained('teams')->nullOnDelete();
            $table->string('top_scorer')->nullable();
            $table->unsignedTinyInteger('points')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bonus_predictions');
    }
};
