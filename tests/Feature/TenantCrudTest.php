<?php

namespace Tests\Feature;

use App\Enums\JenisKelamin;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * TenantCrudTest — memastikan fungsi CRUD penghuni berjalan benar,
 * termasuk validasi custom seperti NIK dan nomor HP.
 */
class TenantCrudTest extends TestCase
{
    use RefreshDatabase;

    private function authenticate(string $role = 'admin'): User
    {
        $user = User::factory()->create(['role' => $role]);
        $this->actingAs($user);
        return $user;
    }

    public function test_guest_cannot_access_tenants()
    {
        $this->get('/tenants')->assertRedirect('/login');
    }

    public function test_admin_and_owner_can_access_tenants_index()
    {
        $this->authenticate('admin');
        $this->get('/tenants')->assertOk();

        $this->authenticate('owner');
        $this->get('/tenants')->assertOk();
    }

    public function test_user_can_create_tenant_with_valid_data()
    {
        $this->authenticate('admin');

        $response = $this->post('/tenants', [
            'name'   => 'Budi Santoso',
            'nik'    => '1234567890123456', // 16 digit
            'phone'  => '081234567890',     // diawali 08
            'gender' => JenisKelamin::Male->value,
        ]);

        $response->assertRedirect('/tenants');
        $this->assertDatabaseHas('tenants', [
            'name' => 'Budi Santoso',
            'nik'  => '1234567890123456',
        ]);
    }

    public function test_create_tenant_validation_fails_on_duplicate_nik()
    {
        Tenant::factory()->create(['nik' => '1111222233334444']);
        
        $this->authenticate('admin');

        $response = $this->post('/tenants', [
            'name'   => 'Orang Lain',
            'nik'    => '1111222233334444', // Duplicate
            'phone'  => '08111222333',
            'gender' => JenisKelamin::Male->value,
        ]);

        $response->assertSessionHasErrors('nik');
    }

    public function test_create_tenant_validation_fails_on_invalid_phone()
    {
        $this->authenticate('admin');

        $response = $this->post('/tenants', [
            'name'   => 'Salah Nomor',
            'nik'    => '9999888877776666',
            'phone'  => '1234567890', // Tidak diawali 08/62
            'gender' => JenisKelamin::Female->value,
        ]);

        $response->assertSessionHasErrors('phone');
    }

    public function test_create_tenant_validation_fails_on_invalid_nik_length()
    {
        $this->authenticate('admin');

        $response = $this->post('/tenants', [
            'name'   => 'Salah NIK',
            'nik'    => '12345', // Kurang dari 16
            'phone'  => '08123456789',
            'gender' => JenisKelamin::Male->value,
        ]);

        $response->assertSessionHasErrors('nik');
    }

    public function test_user_can_upload_tenant_documents_during_create()
    {
        Storage::fake('public');
        $this->authenticate('admin');

        $ktpPhoto    = UploadedFile::fake()->image('ktp.jpg');
        $tenantPhoto = UploadedFile::fake()->image('profil.png');

        $this->post('/tenants', [
            'name'         => 'Dengan Foto',
            'nik'          => '5555666677778888',
            'phone'        => '08555666777',
            'gender'       => JenisKelamin::Female->value,
            'ktp_photo'    => $ktpPhoto,
            'tenant_photo' => $tenantPhoto,
        ]);

        $tenant = Tenant::where('nik', '5555666677778888')->first();
        
        $this->assertNotNull($tenant->ktp_photo_path);
        $this->assertNotNull($tenant->tenant_photo_path);
        
        Storage::disk('public')->assertExists($tenant->ktp_photo_path);
        Storage::disk('public')->assertExists($tenant->tenant_photo_path);
    }

    public function test_user_can_update_tenant()
    {
        $tenant = Tenant::factory()->create(['name' => 'Nama Lama']);
        $this->authenticate('owner');

        $response = $this->patch("/tenants/{$tenant->id}", [
            'name'   => 'Nama Baru',
            'nik'    => $tenant->nik,
            'phone'  => '081299998888', // Nomor baru
            'gender' => $tenant->gender->value,
        ]);

        $response->assertRedirect('/tenants');
        $this->assertDatabaseHas('tenants', [
            'id'    => $tenant->id,
            'name'  => 'Nama Baru',
            'phone' => '081299998888',
        ]);
    }

    public function test_user_can_delete_tenant_and_files_are_deleted()
    {
        Storage::fake('public');
        $this->authenticate('admin');
        
        // Buat file fake di storage dan hubungkan ke tenant
        $ktpFile = UploadedFile::fake()->image('ktp.jpg');
        $ktpPath = $ktpFile->store('tenants/ktp', 'public');
        
        $tenant = Tenant::factory()->create([
            'ktp_photo_path' => $ktpPath
        ]);

        Storage::disk('public')->assertExists($ktpPath);

        $response = $this->delete("/tenants/{$tenant->id}");
        
        $response->assertRedirect('/tenants');
        $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
        
        // File fisik KTP harus terhapus
        Storage::disk('public')->assertMissing($ktpPath);
    }
}
