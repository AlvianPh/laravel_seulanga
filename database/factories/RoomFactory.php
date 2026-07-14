<?php

namespace Database\Factories;

use App\Enums\StatusKamar;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory Room — membuat data dummy kamar kost yang realistis.
 *
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        // Ambil atau buat tipe kamar secara acak
        $tipeNama  = fake()->randomElement(['Standard', 'Deluxe', 'Suite']);
        $roomType  = RoomType::firstOrCreate(
            ['name' => $tipeNama],
            [
                'description'   => match ($tipeNama) {
                    'Standard' => 'Kamar standar dengan fasilitas dasar',
                    'Deluxe'   => 'Kamar deluxe dengan fasilitas lebih lengkap',
                    'Suite'    => 'Kamar suite premium dengan fasilitas terlengkap',
                },
                'default_price' => match ($tipeNama) {
                    'Standard' => 800000,
                    'Deluxe'   => 1500000,
                    'Suite'    => 2000000,
                },
            ]
        );

        $hargaMap = [
            'Standard' => [800_000, 1_200_000],
            'Deluxe'   => [1_200_000, 1_800_000],
            'Suite'    => [1_800_000, 2_500_000],
        ];

        $luasMap = [
            'Standard' => [9, 12],
            'Deluxe'   => [12, 16],
            'Suite'    => [16, 25],
        ];

        [$minHarga, $maxHarga] = $hargaMap[$tipeNama];
        [$minLuas, $maxLuas]   = $luasMap[$tipeNama];

        $hargaBulanan = fake()->numberBetween($minHarga / 100, $maxHarga / 100) * 100;

        return [
            'room_number'   => fake()->unique()->bothify('R-###'), // default faker, seeder akan menimpa
            'floor'         => fake()->numberBetween(1, 3),
            'room_type_id'  => $roomType->id,
            'size_m2'       => fake()->numberBetween($minLuas * 10, $maxLuas * 10) / 10,
            'monthly_price' => $hargaBulanan,
            'deposit_price' => $hargaBulanan,
            'status'        => StatusKamar::Available,
        ];
    }

    /** State: kamar tersedia. */
    public function available(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusKamar::Available,
        ]);
    }

    /** State: kamar terisi. */
    public function occupied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusKamar::Occupied,
        ]);
    }

    /** State: kamar sedang perbaikan. */
    public function maintenance(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => StatusKamar::Maintenance,
        ]);
    }
}
