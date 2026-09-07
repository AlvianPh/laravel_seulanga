<?php

namespace Tests\Feature;

use App\Enums\KategoriMaintenance;
use App\Enums\PermissionStatus;
use App\Enums\PermissionType;
use App\Enums\PrioritasMaintenance;
use App\Enums\RoleUser;
use App\Enums\StatusApplication;
use App\Enums\StatusKamar;
use App\Enums\StatusKontrak;
use App\Enums\StatusMaintenance;
use App\Enums\StatusMoveOut;
use App\Enums\StatusPembayaran;
use App\Enums\StatusTagihan;
use App\Models\Contract;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Invoice;
use App\Models\MaintenanceRequest;
use App\Models\MoveOutRequest;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\TenantApplication;
use App\Models\TenantDocument;
use App\Models\TenantPermission;
use App\Models\User;
use App\Services\MaintenanceRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CrossModuleFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected User $admin;

    protected PaymentMethod $paymentMethod;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
        Storage::fake('local');

        $this->owner = User::factory()->create(['role' => RoleUser::Owner]);
        $this->admin = User::factory()->create(['role' => RoleUser::Admin]);
        $this->paymentMethod = PaymentMethod::factory()->create();
    }

    /**
     * FLOW 1 — NEW TENANT ONBOARDING E2E
     * Register/User -> Browse Room -> Apply -> Staff Approve -> Contract Draft -> Accept Agreement -> Pay Initial Invoice -> Verify -> Activate -> Room Occupied
     */
    public function test_flow_1_new_tenant_onboarding_e2e(): void
    {
        $room = Room::factory()->create(['status' => StatusKamar::Available, 'monthly_price' => 1500000]);
        $user = User::factory()->create(['role' => RoleUser::Tenant]);
        $tenant = Tenant::factory()->create(['user_id' => $user->id]);

        // 1. Tenant applies for room
        $resApply = $this->actingAs($user)->post(route('portal.applications.store'), [
            'room_id' => $room->id,
            'application_notes' => 'Mau sewa 6 bulan',
        ]);
        $resApply->assertRedirect(route('portal.applications.index'));
        $application = TenantApplication::where('tenant_id', $tenant->id)->first();
        $this->assertNotNull($application);
        $this->assertSame(StatusApplication::Pending, $application->status);

        // 2. Staff approves application
        $resApprove = $this->actingAs($this->admin)->post(route('tenant-applications.review', $application), [
            'action' => 'approve',
        ]);
        $resApprove->assertRedirect(route('tenant-applications.show', $application));
        $application->refresh();
        $this->assertSame(StatusApplication::Approved, $application->status);

        // 3. Staff creates draft contract
        $resContract = $this->actingAs($this->admin)->post(route('contracts.store'), [
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'application_id' => $application->id,
            'start_date' => now()->addDay()->toDateString(),
            'end_date' => now()->addMonths(6)->toDateString(),
            'rent_price' => 1500000,
            'deposit_amount' => 500000,
            'is_draft' => 1,
        ]);
        $resContract->assertRedirect();
        $contract = Contract::where('tenant_id', $tenant->id)->first();
        $this->assertNotNull($contract);
        $this->assertSame(StatusKontrak::Draft, $contract->status);

        // Verify initial invoice created
        $initialInvoice = Invoice::where('contract_id', $contract->id)->first();
        $this->assertNotNull($initialInvoice);

        // 4. Tenant accepts digital agreement
        $resAgree = $this->actingAs($user)->post(route('portal.contract.agreement', $contract), [
            'terms_accepted' => '1',
        ]);
        $resAgree->assertRedirect(route('portal.contract.index'));
        $contract->refresh();
        $this->assertTrue($contract->isAgreementAccepted());

        // 5. Tenant pays initial invoice
        $proof = UploadedFile::fake()->create('proof_initial.jpg', 200, 'image/jpeg');
        $resPay = $this->actingAs($user)->post(route('portal.invoices.payments.store', $initialInvoice), [
            'amount' => $initialInvoice->total_amount,
            'payment_date' => now()->toDateString(),
            'payment_method_id' => $this->paymentMethod->id,
            'proof_photo' => $proof,
        ]);
        $resPay->assertRedirect(route('portal.invoices.show', $initialInvoice));
        $payment = Payment::where('invoice_id', $initialInvoice->id)->first();
        $this->assertNotNull($payment);
        $this->assertSame(StatusPembayaran::Pending, $payment->status);

        // 6. Staff verifies payment
        $resVerify = $this->actingAs($this->admin)->post(route('payments.process-verification', $payment), [
            'action' => 'verify',
        ]);
        $resVerify->assertRedirect(route('payments.index'));
        $payment->refresh();
        $initialInvoice->refresh();
        $this->assertSame(StatusPembayaran::Verified, $payment->status);
        $this->assertSame(StatusTagihan::Paid, $initialInvoice->status);

        // 7. Staff activates contract
        $resActivate = $this->actingAs($this->admin)->post(route('contracts.activate', $contract));
        $resActivate->assertRedirect(route('contracts.show', $contract));
        $contract->refresh();
        $room->refresh();
        $this->assertSame(StatusKontrak::Active, $contract->status);
        $this->assertSame(StatusKamar::Occupied, $room->status);
    }

    /**
     * FLOW 2 — MONTHLY TENANCY E2E
     * Active Contract -> Monthly Invoice -> Tenant pays -> Staff verifies -> Invoice Paid
     */
    public function test_flow_2_monthly_tenancy_e2e(): void
    {
        $room = Room::factory()->create(['status' => StatusKamar::Occupied]);
        $user = User::factory()->create(['role' => RoleUser::Tenant]);
        $tenant = Tenant::factory()->create(['user_id' => $user->id]);
        $contract = Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusKontrak::Active,
            'rent_price' => 1500000,
        ]);

        // Create monthly invoice
        $invoice = Invoice::create([
            'contract_id' => $contract->id,
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'invoice_number' => 'INV-2026-TEST',
            'month' => 9,
            'year' => 2026,
            'rent_amount' => 1500000,
            'base_rent' => 1500000,
            'deposit_fee' => 0,
            'total_amount' => 1500000,
            'due_date' => now()->addDays(5)->toDateString(),
            'status' => StatusTagihan::Pending,
        ]);

        // Tenant pays invoice
        $proof = UploadedFile::fake()->create('proof_monthly.jpg', 200, 'image/jpeg');
        $this->actingAs($user)->post(route('portal.invoices.payments.store', $invoice), [
            'amount' => 1500000,
            'payment_date' => now()->toDateString(),
            'payment_method_id' => $this->paymentMethod->id,
            'proof_photo' => $proof,
        ]);

        $payment = Payment::where('invoice_id', $invoice->id)->first();
        $this->assertNotNull($payment);

        // Admin verifies
        $this->actingAs($this->admin)->post(route('payments.process-verification', $payment), [
            'action' => 'verify',
        ]);

        $payment->refresh();
        $invoice->refresh();
        $this->assertSame(StatusPembayaran::Verified, $payment->status);
        $this->assertSame(StatusTagihan::Paid, $invoice->status);
    }

    /**
     * FLOW 3 — MAINTENANCE E2E
     * Active Tenant -> Create Request -> Staff In Progress -> Resolved -> Operational Expense
     */
    public function test_flow_3_maintenance_request_e2e(): void
    {
        $room = Room::factory()->create(['status' => StatusKamar::Occupied]);
        $user = User::factory()->create(['role' => RoleUser::Tenant]);
        $tenant = Tenant::factory()->create(['user_id' => $user->id]);
        $contract = Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusKontrak::Active,
        ]);

        // 1. Tenant reports issue
        $resStore = $this->actingAs($user)->post(route('portal.maintenance.store'), [
            'category' => KategoriMaintenance::Plumbing->value,
            'priority' => PrioritasMaintenance::High->value,
            'location' => 'Kamar Mandi',
            'description' => 'Kran air patah dan bocor deras',
        ]);
        $maintenance = MaintenanceRequest::where('tenant_id', $tenant->id)->first();
        $this->assertNotNull($maintenance);
        $resStore->assertRedirect(route('portal.maintenance.show', $maintenance));
        $this->assertSame(StatusMaintenance::Pending, $maintenance->status);

        // 2. Staff marks in progress
        $this->actingAs($this->admin)->patch(route('maintenance.update-status', $maintenance), [
            'status' => StatusMaintenance::InProgress->value,
            'staff_notes' => 'Tukang sedang membeli kran pengganti',
        ]);
        $maintenance->refresh();
        $this->assertSame(StatusMaintenance::InProgress, $maintenance->status);

        // 3. Staff resolves issue
        $this->actingAs($this->admin)->patch(route('maintenance.update-status', $maintenance), [
            'status' => StatusMaintenance::Resolved->value,
            'staff_notes' => 'Kran baru sudah terpasang dengan baik',
        ]);
        $maintenance->refresh();
        $this->assertSame(StatusMaintenance::Resolved, $maintenance->status);

        // 4. Staff logs operational expense linked to maintenance request
        $category = ExpenseCategory::create(['name' => 'Pemeliharaan']);
        Expense::create([
            'expense_category_id' => $category->id,
            'maintenance_request_id' => $maintenance->id,
            'amount' => 75000,
            'expense_date' => now()->toDateString(),
            'description' => 'Beli kran stainless kamar mandi',
            'created_by' => $this->admin->id,
        ]);

        $this->assertDatabaseHas('expenses', [
            'maintenance_request_id' => $maintenance->id,
            'amount' => 75000,
        ]);
    }

    /**
     * FLOW 4 — PERMISSION REQUEST E2E
     * Active Tenant -> Create Permission Request -> Staff Reviews -> Approve/Reject
     */
    public function test_flow_4_permission_request_e2e(): void
    {
        $room = Room::factory()->create(['status' => StatusKamar::Occupied]);
        $user = User::factory()->create(['role' => RoleUser::Tenant]);
        $tenant = Tenant::factory()->create(['user_id' => $user->id]);
        $contract = Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusKontrak::Active,
        ]);

        // Tenant requests guest stay permission
        $resReq = $this->actingAs($user)->post(route('portal.permissions.store'), [
            'type' => PermissionType::GuestStay->value,
            'title' => 'Izin Tamu Menginap',
            'start_at' => now()->addDays(2)->toDateTimeString(),
            'end_at' => now()->addDays(4)->toDateTimeString(),
            'description' => 'Adik kandung menginap selama 2 hari',
        ]);
        $permission = TenantPermission::where('tenant_id', $tenant->id)->first();
        $this->assertNotNull($permission);
        $resReq->assertRedirect(route('portal.permissions.show', $permission));
        $this->assertSame(PermissionStatus::Pending, $permission->status);

        // Admin approves
        $this->actingAs($this->admin)->post(route('permissions.review', $permission), [
            'action' => 'approve',
            'review_note' => 'Diizinkan dengan mematuhi tata tertib jam malam',
        ]);
        $permission->refresh();
        $this->assertSame(PermissionStatus::Approved, $permission->status);
    }

    /**
     * FLOW 5 — MOVE-OUT & CONTRACT END E2E
     * Active Contract -> Move-Out Request -> Approved -> Inspect -> Settlement -> Finalize -> Contract Ended -> Room Available
     */
    public function test_flow_5_move_out_e2e(): void
    {
        $room = Room::factory()->create(['status' => StatusKamar::Occupied]);
        $user = User::factory()->create(['role' => RoleUser::Tenant]);
        $tenant = Tenant::factory()->create(['user_id' => $user->id]);
        $contract = Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusKontrak::Active,
            'deposit_amount' => 500000,
        ]);

        // 1. Tenant requests move-out
        $resReq = $this->actingAs($user)->post(route('portal.move-outs.store'), [
            'requested_move_out_date' => now()->addDays(14)->toDateString(),
            'reason' => 'Pindah tugas dinas kerja',
            'bank_account_info' => 'BCA 1234567890 a.n. Tenant',
        ]);
        $moveOut = MoveOutRequest::where('tenant_id', $tenant->id)->first();
        $this->assertNotNull($moveOut);
        $resReq->assertRedirect(route('portal.move-outs.show', $moveOut));

        // 2. Staff approves
        $this->actingAs($this->admin)->post(route('move-outs.review', $moveOut), [
            'action' => 'approve',
            'review_note' => 'Jadwal move out dicatat',
        ]);
        $moveOut->refresh();
        $this->assertSame(StatusMoveOut::Approved, $moveOut->status);

        // 3. Staff records room inspection
        $this->actingAs($this->admin)->post(route('move-outs.inspect', $moveOut), [
            'room_condition' => 'baik',
            'damage_notes' => 'Kondisi kamar baik, cat sedikit kotor tapi wajar',
            'damage_cost' => 50000,
            'requires_room_maintenance' => 0,
        ]);
        $moveOut->refresh();
        $this->assertSame(StatusMoveOut::Inspection, $moveOut->status);
        $this->assertEquals(50000, $moveOut->damage_cost);

        // 4. Staff finalizes settlement
        $this->actingAs($this->admin)->post(route('move-outs.finalize', $moveOut), [
            'settlement_notes' => 'Selesai dan kunci telah diserahkan',
        ]);
        $moveOut->refresh();
        $contract->refresh();
        $room->refresh();

        $this->assertSame(StatusMoveOut::Completed, $moveOut->status);
        $this->assertSame(StatusKontrak::Ended, $contract->status);
        $this->assertSame(StatusKamar::Available, $room->status);
        $this->assertEquals(450000, $moveOut->deposit_returned_amount); // 500k deposit - 50k damage = 450k returned
    }

    /**
     * IDOR CROSS-MODULE REGRESSION
     * Tenant A must NEVER access Tenant B resources across all modules
     */
    public function test_cross_module_idor_protection(): void
    {
        $userA = User::factory()->create(['role' => RoleUser::Tenant]);
        $tenantA = Tenant::factory()->create(['user_id' => $userA->id]);

        $userB = User::factory()->create(['role' => RoleUser::Tenant]);
        $tenantB = Tenant::factory()->create(['user_id' => $userB->id]);
        $roomB = Room::factory()->create();
        $contractB = Contract::factory()->create(['tenant_id' => $tenantB->id, 'room_id' => $roomB->id, 'status' => StatusKontrak::Active]);
        $invoiceB = Invoice::factory()->create(['contract_id' => $contractB->id, 'tenant_id' => $tenantB->id, 'room_id' => $roomB->id]);
        $paymentB = Payment::factory()->create(['invoice_id' => $invoiceB->id, 'tenant_id' => $tenantB->id]);

        $maintService = app(MaintenanceRequestService::class);
        $maintenanceB = $maintService->createRequest($tenantB, [
            'category' => KategoriMaintenance::Plumbing,
            'priority' => PrioritasMaintenance::Medium,
            'location' => 'Kamar Mandi',
            'description' => 'Bocor',
        ], null, $userB);

        $permissionB = TenantPermission::create([
            'tenant_id' => $tenantB->id,
            'contract_id' => $contractB->id,
            'type' => PermissionType::GuestStay,
            'title' => 'Tamu B',
            'description' => 'Deskripsi Tamu B',
            'status' => PermissionStatus::Pending,
        ]);

        $moveOutB = MoveOutRequest::create([
            'tenant_id' => $tenantB->id,
            'contract_id' => $contractB->id,
            'room_id' => $roomB->id,
            'reason' => 'Pindah',
            'requested_move_out_date' => now()->addDays(7)->toDateString(),
            'status' => StatusMoveOut::Pending,
        ]);

        $docB = TenantDocument::factory()->create([
            'tenant_id' => $tenantB->id,
            'uploaded_by' => $userB->id,
        ]);

        // Tenant A accesses Tenant B invoice -> 403
        $this->actingAs($userA)->get(route('portal.invoices.show', $invoiceB))->assertForbidden();

        // Tenant A accesses Tenant B payment -> 403
        $this->actingAs($userA)->get(route('portal.payments.show', $paymentB))->assertForbidden();

        // Tenant A accesses Tenant B maintenance -> 403
        $this->actingAs($userA)->get(route('portal.maintenance.show', $maintenanceB))->assertForbidden();

        // Tenant A accesses Tenant B permission -> 403
        $this->actingAs($userA)->get(route('portal.permissions.show', $permissionB))->assertForbidden();

        // Tenant A accesses Tenant B move-out -> 403
        $this->actingAs($userA)->get(route('portal.move-outs.show', $moveOutB))->assertForbidden();

        // Tenant A accesses Tenant B document -> 403
        $this->actingAs($userA)->get(route('portal.documents.show', $docB))->assertForbidden();
    }
}
