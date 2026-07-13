<?php

namespace Database\Factories;

use App\Models\RoomPhoto;
use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory RoomPhoto — membuat data dummy foto kamar.
 *
 * @extends Factory<RoomPhoto>
 */
class RoomPhotoFactory extends Factory
{
    /** @return array<string, mixed> */
    public function definition(): array
    {
        return [
            'room_id'    => Room::factory(),
            'file_path'  => 'rooms/dummy-' . fake()->uuid() . '.jpg',
            'is_primary' => false,
        ];
    }

    /** State: jadikan foto ini sebagai foto utama kamar. */
    public function primary(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_primary' => true,
        ]);
    }
}
