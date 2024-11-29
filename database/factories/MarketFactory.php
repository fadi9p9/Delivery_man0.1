<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Market>
 */
class MarketFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'id' => $this->faker->unique()->numberBetween(1, 100),
            'userId' => User::factory(),
            'title' => $this->faker->sentence,
            'location' => $this->faker->address,
            'img' => $this->faker->imageUrl(),
            'rating' => $this->faker->randomFloat(2, 0, 5),
            // 'ratingCount' => $this->faker->numberBetween(0, 100),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
