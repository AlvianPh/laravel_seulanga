<?php

namespace Tests\Feature;

use App\Enums\RoleUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * AuthorizationTest — memastikan aturan akses Owner vs Admin berjalan benar.
 *
 * Skenario yang diuji:
 *  1. Pengguna yang belum login diarahkan ke halaman login
 *  2. Admin gagal (403) saat mencoba mengakses manajemen user
 *  3. Owner berhasil mengakses manajemen user
 *  4. Admin berhasil mengakses modul operasional (dashboard)
 *  5. Owner berhasil membuat akun user baru
 *  6. Admin gagal membuat akun user baru
 *  7. Owner berhasil update user lain (termasuk ganti role)
 *  8. Owner gagal update dirinya sendiri (proteksi self-edit)
 *  9. Owner berhasil menghapus user lain
 * 10. Owner gagal menghapus dirinya sendiri
 */
class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function owner(array $attrs = []): User
    {
        return User::factory()->owner()->create($attrs);
    }

    private function admin(array $attrs = []): User
    {
        return User::factory()->admin()->create($attrs);
    }

    // ── 1. Guest tidak bisa akses halaman protected ───────────────────────────

    public function test_guest_diarahkan_ke_login_saat_akses_dashboard(): void
    {
        $this->get('/dashboard')
            ->assertRedirect('/login');
    }

    public function test_guest_diarahkan_ke_login_saat_akses_users(): void
    {
        $this->get('/users')
            ->assertRedirect('/login');
    }

    // ── 2. Admin gagal akses manajemen user ──────────────────────────────────

    public function test_admin_mendapat_403_saat_akses_daftar_user(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get('/users')
            ->assertForbidden();
    }

    public function test_admin_mendapat_403_saat_akses_form_tambah_user(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get('/users/create')
            ->assertForbidden();
    }

    public function test_admin_mendapat_403_saat_post_tambah_user(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->post('/users', [
                'name'                  => 'User Baru',
                'email'                 => 'baru@test.com',
                'role'                  => 'admin',
                'password'              => 'password123',
                'password_confirmation' => 'password123',
            ])
            ->assertForbidden();
    }

    public function test_admin_mendapat_403_saat_akses_form_edit_user(): void
    {
        $admin       = $this->admin();
        $targetUser  = $this->owner();

        $this->actingAs($admin)
            ->get("/users/{$targetUser->id}/edit")
            ->assertForbidden();
    }

    public function test_admin_mendapat_403_saat_update_user(): void
    {
        $admin      = $this->admin();
        $targetUser = $this->owner();

        $this->actingAs($admin)
            ->patch("/users/{$targetUser->id}", [
                'name'  => 'Diubah',
                'email' => $targetUser->email,
                'role'  => 'admin',
            ])
            ->assertForbidden();
    }

    public function test_admin_mendapat_403_saat_hapus_user(): void
    {
        $admin      = $this->admin();
        $targetUser = $this->owner();

        $this->actingAs($admin)
            ->delete("/users/{$targetUser->id}")
            ->assertForbidden();
    }

    // ── 3. Owner berhasil akses manajemen user ────────────────────────────────

    public function test_owner_bisa_akses_daftar_user(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get('/users')
            ->assertOk();
    }

    public function test_owner_bisa_akses_form_tambah_user(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get('/users/create')
            ->assertOk();
    }

    public function test_owner_bisa_akses_detail_user(): void
    {
        $owner      = $this->owner();
        $targetUser = $this->admin();

        $this->actingAs($owner)
            ->get("/users/{$targetUser->id}")
            ->assertOk();
    }

    public function test_owner_bisa_akses_form_edit_user(): void
    {
        $owner      = $this->owner();
        $targetUser = $this->admin();

        $this->actingAs($owner)
            ->get("/users/{$targetUser->id}/edit")
            ->assertOk();
    }

    // ── 4. Admin berhasil akses modul operasional (dashboard) ─────────────────

    public function test_admin_bisa_akses_dashboard(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk();
    }

    public function test_owner_bisa_akses_dashboard(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->get('/dashboard')
            ->assertOk();
    }

    // ── 5. Owner berhasil membuat user baru ───────────────────────────────────

    public function test_owner_bisa_buat_user_baru(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->post('/users', [
                'name'                  => 'Admin Baru',
                'email'                 => 'adminbaru@kost.test',
                'role'                  => 'admin',
                'password'              => 'Password123!',
                'password_confirmation' => 'Password123!',
            ])
            ->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'email' => 'adminbaru@kost.test',
            'role'  => 'admin',
        ]);
    }

    public function test_owner_bisa_buat_owner_baru(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->post('/users', [
                'name'                  => 'Owner Baru',
                'email'                 => 'owner2@kost.test',
                'role'                  => 'owner',
                'password'              => 'Password123!',
                'password_confirmation' => 'Password123!',
            ])
            ->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'email' => 'owner2@kost.test',
            'role'  => 'owner',
        ]);
    }

    // ── 6. Owner berhasil update user lain ────────────────────────────────────

    public function test_owner_bisa_update_user_lain(): void
    {
        $owner      = $this->owner();
        $targetUser = $this->admin();

        $this->actingAs($owner)
            ->patch("/users/{$targetUser->id}", [
                'name'  => 'Nama Diubah',
                'email' => $targetUser->email,
                'role'  => 'owner',
            ])
            ->assertRedirect('/users');

        $this->assertDatabaseHas('users', [
            'id'   => $targetUser->id,
            'name' => 'Nama Diubah',
            'role' => 'owner',
        ]);
    }

    // ── 7. Owner tidak bisa update dirinya sendiri ────────────────────────────

    public function test_owner_tidak_bisa_edit_dirinya_sendiri(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->patch("/users/{$owner->id}", [
                'name'  => 'Diubah Sendiri',
                'email' => $owner->email,
                'role'  => 'admin',
            ])
            ->assertForbidden();
    }

    // ── 8. Owner berhasil menghapus user lain ─────────────────────────────────

    public function test_owner_bisa_hapus_user_lain(): void
    {
        $owner      = $this->owner();
        $targetUser = $this->admin();

        $this->actingAs($owner)
            ->delete("/users/{$targetUser->id}")
            ->assertRedirect('/users');

        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
    }

    // ── 9. Owner tidak bisa hapus dirinya sendiri ─────────────────────────────

    public function test_owner_tidak_bisa_hapus_dirinya_sendiri(): void
    {
        $owner = $this->owner();

        $this->actingAs($owner)
            ->delete("/users/{$owner->id}")
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $owner->id]);
    }

    // ── 10. Registrasi publik dinonaktifkan ───────────────────────────────────

    public function test_registrasi_publik_diarahkan_ke_login(): void
    {
        $this->get('/register')
            ->assertRedirect('/login');
    }

    public function test_post_registrasi_publik_diarahkan_ke_login(): void
    {
        $this->post('/register', [
            'name'                  => 'Siapapun',
            'email'                 => 'siapapun@test.com',
            'password'              => 'Password123!',
            'password_confirmation' => 'Password123!',
        ])->assertRedirect('/login');

        $this->assertDatabaseMissing('users', ['email' => 'siapapun@test.com']);
    }
}
