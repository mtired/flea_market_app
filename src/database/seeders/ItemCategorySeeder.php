<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ItemCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('item_categories')->insert([
            // 商品ID:1
            [
                'item_id' => 1,
                'category_id' => 1, // ファッション
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_id' => 1,
                'category_id' => 5, // メンズ
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 商品ID:2
            [
                'item_id' => 2,
                'category_id' => 10, // キッチン
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 商品ID:3
            [
                'item_id' => 3,
                'category_id' => 10, // キッチン
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 商品ID:4
            [
                'item_id' => 4,
                'category_id' => 10, // キッチン
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 商品ID:5
            [
                'item_id' => 5,
                'category_id' => 10, // キッチン
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 商品ID:6
            [
                'item_id' => 6,
                'category_id' => 10, // キッチン
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 商品ID:7
            [
                'item_id' => 7,
                'category_id' => 10, // キッチン
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 商品ID:8
            [
                'item_id' => 8,
                'category_id' => 10, // キッチン
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
