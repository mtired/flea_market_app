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
            // 商品ID:1 腕時計
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

            // 商品ID:2 HDD
            [
                'item_id' => 2,
                'category_id' => 2, // 家電
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 商品ID:3 玉ねぎ3束
            [
                'item_id' => 3,
                'category_id' => 10, // キッチン
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 商品ID:4 革靴
            [
                'item_id' => 4,
                'category_id' => 1, // ファッション
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_id' => 4,
                'category_id' => 5, // メンズ
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 商品ID:5 ノートPC
            [
                'item_id' => 5,
                'category_id' => 2, // 家電
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 商品ID:6 マイク
            [
                'item_id' => 6,
                'category_id' => 2, // 家電
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 商品ID:7 ショルダーバッグ
            [
                'item_id' => 7,
                'category_id' => 1, // ファッション
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'item_id' => 7,
                'category_id' => 4, // レディース
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 商品ID:8 タンブラー
            [
                'item_id' => 8,
                'category_id' => 10, // キッチン
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 商品ID:9 コーヒーミル
            [
                'item_id' => 9,
                'category_id' => 10, // キッチン
                'created_at' => now(),
                'updated_at' => now(),
            ],

            // 商品ID:10 メイクセット
            [
                'item_id' => 10,
                'category_id' => 6, // コスメ
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
