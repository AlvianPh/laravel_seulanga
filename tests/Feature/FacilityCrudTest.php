<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * FacilityCrudTest — memastikan CRUD fasilitas berjalan dengan benar,
 * termasuk penolakan hapus jika fasilitas masih digunakan kamar.
 */
class FacilityCrudTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(string $role = 'admin'): User
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);
        return $user;
    }

    public function test_guest_cannot_access_facilities()
    {
        $this->get('/facilities')->assertRedirect('/login');
    }

    public function test_admin_and_owner_can_access_facilities_index()
    {
        $this->authenticate('admin');
        $this->get('/facilities')->assertOk();

        $this->authenticate('owner');
        $this->get('/facilities')->assertOk();
    }

    public function test_admin_can_create_facility()
    {
        $this->authenticate('admin');

        $response = $this->post('/facilities', [
            'name' => 'Kolam Renang',
            'icon' => 'pool',
        ]);

        $response->assertRedirect('/facilities');
        $this->assertDatabaseHas('facilities', ['name' => 'Kolam Renang', 'icon' => 'pool']);
    }

    public function test_create_facility_fails_with_duplicate_name()
    {
        Facility::create(['name' => 'AC']);
        $this->authenticate('admin');

        $response = $this->post('/facilities', ['name' => 'AC']);

        $response->assertSessionHasErrors('name');
    }

    public function test_admin_can_update_facility()
    {
        $facility = Facility::create(['name' => 'Kipas Kuno']);
        $this->authenticate('admin');

        $response = $this->patch("/facilities/{$facility->id}", [
            'name' => 'Kipas Baru',
            'icon' => 'fan',
        ]);

        $response->assertRedirect('/facilities');
        $this->assertDatabaseHas('facilities', ['id' => $facility->id, 'name' => 'Kipas Baru']);
    }

    public function test_admin_can_delete_unused_facility()
    {
        $facility = Facility::create(['name' => 'Hapus Ini']);
        $this->authenticate('admin');

        $response = $this->delete("/facilities/{$facility->id}");

        $response->assertRedirect('/facilities');
        $this->assertDatabaseMissing('facilities', ['id' => $facility->id]);
    }

    public function test_cannot_delete_facility_used_by_rooms()
    {
        $roomType = RoomType::firstOrCreate(['name' => 'Standard'], ['default_price' => 1000000]);
        $room     = Room::factory()->create(['room_number' => 'Y001', 'room_type_id' => $roomType->id]);
        $facility = Facility::create(['name' => 'TV Tidak Boleh Hapus']);
        $room->facilities()->attach($facility->id);

        $this->authenticate('admin');

        $response = $this->delete("/facilities/{$facility->id}");

        // Harus redirect back dengan error
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('facilities', ['id' => $facility->id]);
    }
}
