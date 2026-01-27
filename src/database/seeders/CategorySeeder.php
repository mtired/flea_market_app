<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('categories')->insert([
            [
                // 1
                'name' => 'ファッション',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 2
                'name' => '家電',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 3
                'name' => 'インテリア',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 4
                'name' => 'レディース',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 5
                'name' => 'メンズ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 6
                'name' => 'コスメ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 7
                'name' => '本',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 8
                'name' => 'ゲーム',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 9
                'name' => 'スポーツ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 10
                'name' => 'キッチン',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 11
                'name' => 'ハンドメイド',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 12
                'name' => 'アクセサリー',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 13
                'name' => 'おもちゃ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                // 14
                'name' => 'ベビー・キッズ',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
