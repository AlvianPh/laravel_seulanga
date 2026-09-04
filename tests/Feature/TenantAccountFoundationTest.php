<?php

namespace Tests\Feature;

use App\Enums\RoleUser;
use App\Enums\StatusKamar;
use App\Enums\StatusKontrak;
use App\Enums\StatusTagihan;
use App\Models\Contract;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * TenantAccountFoundationTest
 *
 * Pengujian komprehensif untuk fondasi akun dan otorisasi tenant (F2.1):
 *  1. Enum RoleUser::Tenant & helper methods
 *  2. Relasi 1-to-1 User ↔ Tenant & backward compatibility user_id null
 *  3. Alur autentikasi dan redirect spesifik role
 *  4. Boundary otorisasi RBAC (Tenant ditolak di rute operasional Owner/Admin)
 *  5. Staff ditolak di rute portal tenant
 *  6. Dashboard portal tenant (state terhubung vs belum terhubung)
 *  7. Profil mandiri tenant (update kontak yang diizinkan & proteksi field terlarang)
 */
class TenantAccountFoundationTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function createOwner(array $attrs = []): User
    {
        return User::factory()->owner()->create($attrs);
    }

    private function createAdmin(array $attrs = []): User
    {
        return User::factory()->admin()->create($attrs);
    }

    private function createTenantUser(array $attrs = []): User
    {
        return User::factory()->tenant()->create($attrs);
    }

    // ── 1. Enum & Model Helpers ──────────────────────────────────────────────

    public function test_role_tenant_exists_and_helpers_work_correctly(): void
    {
        $tenantUser = $this->createTenantUser();
        $adminUser = $this->createAdmin();
        $ownerUser = $this->createOwner();

        $this->assertEquals(RoleUser::Tenant, $tenantUser->role);
        $this->assertEquals('Penghuni', RoleUser::Tenant->label());

        $this->assertTrue($tenantUser->isTenant());
        $this->assertFalse($tenantUser->isAdmin());
        $this->assertFalse($tenantUser->isOwner());

        $this->assertTrue($adminUser->isAdmin());
        $this->assertFalse($adminUser->isTenant());

        $this->assertTrue($ownerUser->isOwner());
        $this->assertFalse($ownerUser->isTenant());
    }

    // ── 2. User ↔ Tenant Relationship & Backward Compatibility ────────────────

    public function test_user_can_have_linked_tenant_record(): void
    {
        $tenantUser = $this->createTenantUser();
        $tenant = Tenant::factory()->create([
            'user_id' => $tenantUser->id,
            'name' => $tenantUser->name,
            'email' => $tenantUser->email,
        ]);

        $this->assertTrue($tenantUser->tenant->is($tenant));
        $this->assertTrue($tenant->user->is($tenantUser));
    }

    public function test_existing_tenant_without_user_remains_valid(): void
    {
        // Data tenant lama (F1) yang belum punya akun login
        $legacyTenant = Tenant::factory()->create([
            'user_id' => null,
            'name' => 'Penghuni Lama',
        ]);

        $this->assertNull($legacyTenant->user_id);
        $this->assertNull($legacyTenant->user);
        $this->assertDatabaseHas('tenants', [
            'id' => $legacyTenant->id,
            'name' => 'Penghuni Lama',
            'user_id' => null,
        ]);
    }

    // ── 3. Authentication & Role-based Redirect ──────────────────────────────

    public function test_tenant_login_redirects_to_portal(): void
    {
        $tenantUser = $this->createTenantUser(['password' => bcrypt('password123')]);

        $response = $this->post('/login', [
            'email' => $tenantUser->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/portal');
        $this->assertAuthenticatedAs($tenantUser);
    }

    public function test_staff_login_redirects_to_dashboard(): void
    {
        $owner = $this->createOwner(['password' => bcrypt('password123')]);

        $response = $this->post('/login', [
            'email' => $owner->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($owner);
    }

    // ── 4. RBAC Authorization Boundaries ─────────────────────────────────────

    public function test_tenant_cannot_access_staff_dashboard(): void
    {
        $tenantUser = $this->createTenantUser();

        $this->actingAs($tenantUser)
            ->get('/dashboard')
            ->assertForbidden();
    }

    public function test_tenant_cannot_access_users_management(): void
    {
        $tenantUser = $this->createTenantUser();

        $this->actingAs($tenantUser)
            ->get('/users')
            ->assertForbidden();
    }

    public function test_tenant_cannot_access_operational_routes(): void
    {
        $tenantUser = $this->createTenantUser();

        $this->actingAs($tenantUser)->get('/rooms')->assertForbidden();
        $this->actingAs($tenantUser)->get('/tenants')->assertForbidden();
        $this->actingAs($tenantUser)->get('/contracts')->assertForbidden();
        $this->actingAs($tenantUser)->get('/invoices')->assertForbidden();
        $this->actingAs($tenantUser)->get('/payments')->assertForbidden();
        $this->actingAs($tenantUser)->get('/expenses')->assertForbidden();
        $this->actingAs($tenantUser)->get('/reports')->assertForbidden();
        $this->actingAs($tenantUser)->get('/settings')->assertForbidden();
    }

    public function test_staff_cannot_access_tenant_portal(): void
    {
        $owner = $this->createOwner();
        $admin = $this->createAdmin();

        $this->actingAs($owner)->get('/portal')->assertForbidden();
        $this->actingAs($admin)->get('/portal')->assertForbidden();
    }

    // ── 5. Tenant Portal Dashboard ───────────────────────────────────────────

    public function test_tenant_can_access_portal_dashboard_when_unlinked(): void
    {
        $tenantUser = $this->createTenantUser();

        $response = $this->actingAs($tenantUser)->get('/portal');

        $response->assertOk();
        $response->assertSee('Halo, '.$tenantUser->name);
        $response->assertSee('Akun Belum Terhubung dengan Data Penghuni');
    }

    public function test_tenant_can_access_portal_dashboard_with_linked_room_and_invoice(): void
    {
        $tenantUser = $this->createTenantUser();
        $tenant = Tenant::factory()->create(['user_id' => $tenantUser->id]);

        $room = Room::factory()->create([
            'room_number' => '201',
            'status' => StatusKamar::Occupied,
        ]);

        $contract = Contract::factory()->create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'status' => StatusKontrak::Active,
        ]);

        $invoice = Invoice::factory()->create([
            'contract_id' => $contract->id,
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'total_amount' => 1500000,
            'status' => StatusTagihan::Pending,
        ]);

        $response = $this->actingAs($tenantUser)->get('/portal');

        $response->assertOk();
        $response->assertSee('Kamar 201');
        $response->assertSee('1.500.000');
    }

    // ── 6. Tenant Profile Self-Service ───────────────────────────────────────

    public function test_tenant_can_view_own_profile(): void
    {
        $tenantUser = $this->createTenantUser();
        $tenant = Tenant::factory()->create([
            'user_id' => $tenantUser->id,
            'phone' => '081299998888',
            'nik' => '3201123456789012',
        ]);

        $response = $this->actingAs($tenantUser)->get('/portal/profile');

        $response->assertOk();
        $response->assertSee($tenantUser->name);
        $response->assertSee($tenantUser->email);
        $response->assertSee('081299998888');
        $response->assertSee('3201123456789012');
    }

    public function test_tenant_can_update_own_profile_and_syncs_to_tenant_record(): void
    {
        $tenantUser = $this->createTenantUser();
        $tenant = Tenant::factory()->create([
            'user_id' => $tenantUser->id,
            'phone' => '081211112222',
        ]);

        $response = $this->actingAs($tenantUser)->patch('/portal/profile', [
            'name' => 'Budi Updated',
            'email' => 'budi.baru@example.test',
            'phone' => '081299990000',
            'address' => 'Jl. Baru No. 12',
            'emergency_contact_name' => 'Ibu Budi',
            'emergency_contact_phone' => '081288887777',
        ]);

        $response->assertRedirect('/portal/profile');
        $response->assertSessionHas('status', 'Profil berhasil diperbarui.');

        // Verify User updated
        $this->assertDatabaseHas('users', [
            'id' => $tenantUser->id,
            'name' => 'Budi Updated',
            'email' => 'budi.baru@example.test',
        ]);

        // Verify Tenant record updated
        $this->assertDatabaseHas('tenants', [
            'id' => $tenant->id,
            'name' => 'Budi Updated',
            'email' => 'budi.baru@example.test',
            'phone' => '081299990000',
            'address' => 'Jl. Baru No. 12',
            'emergency_contact_name' => 'Ibu Budi',
            'emergency_contact_phone' => '081288887777',
        ]);
    }

    public function test_tenant_cannot_tamper_role_via_profile_update(): void
    {
        $tenantUser = $this->createTenantUser();

        $this->actingAs($tenantUser)->patch('/portal/profile', [
            'name' => 'Hacker Tenant',
            'email' => $tenantUser->email,
            'role' => 'owner', // Injeksi atribut terlarang
        ]);

        // Role tetap Tenant, tidak berubah jadi Owner
        $this->assertDatabaseHas('users', [
            'id' => $tenantUser->id,
            'role' => RoleUser::Tenant,
        ]);
    }
}
