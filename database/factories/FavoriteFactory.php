<?php

namespace Database\Factories;

use App\Models\Favorite;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Favorite>
 */
class FavoriteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'favoriteId'=>$this->faker->unique()->numberBetween(1, 100),
            'userId' => User::factory(),
            'productId' => Product::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
