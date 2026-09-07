<?php

namespace Tests\Feature;

use App\Enums\RoleUser;
use App\Enums\StatusKamar;
use App\Enums\StatusKontrak;
use App\Enums\StatusMoveOut;
use App\Enums\StatusPembayaran;
use App\Enums\StatusTagihan;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\MoveOutRequest;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantMoveOutTest extends TestCase
{
    use RefreshDatabase;

    private function createTenantWithActiveContract(float $deposit = 1000000.0, float $rentPrice = 1500000.0): array
    {
        $user = User::factory()->create(['role' => RoleUser::Tenant]);
        $tenant = Tenant::factory()->create(['user_id' => $user->id]);
        $room = Room::factory()->create([
            'room_number' => 'R-'.uniqid(),
            'status' => StatusKamar::Occupied,
        ]);
        $contract = Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusKontrak::Active,
            'deposit_amount' => $deposit,
            'rent_price' => $rentPrice,
        ]);

        return [$user, $tenant, $room, $contract];
    }

    private function createStaffUser(RoleUser $role = RoleUser::Admin): User
    {
        return User::factory()->create(['role' => $role]);
    }

    private function createMoveOutRequest(Tenant $tenant, Contract $contract, Room $room, array $attributes = []): MoveOutRequest
    {
        return MoveOutRequest::create(array_merge([
            'tenant_id' => $tenant->id,
            'contract_id' => $contract->id,
            'room_id' => $room->id,
            'requested_move_out_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reason' => 'Selesai masa studi di kampus.',
            'notes' => 'Nomor rekening BCA 1234567890.',
            'status' => StatusMoveOut::Pending,
        ], $attributes));
    }

    // ─── 1. Tenant Portal Index & Visibility ──────────────────────────────────

    public function test_tenant_can_view_move_outs_list_in_portal(): void
    {
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $moveOut = $this->createMoveOutRequest($tenant, $contract, $room);

        $response = $this->actingAs($user)->get(route('portal.move-outs.index'));

        $response->assertOk();
        $response->assertSee('Pengajuan Keluar Kost (Move-Out)');
        $response->assertSee($moveOut->reason);
        $response->assertSee($moveOut->requested_move_out_date->format('d M Y'));
    }

    public function test_tenant_sees_empty_state_when_no_move_outs_exist(): void
    {
        [$user] = $this->createTenantWithActiveContract();

        $response = $this->actingAs($user)->get(route('portal.move-outs.index'));

        $response->assertOk();
        $response->assertSee('Tidak Ada Permohonan Move-Out');
    }

    public function test_tenant_can_filter_move_outs_by_status_in_portal(): void
    {
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $pending = $this->createMoveOutRequest($tenant, $contract, $room, [
            'reason' => 'Reason Unique Alpha Pending',
            'status' => StatusMoveOut::Pending,
        ]);
        $completed = $this->createMoveOutRequest($tenant, $contract, $room, [
            'reason' => 'Reason Unique Beta Completed',
            'status' => StatusMoveOut::Completed,
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($user)->get(route('portal.move-outs.index', ['status' => 'pending']));
        $response->assertOk();
        $response->assertSee('Reason Unique Alpha Pending');
        $response->assertDontSee('Reason Unique Beta Completed');

        $response = $this->actingAs($user)->get(route('portal.move-outs.index', ['status' => 'completed']));
        $response->assertOk();
        $response->assertSee('Reason Unique Beta Completed');
        $response->assertDontSee('Reason Unique Alpha Pending');
    }

    public function test_tenant_only_sees_own_move_outs_in_portal(): void
    {
        [$user1, $tenant1, $room1, $contract1] = $this->createTenantWithActiveContract();
        [$user2, $tenant2, $room2, $contract2] = $this->createTenantWithActiveContract();

        $this->createMoveOutRequest($tenant1, $contract1, $room1, ['reason' => 'Tenant 1 Secret MoveOut']);
        $this->createMoveOutRequest($tenant2, $contract2, $room2, ['reason' => 'Tenant 2 Secret MoveOut']);

        $response = $this->actingAs($user1)->get(route('portal.move-outs.index'));
        $response->assertOk();
        $response->assertSee('Tenant 1 Secret MoveOut');
        $response->assertDontSee('Tenant 2 Secret MoveOut');
    }

    // ─── 2. Creation & Validation ─────────────────────────────────────────────

    public function test_tenant_can_view_create_move_out_form(): void
    {
        [$user] = $this->createTenantWithActiveContract();

        $response = $this->actingAs($user)->get(route('portal.move-outs.create'));

        $response->assertOk();
        $response->assertSee('Ajukan Permohonan Keluar Kost');
    }

    public function test_tenant_without_active_contract_cannot_access_create_form(): void
    {
        $user = User::factory()->create(['role' => RoleUser::Tenant]);
        Tenant::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('portal.move-outs.create'));

        $response->assertRedirect(route('portal.move-outs.index'));
        $response->assertSessionHas('error');
    }

    public function test_tenant_can_submit_valid_move_out_request(): void
    {
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();

        $response = $this->actingAs($user)->post(route('portal.move-outs.store'), [
            'requested_move_out_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reason' => 'Pindah kerja ke kota lain.',
            'notes' => 'Tolong proses deposit ke BCA 98765.',
        ]);

        $this->assertDatabaseHas('move_out_requests', [
            'tenant_id' => $tenant->id,
            'contract_id' => $contract->id,
            'room_id' => $room->id,
            'reason' => 'Pindah kerja ke kota lain.',
            'status' => StatusMoveOut::Pending->value,
        ]);

        $created = MoveOutRequest::where('reason', 'Pindah kerja ke kota lain.')->first();
        $response->assertRedirect(route('portal.move-outs.show', $created));
        $response->assertSessionHas('success');
    }

    public function test_tenant_cannot_create_duplicate_active_move_out_request(): void
    {
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $this->createMoveOutRequest($tenant, $contract, $room, ['status' => StatusMoveOut::Pending]);

        $response = $this->actingAs($user)->post(route('portal.move-outs.store'), [
            'requested_move_out_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reason' => 'Mencoba membuat request kedua.',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(1, MoveOutRequest::where('contract_id', $contract->id)->count());
    }

    public function test_tenant_cannot_submit_move_out_with_past_date(): void
    {
        [$user] = $this->createTenantWithActiveContract();

        $response = $this->actingAs($user)->post(route('portal.move-outs.store'), [
            'requested_move_out_date' => Carbon::yesterday()->format('Y-m-d'),
            'reason' => 'Keluar kemarin.',
        ]);

        $response->assertSessionHasErrors(['requested_move_out_date']);
    }

    public function test_tenant_cannot_submit_move_out_with_missing_reason(): void
    {
        [$user] = $this->createTenantWithActiveContract();

        $response = $this->actingAs($user)->post(route('portal.move-outs.store'), [
            'requested_move_out_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reason' => '',
        ]);

        $response->assertSessionHasErrors(['reason']);
    }

    public function test_tenant_cannot_forge_tenant_id_contract_id_or_status(): void
    {
        [$user1, $tenant1, $room1, $contract1] = $this->createTenantWithActiveContract();
        [$user2, $tenant2, $room2, $contract2] = $this->createTenantWithActiveContract();

        $response = $this->actingAs($user1)->post(route('portal.move-outs.store'), [
            'tenant_id' => $tenant2->id,
            'contract_id' => $contract2->id,
            'room_id' => $room2->id,
            'status' => StatusMoveOut::Completed->value,
            'requested_move_out_date' => Carbon::tomorrow()->format('Y-m-d'),
            'reason' => 'Forged MoveOut Request',
        ]);

        $created = MoveOutRequest::where('reason', 'Forged MoveOut Request')->first();
        $response->assertRedirect(route('portal.move-outs.show', $created));

        $this->assertDatabaseHas('move_out_requests', [
            'id' => $created->id,
            'tenant_id' => $tenant1->id,
            'contract_id' => $contract1->id,
            'status' => StatusMoveOut::Pending->value,
        ]);
    }

    // ─── 3. Detail View & Security ────────────────────────────────────────────

    public function test_tenant_can_view_own_move_out_detail(): void
    {
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $moveOut = $this->createMoveOutRequest($tenant, $contract, $room, [
            'reason' => 'Detail Rincian MoveOut Khusus',
        ]);

        $response = $this->actingAs($user)->get(route('portal.move-outs.show', $moveOut));

        $response->assertOk();
        $response->assertSee('Rincian Permohonan Move-Out');
        $response->assertSee('Detail Rincian MoveOut Khusus');
    }

    public function test_tenant_cannot_view_other_tenants_move_out_detail(): void
    {
        [$user1, $tenant1, $room1, $contract1] = $this->createTenantWithActiveContract();
        [$user2, $tenant2, $room2, $contract2] = $this->createTenantWithActiveContract();

        $moveOut2 = $this->createMoveOutRequest($tenant2, $contract2, $room2, ['reason' => 'Secret MoveOut']);

        $response = $this->actingAs($user1)->get(route('portal.move-outs.show', $moveOut2));

        $response->assertForbidden();
    }

    public function test_guest_cannot_access_portal_or_staff_move_outs(): void
    {
        $res1 = $this->get(route('portal.move-outs.index'));
        $res1->assertRedirect(route('login'));

        $res2 = $this->get(route('move-outs.index'));
        $res2->assertRedirect(route('login'));
    }

    // ─── 4. Cancellation Flow ─────────────────────────────────────────────────

    public function test_tenant_can_cancel_pending_move_out_request(): void
    {
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $moveOut = $this->createMoveOutRequest($tenant, $contract, $room, [
            'status' => StatusMoveOut::Pending,
        ]);

        $response = $this->actingAs($user)->patch(route('portal.move-outs.cancel', $moveOut));

        $response->assertRedirect(route('portal.move-outs.show', $moveOut));
        $response->assertSessionHas('success');

        $this->assertEquals(StatusMoveOut::Cancelled, $moveOut->fresh()->status);
    }

    public function test_tenant_cannot_cancel_approved_or_completed_move_out_request(): void
    {
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $approved = $this->createMoveOutRequest($tenant, $contract, $room, [
            'status' => StatusMoveOut::Approved,
        ]);
        $completed = $this->createMoveOutRequest($tenant, $contract, $room, [
            'status' => StatusMoveOut::Completed,
        ]);

        $res1 = $this->actingAs($user)->patch(route('portal.move-outs.cancel', $approved));
        $res1->assertForbidden();

        $res2 = $this->actingAs($user)->patch(route('portal.move-outs.cancel', $completed));
        $res2->assertForbidden();
    }

    public function test_tenant_cannot_cancel_other_tenants_move_out_request(): void
    {
        [$user1, $tenant1, $room1, $contract1] = $this->createTenantWithActiveContract();
        [$user2, $tenant2, $room2, $contract2] = $this->createTenantWithActiveContract();

        $moveOut2 = $this->createMoveOutRequest($tenant2, $contract2, $room2, [
            'status' => StatusMoveOut::Pending,
        ]);

        $response = $this->actingAs($user1)->patch(route('portal.move-outs.cancel', $moveOut2));

        $response->assertForbidden();
        $this->assertEquals(StatusMoveOut::Pending, $moveOut2->fresh()->status);
    }

    public function test_tenant_cannot_approve_reject_inspect_or_finalize(): void
    {
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $moveOut = $this->createMoveOutRequest($tenant, $contract, $room, [
            'status' => StatusMoveOut::Pending,
        ]);

        // Tenant cannot review / approve
        $res1 = $this->actingAs($user)->post(route('move-outs.review', $moveOut), ['action' => 'approve']);
        $res1->assertForbidden();

        // Tenant cannot inspect
        $res2 = $this->actingAs($user)->post(route('move-outs.inspect', $moveOut), ['room_condition' => 'baik']);
        $res2->assertForbidden();

        // Tenant cannot finalize
        $res3 = $this->actingAs($user)->post(route('move-outs.finalize', $moveOut), []);
        $res3->assertForbidden();
    }

    // ─── 5. Staff Management & Review ─────────────────────────────────────────

    public function test_admin_and_owner_can_view_move_outs_list(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        $owner = $this->createStaffUser(RoleUser::Owner);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $moveOut = $this->createMoveOutRequest($tenant, $contract, $room);

        $resAdmin = $this->actingAs($admin)->get(route('move-outs.index'));
        $resAdmin->assertOk();
        $resAdmin->assertSee('Daftar Permohonan Keluar Kost');
        $resAdmin->assertSee($moveOut->reason);

        $resOwner = $this->actingAs($owner)->get(route('move-outs.index'));
        $resOwner->assertOk();
        $resOwner->assertSee('Daftar Permohonan Keluar Kost');
        $resOwner->assertSee($moveOut->reason);
    }

    public function test_staff_can_view_move_out_detail(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $moveOut = $this->createMoveOutRequest($tenant, $contract, $room);

        $response = $this->actingAs($admin)->get(route('move-outs.show', $moveOut));

        $response->assertOk();
        $response->assertSee('Detail Proses Keluar Kost (Move-Out)');
        $response->assertSee($moveOut->reason);
    }

    public function test_staff_can_filter_move_outs_by_status_and_search(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();

        $mo1 = $this->createMoveOutRequest($tenant, $contract, $room, [
            'reason' => 'Alpha Special Reason',
            'status' => StatusMoveOut::Pending,
        ]);
        $mo2 = $this->createMoveOutRequest($tenant, $contract, $room, [
            'reason' => 'Beta Regular Reason',
            'status' => StatusMoveOut::Approved,
        ]);

        // Search test
        $resSearch = $this->actingAs($admin)->get(route('move-outs.index', ['search' => 'Alpha']));
        $resSearch->assertOk();
        $resSearch->assertSee('Alpha Special Reason');
        $resSearch->assertDontSee('Beta Regular Reason');

        // Status test
        $resStatus = $this->actingAs($admin)->get(route('move-outs.index', ['status' => 'pending']));
        $resStatus->assertOk();
        $resStatus->assertSee('Alpha Special Reason');
        $resStatus->assertDontSee('Beta Regular Reason');
    }

    public function test_tenant_cannot_access_staff_move_outs_index(): void
    {
        [$user] = $this->createTenantWithActiveContract();

        $response = $this->actingAs($user)->get(route('move-outs.index'));

        $response->assertForbidden();
    }

    public function test_admin_and_owner_can_approve_pending_move_out(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $moveOut = $this->createMoveOutRequest($tenant, $contract, $room);

        $response = $this->actingAs($admin)->post(route('move-outs.review', $moveOut), [
            'action' => 'approve',
            'review_note' => 'Disetujui. Silakan persiapkan kamar untuk inspeksi.',
        ]);

        $response->assertRedirect(route('move-outs.show', $moveOut));
        $response->assertSessionHas('success');

        $fresh = $moveOut->fresh();
        $this->assertEquals(StatusMoveOut::Approved, $fresh->status);
        $this->assertEquals($admin->id, $fresh->reviewed_by);
        $this->assertNotNull($fresh->reviewed_at);

        // Approval does NOT end contract or free room
        $this->assertEquals(StatusKontrak::Active, $contract->fresh()->status);
        $this->assertEquals(StatusKamar::Occupied, $room->fresh()->status);
    }

    public function test_admin_can_reject_pending_move_out_with_mandatory_note(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $moveOut = $this->createMoveOutRequest($tenant, $contract, $room);

        $response = $this->actingAs($admin)->post(route('move-outs.review', $moveOut), [
            'action' => 'reject',
            'review_note' => 'Masa sewa minimum 6 bulan belum tercapai.',
        ]);

        $response->assertRedirect(route('move-outs.show', $moveOut));
        $response->assertSessionHas('success');

        $fresh = $moveOut->fresh();
        $this->assertEquals(StatusMoveOut::Rejected, $fresh->status);
        $this->assertEquals($admin->id, $fresh->reviewed_by);
        $this->assertEquals('Masa sewa minimum 6 bulan belum tercapai.', $fresh->review_note);
    }

    public function test_admin_cannot_reject_move_out_without_note(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $moveOut = $this->createMoveOutRequest($tenant, $contract, $room);

        $response = $this->actingAs($admin)->post(route('move-outs.review', $moveOut), [
            'action' => 'reject',
            'review_note' => '',
        ]);

        $response->assertSessionHasErrors(['review_note']);
        $this->assertEquals(StatusMoveOut::Pending, $moveOut->fresh()->status);
    }

    public function test_admin_cannot_review_already_approved_or_rejected_move_out(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();

        $approved = $this->createMoveOutRequest($tenant, $contract, $room, ['status' => StatusMoveOut::Approved]);
        $rejected = $this->createMoveOutRequest($tenant, $contract, $room, ['status' => StatusMoveOut::Rejected]);

        $res1 = $this->actingAs($admin)->post(route('move-outs.review', $approved), [
            'action' => 'reject',
            'review_note' => 'Change mind',
        ]);
        $res1->assertForbidden();

        $res2 = $this->actingAs($admin)->post(route('move-outs.review', $rejected), [
            'action' => 'approve',
        ]);
        $res2->assertForbidden();
    }

    // ─── 6. Inspection Flow ───────────────────────────────────────────────────

    public function test_admin_can_record_inspection_with_damage_cost_and_maintenance_flag(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();
        $moveOut = $this->createMoveOutRequest($tenant, $contract, $room, [
            'status' => StatusMoveOut::Approved,
        ]);

        $response = $this->actingAs($admin)->post(route('move-outs.inspect', $moveOut), [
            'room_condition' => 'perbaikan_ringan',
            'damage_notes' => 'Kran wastafel patah dan dinding perlu cat ulang.',
            'damage_cost' => 250000,
            'requires_room_maintenance' => 1,
            'inspection_notes' => 'Inspeksi selesai bersama penghuni.',
        ]);

        $response->assertRedirect(route('move-outs.show', $moveOut));
        $response->assertSessionHas('success');

        $fresh = $moveOut->fresh();
        $this->assertEquals(StatusMoveOut::Inspection, $fresh->status);
        $this->assertEquals($admin->id, $fresh->inspected_by);
        $this->assertNotNull($fresh->inspected_at);
        $this->assertEquals(250000.0, (float) $fresh->damage_cost);
        $this->assertTrue($fresh->requires_room_maintenance);
    }

    public function test_admin_cannot_record_inspection_on_pending_or_completed_move_out(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();

        $pending = $this->createMoveOutRequest($tenant, $contract, $room, ['status' => StatusMoveOut::Pending]);
        $completed = $this->createMoveOutRequest($tenant, $contract, $room, ['status' => StatusMoveOut::Completed]);

        $res1 = $this->actingAs($admin)->post(route('move-outs.inspect', $pending), [
            'room_condition' => 'baik',
        ]);
        $res1->assertForbidden();

        $res2 = $this->actingAs($admin)->post(route('move-outs.inspect', $completed), [
            'room_condition' => 'baik',
        ]);
        $res2->assertForbidden();
    }

    // ─── 7. Financial & Settlement Calculations ───────────────────────────────

    public function test_outstanding_invoices_are_calculated_based_on_verified_payments_only(): void
    {
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract(deposit: 1000000.0);
        $pm = PaymentMethod::firstOrCreate(['name' => 'Transfer Bank']);

        // Invoice 1: 500.000 (Paid 200.000 verified -> remaining 300.000)
        $inv1 = Invoice::create([
            'contract_id' => $contract->id,
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'year' => 2026,
            'month' => 8,
            'rent_amount' => 500000,
            'total_amount' => 500000,
            'due_date' => '2026-08-10',
            'status' => StatusTagihan::PartiallyPaid,
        ]);
        Payment::create([
            'invoice_id' => $inv1->id,
            'tenant_id' => $tenant->id,
            'payment_method_id' => $pm->id,
            'amount' => 200000,
            'payment_date' => '2026-08-05',
            'status' => StatusPembayaran::Verified,
        ]);
        // Unverified / Rejected payment should NOT count
        Payment::create([
            'invoice_id' => $inv1->id,
            'tenant_id' => $tenant->id,
            'payment_method_id' => $pm->id,
            'amount' => 100000,
            'payment_date' => '2026-08-06',
            'status' => StatusPembayaran::Rejected,
        ]);
        Payment::create([
            'invoice_id' => $inv1->id,
            'tenant_id' => $tenant->id,
            'payment_method_id' => $pm->id,
            'amount' => 100000,
            'payment_date' => '2026-08-07',
            'status' => StatusPembayaran::Pending,
        ]);

        // Invoice 2: 400.000 (Unpaid -> remaining 400.000)
        Invoice::create([
            'contract_id' => $contract->id,
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'year' => 2026,
            'month' => 9,
            'rent_amount' => 400000,
            'total_amount' => 400000,
            'due_date' => '2026-09-10',
            'status' => StatusTagihan::Pending,
        ]);

        $moveOut = $this->createMoveOutRequest($tenant, $contract, $room, [
            'status' => StatusMoveOut::Inspection,
            'damage_cost' => 150000, // 150.000
        ]);

        $admin = $this->createStaffUser(RoleUser::Admin);
        $response = $this->actingAs($admin)->get(route('move-outs.show', $moveOut));

        $response->assertOk();
        // Total Outstanding = 300.000 + 400.000 = 700.000
        // Damage = 150.000
        // Total Obligations = 850.000
        // Deposit = 1.000.000
        // Deposit Returned = 150.000
        $response->assertSee('700.000');
        $response->assertSee('150.000');
    }

    public function test_when_bills_and_damage_exceed_deposit_returned_deposit_is_zero_and_liability_is_recorded(): void
    {
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract(deposit: 500000.0);

        // Outstanding Invoice: 400.000
        Invoice::create([
            'contract_id' => $contract->id,
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'year' => 2026,
            'month' => 9,
            'rent_amount' => 400000,
            'total_amount' => 400000,
            'due_date' => '2026-09-10',
            'status' => StatusTagihan::Pending,
        ]);

        // Damage: 300.000
        // Total obligations: 700.000 vs Deposit: 500.000
        // Deposit Returned: 0, Remaining liability: 200.000
        $moveOut = $this->createMoveOutRequest($tenant, $contract, $room, [
            'status' => StatusMoveOut::Inspection,
            'damage_cost' => 300000,
        ]);

        $admin = $this->createStaffUser(RoleUser::Admin);
        $response = $this->actingAs($admin)->get(route('move-outs.show', $moveOut));

        $response->assertOk();
        $response->assertSee('200.000');
    }

    // ─── 8. Finalization, Atomicity & Room Sync ───────────────────────────────

    public function test_admin_can_finalize_settlement_and_end_contract_with_room_available(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract(deposit: 1000000.0);

        $moveOut = $this->createMoveOutRequest($tenant, $contract, $room, [
            'status' => StatusMoveOut::Inspection,
            'damage_cost' => 0,
            'requires_room_maintenance' => false,
        ]);

        $response = $this->actingAs($admin)->post(route('move-outs.finalize', $moveOut), [
            'settlement_notes' => 'Deposit Rp 1.000.000 telah ditransfer penuh ke rekening tenant.',
        ]);

        $response->assertRedirect(route('move-outs.show', $moveOut));
        $response->assertSessionHas('success');

        $freshMo = $moveOut->fresh();
        $this->assertEquals(StatusMoveOut::Completed, $freshMo->status);
        $this->assertEquals($admin->id, $freshMo->settled_by);
        $this->assertEquals($admin->id, $freshMo->completed_by);
        $this->assertNotNull($freshMo->completed_at);
        $this->assertEquals(1000000.0, (float) $freshMo->deposit_returned_amount);
        $this->assertEquals(0.0, (float) $freshMo->remaining_tenant_liability);

        // Contract must be ended
        $this->assertEquals(StatusKontrak::Ended, $contract->fresh()->status);
        // Room must become available
        $this->assertEquals(StatusKamar::Available, $room->fresh()->status);
        // Tenant must have no active contract
        $this->assertNull($tenant->fresh()->activeContract());
    }

    public function test_when_contract_ends_with_maintenance_requirement_room_becomes_maintenance(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract(deposit: 1000000.0);

        $moveOut = $this->createMoveOutRequest($tenant, $contract, $room, [
            'status' => StatusMoveOut::Inspection,
            'damage_cost' => 200000,
            'requires_room_maintenance' => true,
        ]);

        $response = $this->actingAs($admin)->post(route('move-outs.finalize', $moveOut), [
            'settlement_notes' => 'Kamar butuh perbaikan cat dan kran.',
        ]);

        $response->assertRedirect(route('move-outs.show', $moveOut));

        $this->assertEquals(StatusKontrak::Ended, $contract->fresh()->status);
        $this->assertEquals(StatusKamar::Maintenance, $room->fresh()->status);
    }

    public function test_finalization_is_idempotent_and_cannot_be_finalized_twice(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract();

        $moveOut = $this->createMoveOutRequest($tenant, $contract, $room, [
            'status' => StatusMoveOut::Completed,
            'completed_at' => now(),
        ]);

        $response = $this->actingAs($admin)->post(route('move-outs.finalize', $moveOut), [
            'settlement_notes' => 'Duplicate call',
        ]);

        $response->assertForbidden();
    }

    public function test_owner_can_finalize_settlement_and_end_contract(): void
    {
        $owner = $this->createStaffUser(RoleUser::Owner);
        [$user, $tenant, $room, $contract] = $this->createTenantWithActiveContract(deposit: 1000000.0);

        $moveOut = $this->createMoveOutRequest($tenant, $contract, $room, [
            'status' => StatusMoveOut::Inspection,
            'damage_cost' => 0,
            'requires_room_maintenance' => false,
        ]);

        $response = $this->actingAs($owner)->post(route('move-outs.finalize', $moveOut), [
            'settlement_notes' => 'Owner finalize move-out settlement.',
        ]);

        $response->assertRedirect(route('move-outs.show', $moveOut));
        $this->assertEquals(StatusMoveOut::Completed, $moveOut->fresh()->status);
        $this->assertEquals(StatusKontrak::Ended, $contract->fresh()->status);
        $this->assertEquals(StatusKamar::Available, $room->fresh()->status);
    }
}
