<?php

namespace Tests\Feature;

use App\Enums\RoleUser;
use App\Enums\StatusKontrak;
use App\Enums\StatusPembayaran;
use App\Enums\StatusTagihan;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use App\Services\ContractService;
use App\Services\VerifyPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TenantInvoicePaymentTest extends TestCase
{
    use RefreshDatabase;

    private function createTenantUser(): array
    {
        $user = User::factory()->create(['role' => RoleUser::Tenant]);
        $tenant = Tenant::factory()->create(['user_id' => $user->id]);

        return [$user, $tenant];
    }

    private function createStaffUser(RoleUser $role = RoleUser::Admin): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function createInvoiceForTenant(Tenant $tenant, float $amount = 1500000, StatusTagihan $status = StatusTagihan::Pending): Invoice
    {
        $room = Room::factory()->create(['room_number' => 'R-'.uniqid()]);
        $contract = Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusKontrak::Draft,
        ]);

        return Invoice::factory()->create([
            'contract_id' => $contract->id,
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'year' => 2026,
            'month' => 8,
            'rent_amount' => $amount,
            'total_amount' => $amount,
            'status' => $status,
            'due_date' => now()->addDays(10),
        ]);
    }

    // ─── 1. Invoice Ownership Tests ──────────────────────────────────────────

    public function test_tenant_can_view_own_invoices_list_and_detail(): void
    {
        [$userA, $tenantA] = $this->createTenantUser();
        $invoiceA = $this->createInvoiceForTenant($tenantA);

        $responseList = $this->actingAs($userA)->get(route('portal.invoices.index'));
        $responseList->assertOk();
        $responseList->assertSee('Tagihan');
        $responseList->assertSee('#INV-202608-'.str_pad((string) $invoiceA->id, 4, '0', STR_PAD_LEFT));

        $responseDetail = $this->actingAs($userA)->get(route('portal.invoices.show', $invoiceA));
        $responseDetail->assertOk();
        $responseDetail->assertSee(number_format($invoiceA->total_amount, 0, ',', '.'));
    }

    public function test_tenant_cannot_view_other_tenants_invoice(): void
    {
        [$userA, $tenantA] = $this->createTenantUser();
        [$userB, $tenantB] = $this->createTenantUser();
        $invoiceB = $this->createInvoiceForTenant($tenantB);

        $response = $this->actingAs($userA)->get(route('portal.invoices.show', $invoiceB));
        $response->assertForbidden();
    }

    public function test_invoice_id_manipulation_cannot_bypass_ownership(): void
    {
        [$userA, $tenantA] = $this->createTenantUser();
        [$userB, $tenantB] = $this->createTenantUser();
        $invoiceB = $this->createInvoiceForTenant($tenantB);

        $response = $this->actingAs($userA)->get("/portal/invoices/{$invoiceB->id}");
        $response->assertForbidden();
    }

    // ─── 2. Payment Submission & Security Tests ──────────────────────────────

    public function test_tenant_can_submit_payment_for_own_invoice(): void
    {
        Storage::fake('public');
        [$userA, $tenantA] = $this->createTenantUser();
        $invoiceA = $this->createInvoiceForTenant($tenantA, 1500000);
        $method = PaymentMethod::factory()->create(['name' => 'BCA Transfer']);

        $file = UploadedFile::fake()->image('bukti_transfer.jpg', 600, 600);

        $response = $this->actingAs($userA)->post(route('portal.invoices.payments.store', $invoiceA), [
            'amount' => 1500000,
            'payment_method_id' => $method->id,
            'payment_date' => now()->toDateString(),
            'proof_photo' => $file,
            'notes' => 'Transfer dari rekening BCA Budi',
        ]);

        $response->assertRedirect(route('portal.invoices.show', $invoiceA));
        $response->assertSessionHas('status');

        $this->assertDatabaseHas('payments', [
            'invoice_id' => $invoiceA->id,
            'tenant_id' => $tenantA->id,
            'amount' => 1500000,
            'payment_method_id' => $method->id,
            'status' => StatusPembayaran::Pending->value,
            'verified_by' => null,
            'notes' => 'Transfer dari rekening BCA Budi',
        ]);

        $payment = Payment::where('invoice_id', $invoiceA->id)->first();
        $this->assertNotNull($payment->proof_path);
        Storage::disk('public')->assertExists($payment->proof_path);
    }

    public function test_tenant_cannot_submit_payment_for_other_tenants_invoice(): void
    {
        Storage::fake('public');
        [$userA, $tenantA] = $this->createTenantUser();
        [$userB, $tenantB] = $this->createTenantUser();
        $invoiceB = $this->createInvoiceForTenant($tenantB, 1500000);
        $method = PaymentMethod::factory()->create();

        $file = UploadedFile::fake()->image('bukti.jpg');

        $response = $this->actingAs($userA)->post(route('portal.invoices.payments.store', $invoiceB), [
            'amount' => 1500000,
            'payment_method_id' => $method->id,
            'payment_date' => now()->toDateString(),
            'proof_photo' => $file,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('payments', 0);
    }

    public function test_payment_creation_ignores_client_tampering_of_tenant_and_verified_fields(): void
    {
        Storage::fake('public');
        [$userA, $tenantA] = $this->createTenantUser();
        [$userB, $tenantB] = $this->createTenantUser();
        $invoiceA = $this->createInvoiceForTenant($tenantA, 1000000);
        $method = PaymentMethod::factory()->create();

        $file = UploadedFile::fake()->image('bukti.png');

        $response = $this->actingAs($userA)->post(route('portal.invoices.payments.store', $invoiceA), [
            'tenant_id' => $tenantB->id, // client mencoba manipulasi tenant
            'status' => StatusPembayaran::Verified->value, // client mencoba inject verified
            'verified_by' => 999, // client mencoba isi verifier
            'amount' => 1000000,
            'payment_method_id' => $method->id,
            'payment_date' => now()->toDateString(),
            'proof_photo' => $file,
        ]);

        $response->assertRedirect(route('portal.invoices.show', $invoiceA));

        // Server harus enforce tenant_id asli dan status Pending
        $this->assertDatabaseHas('payments', [
            'invoice_id' => $invoiceA->id,
            'tenant_id' => $tenantA->id, // BUKAN tenantB
            'status' => StatusPembayaran::Pending->value, // BUKAN verified
            'verified_by' => null, // BUKAN 999
        ]);
    }

    public function test_tenant_cannot_submit_payment_exceeding_remaining_balance(): void
    {
        Storage::fake('public');
        [$userA, $tenantA] = $this->createTenantUser();
        $invoiceA = $this->createInvoiceForTenant($tenantA, 1000000); // 1.000.000

        // Ada pembayaran Rp300.000 yang sudah verified
        Payment::factory()->create([
            'invoice_id' => $invoiceA->id,
            'tenant_id' => $tenantA->id,
            'amount' => 300000,
            'status' => StatusPembayaran::Verified,
        ]);

        $this->assertEquals(700000, $invoiceA->remainingBalance());

        $method = PaymentMethod::factory()->create();
        $file = UploadedFile::fake()->image('bukti.jpg');

        // Submit over-balance: Rp 700.001 -> Ditolak
        $responseOver = $this->actingAs($userA)->post(route('portal.invoices.payments.store', $invoiceA), [
            'amount' => 700001,
            'payment_method_id' => $method->id,
            'payment_date' => now()->toDateString(),
            'proof_photo' => $file,
        ]);

        $responseOver->assertSessionHasErrors('amount');

        // Submit over-balance: Rp 1.000.000 -> Ditolak
        $responseTotal = $this->actingAs($userA)->post(route('portal.invoices.payments.store', $invoiceA), [
            'amount' => 1000000,
            'payment_method_id' => $method->id,
            'payment_date' => now()->toDateString(),
            'proof_photo' => $file,
        ]);

        $responseTotal->assertSessionHasErrors('amount');

        // Submit pas balance: Rp 700.000 -> Diterima
        $responseValid = $this->actingAs($userA)->post(route('portal.invoices.payments.store', $invoiceA), [
            'amount' => 700000,
            'payment_method_id' => $method->id,
            'payment_date' => now()->toDateString(),
            'proof_photo' => $file,
        ]);

        $responseValid->assertSessionHasNoErrors();
        $responseValid->assertRedirect(route('portal.invoices.show', $invoiceA));
    }

    // ─── 3. Staff Verification & Policy Tests ────────────────────────────────

    public function test_pending_tenant_payment_appears_in_staff_payments_index(): void
    {
        [$userA, $tenantA] = $this->createTenantUser();
        $invoiceA = $this->createInvoiceForTenant($tenantA, 1000000);
        $method = PaymentMethod::factory()->create();

        $payment = Payment::factory()->create([
            'invoice_id' => $invoiceA->id,
            'tenant_id' => $tenantA->id,
            'amount' => 1000000,
            'status' => StatusPembayaran::Pending,
            'payment_method_id' => $method->id,
        ]);

        $admin = $this->createStaffUser(RoleUser::Admin);
        $response = $this->actingAs($admin)->get(route('payments.index'));
        $response->assertOk();
        $response->assertSee($tenantA->name);
    }

    public function test_admin_can_verify_pending_payment(): void
    {
        [$userA, $tenantA] = $this->createTenantUser();
        $invoiceA = $this->createInvoiceForTenant($tenantA, 1000000);
        $payment = Payment::factory()->create([
            'invoice_id' => $invoiceA->id,
            'tenant_id' => $tenantA->id,
            'amount' => 1000000,
            'status' => StatusPembayaran::Pending,
        ]);

        $admin = $this->createStaffUser(RoleUser::Admin);

        $response = $this->actingAs($admin)->post(route('payments.process-verification', $payment), [
            'action' => 'verify',
        ]);

        $response->assertRedirect(route('payments.index'));

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => StatusPembayaran::Verified->value,
            'verified_by' => $admin->id,
        ]);

        $this->assertDatabaseHas('invoices', [
            'id' => $invoiceA->id,
            'status' => StatusTagihan::Paid->value,
        ]);
    }

    public function test_owner_can_verify_pending_payment(): void
    {
        [$userA, $tenantA] = $this->createTenantUser();
        $invoiceA = $this->createInvoiceForTenant($tenantA, 1000000);
        $payment = Payment::factory()->create([
            'invoice_id' => $invoiceA->id,
            'tenant_id' => $tenantA->id,
            'amount' => 1000000,
            'status' => StatusPembayaran::Pending,
        ]);

        $owner = $this->createStaffUser(RoleUser::Owner);

        $response = $this->actingAs($owner)->post(route('payments.process-verification', $payment), [
            'action' => 'verify',
        ]);

        $response->assertRedirect(route('payments.index'));

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => StatusPembayaran::Verified->value,
            'verified_by' => $owner->id,
        ]);

        // Invoice harus otomatis menjadi Paid
        $this->assertDatabaseHas('invoices', [
            'id' => $invoiceA->id,
            'status' => StatusTagihan::Paid->value,
        ]);
    }

    public function test_tenant_cannot_verify_payment(): void
    {
        [$userA, $tenantA] = $this->createTenantUser();
        $invoiceA = $this->createInvoiceForTenant($tenantA, 1000000);
        $payment = Payment::factory()->create([
            'invoice_id' => $invoiceA->id,
            'tenant_id' => $tenantA->id,
            'amount' => 1000000,
            'status' => StatusPembayaran::Pending,
        ]);

        // Tenant mengakses staff verification route
        $response = $this->actingAs($userA)->post(route('payments.process-verification', $payment), [
            'action' => 'verify',
        ]);

        // Terblokir oleh EnsureStaff middleware / Policy
        $response->assertForbidden();
    }

    public function test_rejected_payment_does_not_reduce_invoice_balance(): void
    {
        [$userA, $tenantA] = $this->createTenantUser();
        $invoiceA = $this->createInvoiceForTenant($tenantA, 1000000);
        $payment = Payment::factory()->create([
            'invoice_id' => $invoiceA->id,
            'tenant_id' => $tenantA->id,
            'amount' => 1000000,
            'status' => StatusPembayaran::Pending,
        ]);

        $owner = $this->createStaffUser(RoleUser::Owner);
        $this->actingAs($owner)->post(route('payments.process-verification', $payment), [
            'action' => 'reject',
            'notes' => 'Bukti mutasi tidak valid',
        ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => StatusPembayaran::Rejected->value,
            'notes' => 'Bukti mutasi tidak valid',
        ]);

        $invoiceA->refresh();
        $this->assertEquals(0, $invoiceA->paidAmount());
        $this->assertEquals(1000000, $invoiceA->remainingBalance());
        $this->assertEquals(StatusTagihan::Pending, $invoiceA->status);
    }

    // ─── 4. Partial Payment Calculation Tests ────────────────────────────────

    public function test_partial_payment_updates_invoice_to_partially_paid(): void
    {
        [$userA, $tenantA] = $this->createTenantUser();
        $invoiceA = $this->createInvoiceForTenant($tenantA, 1500000); // Total 1.5jt

        $payment1 = Payment::factory()->create([
            'invoice_id' => $invoiceA->id,
            'tenant_id' => $tenantA->id,
            'amount' => 500000,
            'status' => StatusPembayaran::Pending,
        ]);

        $owner = $this->createStaffUser(RoleUser::Owner);
        $verifyService = app(VerifyPaymentService::class);
        $verifyService->verify($payment1, $owner->id);

        $invoiceA->refresh();
        $this->assertEquals(500000, $invoiceA->paidAmount());
        $this->assertEquals(1000000, $invoiceA->remainingBalance());
        $this->assertEquals(StatusTagihan::PartiallyPaid, $invoiceA->status);
        $this->assertTrue($invoiceA->isPartiallyPaid());
        $this->assertFalse($invoiceA->isPaid());
    }

    public function test_multiple_partial_payments_accumulate_correctly_to_fully_paid(): void
    {
        [$userA, $tenantA] = $this->createTenantUser();
        $invoiceA = $this->createInvoiceForTenant($tenantA, 1500000);

        $payment1 = Payment::factory()->create([
            'invoice_id' => $invoiceA->id,
            'tenant_id' => $tenantA->id,
            'amount' => 1000000,
            'status' => StatusPembayaran::Pending,
        ]);
        $payment2 = Payment::factory()->create([
            'invoice_id' => $invoiceA->id,
            'tenant_id' => $tenantA->id,
            'amount' => 500000,
            'status' => StatusPembayaran::Pending,
        ]);

        $owner = $this->createStaffUser(RoleUser::Owner);
        $verifyService = app(VerifyPaymentService::class);

        // Verifikasi payment 1 -> status partially_paid
        $verifyService->verify($payment1, $owner->id);
        $invoiceA->refresh();
        $this->assertEquals(StatusTagihan::PartiallyPaid, $invoiceA->status);
        $this->assertEquals(500000, $invoiceA->remainingBalance());

        // Verifikasi payment 2 -> status paid
        $verifyService->verify($payment2, $owner->id);
        $invoiceA->refresh();
        $this->assertEquals(StatusTagihan::Paid, $invoiceA->status);
        $this->assertEquals(1500000, $invoiceA->paidAmount());
        $this->assertEquals(0, $invoiceA->remainingBalance());
        $this->assertTrue($invoiceA->isPaid());
    }

    public function test_pending_and_rejected_payments_do_not_count_towards_paid_amount(): void
    {
        [$userA, $tenantA] = $this->createTenantUser();
        $invoiceA = $this->createInvoiceForTenant($tenantA, 1000000);

        Payment::factory()->create([
            'invoice_id' => $invoiceA->id,
            'tenant_id' => $tenantA->id,
            'amount' => 500000,
            'status' => StatusPembayaran::Pending,
        ]);
        Payment::factory()->create([
            'invoice_id' => $invoiceA->id,
            'tenant_id' => $tenantA->id,
            'amount' => 500000,
            'status' => StatusPembayaran::Rejected,
        ]);

        $this->assertEquals(0, $invoiceA->paidAmount());
        $this->assertEquals(1000000, $invoiceA->remainingBalance());
    }

    // ─── 5. Receipt Access & Isolation Tests ─────────────────────────────────

    public function test_tenant_can_view_receipt_for_own_verified_payment(): void
    {
        [$userA, $tenantA] = $this->createTenantUser();
        $invoiceA = $this->createInvoiceForTenant($tenantA, 1000000);
        $payment = Payment::factory()->create([
            'invoice_id' => $invoiceA->id,
            'tenant_id' => $tenantA->id,
            'amount' => 1000000,
            'status' => StatusPembayaran::Verified,
            'verified_by' => $this->createStaffUser(RoleUser::Owner)->id,
        ]);

        $response = $this->actingAs($userA)->get(route('portal.payments.receipt', $payment));
        $response->assertOk();
        $response->assertSee('Kuitansi Pembayaran');
        $response->assertSee($payment->receiptNumber());
        $response->assertSee($tenantA->name);
    }

    public function test_tenant_cannot_view_receipt_for_other_tenants_payment(): void
    {
        [$userA, $tenantA] = $this->createTenantUser();
        [$userB, $tenantB] = $this->createTenantUser();
        $invoiceB = $this->createInvoiceForTenant($tenantB, 1000000);
        $paymentB = Payment::factory()->create([
            'invoice_id' => $invoiceB->id,
            'tenant_id' => $tenantB->id,
            'amount' => 1000000,
            'status' => StatusPembayaran::Verified,
        ]);

        $response = $this->actingAs($userA)->get(route('portal.payments.receipt', $paymentB));
        $response->assertForbidden();
    }

    public function test_receipt_cannot_be_viewed_if_payment_is_not_verified(): void
    {
        [$userA, $tenantA] = $this->createTenantUser();
        $invoiceA = $this->createInvoiceForTenant($tenantA, 1000000);
        $paymentPending = Payment::factory()->create([
            'invoice_id' => $invoiceA->id,
            'tenant_id' => $tenantA->id,
            'amount' => 1000000,
            'status' => StatusPembayaran::Pending,
        ]);

        $response = $this->actingAs($userA)->get(route('portal.payments.receipt', $paymentPending));
        $response->assertForbidden();
    }

    // ─── 6. F2.3 Contract Onboarding Integration Tests ───────────────────────

    public function test_verified_initial_payment_satisfies_f2_3_contract_prerequisite(): void
    {
        [$userA, $tenantA] = $this->createTenantUser();
        $room = Room::factory()->create(['room_number' => 'R-ONB-1']);
        $contract = Contract::factory()->create([
            'tenant_id' => $tenantA->id,
            'room_id' => $room->id,
            'status' => StatusKontrak::Draft,
            'agreement_accepted_at' => now(),
            'agreement_accepted_by' => $userA->id,
        ]);

        $initialInvoice = Invoice::factory()->create([
            'contract_id' => $contract->id,
            'tenant_id' => $tenantA->id,
            'room_id' => $room->id,
            'year' => 2026,
            'month' => 9,
            'rent_amount' => 1200000,
            'total_amount' => 1200000,
            'status' => StatusTagihan::Pending,
        ]);

        $this->assertFalse($contract->hasVerifiedInitialPayment());

        // Buat pembayaran dan verifikasi
        $payment = Payment::factory()->create([
            'invoice_id' => $initialInvoice->id,
            'tenant_id' => $tenantA->id,
            'amount' => 1200000,
            'status' => StatusPembayaran::Pending,
        ]);

        $owner = $this->createStaffUser(RoleUser::Owner);
        app(VerifyPaymentService::class)->verify($payment, $owner->id);

        $contract->refresh();
        $this->assertTrue($contract->hasVerifiedInitialPayment());

        // Verifikasi pembayaran TIDAK otomatis mengaktifkan kontrak atau mengubah room jadi occupied
        $this->assertEquals(StatusKontrak::Draft, $contract->status);
        $room->refresh();
        $this->assertNotEquals('occupied', $room->status->value);

        // Staff kemudian dapat mengaktifkan kontrak secara formal
        $contractService = app(ContractService::class);
        $activeContract = $contractService->activateContract($contract, $owner->id);
        $this->assertEquals(StatusKontrak::Active, $activeContract->status);
        $room->refresh();
        $this->assertEquals('occupied', $room->status->value);
    }
}
