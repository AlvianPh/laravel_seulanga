<?php

namespace Tests\Feature;

use App\Enums\StatusApplication;
use App\Enums\StatusKamar;
use App\Enums\StatusKontrak;
use App\Enums\StatusPembayaran;
use App\Enums\StatusTagihan;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\TenantApplication;
use App\Models\User;
use App\Services\ContractService;
use App\Services\VerifyPaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ContractPortalTest
 *
 * Pengujian komprehensif untuk F2.3 Contract Portal & Onboarding Agreement:
 *  1. Contract Creation & Draft Generation from Application
 *  2. Tenant Access & Isolation (Tenant A vs Tenant B)
 *  3. Onboarding Agreement (Persetujuan Tata Tertib)
 *  4. Initial Payment Verification Prerequisite
 *  5. Contract Activation & Synchronization (Room becomes Occupied & Tenant becomes Active)
 *  6. Security & Privilege Boundaries
 */
class ContractPortalTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function createOwner(): User
    {
        return User::factory()->owner()->create();
    }

    private function createAdmin(): User
    {
        return User::factory()->admin()->create();
    }

    private function createTenantUserWithProfile(array $tenantAttrs = []): array
    {
        $user = User::factory()->tenant()->create();
        $tenant = Tenant::factory()->create(array_merge([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => '081234567890',
        ], $tenantAttrs));

        return [$user, $tenant];
    }

    // ── 1. Contract Creation & Draft Generation ──────────────────────────────

    public function test_approved_application_can_generate_draft_contract(): void
    {
        $admin = $this->createAdmin();
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create([
            'room_number' => '101',
            'status' => StatusKamar::Available,
            'monthly_price' => 1200000,
            'deposit_price' => 500000,
        ]);

        $application = TenantApplication::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusApplication::Approved,
        ]);

        $service = app(ContractService::class);
        $contract = $service->createDraftFromApplication($application, [
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonths(6)->format('Y-m-d'),
        ], $admin->id);

        $this->assertEquals(StatusKontrak::Draft, $contract->status);
        $this->assertEquals($tenant->id, $contract->tenant_id);
        $this->assertEquals($room->id, $contract->room_id);
        $this->assertEquals($application->id, $contract->application_id);

        // Room remains Available while contract is Draft
        $room->refresh();
        $this->assertEquals(StatusKamar::Available, $room->status);

        // Initial invoice is automatically generated
        $this->assertDatabaseHas('invoices', [
            'contract_id' => $contract->id,
            'tenant_id' => $tenant->id,
            'total_amount' => 1200000,
            'status' => StatusTagihan::Pending->value,
        ]);
    }

    public function test_rejected_application_cannot_generate_contract(): void
    {
        $admin = $this->createAdmin();
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);
        $application = TenantApplication::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusApplication::Rejected,
            'rejection_reason' => 'Tidak memenuhi syarat',
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $service = app(ContractService::class);
        $service->createDraftFromApplication($application, [], $admin->id);
    }

    public function test_cancelled_application_cannot_generate_contract(): void
    {
        $admin = $this->createAdmin();
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);
        $application = TenantApplication::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusApplication::Cancelled,
        ]);

        $this->expectException(\InvalidArgumentException::class);
        $service = app(ContractService::class);
        $service->createDraftFromApplication($application, [], $admin->id);
    }

    public function test_cannot_create_contract_if_tenant_already_has_active_contract(): void
    {
        $admin = $this->createAdmin();
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room1 = Room::factory()->create(['status' => StatusKamar::Occupied]);
        Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room1->id,
            'status' => StatusKontrak::Active,
        ]);

        $room2 = Room::factory()->create(['status' => StatusKamar::Available]);

        $this->expectException(\InvalidArgumentException::class);
        $service = app(ContractService::class);
        $service->createContract([
            'tenant_id' => $tenant->id,
            'room_id' => $room2->id,
            'start_date' => now()->format('Y-m-d'),
            'end_date' => now()->addMonths(6)->format('Y-m-d'),
            'rent_price' => 1200000,
            'deposit_amount' => 500000,
        ], $admin->id);
    }

    // ── 2. Tenant Access & Isolation ─────────────────────────────────────────

    public function test_tenant_can_view_own_contract_portal(): void
    {
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create([
            'room_number' => 'Kamar 303',
            'floor' => 3,
            'status' => StatusKamar::Available,
        ]);

        $contract = Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusKontrak::Draft,
            'rent_price' => 1500000,
            'deposit_amount' => 500000,
        ]);

        $response = $this->actingAs($user)->get(route('portal.contract.index'));

        $response->assertOk();
        $response->assertSee('Kamar 303');
        $response->assertSee('1.500.000');
        $response->assertSee('500.000');
        $response->assertSee('Tata Tertib');
    }

    public function test_tenant_without_contract_sees_empty_state(): void
    {
        [$user] = $this->createTenantUserWithProfile();

        $response = $this->actingAs($user)->get(route('portal.contract.index'));

        $response->assertOk();
        $response->assertSee('Belum Ada Kontrak Sewa');
    }

    public function test_tenant_cannot_access_staff_contract_management(): void
    {
        [$user] = $this->createTenantUserWithProfile();

        $this->actingAs($user)->get(route('contracts.index'))->assertForbidden();
        $this->actingAs($user)->get(route('contracts.create'))->assertForbidden();
    }

    public function test_tenant_cannot_activate_contract(): void
    {
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);
        $contract = Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusKontrak::Draft,
        ]);

        $response = $this->actingAs($user)->post(route('contracts.activate', $contract));
        $response->assertForbidden();

        $this->assertDatabaseHas('contracts', [
            'id' => $contract->id,
            'status' => StatusKontrak::Draft->value,
        ]);
    }

    // ── 3. Agreement / Tata Tertib ───────────────────────────────────────────

    public function test_tenant_can_accept_agreement(): void
    {
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);
        $contract = Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusKontrak::Draft,
            'agreement_accepted_at' => null,
        ]);

        $response = $this->actingAs($user)->post(route('portal.contract.agreement', $contract), [
            'terms_accepted' => '1',
        ]);

        $response->assertRedirect(route('portal.contract.index'));
        $response->assertSessionHas('status');

        $contract->refresh();
        $this->assertTrue($contract->isAgreementAccepted());
        $this->assertEquals($user->id, $contract->agreement_accepted_by);
        $this->assertNotNull($contract->agreement_accepted_at);
        $this->assertEquals('v1.0', $contract->agreement_version);
    }

    public function test_tenant_cannot_accept_agreement_without_checking_terms(): void
    {
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);
        $contract = Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusKontrak::Draft,
        ]);

        $response = $this->actingAs($user)->post(route('portal.contract.agreement', $contract), [
            'terms_accepted' => '0',
        ]);

        $response->assertSessionHasErrors('terms_accepted');
        $this->assertNull($contract->fresh()->agreement_accepted_at);
    }

    public function test_tenant_a_cannot_accept_agreement_for_tenant_b(): void
    {
        [$userA] = $this->createTenantUserWithProfile();
        [$userB, $tenantB] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);
        $contractB = Contract::factory()->create([
            'tenant_id' => $tenantB->id,
            'room_id' => $room->id,
            'status' => StatusKontrak::Draft,
        ]);

        $response = $this->actingAs($userA)->post(route('portal.contract.agreement', $contractB), [
            'terms_accepted' => '1',
        ]);

        $response->assertForbidden();
        $this->assertNull($contractB->fresh()->agreement_accepted_at);
    }

    public function test_tenant_cannot_reaccept_already_accepted_agreement(): void
    {
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);
        $contract = Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusKontrak::Draft,
            'agreement_accepted_at' => now()->subDay(),
            'agreement_accepted_by' => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('portal.contract.agreement', $contract), [
            'terms_accepted' => '1',
        ]);

        $response->assertForbidden();
    }

    // ── 4. Initial Payment & Verification ────────────────────────────────────

    public function test_initial_payment_requires_verification_before_considered_satisfied(): void
    {
        $admin = $this->createAdmin();
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);
        $contract = Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'rent_price' => 1000000,
            'status' => StatusKontrak::Draft,
            'agreement_accepted_at' => now(),
            'agreement_accepted_by' => $user->id,
        ]);

        $invoice = Invoice::factory()->create([
            'contract_id' => $contract->id,
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'total_amount' => 1000000,
            'status' => StatusTagihan::Pending,
        ]);

        $paymentMethod = PaymentMethod::create([
            'name' => 'Transfer BCA',
            'code' => 'BCA',
            'is_active' => true,
        ]);

        // 1. Unverified payment -> not satisfied
        $payment = Payment::create([
            'invoice_id' => $invoice->id,
            'tenant_id' => $tenant->id,
            'amount' => 1000000,
            'payment_date' => now()->format('Y-m-d'),
            'payment_method_id' => $paymentMethod->id,
            'status' => StatusPembayaran::Pending,
        ]);

        $this->assertFalse($contract->hasVerifiedInitialPayment());

        // 2. Reject payment -> not satisfied
        $verifyService = app(VerifyPaymentService::class);
        $verifyService->reject($payment, $admin->id, 'Bukti transfer buram');
        $this->assertFalse($contract->hasVerifiedInitialPayment());

        // 3. New valid payment verified -> satisfied
        $payment2 = Payment::create([
            'invoice_id' => $invoice->id,
            'tenant_id' => $tenant->id,
            'amount' => 1000000,
            'payment_date' => now()->format('Y-m-d'),
            'payment_method_id' => $paymentMethod->id,
            'status' => StatusPembayaran::Pending,
        ]);

        $verifyService->verify($payment2, $admin->id);
        $this->assertTrue($contract->fresh()->hasVerifiedInitialPayment());
    }

    // ── 5. Contract Activation & Synchronization ─────────────────────────────

    public function test_staff_cannot_activate_contract_if_agreement_not_accepted(): void
    {
        $admin = $this->createAdmin();
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);
        $contract = Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusKontrak::Draft,
            'agreement_accepted_at' => null, // Belum disetujui
        ]);

        $response = $this->actingAs($admin)->post(route('contracts.activate', $contract));

        $response->assertRedirect();
        $response->assertSessionHasErrors('error');
        $this->assertEquals(StatusKontrak::Draft, $contract->fresh()->status);
    }

    public function test_staff_cannot_activate_contract_if_room_is_occupied(): void
    {
        $admin = $this->createAdmin();
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Occupied]); // Kamar terisi
        $contract = Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusKontrak::Draft,
            'agreement_accepted_at' => now(),
            'agreement_accepted_by' => $user->id,
        ]);

        $response = $this->actingAs($admin)->post(route('contracts.activate', $contract));

        $response->assertRedirect();
        $response->assertSessionHasErrors('error');
        $this->assertEquals(StatusKontrak::Draft, $contract->fresh()->status);
    }

    public function test_staff_can_activate_contract_when_all_prerequisites_are_met(): void
    {
        $admin = $this->createAdmin();
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);
        $contract = Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'rent_price' => 1200000,
            'status' => StatusKontrak::Draft,
            'agreement_accepted_at' => now(),
            'agreement_accepted_by' => $user->id,
        ]);

        // Tagihan awal lunas
        $invoice = Invoice::factory()->create([
            'contract_id' => $contract->id,
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'total_amount' => 1200000,
            'status' => StatusTagihan::Paid,
        ]);

        $response = $this->actingAs($admin)->post(route('contracts.activate', $contract));

        $response->assertRedirect(route('contracts.show', $contract));
        $response->assertSessionHas('success');

        // 1. Contract becomes Active
        $contract->refresh();
        $this->assertEquals(StatusKontrak::Active, $contract->status);

        // 2. Room status synchronized to Occupied
        $room->refresh();
        $this->assertEquals(StatusKamar::Occupied, $room->status);

        // 3. Tenant has active contract
        $this->assertNotNull($tenant->activeContract());
    }

    public function test_owner_has_full_management_access_to_activate_contract(): void
    {
        $owner = $this->createOwner();
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);
        $contract = Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusKontrak::Draft,
            'agreement_accepted_at' => now(),
            'agreement_accepted_by' => $user->id,
        ]);

        Invoice::factory()->create([
            'contract_id' => $contract->id,
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'total_amount' => 1000000,
            'status' => StatusTagihan::Paid,
        ]);

        $response = $this->actingAs($owner)->post(route('contracts.activate', $contract));

        $response->assertRedirect(route('contracts.show', $contract));
        $this->assertEquals(StatusKontrak::Active, $contract->fresh()->status);
        $this->assertEquals(StatusKamar::Occupied, $room->fresh()->status);
    }
}
