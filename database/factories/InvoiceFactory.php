<?php

namespace Database\Factories;

use App\Enums\StatusTagihan;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory Invoice — membuat data dummy tagihan bulanan.
 *
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        $rentAmount      = fake()->randomElement([800000, 900000, 1000000, 1200000, 1500000]);
        $electricityFee  = fake()->numberBetween(500, 1500) * 100;
        $waterFee        = fake()->numberBetween(100, 500) * 100;
        $internetFee     = 100000;
        $penaltyFee      = 0;
        $otherFee        = 0;
        $totalAmount     = $rentAmount + $electricityFee + $waterFee + $internetFee;

        $year  = (int) now()->format('Y');
        $month = (int) now()->format('m');

        return [
            'contract_id'     => Contract::factory(),
            'tenant_id'       => Tenant::factory(),
            'room_id'         => Room::factory(),
            'year'            => $year,
            'month'           => $month,
            'rent_amount'     => $rentAmount,
            'electricity_fee' => $electricityFee,
            'water_fee'       => $waterFee,
            'internet_fee'    => $internetFee,
            'penalty_fee'     => $penaltyFee,
            'other_fee'       => $otherFee,
            'total_amount'    => $totalAmount,
            'due_date'        => now()->startOfMonth()->addDays(9)->format('Y-m-d'),
            'status'          => StatusTagihan::Pending,
        ];
    }

    /** State: tagihan sudah lunas. */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusTagihan::Paid,
        ]);
    }

    /** State: tagihan jatuh tempo (overdue). */
    public function overdue(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusTagihan::Overdue,
        ]);
    }

    /** State: tagihan menunggu pembayaran (pending). */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusTagihan::Pending,
        ]);
    }
}
