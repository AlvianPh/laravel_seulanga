<?php

namespace Tests\Feature;

use App\Enums\StatusKamar;
use App\Models\Facility;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * RoomCrudTest — memastikan fungsi CRUD kamar berjalan dengan benar
 * setelah migrasi ke room_type_id dan pivot fasilitas.
 */
class RoomCrudTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(string $role = 'admin'): User
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);
        return $user;
    }

    /** Buat RoomType bisa digunakan dalam test. */
    private function roomType(string $name = 'Standard'): RoomType
    {
        return RoomType::firstOrCreate(['name' => $name], [
            'description'   => 'Test type',
            'default_price' => 1000000,
        ]);
    }

    public function test_guest_cannot_access_rooms()
    {
        $this->get('/rooms')->assertRedirect('/login');
    }

    public function test_admin_and_owner_can_access_rooms_index()
    {
        $this->authenticate('admin');
        $this->get('/rooms')->assertOk();

        $this->authenticate('owner');
        $this->get('/rooms')->assertOk();
    }

    public function test_user_can_create_room()
    {
        $this->authenticate('admin');
        $roomType = $this->roomType();

        $response = $this->post('/rooms', [
            'room_number'   => 'A101',
            'floor'         => 1,
            'room_type_id'  => $roomType->id,
            'size_m2'       => 12.5,
            'monthly_price' => 1000000,
            'deposit_price' => 500000,
            'status'        => StatusKamar::Available->value,
        ]);

        $response->assertRedirect('/rooms');
        $this->assertDatabaseHas('rooms', [
            'room_number'  => 'A101',
            'room_type_id' => $roomType->id,
        ]);
    }

    public function test_user_can_create_room_with_facilities()
    {
        $this->authenticate('admin');
        $roomType = $this->roomType();
        $fac1     = Facility::create(['name' => 'AC']);
        $fac2     = Facility::create(['name' => 'WiFi']);

        $this->post('/rooms', [
            'room_number'   => 'A102',
            'floor'         => 1,
            'room_type_id'  => $roomType->id,
            'size_m2'       => 12,
            'monthly_price' => 1000000,
            'deposit_price' => 500000,
            'status'        => StatusKamar::Available->value,
            'facilities'    => [$fac1->id, $fac2->id],
        ]);

        $room = Room::where('room_number', 'A102')->first();
        $this->assertNotNull($room);
        $this->assertCount(2, $room->facilities);
        $this->assertTrue($room->facilities->contains($fac1));
        $this->assertTrue($room->facilities->contains($fac2));
    }

    public function test_create_room_validation_fails_on_duplicate_room_number()
    {
        $roomType = $this->roomType();
        Room::factory()->create(['room_number' => 'B202', 'room_type_id' => $roomType->id]);

        $this->authenticate('admin');

        $response = $this->post('/rooms', [
            'room_number'   => 'B202', // Duplicate
            'floor'         => 2,
            'room_type_id'  => $roomType->id,
            'size_m2'       => 15,
            'monthly_price' => 1500000,
            'deposit_price' => 500000,
            'status'        => StatusKamar::Available->value,
        ]);

        $response->assertSessionHasErrors('room_number');
    }

    public function test_user_can_upload_room_photos_during_create()
    {
        Storage::fake('public');
        $this->authenticate('admin');
        $roomType = $this->roomType();

        $file1 = UploadedFile::fake()->image('photo1.jpg');
        $file2 = UploadedFile::fake()->image('photo2.png');

        $this->post('/rooms', [
            'room_number'   => 'A103',
            'floor'         => 1,
            'room_type_id'  => $roomType->id,
            'size_m2'       => 12,
            'monthly_price' => 1000000,
            'deposit_price' => 500000,
            'status'        => StatusKamar::Available->value,
            'photos'        => [$file1, $file2],
        ]);

        $room = Room::where('room_number', 'A103')->first();
        $this->assertCount(2, $room->photos);

        // Foto pertama harus jadi primary
        $this->assertTrue((bool)$room->photos[0]->is_primary);
        $this->assertFalse((bool)$room->photos[1]->is_primary);

        Storage::disk('public')->assertExists($room->photos[0]->file_path);
        Storage::disk('public')->assertExists($room->photos[1]->file_path);
    }

    public function test_user_can_update_room()
    {
        $roomType = $this->roomType();
        $room = Room::factory()->create(['room_number' => 'OLD101', 'room_type_id' => $roomType->id]);
        $this->authenticate('owner');

        $response = $this->patch("/rooms/{$room->id}", [
            'room_number'   => 'NEW101',
            'floor'         => $room->floor,
            'room_type_id'  => $roomType->id,
            'size_m2'       => $room->size_m2,
            'monthly_price' => 2000000, // Harga naik
            'deposit_price' => $room->deposit_price,
            'status'        => StatusKamar::Maintenance->value,
        ]);

        $response->assertRedirect('/rooms');
        $this->assertDatabaseHas('rooms', [
            'id'            => $room->id,
            'room_number'   => 'NEW101',
            'monthly_price' => 2000000,
            'status'        => StatusKamar::Maintenance->value,
        ]);
    }

    public function test_user_can_update_room_facilities()
    {
        $roomType = $this->roomType();
        $room     = Room::factory()->create(['room_number' => 'FAC101', 'room_type_id' => $roomType->id]);
        $fac1     = Facility::create(['name' => 'AC']);
        $fac2     = Facility::create(['name' => 'WiFi']);
        $fac3     = Facility::create(['name' => 'Kasur']);

        $room->facilities()->sync([$fac1->id, $fac2->id]);

        $this->authenticate('admin');

        $this->patch("/rooms/{$room->id}", [
            'room_number'   => $room->room_number,
            'floor'         => $room->floor,
            'room_type_id'  => $roomType->id,
            'size_m2'       => $room->size_m2,
            'monthly_price' => $room->monthly_price,
            'deposit_price' => $room->deposit_price,
            'status'        => $room->status->value,
            'facilities'    => [$fac2->id, $fac3->id], // Ganti: hapus AC, tambah Kasur
        ]);

        $room->refresh();
        $facilityIds = $room->facilities->pluck('id')->toArray();
        $this->assertContains($fac2->id, $facilityIds);
        $this->assertContains($fac3->id, $facilityIds);
        $this->assertNotContains($fac1->id, $facilityIds);
    }

    public function test_user_can_delete_room_without_active_contract()
    {
        Storage::fake('public');
        $this->authenticate('admin');

        $roomType = $this->roomType();
        $room = Room::factory()->create(['room_number' => 'DEL101', 'room_type_id' => $roomType->id]);

        // Buat file fake di storage dan hubungkan ke room
        $file = UploadedFile::fake()->image('test.jpg');
        $path = $file->store('rooms', 'public');

        $room->photos()->create([
            'file_path'  => $path,
            'is_primary' => true
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->delete("/rooms/{$room->id}");

        $response->assertRedirect('/rooms');

        // Room should be soft deleted
        $this->assertSoftDeleted('rooms', ['id' => $room->id]);

        // File fisik di storage tidak dihapus (karena soft delete)
        Storage::disk('public')->assertExists($path);
    }

    public function test_user_cannot_delete_room_with_active_contract()
    {
        $this->authenticate('admin');

        $roomType = $this->roomType();
        $room     = Room::factory()->create(['room_number' => 'DEL102', 'room_type_id' => $roomType->id]);
        $tenant   = \App\Models\Tenant::factory()->create();

        // Create an active contract for the room
        \App\Models\Contract::factory()->create([
            'room_id'   => $room->id,
            'tenant_id' => $tenant->id,
            'status'    => \App\Enums\StatusKontrak::Active->value,
        ]);

        $response = $this->delete("/rooms/{$room->id}");

        // Should return back with error
        $response->assertSessionHas('error', 'Kamar ini masih memiliki kontrak aktif dan tidak dapat dihapus. Akhiri kontrak terlebih dahulu.');

        // Room should NOT be deleted
        $this->assertDatabaseHas('rooms', ['id' => $room->id, 'deleted_at' => null]);
    }
}
