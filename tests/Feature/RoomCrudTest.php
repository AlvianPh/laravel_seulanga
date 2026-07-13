<?php

namespace Tests\Feature;

use App\Enums\StatusKamar;
use App\Enums\TipeKamar;
use App\Models\Room;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * RoomCrudTest — memastikan fungsi CRUD kamar berjalan dengan benar.
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

        $response = $this->post('/rooms', [
            'room_number'   => 'A101',
            'floor'         => 1,
            'type'          => TipeKamar::Standard->value,
            'size_m2'       => 12.5,
            'monthly_price' => 1000000,
            'deposit_price' => 500000,
            'status'        => StatusKamar::Available->value,
            'facilities'    => ['AC', 'WiFi'],
        ]);

        $response->assertRedirect('/rooms');
        $this->assertDatabaseHas('rooms', [
            'room_number' => 'A101',
            'type'        => TipeKamar::Standard->value,
        ]);
    }

    public function test_create_room_validation_fails_on_duplicate_room_number()
    {
        Room::factory()->create(['room_number' => 'B202']);
        
        $this->authenticate('admin');

        $response = $this->post('/rooms', [
            'room_number'   => 'B202', // Duplicate
            'floor'         => 2,
            'type'          => TipeKamar::Deluxe->value,
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

        $file1 = UploadedFile::fake()->image('photo1.jpg');
        $file2 = UploadedFile::fake()->image('photo2.png');

        $this->post('/rooms', [
            'room_number'   => 'A102',
            'floor'         => 1,
            'type'          => TipeKamar::Standard->value,
            'size_m2'       => 12,
            'monthly_price' => 1000000,
            'deposit_price' => 500000,
            'status'        => StatusKamar::Available->value,
            'photos'        => [$file1, $file2],
        ]);

        $room = Room::where('room_number', 'A102')->first();
        $this->assertCount(2, $room->photos);
        
        // Foto pertama harus jadi primary
        $this->assertTrue((bool)$room->photos[0]->is_primary);
        $this->assertFalse((bool)$room->photos[1]->is_primary);

        Storage::disk('public')->assertExists($room->photos[0]->file_path);
        Storage::disk('public')->assertExists($room->photos[1]->file_path);
    }

    public function test_user_can_update_room()
    {
        $room = Room::factory()->create(['room_number' => 'OLD101']);
        $this->authenticate('owner');

        $response = $this->patch("/rooms/{$room->id}", [
            'room_number'   => 'NEW101',
            'floor'         => $room->floor,
            'type'          => $room->type->value,
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

    public function test_user_can_delete_room_and_photos_are_deleted()
    {
        Storage::fake('public');
        $this->authenticate('admin');
        
        $room = Room::factory()->create(['room_number' => 'DEL101']);
        
        // Buat file fake di storage dan hubungkan ke room
        $file = UploadedFile::fake()->image('test.jpg');
        $path = $file->store('rooms', 'public');
        
        $room->photos()->create([
            'file_path' => $path,
            'is_primary' => true
        ]);

        Storage::disk('public')->assertExists($path);

        $response = $this->delete("/rooms/{$room->id}");
        
        $response->assertRedirect('/rooms');
        $this->assertDatabaseMissing('rooms', ['id' => $room->id]);
        $this->assertDatabaseMissing('room_photos', ['room_id' => $room->id]);
        
        // File fisik di storage juga harus terhapus
        Storage::disk('public')->assertMissing($path);
    }
}
