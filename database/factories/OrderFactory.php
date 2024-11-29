<?php

namespace Database\Factories;

use App\Models\Cart;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'orderId' => $this->faker->unique()->randomNumber(),
            'status' => $this->faker->randomElement(['Pending', 'Active', 'Canceled', 'Done']),
            'cartId' => Cart::factory(),
            'orderLocation'=> $this->faker->address,
            'customerId' => User::factory(),
            // 'total_price' => $this->faker->randomFloat(2, 10, 1000),
            'deliveryId' => User::factory(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
