<?php

namespace Tests\Feature;

use App\Enums\KategoriPengeluaran;
use App\Enums\StatusKamar;
use App\Enums\StatusKontrak;
use App\Enums\StatusPembayaran;
use App\Models\Contract;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(string $role = 'admin'): User
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);
        return $user;
    }

    public function test_guest_cannot_access_dashboard()
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_dashboard()
    {
        $this->authenticate('owner');
        $response = $this->get('/dashboard');
        $response->assertOk();
        $response->assertViewHasAll(['stats', 'financials', 'chartData']);
    }

    public function test_dashboard_metrics_calculation_is_correct()
    {
        $this->authenticate('owner');

        // Setup 2 Rooms: 1 available, 1 occupied
        Room::factory()->create(['status' => StatusKamar::Available->value, 'room_number' => 'DASH-01']);
        $roomOccupied = Room::factory()->create(['status' => StatusKamar::Occupied->value, 'room_number' => 'DASH-02']);

        // 1 Active Tenant
        $tenant = Tenant::factory()->create();
        $contract = Contract::factory()->create([
            'room_id' => $roomOccupied->id,
            'tenant_id' => $tenant->id,
            'status' => StatusKontrak::Active->value,
        ]);

        $invoice = Invoice::factory()->create([
            'room_id' => $roomOccupied->id,
            'contract_id' => $contract->id,
            'tenant_id' => $tenant->id,
            'total_amount' => 1000000,
        ]);

        // Payment Today = 1.000.000
        Payment::factory()->create([
            'invoice_id' => $invoice->id,
            'tenant_id' => $tenant->id,
            'amount' => 1000000,
            'status' => StatusPembayaran::Verified->value,
            'payment_date' => Carbon::now()->toDateString()
        ]);

        // Expense Today = 400.000
        Expense::factory()->create([
            'amount' => 400000,
            'category' => KategoriPengeluaran::Electricity->value,
            'expense_date' => Carbon::now()->toDateString()
        ]);

        $response = $this->get('/dashboard');
        $response->assertOk();

        // Check metrics
        $stats = $response->original->getData()['stats'];
        $financials = $response->original->getData()['financials'];

        // Rooms
        $this->assertEquals(2, $stats['totalRooms']);
        $this->assertEquals(1, $stats['availableRooms']);
        $this->assertEquals(1, $stats['occupiedRooms']);
        $this->assertEquals(50.0, $stats['occupancyRate']);

        // Tenants
        $this->assertEquals(1, $stats['activeTenants']);

        // Financials
        $this->assertEquals(1000000, $stats['paymentsToday']);
        $this->assertEquals(1000000, $financials['income']);
        $this->assertEquals(400000, $financials['expense']);
        $this->assertEquals(600000, $financials['profit']); // 1jt - 400rb
    }
}
