<?php

namespace Tests\Feature;

use App\Enums\KategoriPengeluaran;
use App\Enums\StatusKamar;
use App\Enums\StatusKontrak;
use App\Enums\StatusPembayaran;
use App\Enums\StatusTagihan;
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

class ReportTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(string $role = 'admin'): User
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);
        return $user;
    }

    private function setupData()
    {
        // 1. Kamar & Tenant
        $room = Room::factory()->create(['status' => StatusKamar::Occupied->value, 'room_number' => 'RPT-01']);
        $tenant = Tenant::factory()->create();
        $contract = Contract::factory()->create([
            'room_id' => $room->id,
            'tenant_id' => $tenant->id,
            'status' => StatusKontrak::Active->value,
        ]);

        // 2. Invoice Paid & Payment
        $invoicePaid = Invoice::factory()->create([
            'room_id' => $room->id,
            'contract_id' => $contract->id,
            'tenant_id' => $tenant->id,
            'total_amount' => 1500000,
            'status' => StatusTagihan::Paid->value,
            'month' => 6,
            'year' => 2026,
        ]);
        Payment::factory()->create([
            'invoice_id' => $invoicePaid->id,
            'tenant_id' => $tenant->id,
            'amount' => 1500000,
            'status' => StatusPembayaran::Verified->value,
            'payment_date' => Carbon::now()->startOfMonth()->addDays(2)
        ]);

        // 3. Invoice Pending (Receivable)
        $invoicePending = Invoice::factory()->create([
            'room_id' => $room->id,
            'contract_id' => $contract->id,
            'tenant_id' => $tenant->id,
            'total_amount' => 1200000,
            'status' => StatusTagihan::Pending->value,
            'month' => 7,
            'year' => 2026,
        ]);

        // 4. Expense
        Expense::factory()->create([
            'amount' => 500000,
            'expense_category_id' => \App\Models\ExpenseCategory::firstOrCreate(['name'=>'Listrik'])->id,
            'expense_date' => Carbon::now()->startOfMonth()->addDays(5)
        ]);
    }

    public function test_guest_cannot_access_reports()
    {
        $this->get('/reports')->assertRedirect('/login');
    }

    public function test_authenticated_user_can_access_reports()
    {
        $this->authenticate('owner');
        $this->get('/reports')->assertOk()->assertViewIs('reports.index');
    }

    public function test_generate_profit_loss_report_view()
    {
        $this->authenticate('admin');
        $this->setupData();

        $response = $this->post('/reports/generate', [
            'type' => 'profit_loss',
            'filter' => 'monthly',
            'action' => 'view'
        ]);

        $response->assertOk();
        $response->assertViewIs('reports.results');

        // Net profit should be 1.500.000 - 500.000 = 1.000.000
        $data = $response->original->getData()['data'];
        $this->assertEquals(1000000, $data['report']['net_profit']);
    }

    public function test_generate_receivables_report()
    {
        $this->authenticate('admin');
        $this->setupData();

        $response = $this->post('/reports/generate', [
            'type' => 'receivables',
            'filter' => 'monthly',
            'action' => 'view'
        ]);

        $data = $response->original->getData()['data'];
        
        // Should contain 1 pending invoice of 1.200.000
        $this->assertCount(1, $data['records']);
        $this->assertEquals(1200000, $data['records']->first()->total_amount);
    }

    public function test_export_pdf_returns_pdf_file()
    {
        $this->authenticate('owner');
        $this->setupData();

        $response = $this->post('/reports/generate', [
            'type' => 'income',
            'filter' => 'monthly',
            'action' => 'pdf'
        ]);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_export_excel_returns_xlsx_file()
    {
        $this->authenticate('admin');
        $this->setupData();

        $response = $this->post('/reports/generate', [
            'type' => 'expense',
            'filter' => 'yearly',
            'action' => 'excel'
        ]);

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}
