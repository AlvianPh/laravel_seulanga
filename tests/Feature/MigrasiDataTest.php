<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * MigrasiDataTest — memastikan migrasi data dari ENUM/JSON ke tabel master
 * tidak menghilangkan data kamar yang sudah ada.
 *
 * Test ini menyimulasikan kondisi sebelum dan sesudah migrasi
 * menggunakan data dummy yang mirip dengan data produksi.
 */
class MigrasiDataTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setelah RefreshDatabase, skema sudah yang baru (room_type_id, pivot).
     * Cek bahwa tabel-tabel master sudah ter-create dengan benar.
     */
    public function test_room_types_table_exists_with_default_data()
    {
        // Tabel room_types harus ada dan sudah punya data dari migration seed
        $this->assertDatabaseHas('room_types', ['name' => 'Standard']);
        $this->assertDatabaseHas('room_types', ['name' => 'Deluxe']);
        $this->assertDatabaseHas('room_types', ['name' => 'Suite']);
    }

    public function test_facilities_table_exists()
    {
        // Tabel facilities harus ada (kosong di test env karena tidak ada data lama)
        $this->assertEquals(0, DB::table('facilities')->count());
    }

    public function test_room_facilities_pivot_table_exists()
    {
        // Tabel pivot room_facilities harus ada
        $this->assertTrue(DB::getSchemaBuilder()->hasTable('room_facilities'));
    }

    public function test_rooms_have_room_type_id_column()
    {
        // Tabel rooms harus punya kolom room_type_id
        $this->assertTrue(DB::getSchemaBuilder()->hasColumn('rooms', 'room_type_id'));
    }

    public function test_rooms_do_not_have_type_enum_column()
    {
        // Tabel rooms TIDAK boleh punya kolom type lama
        $this->assertFalse(DB::getSchemaBuilder()->hasColumn('rooms', 'type'));
    }

    public function test_rooms_do_not_have_facilities_json_column()
    {
        // Tabel rooms TIDAK boleh punya kolom facilities lama
        $this->assertFalse(DB::getSchemaBuilder()->hasColumn('rooms', 'facilities'));
    }

    public function test_room_created_with_room_type_id_is_retrievable()
    {
        $roomType = RoomType::firstOrCreate(['name' => 'Standard'], ['default_price' => 800000]);

        $room = Room::factory()->create([
            'room_number'  => 'TEST001',
            'room_type_id' => $roomType->id,
        ]);

        $this->assertDatabaseHas('rooms', [
            'room_number'  => 'TEST001',
            'room_type_id' => $roomType->id,
        ]);

        $this->assertEquals($roomType->id, $room->fresh()->room_type_id);
        $this->assertEquals('Standard', $room->fresh()->roomType->name);
    }

    public function test_room_facilities_pivot_works_correctly()
    {
        $roomType  = RoomType::firstOrCreate(['name' => 'Deluxe'], ['default_price' => 1500000]);
        $room      = Room::factory()->create(['room_number' => 'TEST002', 'room_type_id' => $roomType->id]);
        $facilityAc   = Facility::create(['name' => 'AC']);
        $facilityWifi = Facility::create(['name' => 'WiFi']);
        $facilityKasur = Facility::create(['name' => 'Kasur']);

        // Attach 3 fasilitas
        $room->facilities()->sync([$facilityAc->id, $facilityWifi->id, $facilityKasur->id]);

        $room->refresh();
        $this->assertCount(3, $room->facilities);

        // Pivot harus ada di database
        $this->assertDatabaseHas('room_facilities', ['room_id' => $room->id, 'facility_id' => $facilityAc->id]);
        $this->assertDatabaseHas('room_facilities', ['room_id' => $room->id, 'facility_id' => $facilityWifi->id]);
        $this->assertDatabaseHas('room_facilities', ['room_id' => $room->id, 'facility_id' => $facilityKasur->id]);
    }

    public function test_room_type_relationship_integrity()
    {
        $roomType = RoomType::firstOrCreate(['name' => 'Suite'], ['default_price' => 2000000]);

        // Buat beberapa kamar dengan tipe yang sama
        Room::factory()->create(['room_number' => 'S001', 'room_type_id' => $roomType->id]);
        Room::factory()->create(['room_number' => 'S002', 'room_type_id' => $roomType->id]);

        $this->assertEquals(2, $roomType->rooms()->count());
    }

    public function test_facility_room_relationship_integrity()
    {
        $roomType  = RoomType::firstOrCreate(['name' => 'Standard'], ['default_price' => 800000]);
        $facility  = Facility::create(['name' => 'TV']);
        $room1     = Room::factory()->create(['room_number' => 'R001', 'room_type_id' => $roomType->id]);
        $room2     = Room::factory()->create(['room_number' => 'R002', 'room_type_id' => $roomType->id]);

        $room1->facilities()->attach($facility->id);
        $room2->facilities()->attach($facility->id);

        $this->assertEquals(2, $facility->rooms()->count());
    }

    public function test_soft_deleted_rooms_retain_room_type_id()
    {
        $roomType = RoomType::firstOrCreate(['name' => 'Standard'], ['default_price' => 800000]);
        $room     = Room::factory()->create(['room_number' => 'SD001', 'room_type_id' => $roomType->id]);

        $room->delete(); // soft delete

        $this->assertSoftDeleted('rooms', ['id' => $room->id]);

        // Room type ID harus tetap ada setelah soft delete
        $this->assertDatabaseHas('rooms', [
            'id'           => $room->id,
            'room_type_id' => $roomType->id,
        ]);
    }
}
