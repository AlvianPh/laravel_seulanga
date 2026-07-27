<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\RoomType>
 */
class RoomTypeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Tipe ' . $this->faker->unique()->word(),
            'description' => $this->faker->sentence(),
            'default_price' => $this->faker->randomElement([1000000, 1500000, 2000000]),
        ];
    }
}
