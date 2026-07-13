<?php

namespace Database\Factories;

use App\Enums\StatusKontrak;
use App\Models\Contract;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory Contract — membuat data dummy kontrak sewa.
 *
 * @extends Factory<Contract>
 */
class ContractFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        $startDate = fake()->dateTimeBetween('-2 years', '-1 month');
        $endDate   = (clone $startDate)->modify('+12 months');

        return [
            'tenant_id'      => Tenant::factory(),
            'room_id'        => Room::factory(),
            'start_date'     => $startDate->format('Y-m-d'),
            'end_date'       => $endDate->format('Y-m-d'),
            'rent_price'     => fake()->randomElement([800000, 900000, 1000000, 1200000, 1500000, 1800000]),
            'deposit_amount' => fake()->randomElement([800000, 900000, 1000000, 1200000, 1500000, 1800000]),
            'status'         => StatusKontrak::Active,
            'notes'          => null,
            'created_by'     => User::factory(),
        ];
    }

    /** State: kontrak aktif. */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'   => StatusKontrak::Active,
            'end_date' => now()->addMonths(fake()->numberBetween(1, 8))->format('Y-m-d'),
        ]);
    }

    /** State: kontrak sudah selesai. */
    public function ended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status'     => StatusKontrak::Ended,
            'start_date' => fake()->dateTimeBetween('-2 years', '-14 months')->format('Y-m-d'),
            'end_date'   => fake()->dateTimeBetween('-13 months', '-1 month')->format('Y-m-d'),
        ]);
    }
}
