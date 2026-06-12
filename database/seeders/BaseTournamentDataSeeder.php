<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BaseTournamentDataSeeder extends Seeder
{
    public function run(): void
    {
        $sqlPath = database_path('seeders/data/base_tournament_data.sql');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::table('match_games')->truncate();
        DB::table('teams')->truncate();
        DB::table('app_settings')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        DB::unprepared(file_get_contents($sqlPath));

        DB::table('app_settings')->insert([
            [
                'key' => 'company_name',
                'value' => 'Polla Mundialista Empresarial 2026',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'registration_email_restriction_enabled',
                'value' => '0',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'registration_enabled',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'allowed_registration_domains',
                'value' => '@wexler.com.co',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'allowed_registration_emails',
                'value' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
