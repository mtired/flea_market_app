<?php

namespace Database\Factories;

use App\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
    protected $model = Profile::class;

    public function definition(): array
    {
        return [
            'postal_code' => $this->faker->postcode(),
            'address' => $this->faker->address(),
            'building' => $this->faker->optional()->secondaryAddress(),
            'image' => 'https://example.com/profile.jpg',
            'profile_completed_at' => now(),
        ];
    }

    /**
     * プロフィール完了済み状態
     */
    public function completed(): static
    {
        return $this->state(fn () => [
            'profile_completed_at' => now(),
        ]);
    }

    /**
     * プロフィール未完了状態
     */
    public function incomplete(): static
    {
        return $this->state(fn () => [
            'profile_completed_at' => null,
        ]);
    }
}