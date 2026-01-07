<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Like;

class LikesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Like::create([
            'user_id' => 1,
            'item_id' => 1,
        ]);

        Like::create([
            'user_id' => 1,
            'item_id' => 2,
        ]);

        Like::create([
            'user_id' => 2,
            'item_id' => 2,
        ]);

        Like::create([
            'user_id' => 2,
            'item_id' => 4,
        ]);
    }
}
