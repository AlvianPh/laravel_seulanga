<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomPhoto;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rooms = collect();
        $lantai = [1, 1, 1, 1, 2, 2, 2, 2, 3, 3];
        $nomor  = ['101', '102', '103', '104', '201', '202', '203', '204', '301', '302'];

        foreach (range(0, 9) as $i) {
            $room = Room::factory()->create([
                'room_number' => $nomor[$i],
                'floor'       => $lantai[$i],
            ]);
            $rooms->push($room);
        }

        foreach ($rooms as $room) {
            RoomPhoto::factory()->primary()->create(['room_id' => $room->id]);
            RoomPhoto::factory()->create(['room_id' => $room->id]);
        }
    }
}
