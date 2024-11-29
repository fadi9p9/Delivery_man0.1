<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // 'id' => $this->faker->unique()->numberBetween(1, 100),
            'name' => $this->faker->name,
            'lastName' => $this->faker->unique()->lastName,
            'email' => $this->faker->unique()->safeEmail,
            'phoneNumber' => $this->faker->unique()->numerify( '##########'),
            'password' => static::$password ??= Hash::make('password'), // password
            'img' => $this->faker->imageUrl(),
            'location' => $this->faker->address,
            'remember_token' => Str::random(10),
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),

            
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
