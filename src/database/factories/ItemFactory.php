<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\User;
use App\Models\Condition;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Item>
 */
class ItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'user_id' => User::factory(),
            'image' => 'https://example.com/test.jpg',
            'status' => 0,
            'brand' => $this->faker->company(),
            'description' => $this->faker->sentence(),
            'condition_id' => Condition::factory(),
            'price' => 1000,
        ];
    }


    /**
     * SOLD状態
     */
    public function sold(): self
    {
        return $this->state(fn () => [
            'status' => 1,
        ]);
    }
}
