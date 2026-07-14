<?php

namespace Tests\Feature;


use App\Enums\StatusPembayaran;
use App\Enums\StatusTagihan;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PaymentCrudTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(string $role = 'admin'): User
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);
        return $user;
    }

    private function createPendingInvoice(): Invoice
    {
        $room = Room::factory()->create(['room_number' => 'PAY_R1_' . uniqid()]);
        $tenant = Tenant::factory()->create();
        $contract = Contract::factory()->create(['room_id' => $room->id, 'tenant_id' => $tenant->id]);

        return Invoice::factory()->create([
            'room_id' => $room->id,
            'contract_id' => $contract->id,
            'tenant_id' => $tenant->id,
            'total_amount' => 1000000,
            'status' => StatusTagihan::Pending->value,
        ]);
    }

    public function test_payment_can_be_created_for_pending_invoice()
    {
        $this->authenticate('admin');
        $invoice = $this->createPendingInvoice();

        $response = $this->post('/payments', [
            'invoice_id'   => $invoice->id,
            'amount'       => 500000,
            'payment_date' => '2026-07-13',
            'payment_method_id' => \App\Models\PaymentMethod::firstOrCreate(['name'=>'Tunai'])->id,
        ]);

        $response->assertRedirect('/payments');
        $this->assertDatabaseHas('payments', [
            'invoice_id' => $invoice->id,
            'amount'     => 500000,
            'status'     => StatusPembayaran::Pending->value,
        ]);
    }

    public function test_payment_fails_for_paid_invoice()
    {
        $this->authenticate('admin');
        $invoice = $this->createPendingInvoice();
        $invoice->update(['status' => StatusTagihan::Paid]);

        $response = $this->post('/payments', [
            'invoice_id'   => $invoice->id,
            'amount'       => 500000,
            'payment_date' => '2026-07-13',
            'payment_method_id' => \App\Models\PaymentMethod::firstOrCreate(['name'=>'Tunai'])->id,
        ]);

        $response->assertSessionHasErrors('invoice_id');
    }

    public function test_proof_is_required_for_transfer()
    {
        $this->authenticate('admin');
        $invoice = $this->createPendingInvoice();

        $response = $this->post('/payments', [
            'invoice_id'   => $invoice->id,
            'amount'       => 500000,
            'payment_date' => '2026-07-13',
            'payment_method_id' => \App\Models\PaymentMethod::firstOrCreate(['name'=>'Transfer Bank'])->id,
        ]);

        $response->assertSessionHasErrors('proof_photo');
    }

    public function test_admin_cannot_access_verification_page()
    {
        $this->authenticate('admin');
        $invoice = $this->createPendingInvoice();
        
        $payment = Payment::factory()->create([
            'invoice_id' => $invoice->id,
            'tenant_id'  => $invoice->tenant_id,
            'status'     => StatusPembayaran::Pending->value,
        ]);

        $response = $this->get("/payments/{$payment->id}/verify");
        $response->assertForbidden(); // 403
    }

    public function test_owner_can_verify_payment_and_update_invoice_status()
    {
        $owner = $this->authenticate('owner');
        $invoice = $this->createPendingInvoice(); // total_amount = 1.000.000
        
        $payment1 = Payment::factory()->create([
            'invoice_id' => $invoice->id,
            'tenant_id'  => $invoice->tenant_id,
            'amount'     => 600000,
            'status'     => StatusPembayaran::Pending->value,
        ]);

        $payment2 = Payment::factory()->create([
            'invoice_id' => $invoice->id,
            'tenant_id'  => $invoice->tenant_id,
            'amount'     => 400000,
            'status'     => StatusPembayaran::Pending->value,
        ]);

        // Verifikasi payment 1
        $this->post("/payments/{$payment1->id}/verify", ['action' => 'verify']);
        
        // Cek DB payment 1 jadi verified, tp invoice masih pending
        $this->assertDatabaseHas('payments', ['id' => $payment1->id, 'status' => StatusPembayaran::Verified->value, 'verified_by' => $owner->id]);
        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => StatusTagihan::Pending->value]);

        // Verifikasi payment 2
        $this->post("/payments/{$payment2->id}/verify", ['action' => 'verify']);
        
        // Total verified sekarang 1.000.000 (>= total_amount), maka invoice harus jadi paid
        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => StatusTagihan::Paid->value]);
    }

    public function test_rejected_payment_does_not_affect_invoice_status()
    {
        $owner = $this->authenticate('owner');
        $invoice = $this->createPendingInvoice();
        
        $payment = Payment::factory()->create([
            'invoice_id' => $invoice->id,
            'tenant_id'  => $invoice->tenant_id,
            'amount'     => 1000000,
            'status'     => StatusPembayaran::Pending->value,
        ]);

        $this->post("/payments/{$payment->id}/verify", [
            'action' => 'reject',
            'notes'  => 'Bukti palsu'
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id, 
            'status' => StatusPembayaran::Rejected->value,
            'notes' => 'Bukti palsu'
        ]);
        
        // Invoice tetap pending
        $this->assertDatabaseHas('invoices', ['id' => $invoice->id, 'status' => StatusTagihan::Pending->value]);
    }
}
