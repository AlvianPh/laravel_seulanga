<?php

namespace Tests\Feature;

use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * RoomTypeCrudTest — memastikan CRUD tipe kamar berjalan dengan benar,
 * termasuk penolakan hapus jika tipe masih digunakan kamar.
 */
class RoomTypeCrudTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(string $role = 'admin'): User
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);
        return $user;
    }

    public function test_guest_cannot_access_room_types()
    {
        $this->get('/room_types')->assertRedirect('/login');
    }

    public function test_admin_and_owner_can_access_room_types_index()
    {
        $this->authenticate('admin');
        $this->get('/room_types')->assertOk();

        $this->authenticate('owner');
        $this->get('/room_types')->assertOk();
    }

    public function test_admin_can_create_room_type()
    {
        $this->authenticate('admin');

        $response = $this->post('/room_types', [
            'name'          => 'VIP',
            'description'   => 'Kamar VIP eksklusif',
            'default_price' => 3000000,
        ]);

        $response->assertRedirect('/room_types');
        $this->assertDatabaseHas('room_types', ['name' => 'VIP']);
    }

    public function test_create_room_type_fails_with_duplicate_name()
    {
        RoomType::create(['name' => 'Premium']);
        $this->authenticate('admin');

        $response = $this->post('/room_types', ['name' => 'Premium']);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_room_type()
    {
        $roomType = RoomType::create(['name' => 'Biasa', 'description' => 'Lama']);
        $this->authenticate('admin');

        $response = $this->patch("/room_types/{$roomType->id}", [
            'name'        => 'Biasa Updated',
            'description' => 'Baru',
        ]);

        $response->assertRedirect('/room_types');
        $this->assertDatabaseHas('room_types', ['id' => $roomType->id, 'name' => 'Biasa Updated']);
    }

    public function test_admin_can_delete_unused_room_type()
    {
        $roomType = RoomType::create(['name' => 'Hapus Ini']);
        $this->authenticate('admin');

        $response = $this->delete("/room_types/{$roomType->id}");

        $response->assertRedirect('/room_types');
        $this->assertDatabaseMissing('room_types', ['id' => $roomType->id]);
    }

    public function test_cannot_delete_room_type_used_by_rooms()
    {
        $roomType = RoomType::create(['name' => 'Tidak Boleh Hapus', 'default_price' => 1000000]);
        Room::factory()->create(['room_number' => 'X001', 'room_type_id' => $roomType->id]);

        $this->authenticate('admin');

        $response = $this->delete("/room_types/{$roomType->id}");

        // Harus redirect back dengan error
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('room_types', ['id' => $roomType->id]);
    }
}
