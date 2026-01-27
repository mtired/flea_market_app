<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProfilesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('profiles')->insert([
            [
                'user_id' => 5,
                'postal_code' => '123-4567',
                'address' => '東京都テスト区1-2-3',
                'building'      => 'テストビル',
                'image' => null,
                'profile_completed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
