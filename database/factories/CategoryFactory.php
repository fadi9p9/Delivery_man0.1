<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
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
            'name' => $this->faker->word,
            'img' => $this->faker->imageUrl(),
            'created_at' => now(),
            'updated_at' => now(),


        ];
    }
}
