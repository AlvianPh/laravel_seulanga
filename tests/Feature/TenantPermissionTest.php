<?php

namespace Tests\Feature;

use App\Enums\PermissionStatus;
use App\Enums\PermissionType;
use App\Enums\RoleUser;
use App\Enums\StatusKontrak;
use App\Models\Contract;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\TenantPermission;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantPermissionTest extends TestCase
{
    use RefreshDatabase;

    private function createTenantWithActiveContract(): array
    {
        $user = User::factory()->create(['role' => RoleUser::Tenant]);
        $tenant = Tenant::factory()->create(['user_id' => $user->id]);
        $room = Room::factory()->create(['room_number' => 'R-'.uniqid()]);
        $contract = Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusKontrak::Active,
        ]);

        return [$user, $tenant, $room, $contract];
    }

    private function createStaffUser(RoleUser $role = RoleUser::Admin): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function createPermission(Tenant $tenant, Contract $contract, Room $room, array $attributes = []): TenantPermission
    {
        return TenantPermission::create(array_merge([
            'tenant_id' => $tenant->id,
            'contract_id' => $contract->id,
            'type' => PermissionType::GuestStay,
            'title' => 'Izin Menginapkan Saudara',
            'description' => 'Saudara kandung menginap selama 2 malam.',
            'start_at' => Carbon::tomorrow()->setTime(9, 0),
            'end_at' => Carbon::tomorrow()->addDays(2)->setTime(18, 0),
            'status' => PermissionStatus::Pending,
        ], $attributes));
    }

    // ─── 1. Tenant Portal Index & Visibility ──────────────────────────────────

    public function test_tenant_can_view_permissions_list_in_portal(): void
    {
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $perm = $this->createPermission($tenant, $contract, $room);

        $response = $this->actingAs($user)->get(route('portal.permissions.index'));

        $response->assertOk();
        $response->assertSee('Permohonan Izin');
        $response->assertSee($perm->title);
        $response->assertSee('Izin Menginapkan Tamu');
    }

    public function test_tenant_sees_empty_state_when_no_permissions_exist(): void
    {
        [$user] = $this->createTenantWithActiveContract();

        $response = $this->actingAs($user)->get(route('portal.permissions.index'));

        $response->assertOk();
        $response->assertSee('Tidak Ada Permohonan Izin');
    }

    public function test_tenant_can_filter_permissions_by_status_in_portal(): void
    {
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $pending = $this->createPermission($tenant, $contract, $room, [
            'title' => 'Pending Request Unique Title',
            'status' => PermissionStatus::Pending,
        ]);
        $approved = $this->createPermission($tenant, $contract, $room, [
            'title' => 'Approved Request Unique Title',
            'status' => PermissionStatus::Approved,
            'reviewed_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('portal.permissions.index', ['status' => 'pending']));
        $response->assertOk();
        $response->assertSee('Pending Request Unique Title');
        $response->assertDontSee('Approved Request Unique Title');

        $response = $this->actingAs($user)->get(route('portal.permissions.index', ['status' => 'approved']));
        $response->assertOk();
        $response->assertSee('Approved Request Unique Title');
        $response->assertDontSee('Pending Request Unique Title');
    }

    public function test_tenant_can_filter_permissions_by_type_in_portal(): void
    {
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $guest = $this->createPermission($tenant, $contract, $room, [
            'type' => PermissionType::GuestStay,
            'title' => 'Guest Request Unique Title',
        ]);
        $device = $this->createPermission($tenant, $contract, $room, [
            'type' => PermissionType::ElectronicDevice,
            'title' => 'Device Request Unique Title',
        ]);

        $response = $this->actingAs($user)->get(route('portal.permissions.index', ['type' => 'guest_stay']));
        $response->assertOk();
        $response->assertSee('Guest Request Unique Title');
        $response->assertDontSee('Device Request Unique Title');
    }

    public function test_tenant_only_sees_own_permissions_in_portal(): void
    {
        [$user1, $tenant1, $room1, $contract1] = $this->createTenantWithActiveContract();
        [$user2, $tenant2, $room2, $contract2] = $this->createTenantWithActiveContract();

        $perm1 = $this->createPermission($tenant1, $contract1, $room1, ['title' => 'Tenant 1 Unique Permission']);
        $perm2 = $this->createPermission($tenant2, $contract2, $room2, ['title' => 'Tenant 2 Secret Permission']);

        $response = $this->actingAs($user1)->get(route('portal.permissions.index'));
        $response->assertOk();
        $response->assertSee('Tenant 1 Unique Permission');
        $response->assertDontSee('Tenant 2 Secret Permission');
    }

    // ─── 2. Creation & Validation ─────────────────────────────────────────────

    public function test_tenant_can_view_create_permission_form(): void
    {
        [$user] = $this->createTenantWithActiveContract();

        $response = $this->actingAs($user)->get(route('portal.permissions.create'));

        $response->assertOk();
        $response->assertSee('Ajukan Permohonan Izin');
    }

    public function test_tenant_without_active_contract_cannot_access_create_form(): void
    {
        $user = User::factory()->create(['role' => RoleUser::Tenant]);
        Tenant::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('portal.permissions.create'));

        $response->assertRedirect(route('portal.permissions.index'));
        $response->assertSessionHas('error');
    }

    public function test_tenant_can_submit_valid_permission_request(): void
    {
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();

        $start = Carbon::tomorrow()->setTime(8, 0);
        $end = Carbon::tomorrow()->addDay()->setTime(17, 0);

        $response = $this->actingAs($user)->post(route('portal.permissions.store'), [
            'type' => PermissionType::LateReturn->value,
            'title' => 'Izin Pulang Larut Malam Lembur Kerja',
            'start_at' => $start->format('Y-m-d H:i'),
            'end_at' => $end->format('Y-m-d H:i'),
            'description' => 'Ada deadline proyek di kantor sampai jam 2 subuh.',
        ]);

        $this->assertDatabaseHas('tenant_permissions', [
            'tenant_id' => $tenant->id,
            'contract_id' => $contract->id,
            'type' => PermissionType::LateReturn->value,
            'title' => 'Izin Pulang Larut Malam Lembur Kerja',
            'status' => PermissionStatus::Pending->value,
        ]);

        $created = TenantPermission::where('title', 'Izin Pulang Larut Malam Lembur Kerja')->first();
        $response->assertRedirect(route('portal.permissions.show', $created));
        $response->assertSessionHas('success');
    }

    public function test_tenant_cannot_submit_permission_with_end_date_before_start_date(): void
    {
        [$user] = $this->createTenantWithActiveContract();

        $start = Carbon::tomorrow()->setTime(18, 0);
        $end = Carbon::tomorrow()->setTime(10, 0);

        $response = $this->actingAs($user)->post(route('portal.permissions.store'), [
            'type' => PermissionType::GuestStay->value,
            'title' => 'Izin Tanggal Terbalik',
            'start_at' => $start->format('Y-m-d H:i'),
            'end_at' => $end->format('Y-m-d H:i'),
            'description' => 'Tanggal selesai lebih awal dari tanggal mulai.',
        ]);

        $response->assertSessionHasErrors(['end_at']);
    }

    public function test_tenant_cannot_submit_permission_with_invalid_type(): void
    {
        [$user] = $this->createTenantWithActiveContract();

        $start = Carbon::tomorrow()->setTime(8, 0);
        $end = Carbon::tomorrow()->addDay()->setTime(17, 0);

        $response = $this->actingAs($user)->post(route('portal.permissions.store'), [
            'type' => 'invalid_type_here',
            'title' => 'Invalid Type Request',
            'start_at' => $start->format('Y-m-d H:i'),
            'end_at' => $end->format('Y-m-d H:i'),
            'description' => 'Test invalid type.',
        ]);

        $response->assertSessionHasErrors(['type']);
    }

    public function test_tenant_cannot_submit_permission_with_missing_required_fields(): void
    {
        [$user] = $this->createTenantWithActiveContract();

        $response = $this->actingAs($user)->post(route('portal.permissions.store'), []);

        $response->assertSessionHasErrors(['type', 'title', 'description']);
    }

    public function test_tenant_cannot_forge_tenant_id_or_contract_id_or_status(): void
    {
        [$user1, $tenant1, $room1, $contract1] = $this->createTenantWithActiveContract();
        [$user2, $tenant2, $room2, $contract2] = $this->createTenantWithActiveContract();

        $start = Carbon::tomorrow()->setTime(8, 0);
        $end = Carbon::tomorrow()->addDay()->setTime(17, 0);

        $response = $this->actingAs($user1)->post(route('portal.permissions.store'), [
            'tenant_id' => $tenant2->id,
            'contract_id' => $contract2->id,
            'status' => PermissionStatus::Approved->value,
            'type' => PermissionType::GuestStay->value,
            'title' => 'Forged Request',
            'start_at' => $start->format('Y-m-d H:i'),
            'end_at' => $end->format('Y-m-d H:i'),
            'description' => 'Trying to forge tenant and status.',
        ]);

        $created = TenantPermission::where('title', 'Forged Request')->first();
        $response->assertRedirect(route('portal.permissions.show', $created));

        // Verify the database record belongs strictly to user 1's tenant and is pending
        $this->assertDatabaseHas('tenant_permissions', [
            'id' => $created->id,
            'tenant_id' => $tenant1->id,
            'contract_id' => $contract1->id,
            'status' => PermissionStatus::Pending->value,
            'title' => 'Forged Request',
        ]);
    }

    // ─── 3. Detail View & Security ────────────────────────────────────────────

    public function test_tenant_can_view_own_permission_detail(): void
    {
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $perm = $this->createPermission($tenant, $contract, $room, [
            'title' => 'Rincian Izin Khusus',
        ]);

        $response = $this->actingAs($user)->get(route('portal.permissions.show', $perm));

        $response->assertOk();
        $response->assertSee('Rincian Permohonan Izin');
        $response->assertSee('Rincian Izin Khusus');
    }

    public function test_tenant_cannot_view_other_tenants_permission_detail(): void
    {
        [$user1, $tenant1, $room1, $contract1] = $this->createTenantWithActiveContract();
        [$user2, $tenant2, $room2, $contract2] = $this->createTenantWithActiveContract();

        $perm2 = $this->createPermission($tenant2, $contract2, $room2, ['title' => 'Secret Permission']);

        $response = $this->actingAs($user1)->get(route('portal.permissions.show', $perm2));

        $response->assertForbidden();
    }

    public function test_guest_cannot_access_portal_permissions(): void
    {
        $response = $this->get(route('portal.permissions.index'));
        $response->assertRedirect(route('login'));
    }

    // ─── 4. Cancellation Flow ─────────────────────────────────────────────────

    public function test_tenant_can_cancel_pending_permission(): void
    {
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $perm = $this->createPermission($tenant, $contract, $room, [
            'status' => PermissionStatus::Pending,
        ]);

        $response = $this->actingAs($user)->patch(route('portal.permissions.cancel', $perm));

        $response->assertRedirect(route('portal.permissions.show', $perm));
        $response->assertSessionHas('success');

        $this->assertEquals(PermissionStatus::Cancelled, $perm->fresh()->status);
    }

    public function test_tenant_cannot_cancel_approved_permission(): void
    {
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $perm = $this->createPermission($tenant, $contract, $room, [
            'status' => PermissionStatus::Approved,
        ]);

        $response = $this->actingAs($user)->patch(route('portal.permissions.cancel', $perm));

        $response->assertForbidden();
        $this->assertEquals(PermissionStatus::Approved, $perm->fresh()->status);
    }

    public function test_tenant_cannot_cancel_rejected_permission(): void
    {
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $perm = $this->createPermission($tenant, $contract, $room, [
            'status' => PermissionStatus::Rejected,
        ]);

        $response = $this->actingAs($user)->patch(route('portal.permissions.cancel', $perm));

        $response->assertForbidden();
        $this->assertEquals(PermissionStatus::Rejected, $perm->fresh()->status);
    }

    public function test_tenant_cannot_cancel_other_tenants_permission(): void
    {
        [$user1, $tenant1, $room1, $contract1] = $this->createTenantWithActiveContract();
        [$user2, $tenant2, $room2, $contract2] = $this->createTenantWithActiveContract();

        $perm2 = $this->createPermission($tenant2, $contract2, $room2, [
            'status' => PermissionStatus::Pending,
        ]);

        $response = $this->actingAs($user1)->patch(route('portal.permissions.cancel', $perm2));

        $response->assertForbidden();
        $this->assertEquals(PermissionStatus::Pending, $perm2->fresh()->status);
    }

    // ─── 5. Staff Management & Review ─────────────────────────────────────────

    public function test_admin_can_view_permissions_list(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $perm = $this->createPermission($tenant, $contract, $room);

        $response = $this->actingAs($admin)->get(route('permissions.index'));

        $response->assertOk();
        $response->assertSee('Daftar Permohonan Izin');
        $response->assertSee($perm->title);
    }

    public function test_owner_can_view_permissions_list(): void
    {
        $owner = $this->createStaffUser(RoleUser::Owner);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $perm = $this->createPermission($tenant, $contract, $room);

        $response = $this->actingAs($owner)->get(route('permissions.index'));

        $response->assertOk();
        $response->assertSee('Daftar Permohonan Izin');
        $response->assertSee($perm->title);
    }

    public function test_staff_can_filter_permissions_by_status_type_and_search(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();

        $perm1 = $this->createPermission($tenant, $contract, $room, [
            'title' => 'Alpha Special Permission',
            'status' => PermissionStatus::Pending,
            'type' => PermissionType::GuestStay,
        ]);
        $perm2 = $this->createPermission($tenant, $contract, $room, [
            'title' => 'Beta Regular Permission',
            'status' => PermissionStatus::Approved,
            'type' => PermissionType::LateReturn,
        ]);

        // Search test
        $response = $this->actingAs($admin)->get(route('permissions.index', ['search' => 'Alpha']));
        $response->assertOk();
        $response->assertSee('Alpha Special Permission');
        $response->assertDontSee('Beta Regular Permission');

        // Status test
        $response = $this->actingAs($admin)->get(route('permissions.index', ['status' => 'pending']));
        $response->assertOk();
        $response->assertSee('Alpha Special Permission');
        $response->assertDontSee('Beta Regular Permission');

        // Type test
        $response = $this->actingAs($admin)->get(route('permissions.index', ['type' => 'guest_stay']));
        $response->assertOk();
        $response->assertSee('Alpha Special Permission');
        $response->assertDontSee('Beta Regular Permission');
    }

    public function test_tenant_cannot_access_staff_permissions_index(): void
    {
        [$user] = $this->createTenantWithActiveContract();

        $response = $this->actingAs($user)->get(route('permissions.index'));

        $response->assertForbidden();
    }

    public function test_staff_can_view_permission_detail(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $perm = $this->createPermission($tenant, $contract, $room);

        $response = $this->actingAs($admin)->get(route('permissions.show', $perm));

        $response->assertOk();
        $response->assertSee('Detail Permohonan Izin');
        $response->assertSee($perm->title);
    }

    public function test_admin_can_approve_pending_permission(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $perm = $this->createPermission($tenant, $contract, $room);

        $response = $this->actingAs($admin)->post(route('permissions.review', $perm), [
            'action' => 'approve',
            'review_note' => 'Disetujui dengan catatan menjaga ketenangan.',
        ]);

        $response->assertRedirect(route('permissions.show', $perm));
        $response->assertSessionHas('success');

        $fresh = $perm->fresh();
        $this->assertEquals(PermissionStatus::Approved, $fresh->status);
        $this->assertEquals($admin->id, $fresh->reviewed_by);
        $this->assertNotNull($fresh->reviewed_at);
        $this->assertEquals('Disetujui dengan catatan menjaga ketenangan.', $fresh->review_note);
    }

    public function test_owner_can_approve_pending_permission(): void
    {
        $owner = $this->createStaffUser(RoleUser::Owner);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $perm = $this->createPermission($tenant, $contract, $room);

        $response = $this->actingAs($owner)->post(route('permissions.review', $perm), [
            'action' => 'approve',
            'review_note' => 'Disetujui oleh Owner.',
        ]);

        $response->assertRedirect(route('permissions.show', $perm));
        $response->assertSessionHas('success');

        $fresh = $perm->fresh();
        $this->assertEquals(PermissionStatus::Approved, $fresh->status);
        $this->assertEquals($owner->id, $fresh->reviewed_by);
    }

    public function test_admin_can_reject_pending_permission_with_mandatory_note(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $perm = $this->createPermission($tenant, $contract, $room);

        $response = $this->actingAs($admin)->post(route('permissions.review', $perm), [
            'action' => 'reject',
            'review_note' => 'Kapasitas parkir dan kamar tidak mencukupi untuk tamu tambahan.',
        ]);

        $response->assertRedirect(route('permissions.show', $perm));
        $response->assertSessionHas('success');

        $fresh = $perm->fresh();
        $this->assertEquals(PermissionStatus::Rejected, $fresh->status);
        $this->assertEquals($admin->id, $fresh->reviewed_by);
        $this->assertEquals('Kapasitas parkir dan kamar tidak mencukupi untuk tamu tambahan.', $fresh->review_note);
    }

    public function test_admin_cannot_reject_permission_without_note(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $perm = $this->createPermission($tenant, $contract, $room);

        $response = $this->actingAs($admin)->post(route('permissions.review', $perm), [
            'action' => 'reject',
            'review_note' => '',
        ]);

        $response->assertSessionHasErrors(['review_note']);
        $this->assertEquals(PermissionStatus::Pending, $perm->fresh()->status);
    }

    public function test_admin_cannot_review_already_approved_or_rejected_or_cancelled_permission(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();

        $approved = $this->createPermission($tenant, $contract, $room, ['status' => PermissionStatus::Approved]);
        $cancelled = $this->createPermission($tenant, $contract, $room, ['status' => PermissionStatus::Cancelled]);

        $res1 = $this->actingAs($admin)->post(route('permissions.review', $approved), [
            'action' => 'reject',
            'review_note' => 'Change mind',
        ]);
        $res1->assertForbidden();

        $res2 = $this->actingAs($admin)->post(route('permissions.review', $cancelled), [
            'action' => 'approve',
            'review_note' => 'Reactivate',
        ]);
        $res2->assertForbidden();
    }
}
