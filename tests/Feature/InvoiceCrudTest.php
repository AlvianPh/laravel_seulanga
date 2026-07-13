<?php

namespace Tests\Feature;

use App\Enums\StatusKontrak;
use App\Enums\StatusTagihan;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use App\Services\GenerateInvoiceService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * InvoiceCrudTest — memastikan logika generate tagihan, hitung ulang fee,
 * dan pengecekan overdue berjalan dengan benar.
 */
class InvoiceCrudTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(string $role = 'admin'): User
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);
        return $user;
    }

    public function test_guest_cannot_access_invoices()
    {
        $this->get('/invoices')->assertRedirect('/login');
    }

    public function test_admin_can_access_invoices()
    {
        $this->authenticate('admin');
        $this->get('/invoices')->assertOk();
    }

    public function test_generate_invoice_service_creates_invoices_for_active_contracts()
    {
        // Setup 2 kontrak aktif, 1 ended
        $room1 = Room::factory()->create(['room_number' => 'INV_R1']);
        $room2 = Room::factory()->create(['room_number' => 'INV_R2']);
        $tenant1 = Tenant::factory()->create();
        $tenant2 = Tenant::factory()->create();

        Contract::factory()->create([
            'room_id' => $room1->id, 'tenant_id' => $tenant1->id,
            'status' => StatusKontrak::Active->value, 'rent_price' => 1000000
        ]);
        Contract::factory()->create([
            'room_id' => $room2->id, 'tenant_id' => $tenant2->id,
            'status' => StatusKontrak::Ended->value, 'rent_price' => 2000000
        ]);

        $service = app(GenerateInvoiceService::class);
        $count = $service->generateMonthlyInvoices(10, 2026);

        $this->assertEquals(1, $count);
        $this->assertDatabaseCount('invoices', 1);
        $this->assertDatabaseHas('invoices', [
            'month' => 10,
            'year'  => 2026,
            'rent_amount' => 1000000,
            'total_amount' => 1000000,
            'status' => StatusTagihan::Pending->value,
            'due_date' => '2026-10-10 00:00:00', // Laravel cast format
        ]);
    }

    public function test_generate_invoice_service_does_not_duplicate()
    {
        $room = Room::factory()->create(['room_number' => 'INV_R3']);
        $contract = Contract::factory()->create(['status' => StatusKontrak::Active->value, 'room_id' => $room->id]);

        $service = app(GenerateInvoiceService::class);
        
        // Panggil pertama kali
        $count1 = $service->generateMonthlyInvoices(11, 2026);
        $this->assertEquals(1, $count1);

        // Panggil kedua kali untuk bulan/tahun yang sama
        $count2 = $service->generateMonthlyInvoices(11, 2026);
        $this->assertEquals(0, $count2); // Skip, tidak duplikat

        $this->assertDatabaseCount('invoices', 1);
    }

    public function test_check_overdue_service_marks_past_due_invoices()
    {
        $room = Room::factory()->create(['room_number' => 'INV_R4']);
        $contract = Contract::factory()->create(['room_id' => $room->id]);
        
        // Tagihan pending tapi belum jatuh tempo
        Invoice::factory()->create([
            'room_id' => $room->id,
            'contract_id' => $contract->id,
            'month' => 6,
            'due_date' => Carbon::tomorrow(),
            'status' => StatusTagihan::Pending->value,
        ]);

        // Tagihan pending sudah lewat
        $overdueInvoice = Invoice::factory()->create([
            'room_id' => $room->id,
            'contract_id' => $contract->id,
            'month' => 5,
            'due_date' => Carbon::yesterday(),
            'status' => StatusTagihan::Pending->value,
        ]);

        $service = app(GenerateInvoiceService::class);
        $updated = $service->markOverdueInvoices();

        $this->assertEquals(1, $updated);

        // Pastikan yg kemarin jadi overdue
        $this->assertDatabaseHas('invoices', [
            'id' => $overdueInvoice->id,
            'status' => StatusTagihan::Overdue->value,
        ]);
    }

    public function test_updating_invoice_recalculates_total_amount()
    {
        $this->authenticate('admin');
        
        $room = Room::factory()->create(['room_number' => 'INV_R5']);
        $contract = Contract::factory()->create(['room_id' => $room->id]);
        
        $invoice = Invoice::factory()->create([
            'room_id' => $room->id,
            'contract_id' => $contract->id,
            'rent_amount' => 1000000,
            'electricity_fee' => 0,
            'total_amount' => 1000000,
            'status' => StatusTagihan::Pending->value,
        ]);

        $response = $this->patch("/invoices/{$invoice->id}", [
            'electricity_fee' => 150000,
            'water_fee'       => 50000,
            'status'          => StatusTagihan::Pending->value,
        ]);

        $response->assertRedirect('/invoices');

        // Total harus menjadi 1.200.000
        $this->assertDatabaseHas('invoices', [
            'id' => $invoice->id,
            'electricity_fee' => 150000,
            'water_fee'       => 50000,
            'total_amount'    => 1200000,
        ]);
    }

    public function test_manual_generate_button()
    {
        $this->authenticate('owner');
        $room = Room::factory()->create(['room_number' => 'INV_R6']);
        Contract::factory()->create(['status' => StatusKontrak::Active->value, 'room_id' => $room->id]);

        $response = $this->post('/invoices/generate-manual', [
            'month' => 5,
            'year'  => 2027,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('invoices', [
            'month' => 5,
            'year'  => 2027,
        ]);
    }
}
