<?php

namespace Tests\Feature;

use App\Enums\KategoriMaintenance;
use App\Enums\PrioritasMaintenance;
use App\Enums\RoleUser;
use App\Enums\StatusKontrak;
use App\Enums\StatusMaintenance;
use App\Models\Contract;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\MaintenanceRequest;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use App\Services\MaintenanceRequestService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TenantMaintenanceTest extends TestCase
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

    private function createMaintenanceRequest(Tenant $tenant, Room $room, array $attributes = []): MaintenanceRequest
    {
        $service = app(MaintenanceRequestService::class);
        $ticketNumber = $service->generateTicketNumber();

        return MaintenanceRequest::create(array_merge([
            'ticket_number' => $ticketNumber,
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'category' => KategoriMaintenance::Plumbing,
            'priority' => PrioritasMaintenance::Medium,
            'status' => StatusMaintenance::Pending,
            'location' => 'Kamar Mandi',
            'description' => 'Kran air wastafel bocor dan menetes terus menerus.',
            'reported_at' => now(),
            'created_by' => $tenant->user_id ?? User::factory()->create()->id,
        ], $attributes));
    }

    // ─── 1. Tenant Portal List & Visibility Tests ─────────────────────────────

    public function test_tenant_can_view_maintenance_requests_list_in_portal(): void
    {
        [$user, $tenant, $room] = $this->createTenantWithActiveContract();
        $request = $this->createMaintenanceRequest($tenant, $room);

        $response = $this->actingAs($user)->get(route('portal.maintenance.index'));

        $response->assertOk();
        $response->assertSee('Layanan Perbaikan');
        $response->assertSee($request->ticket_number);
        $response->assertSee('Kran air wastafel bocor');
    }

    public function test_tenant_sees_empty_state_when_no_maintenance_requests_exist(): void
    {
        [$user] = $this->createTenantWithActiveContract();

        $response = $this->actingAs($user)->get(route('portal.maintenance.index'));

        $response->assertOk();
        $response->assertSee('Tidak Ada Permintaan Perbaikan');
    }

    public function test_tenant_can_filter_maintenance_requests_by_status_in_portal(): void
    {
        [$user, $tenant, $room] = $this->createTenantWithActiveContract();
        $pendingReq = $this->createMaintenanceRequest($tenant, $room, [
            'ticket_number' => 'MNT-20260907-0001',
            'status' => StatusMaintenance::Pending,
            'description' => 'Pending request issue',
        ]);
        $resolvedReq = $this->createMaintenanceRequest($tenant, $room, [
            'ticket_number' => 'MNT-20260907-0002',
            'status' => StatusMaintenance::Resolved,
            'description' => 'Resolved request issue',
        ]);

        $responsePending = $this->actingAs($user)->get(route('portal.maintenance.index', ['status' => 'pending']));
        $responsePending->assertOk();
        $responsePending->assertSee('MNT-20260907-0001');
        $responsePending->assertDontSee('MNT-20260907-0002');

        $responseResolved = $this->actingAs($user)->get(route('portal.maintenance.index', ['status' => 'resolved']));
        $responseResolved->assertOk();
        $responseResolved->assertSee('MNT-20260907-0002');
        $responseResolved->assertDontSee('MNT-20260907-0001');
    }

    public function test_tenant_cannot_access_maintenance_request_belonging_to_other_tenant(): void
    {
        [$userA, $tenantA, $roomA] = $this->createTenantWithActiveContract();
        [$userB, $tenantB, $roomB] = $this->createTenantWithActiveContract();

        $requestB = $this->createMaintenanceRequest($tenantB, $roomB);

        $response = $this->actingAs($userA)->get(route('portal.maintenance.show', $requestB));
        $response->assertForbidden();
    }

    // ─── 2. Tenant Submission Tests ───────────────────────────────────────────

    public function test_tenant_with_active_contract_can_view_maintenance_create_form(): void
    {
        [$user, $tenant, $room] = $this->createTenantWithActiveContract();

        $response = $this->actingAs($user)->get(route('portal.maintenance.create'));

        $response->assertOk();
        $response->assertSee('Lapor Kerusakan Kamar');
        $response->assertSee("Kamar {$room->room_number}");
    }

    public function test_tenant_without_active_contract_cannot_access_maintenance_create_form(): void
    {
        $user = User::factory()->create(['role' => RoleUser::Tenant]);
        $tenant = Tenant::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->get(route('portal.maintenance.create'));

        $response->assertRedirect(route('portal.maintenance.index'));
        $response->assertSessionHas('error');
    }

    public function test_tenant_can_submit_maintenance_request_with_valid_data(): void
    {
        [$user, $tenant, $room] = $this->createTenantWithActiveContract();

        $response = $this->actingAs($user)->post(route('portal.maintenance.store'), [
            'category' => KategoriMaintenance::Electrical->value,
            'priority' => PrioritasMaintenance::High->value,
            'location' => 'Saklar Utama Dekat Pintu',
            'description' => 'Saklar lampu berbunyi gemertak dan lampu berkedip terus.',
        ]);

        $this->assertDatabaseHas('maintenance_requests', [
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'category' => KategoriMaintenance::Electrical->value,
            'priority' => PrioritasMaintenance::High->value,
            'status' => StatusMaintenance::Pending->value,
            'location' => 'Saklar Utama Dekat Pintu',
            'description' => 'Saklar lampu berbunyi gemertak dan lampu berkedip terus.',
            'created_by' => $user->id,
        ]);

        $created = MaintenanceRequest::where('tenant_id', $tenant->id)->first();
        $response->assertRedirect(route('portal.maintenance.show', $created));
        $response->assertSessionHas('success');
    }

    public function test_tenant_can_submit_maintenance_request_with_photo_attachment(): void
    {
        Storage::fake('public');
        [$user, $tenant, $room] = $this->createTenantWithActiveContract();

        $photo = UploadedFile::fake()->image('damage.jpg', 600, 600);

        $response = $this->actingAs($user)->post(route('portal.maintenance.store'), [
            'category' => KategoriMaintenance::Furniture->value,
            'priority' => PrioritasMaintenance::Low->value,
            'location' => 'Engsel Lemari Pakaian',
            'description' => 'Engsel pintu lemari patah di bagian atas.',
            'photo' => $photo,
        ]);

        $created = MaintenanceRequest::where('tenant_id', $tenant->id)->first();
        $this->assertNotNull($created);
        $this->assertNotNull($created->photo_path);
        Storage::disk('public')->assertExists($created->photo_path);

        $response->assertRedirect(route('portal.maintenance.show', $created));
    }

    public function test_maintenance_request_requires_valid_category(): void
    {
        [$user] = $this->createTenantWithActiveContract();

        $response = $this->actingAs($user)->post(route('portal.maintenance.store'), [
            'category' => 'invalid_category_xyz',
            'priority' => PrioritasMaintenance::Medium->value,
            'location' => 'Kamar',
            'description' => 'Kerusakan apapun',
        ]);

        $response->assertSessionHasErrors('category');
    }

    public function test_maintenance_request_requires_valid_priority(): void
    {
        [$user] = $this->createTenantWithActiveContract();

        $response = $this->actingAs($user)->post(route('portal.maintenance.store'), [
            'category' => KategoriMaintenance::Other->value,
            'priority' => 'super_urgent_invalid',
            'location' => 'Kamar',
            'description' => 'Kerusakan apapun',
        ]);

        $response->assertSessionHasErrors('priority');
    }

    public function test_maintenance_request_requires_location(): void
    {
        [$user] = $this->createTenantWithActiveContract();

        $response = $this->actingAs($user)->post(route('portal.maintenance.store'), [
            'category' => KategoriMaintenance::Other->value,
            'priority' => PrioritasMaintenance::Low->value,
            'location' => '',
            'description' => 'Kerusakan apapun',
        ]);

        $response->assertSessionHasErrors('location');
    }

    public function test_maintenance_request_requires_description(): void
    {
        [$user] = $this->createTenantWithActiveContract();

        $response = $this->actingAs($user)->post(route('portal.maintenance.store'), [
            'category' => KategoriMaintenance::Other->value,
            'priority' => PrioritasMaintenance::Low->value,
            'location' => 'Kamar Mandi',
            'description' => '',
        ]);

        $response->assertSessionHasErrors('description');
    }

    public function test_maintenance_request_photo_validation_fails_for_non_image_file(): void
    {
        [$user] = $this->createTenantWithActiveContract();

        $file = UploadedFile::fake()->create('malicious.pdf', 1000);

        $response = $this->actingAs($user)->post(route('portal.maintenance.store'), [
            'category' => KategoriMaintenance::Other->value,
            'priority' => PrioritasMaintenance::Low->value,
            'location' => 'Kamar',
            'description' => 'Kerusakan apapun',
            'photo' => $file,
        ]);

        $response->assertSessionHasErrors('photo');
    }

    public function test_maintenance_request_ticket_number_is_generated_with_correct_format(): void
    {
        [$user, $tenant] = $this->createTenantWithActiveContract();

        $this->actingAs($user)->post(route('portal.maintenance.store'), [
            'category' => KategoriMaintenance::Plumbing->value,
            'priority' => PrioritasMaintenance::Medium->value,
            'location' => 'Kamar Mandi',
            'description' => 'Pipa wastafel tersumbat.',
        ]);

        $created = MaintenanceRequest::where('tenant_id', $tenant->id)->first();
        $this->assertNotNull($created);
        $today = now()->format('Ymd');
        $this->assertMatchesRegularExpression("/^MNT-{$today}-\d{4}$/", $created->ticket_number);
    }

    public function test_maintenance_request_room_is_automatically_resolved_from_tenant_active_contract(): void
    {
        [$user, $tenant, $room] = $this->createTenantWithActiveContract();
        $otherRoom = Room::factory()->create(['room_number' => 'R-999-'.uniqid()]);

        // Even if attacker tenant attempts to spoof room_id in post payload
        $this->actingAs($user)->post(route('portal.maintenance.store'), [
            'room_id' => $otherRoom->id,
            'category' => KategoriMaintenance::Plumbing->value,
            'priority' => PrioritasMaintenance::Medium->value,
            'location' => 'Kamar Mandi',
            'description' => 'Air tidak mengalir.',
        ]);

        $created = MaintenanceRequest::where('tenant_id', $tenant->id)->first();
        $this->assertEquals($room->id, $created->room_id);
    }

    // ─── 3. Staff Management & Search Tests ───────────────────────────────────

    public function test_staff_can_view_all_maintenance_requests_in_staff_panel(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$userA, $tenantA, $roomA] = $this->createTenantWithActiveContract();
        $requestA = $this->createMaintenanceRequest($tenantA, $roomA);

        $response = $this->actingAs($admin)->get(route('maintenance.index'));

        $response->assertOk();
        $response->assertSee('Manajemen Perbaikan');
        $response->assertSee($requestA->ticket_number);
        $response->assertSee($tenantA->name);
    }

    public function test_staff_can_filter_maintenance_requests_by_status_priority_and_category(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$userA, $tenantA, $roomA] = $this->createTenantWithActiveContract();

        $req1 = $this->createMaintenanceRequest($tenantA, $roomA, [
            'ticket_number' => 'MNT-20260907-1001',
            'status' => StatusMaintenance::Pending,
            'priority' => PrioritasMaintenance::Urgent,
            'category' => KategoriMaintenance::Plumbing,
        ]);

        $req2 = $this->createMaintenanceRequest($tenantA, $roomA, [
            'ticket_number' => 'MNT-20260907-1002',
            'status' => StatusMaintenance::Resolved,
            'priority' => PrioritasMaintenance::Low,
            'category' => KategoriMaintenance::Furniture,
        ]);

        $response = $this->actingAs($admin)->get(route('maintenance.index', [
            'status' => 'pending',
            'priority' => 'urgent',
        ]));

        $response->assertOk();
        $response->assertSee('MNT-20260907-1001');
        $response->assertDontSee('MNT-20260907-1002');
    }

    public function test_staff_can_search_maintenance_requests_by_ticket_number_or_tenant_name(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$userA, $tenantA, $roomA] = $this->createTenantWithActiveContract();
        [$userB, $tenantB, $roomB] = $this->createTenantWithActiveContract();

        $reqA = $this->createMaintenanceRequest($tenantA, $roomA, ['ticket_number' => 'MNT-20260907-8888']);
        $reqB = $this->createMaintenanceRequest($tenantB, $roomB, ['ticket_number' => 'MNT-20260907-9999']);

        $response = $this->actingAs($admin)->get(route('maintenance.index', ['search' => '8888']));
        $response->assertOk();
        $response->assertSee('MNT-20260907-8888');
        $response->assertDontSee('MNT-20260907-9999');

        $responseName = $this->actingAs($admin)->get(route('maintenance.index', ['search' => $tenantB->name]));
        $responseName->assertOk();
        $responseName->assertSee('MNT-20260907-9999');
    }

    public function test_staff_can_view_maintenance_request_detail(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room] = $this->createTenantWithActiveContract();
        $request = $this->createMaintenanceRequest($tenant, $room);

        $response = $this->actingAs($admin)->get(route('maintenance.show', $request));

        $response->assertOk();
        $response->assertSee($request->ticket_number);
        $response->assertSee($tenant->name);
        $response->assertSee($room->room_number);
    }

    // ─── 4. Staff Status Transitions Tests ────────────────────────────────────

    public function test_staff_can_update_maintenance_request_status_to_in_progress(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room] = $this->createTenantWithActiveContract();
        $request = $this->createMaintenanceRequest($tenant, $room);

        $response = $this->actingAs($admin)->patch(route('maintenance.update-status', $request), [
            'status' => StatusMaintenance::InProgress->value,
            'notes' => 'Teknisi sedang menuju ke lokasi kamar.',
        ]);

        $response->assertRedirect(route('maintenance.show', $request));
        $this->assertDatabaseHas('maintenance_requests', [
            'id' => $request->id,
            'status' => StatusMaintenance::InProgress->value,
            'notes' => 'Teknisi sedang menuju ke lokasi kamar.',
            'updated_by' => $admin->id,
        ]);
    }

    public function test_staff_can_update_maintenance_request_status_to_resolved_with_notes(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room] = $this->createTenantWithActiveContract();
        $request = $this->createMaintenanceRequest($tenant, $room, [
            'status' => StatusMaintenance::InProgress,
        ]);

        $response = $this->actingAs($admin)->patch(route('maintenance.update-status', $request), [
            'status' => StatusMaintenance::Resolved->value,
            'notes' => 'Kran wastafel sudah diganti dengan unit baru yang berkualitas.',
        ]);

        $response->assertRedirect(route('maintenance.show', $request));
        $request->refresh();
        $this->assertEquals(StatusMaintenance::Resolved, $request->status);
        $this->assertNotNull($request->resolved_at);
        $this->assertEquals($admin->id, $request->updated_by);
        $this->assertEquals('Kran wastafel sudah diganti dengan unit baru yang berkualitas.', $request->notes);
    }

    public function test_staff_can_reject_maintenance_request_with_rejection_reason(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room] = $this->createTenantWithActiveContract();
        $request = $this->createMaintenanceRequest($tenant, $room);

        $response = $this->actingAs($admin)->patch(route('maintenance.update-status', $request), [
            'status' => StatusMaintenance::Rejected->value,
            'rejection_reason' => 'Kerusakan akibat kelalaian pribadi penghuni bukan fasilitas bawaan.',
        ]);

        $response->assertRedirect(route('maintenance.show', $request));
        $this->assertDatabaseHas('maintenance_requests', [
            'id' => $request->id,
            'status' => StatusMaintenance::Rejected->value,
            'rejection_reason' => 'Kerusakan akibat kelalaian pribadi penghuni bukan fasilitas bawaan.',
            'updated_by' => $admin->id,
        ]);
    }

    public function test_staff_cannot_reject_maintenance_request_without_rejection_reason(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room] = $this->createTenantWithActiveContract();
        $request = $this->createMaintenanceRequest($tenant, $room);

        $response = $this->actingAs($admin)->patch(route('maintenance.update-status', $request), [
            'status' => StatusMaintenance::Rejected->value,
            'rejection_reason' => '',
        ]);

        $response->assertSessionHasErrors('rejection_reason');
    }

    public function test_resolved_maintenance_request_cannot_be_updated_again(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room] = $this->createTenantWithActiveContract();
        $request = $this->createMaintenanceRequest($tenant, $room, [
            'status' => StatusMaintenance::Resolved,
            'resolved_at' => now(),
        ]);

        $response = $this->actingAs($admin)->patch(route('maintenance.update-status', $request), [
            'status' => StatusMaintenance::InProgress->value,
            'notes' => 'Coba ubah status lagi',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(StatusMaintenance::Resolved, $request->fresh()->status);
    }

    public function test_rejected_maintenance_request_cannot_be_updated_again(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room] = $this->createTenantWithActiveContract();
        $request = $this->createMaintenanceRequest($tenant, $room, [
            'status' => StatusMaintenance::Rejected,
            'rejection_reason' => 'Ditolak sebelumnya',
        ]);

        $response = $this->actingAs($admin)->patch(route('maintenance.update-status', $request), [
            'status' => StatusMaintenance::InProgress->value,
            'notes' => 'Coba buka lagi',
        ]);

        $response->assertSessionHas('error');
        $this->assertEquals(StatusMaintenance::Rejected, $request->fresh()->status);
    }

    // ─── 5. Expense Integration & Total Cost Tests ────────────────────────────

    public function test_staff_can_link_expense_to_maintenance_request(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room] = $this->createTenantWithActiveContract();
        $request = $this->createMaintenanceRequest($tenant, $room);
        $category = ExpenseCategory::firstOrCreate(['name' => 'Perbaikan']);

        $response = $this->actingAs($admin)->post(route('expenses.store'), [
            'expense_category_id' => $category->id,
            'maintenance_request_id' => $request->id,
            'description' => 'Pembelian kran stainless steel',
            'amount' => 75000,
            'expense_date' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('expenses.index'));
        $this->assertDatabaseHas('expenses', [
            'maintenance_request_id' => $request->id,
            'description' => 'Pembelian kran stainless steel',
            'amount' => 75000,
            'created_by' => $admin->id,
        ]);
    }

    public function test_total_cost_is_dynamically_calculated_from_linked_expenses(): void
    {
        $admin = $this->createStaffUser(RoleUser::Admin);
        [$user, $tenant, $room] = $this->createTenantWithActiveContract();
        $request = $this->createMaintenanceRequest($tenant, $room);
        $category = ExpenseCategory::firstOrCreate(['name' => 'Biaya Umum']);

        Expense::create([
            'expense_category_id' => $category->id,
            'maintenance_request_id' => $request->id,
            'description' => 'Biaya sparepart',
            'amount' => 120000,
            'expense_date' => now()->format('Y-m-d'),
            'created_by' => $admin->id,
        ]);

        Expense::create([
            'expense_category_id' => $category->id,
            'maintenance_request_id' => $request->id,
            'description' => 'Upah teknisi tukang pipa',
            'amount' => 80000,
            'expense_date' => now()->format('Y-m-d'),
            'created_by' => $admin->id,
        ]);

        $this->assertEquals(200000, $request->total_cost);
    }

    // ─── 6. RBAC & Security Isolation Tests ───────────────────────────────────

    public function test_tenant_cannot_access_staff_maintenance_routes(): void
    {
        [$user, $tenant, $room] = $this->createTenantWithActiveContract();
        $request = $this->createMaintenanceRequest($tenant, $room);

        $responseIndex = $this->actingAs($user)->get(route('maintenance.index'));
        $responseIndex->assertForbidden();

        $responseShow = $this->actingAs($user)->get(route('maintenance.show', $request));
        $responseShow->assertForbidden();

        $responseUpdate = $this->actingAs($user)->patch(route('maintenance.update-status', $request), [
            'status' => StatusMaintenance::Resolved->value,
        ]);
        $responseUpdate->assertForbidden();
    }

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $responsePortal = $this->get(route('portal.maintenance.index'));
        $responsePortal->assertRedirect(route('login'));

        $responseStaff = $this->get(route('maintenance.index'));
        $responseStaff->assertRedirect(route('login'));
    }
}
