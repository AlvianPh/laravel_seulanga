<?php

namespace Database\Factories;

use App\Enums\StatusKamar;
use App\Enums\TipeKamar;
use App\Models\Room;
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
        $tipe = fake()->randomElement(TipeKamar::cases());

        $hargaMap = [
            TipeKamar::Standard->value => [800_000, 1_200_000],
            TipeKamar::Deluxe->value   => [1_200_000, 1_800_000],
            TipeKamar::Suite->value    => [1_800_000, 2_500_000],
        ];

        $luasMap = [
            TipeKamar::Standard->value => [9, 12],
            TipeKamar::Deluxe->value   => [12, 16],
            TipeKamar::Suite->value    => [16, 25],
        ];

        [$minHarga, $maxHarga] = $hargaMap[$tipe->value];
        [$minLuas, $maxLuas]   = $luasMap[$tipe->value];

        $hargaBulanan = fake()->numberBetween($minHarga / 100, $maxHarga / 100) * 100;

        $fasilitasStandard = ['Kasur', 'Lemari', 'Meja Belajar', 'Kursi'];
        $fasilitasExtra    = ['AC', 'WiFi', 'Kamar Mandi Dalam', 'Kulkas Mini', 'TV', 'Microwave'];

        $fasilitas = $fasilitasStandard;
        if ($tipe !== TipeKamar::Standard) {
            $fasilitas = array_merge($fasilitas, fake()->randomElements($fasilitasExtra, 3));
        }
        if ($tipe === TipeKamar::Suite) {
            $fasilitas = array_merge($fasilitas, fake()->randomElements($fasilitasExtra, 2));
            $fasilitas = array_unique($fasilitas);
        }

        return [
            'room_number'   => null, // diisi di seeder agar unik & berurutan
            'floor'         => fake()->numberBetween(1, 3),
            'type'          => $tipe,
            'size_m2'       => fake()->numberBetween($minLuas * 10, $maxLuas * 10) / 10,
            'monthly_price' => $hargaBulanan,
            'deposit_price' => $hargaBulanan,
            'status'        => StatusKamar::Available,
            'facilities'    => array_values(array_unique($fasilitas)),
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
