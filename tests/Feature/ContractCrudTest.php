<?php

namespace Tests\Feature;

use App\Enums\StatusKamar;
use App\Enums\StatusKontrak;
use App\Models\Contract;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ContractCrudTest — memastikan business logic Kontrak berjalan dengan benar,
 * terutama sinkronisasi status kamar (available/occupied).
 */
class ContractCrudTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(string $role = 'admin'): User
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);
        return $user;
    }

    public function test_guest_cannot_access_contracts()
    {
        $this->get('/contracts')->assertRedirect('/login');
    }

    public function test_admin_and_owner_can_access_contracts_index()
    {
        $this->authenticate('admin');
        $this->get('/contracts')->assertOk();
    }

    public function test_cannot_create_contract_if_room_not_available()
    {
        $this->authenticate('admin');
        $room = Room::factory()->create(['status' => StatusKamar::Occupied->value, 'room_number' => 'OCC1']);
        $tenant = Tenant::factory()->create();

        $response = $this->post('/contracts', [
            'tenant_id'      => $tenant->id,
            'room_id'        => $room->id,
            'start_date'     => now()->format('Y-m-d'),
            'end_date'       => now()->addMonths(6)->format('Y-m-d'),
            'rent_price'     => 1500000,
            'deposit_amount' => 500000,
        ]);

        $response->assertSessionHasErrors('room_id');
        $this->assertDatabaseCount('contracts', 0);
    }

    public function test_create_contract_success_and_room_becomes_occupied()
    {
        $this->authenticate('admin');
        $room = Room::factory()->create(['status' => StatusKamar::Available->value, 'room_number' => 'AVA1']);
        $tenant = Tenant::factory()->create();

        $response = $this->post('/contracts', [
            'tenant_id'      => $tenant->id,
            'room_id'        => $room->id,
            'start_date'     => now()->format('Y-m-d'),
            'end_date'       => now()->addMonths(6)->format('Y-m-d'),
            'rent_price'     => 1500000,
            'deposit_amount' => 500000,
        ]);

        $response->assertRedirect('/contracts');
        
        $this->assertDatabaseHas('contracts', [
            'tenant_id' => $tenant->id,
            'room_id'   => $room->id,
            'status'    => StatusKontrak::Active->value,
        ]);

        $this->assertDatabaseHas('rooms', [
            'id'     => $room->id,
            'status' => StatusKamar::Occupied->value,
        ]);
    }

    public function test_terminate_contract_success_and_room_becomes_available()
    {
        $this->authenticate('owner');
        
        // Simulasi keadaan awal: Kamar occupied, Kontrak aktif
        $room = Room::factory()->create(['status' => StatusKamar::Occupied->value, 'room_number' => 'OCC2']);
        $tenant = Tenant::factory()->create();
        $contract = Contract::factory()->create([
            'room_id'   => $room->id,
            'tenant_id' => $tenant->id,
            'status'    => StatusKontrak::Active->value,
        ]);

        $response = $this->post("/contracts/{$contract->id}/terminate");

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('contracts', [
            'id'     => $contract->id,
            'status' => StatusKontrak::Terminated->value,
        ]);

        $this->assertDatabaseHas('rooms', [
            'id'     => $room->id,
            'status' => StatusKamar::Available->value,
        ]);
    }

    public function test_renew_contract_creates_new_contract_and_ends_old_one()
    {
        $user = $this->authenticate('admin');
        
        // Simulasi keadaan awal
        $room = Room::factory()->create(['status' => StatusKamar::Occupied->value, 'room_number' => 'OCC3']);
        $tenant = Tenant::factory()->create();
        $oldContract = Contract::factory()->create([
            'room_id'   => $room->id,
            'tenant_id' => $tenant->id,
            'status'    => StatusKontrak::Active->value,
            'start_date' => now()->subMonths(6)->format('Y-m-d'),
            'end_date'   => now()->format('Y-m-d'),
        ]);

        $newStartDate = now()->addDay()->format('Y-m-d');
        $newEndDate = now()->addMonths(6)->addDay()->format('Y-m-d');

        $response = $this->post("/contracts/{$oldContract->id}/renew", [
            'start_date'     => $newStartDate,
            'end_date'       => $newEndDate,
            'rent_price'     => 1600000,
            'deposit_amount' => 500000,
            'notes'          => 'Perpanjangan test',
        ]);

        $response->assertRedirect('/contracts');

        // Kontrak lama harus Ended
        $this->assertDatabaseHas('contracts', [
            'id'     => $oldContract->id,
            'status' => StatusKontrak::Ended->value,
        ]);

        // Kontrak baru harus dibuat dan Active
        $this->assertDatabaseHas('contracts', [
            'tenant_id'  => $tenant->id,
            'room_id'    => $room->id,
            'start_date' => $newStartDate . ' 00:00:00',
            'end_date'   => $newEndDate . ' 00:00:00',
            'rent_price' => 1600000,
            'status'     => StatusKontrak::Active->value,
            'created_by' => $user->id,
        ]);

        // Kamar tetap occupied
        $this->assertDatabaseHas('rooms', [
            'id'     => $room->id,
            'status' => StatusKamar::Occupied->value,
        ]);
    }

    public function test_validation_fails_if_end_date_before_start_date()
    {
        $this->authenticate('admin');
        $room = Room::factory()->create(['status' => StatusKamar::Available->value, 'room_number' => 'AVA2']);
        $tenant = Tenant::factory()->create();

        $response = $this->post('/contracts', [
            'tenant_id'      => $tenant->id,
            'room_id'        => $room->id,
            'start_date'     => now()->format('Y-m-d'),
            'end_date'       => now()->subDays(5)->format('Y-m-d'), // Selesai lebih awal dari mulai
            'rent_price'     => 1500000,
            'deposit_amount' => 500000,
        ]);

        $response->assertSessionHasErrors('end_date');
    }
}
