<?php

namespace Database\Factories;

use App\Models\Order;
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

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
            'total' => $this->faker->randomFloat(2, 10, 500),
        ];
    }
}
