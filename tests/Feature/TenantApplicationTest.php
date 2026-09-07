<?php

namespace Tests\Feature;

use App\Enums\StatusApplication;
use App\Enums\StatusKamar;
use App\Enums\StatusKontrak;
use App\Models\Contract;
use App\Models\Facility;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\TenantApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TenantApplicationTest
 *
 * Pengujian komprehensif untuk F2.2 Tenant Application & Room Discovery:
 *  1. Room Discovery: listing kamar eligible & detail kamar
 *  2. Application Submission: pengajuan kamar, validasi eligibility, duplicate pending check, active contract check
 *  3. Application Ownership & Isolation: isolasi data antar tenant, cancel pending application
 *  4. Admin/Owner Review: approve & reject pengajuan dengan reason
 *  5. Critical Business Rule: Approval TIDAK otomatis membuat kontrak aktif atau mengubah room status menjadi occupied
 */
class TenantApplicationTest extends TestCase
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

    // ── 1. Room Discovery ────────────────────────────────────────────────────

    public function test_tenant_can_view_available_rooms_in_discovery_catalog(): void
    {
        [$user] = $this->createTenantUserWithProfile();

        $availableRoom = Room::factory()->create([
            'room_number' => 'A101',
            'status' => StatusKamar::Available,
        ]);

        $occupiedRoom = Room::factory()->create([
            'room_number' => 'B202',
            'status' => StatusKamar::Occupied,
        ]);

        $maintenanceRoom = Room::factory()->create([
            'room_number' => 'C303',
            'status' => StatusKamar::Maintenance,
        ]);

        $response = $this->actingAs($user)->get(route('portal.rooms.index'));

        $response->assertOk();
        $response->assertSee('A101');
        $response->assertDontSee('B202');
        $response->assertDontSee('C303');
    }

    public function test_tenant_can_view_room_detail_page(): void
    {
        [$user] = $this->createTenantUserWithProfile();

        $facility = Facility::create(['name' => 'WiFi 100Mbps']);

        $room = Room::factory()->create([
            'room_number' => 'A102',
            'floor' => 1,
            'status' => StatusKamar::Available,
            'monthly_price' => 1200000,
            'deposit_price' => 500000,
            'size_m2' => 12.0,
        ]);
        $room->facilities()->attach($facility);

        $response = $this->actingAs($user)->get(route('portal.rooms.show', $room));

        $response->assertOk();
        $response->assertSee('Kamar A102');
        $response->assertSee('WiFi 100Mbps');
        $response->assertSee('1.200.000');
        $response->assertSee('500.000');
        $response->assertSee('m²');
    }

    public function test_tenant_cannot_modify_room(): void
    {
        [$user] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);

        // Attempting to hit staff room update/delete routes
        $this->actingAs($user)->put(route('rooms.update', $room), ['room_number' => 'HACKED'])->assertForbidden();
        $this->actingAs($user)->delete(route('rooms.destroy', $room))->assertForbidden();
    }

    // ── 2. Application Submission ────────────────────────────────────────────

    public function test_tenant_can_submit_application_for_available_room(): void
    {
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);

        $response = $this->actingAs($user)->post(route('portal.applications.store'), [
            'room_id' => $room->id,
            'application_notes' => 'Saya ingin mulai sewa bulan depan.',
        ]);

        $response->assertRedirect(route('portal.applications.index'));
        $response->assertSessionHas('status', 'Pengajuan sewa kamar berhasil dikirim! Silakan menunggu konfirmasi dari pengelola kost.');

        $this->assertDatabaseHas('tenant_applications', [
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusApplication::Pending->value,
            'application_notes' => 'Saya ingin mulai sewa bulan depan.',
        ]);
    }

    public function test_tenant_cannot_submit_without_linked_tenant_record(): void
    {
        $unlinkedUser = User::factory()->tenant()->create();
        $room = Room::factory()->create(['status' => StatusKamar::Available]);

        $response = $this->actingAs($unlinkedUser)->post(route('portal.applications.store'), [
            'room_id' => $room->id,
            'application_notes' => 'Test tanpa profil',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('tenant_applications', 0);
    }

    public function test_tenant_cannot_submit_application_for_occupied_or_maintenance_room(): void
    {
        [$user] = $this->createTenantUserWithProfile();

        $occupiedRoom = Room::factory()->create(['status' => StatusKamar::Occupied]);
        $maintenanceRoom = Room::factory()->create(['status' => StatusKamar::Maintenance]);

        // Occupied room submission
        $response1 = $this->actingAs($user)->post(route('portal.applications.store'), [
            'room_id' => $occupiedRoom->id,
        ]);
        $response1->assertRedirect();
        $response1->assertSessionHas('error');

        // Maintenance room submission
        $response2 = $this->actingAs($user)->post(route('portal.applications.store'), [
            'room_id' => $maintenanceRoom->id,
        ]);
        $response2->assertRedirect();
        $response2->assertSessionHas('error');

        $this->assertDatabaseCount('tenant_applications', 0);
    }

    public function test_tenant_cannot_submit_second_pending_application(): void
    {
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room1 = Room::factory()->create(['status' => StatusKamar::Available]);
        $room2 = Room::factory()->create(['status' => StatusKamar::Available]);

        TenantApplication::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room1->id,
            'status' => StatusApplication::Pending,
        ]);

        $response = $this->actingAs($user)->post(route('portal.applications.store'), [
            'room_id' => $room2->id,
            'application_notes' => 'Pengajuan kedua',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('tenant_applications', 1);
    }

    public function test_active_tenant_cannot_submit_application(): void
    {
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $currentRoom = Room::factory()->create(['status' => StatusKamar::Occupied]);
        Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $currentRoom->id,
            'status' => StatusKontrak::Active,
        ]);

        $newRoom = Room::factory()->create(['status' => StatusKamar::Available]);

        $response = $this->actingAs($user)->post(route('portal.applications.store'), [
            'room_id' => $newRoom->id,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseCount('tenant_applications', 0);
    }

    public function test_spoofed_tenant_id_in_request_is_ignored(): void
    {
        [$userA, $tenantA] = $this->createTenantUserWithProfile();
        [$userB, $tenantB] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);

        // Tenant A tries to submit on behalf of Tenant B
        $this->actingAs($userA)->post(route('portal.applications.store'), [
            'tenant_id' => $tenantB->id,
            'room_id' => $room->id,
        ]);

        // Record must belong to Tenant A
        $this->assertDatabaseHas('tenant_applications', [
            'tenant_id' => $tenantA->id,
            'room_id' => $room->id,
        ]);
        $this->assertDatabaseMissing('tenant_applications', [
            'tenant_id' => $tenantB->id,
        ]);
    }

    // ── 3. Application Ownership & Isolation ─────────────────────────────────

    public function test_tenant_can_view_own_applications(): void
    {
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['room_number' => 'Kamar 105', 'status' => StatusKamar::Available]);
        TenantApplication::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusApplication::Pending,
        ]);

        $response = $this->actingAs($user)->get(route('portal.applications.index'));

        $response->assertOk();
        $response->assertSee('Kamar 105');
        $response->assertSee('Menunggu Review');
    }

    public function test_tenant_cannot_view_another_tenant_application(): void
    {
        [$userA] = $this->createTenantUserWithProfile();
        [$userB, $tenantB] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['room_number' => 'Kamar 205', 'status' => StatusKamar::Available]);
        $appB = TenantApplication::factory()->create([
            'tenant_id' => $tenantB->id,
            'room_id' => $room->id,
            'status' => StatusApplication::Pending,
        ]);

        // Tenant A index should not see Tenant B's application details
        $response = $this->actingAs($userA)->get(route('portal.applications.index'));
        $response->assertDontSee('Kamar 205');
    }

    public function test_tenant_can_cancel_own_pending_application(): void
    {
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);
        $application = TenantApplication::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusApplication::Pending,
        ]);

        $response = $this->actingAs($user)->patch(route('portal.applications.cancel', $application));

        $response->assertRedirect(route('portal.applications.index'));
        $response->assertSessionHas('status', 'Pengajuan sewa kamar telah dibatalkan.');

        $this->assertDatabaseHas('tenant_applications', [
            'id' => $application->id,
            'status' => StatusApplication::Cancelled->value,
        ]);
    }

    public function test_tenant_cannot_cancel_another_tenant_application(): void
    {
        [$userA] = $this->createTenantUserWithProfile();
        [$userB, $tenantB] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);
        $appB = TenantApplication::factory()->create([
            'tenant_id' => $tenantB->id,
            'room_id' => $room->id,
            'status' => StatusApplication::Pending,
        ]);

        $response = $this->actingAs($userA)->patch(route('portal.applications.cancel', $appB));
        $response->assertForbidden();

        $this->assertDatabaseHas('tenant_applications', [
            'id' => $appB->id,
            'status' => StatusApplication::Pending->value,
        ]);
    }

    public function test_tenant_cannot_cancel_already_approved_application(): void
    {
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);
        $application = TenantApplication::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusApplication::Approved,
        ]);

        $response = $this->actingAs($user)->patch(route('portal.applications.cancel', $application));

        $response->assertForbidden();
        $this->assertDatabaseHas('tenant_applications', [
            'id' => $application->id,
            'status' => StatusApplication::Approved->value,
        ]);
    }

    // ── 4. Admin & Owner Application Management ──────────────────────────────

    public function test_admin_and_owner_can_view_applications_list(): void
    {
        $admin = $this->createAdmin();
        $owner = $this->createOwner();

        $this->actingAs($admin)->get(route('tenant-applications.index'))->assertOk();
        $this->actingAs($owner)->get(route('tenant-applications.index'))->assertOk();
    }

    public function test_admin_can_approve_pending_application(): void
    {
        $admin = $this->createAdmin();
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);
        $application = TenantApplication::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusApplication::Pending,
        ]);

        $response = $this->actingAs($admin)->post(route('tenant-applications.review', $application), [
            'action' => 'approve',
        ]);

        $response->assertRedirect(route('tenant-applications.show', $application));
        $response->assertSessionHas('status', 'Pengajuan sewa kamar berhasil disetujui. Langkah selanjutnya: buat kontrak sewa untuk penghuni.');

        $this->assertDatabaseHas('tenant_applications', [
            'id' => $application->id,
            'status' => StatusApplication::Approved->value,
            'reviewed_by' => $admin->id,
        ]);
    }

    public function test_approval_does_not_create_active_contract_or_occupy_room(): void
    {
        // CRITICAL BUSINESS RULE:
        // Application approval ONLY means tenant is approved to proceed.
        // It DOES NOT create a contract, and room status remains Available.
        $admin = $this->createAdmin();
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);
        $application = TenantApplication::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusApplication::Pending,
        ]);

        $this->actingAs($admin)->post(route('tenant-applications.review', $application), [
            'action' => 'approve',
        ]);

        // 1. Contract count must still be 0
        $this->assertEquals(0, Contract::count());

        // 2. Room status must remain Available
        $room->refresh();
        $this->assertEquals(StatusKamar::Available, $room->status);
    }

    public function test_admin_can_reject_pending_application_with_reason(): void
    {
        $admin = $this->createAdmin();
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);
        $application = TenantApplication::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusApplication::Pending,
        ]);

        $response = $this->actingAs($admin)->post(route('tenant-applications.review', $application), [
            'action' => 'reject',
            'rejection_reason' => 'Kapasitas maksimal penghuni per kamar terlampaui.',
        ]);

        $response->assertRedirect(route('tenant-applications.show', $application));
        $response->assertSessionHas('status', 'Pengajuan sewa kamar telah ditolak.');

        $this->assertDatabaseHas('tenant_applications', [
            'id' => $application->id,
            'status' => StatusApplication::Rejected->value,
            'reviewed_by' => $admin->id,
            'rejection_reason' => 'Kapasitas maksimal penghuni per kamar terlampaui.',
        ]);
    }

    public function test_admin_cannot_reject_without_rejection_reason(): void
    {
        $admin = $this->createAdmin();
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);
        $application = TenantApplication::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusApplication::Pending,
        ]);

        $response = $this->actingAs($admin)->post(route('tenant-applications.review', $application), [
            'action' => 'reject',
            'rejection_reason' => '',
        ]);

        $response->assertSessionHasErrors('rejection_reason');
        $this->assertDatabaseHas('tenant_applications', [
            'id' => $application->id,
            'status' => StatusApplication::Pending->value,
        ]);
    }

    public function test_admin_cannot_approve_already_rejected_application(): void
    {
        $admin = $this->createAdmin();
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Available]);
        $application = TenantApplication::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusApplication::Rejected,
            'rejection_reason' => 'Ditolak sebelumnya',
        ]);

        $response = $this->actingAs($admin)->post(route('tenant-applications.review', $application), [
            'action' => 'approve',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseHas('tenant_applications', [
            'id' => $application->id,
            'status' => StatusApplication::Rejected->value,
        ]);
    }

    public function test_admin_cannot_approve_when_room_is_no_longer_available(): void
    {
        $admin = $this->createAdmin();
        [$user, $tenant] = $this->createTenantUserWithProfile();

        $room = Room::factory()->create(['status' => StatusKamar::Occupied]);
        $application = TenantApplication::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusApplication::Pending,
        ]);

        $response = $this->actingAs($admin)->post(route('tenant-applications.review', $application), [
            'action' => 'approve',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('tenant_applications', [
            'id' => $application->id,
            'status' => StatusApplication::Pending->value,
        ]);
    }

    public function test_tenant_cannot_access_admin_application_management(): void
    {
        [$user] = $this->createTenantUserWithProfile();

        $this->actingAs($user)->get(route('tenant-applications.index'))->assertForbidden();
    }
}
