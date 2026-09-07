<?php

namespace Tests\Feature;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Enums\RoleUser;
use App\Models\Tenant;
use App\Models\TenantDocument;
use App\Models\User;
use App\Services\TenantDocumentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TenantDocumentTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected User $admin;

    protected User $tenantUserA;

    protected Tenant $tenantA;

    protected User $tenantUserB;

    protected Tenant $tenantB;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');

        $this->owner = User::factory()->create(['role' => RoleUser::Owner]);
        $this->admin = User::factory()->create(['role' => RoleUser::Admin]);

        $this->tenantUserA = User::factory()->create(['role' => RoleUser::Tenant]);
        $this->tenantA = Tenant::factory()->create(['user_id' => $this->tenantUserA->id]);

        $this->tenantUserB = User::factory()->create(['role' => RoleUser::Tenant]);
        $this->tenantB = Tenant::factory()->create(['user_id' => $this->tenantUserB->id]);
    }

    // ─── Tenant Access & IDOR ────────────────────────────────────────────────

    public function test_tenant_can_view_own_documents_index(): void
    {
        TenantDocument::factory()->create([
            'tenant_id' => $this->tenantA->id,
            'uploaded_by' => $this->tenantUserA->id,
            'title' => 'KTP Penghuni A',
        ]);

        $response = $this->actingAs($this->tenantUserA)->get(route('portal.documents.index'));

        $response->assertOk()
            ->assertSee('KTP Penghuni A');
    }

    public function test_tenant_cannot_see_other_tenant_documents_in_index(): void
    {
        TenantDocument::factory()->create([
            'tenant_id' => $this->tenantA->id,
            'uploaded_by' => $this->tenantUserA->id,
            'title' => 'Dokumen Rahasia A',
        ]);

        TenantDocument::factory()->create([
            'tenant_id' => $this->tenantB->id,
            'uploaded_by' => $this->tenantUserB->id,
            'title' => 'Dokumen Rahasia B',
        ]);

        $response = $this->actingAs($this->tenantUserA)->get(route('portal.documents.index'));

        $response->assertOk()
            ->assertSee('Dokumen Rahasia A')
            ->assertDontSee('Dokumen Rahasia B');
    }

    public function test_tenant_can_view_own_document_detail(): void
    {
        $doc = TenantDocument::factory()->create([
            'tenant_id' => $this->tenantA->id,
            'uploaded_by' => $this->tenantUserA->id,
            'title' => 'Dokumen Saya',
        ]);

        $response = $this->actingAs($this->tenantUserA)->get(route('portal.documents.show', $doc));

        $response->assertOk()
            ->assertSee('Dokumen Saya');
    }

    public function test_tenant_cannot_view_other_tenant_document_detail_idor_blocked(): void
    {
        $docB = TenantDocument::factory()->create([
            'tenant_id' => $this->tenantB->id,
            'uploaded_by' => $this->tenantUserB->id,
            'title' => 'Dokumen Tenant B',
        ]);

        $response = $this->actingAs($this->tenantUserA)->get(route('portal.documents.show', $docB));

        $response->assertForbidden();
    }

    public function test_tenant_can_download_and_preview_own_document(): void
    {
        $file = UploadedFile::fake()->create('ktp_asli.pdf', 500, 'application/pdf');
        $service = app(TenantDocumentService::class);
        $doc = $service->createDocument($this->tenantA, $this->tenantUserA, [
            'title' => 'KTP Asli',
            'type' => DocumentType::Ktp,
        ], $file);

        // Download
        $resDownload = $this->actingAs($this->tenantUserA)->get(route('portal.documents.download', $doc));
        $resDownload->assertOk();

        // Preview
        $resPreview = $this->actingAs($this->tenantUserA)->get(route('portal.documents.preview', $doc));
        $resPreview->assertOk();
    }

    public function test_tenant_cannot_download_other_tenant_document_idor_blocked(): void
    {
        $file = UploadedFile::fake()->create('ktp_b.pdf', 500, 'application/pdf');
        $service = app(TenantDocumentService::class);
        $docB = $service->createDocument($this->tenantB, $this->tenantUserB, [
            'title' => 'KTP Tenant B',
            'type' => DocumentType::Ktp,
        ], $file);

        $response = $this->actingAs($this->tenantUserA)->get(route('portal.documents.download', $docB));

        $response->assertForbidden();
    }

    // ─── Tenant Upload & File Validation ─────────────────────────────────────

    public function test_tenant_can_upload_allowed_document_type(): void
    {
        $file = UploadedFile::fake()->create('ktp_saya.jpg', 300, 'image/jpeg');

        $response = $this->actingAs($this->tenantUserA)->post(route('portal.documents.store'), [
            'title' => 'Foto KTP Depan',
            'type' => 'ktp',
            'file' => $file,
            'description' => 'Foto e-KTP jelas tanpa watermark',
        ]);

        $response->assertRedirect(route('portal.documents.index'));
        $this->assertDatabaseHas('tenant_documents', [
            'tenant_id' => $this->tenantA->id,
            'uploaded_by' => $this->tenantUserA->id,
            'title' => 'Foto KTP Depan',
            'type' => 'ktp',
            'status' => 'pending',
            'file_name' => 'ktp_saya.jpg',
        ]);

        $doc = TenantDocument::where('title', 'Foto KTP Depan')->first();
        $this->assertNotNull($doc);
        Storage::disk('local')->assertExists($doc->file_path);
    }

    public function test_tenant_upload_disallowed_type_is_rejected(): void
    {
        $file = UploadedFile::fake()->create('kontrak.pdf', 300, 'application/pdf');

        $response = $this->actingAs($this->tenantUserA)->post(route('portal.documents.store'), [
            'title' => 'Kontrak Sewa Sendiri',
            'type' => 'contract', // disallowed for tenant upload
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('type');
    }

    public function test_tenant_upload_invalid_mime_is_rejected(): void
    {
        $file = UploadedFile::fake()->create('script.exe', 100, 'application/x-msdownload');

        $response = $this->actingAs($this->tenantUserA)->post(route('portal.documents.store'), [
            'title' => 'File Berbahaya',
            'type' => 'other',
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_tenant_upload_oversized_file_is_rejected(): void
    {
        $file = UploadedFile::fake()->create('large_doc.pdf', 6000, 'application/pdf'); // 6MB > 5MB

        $response = $this->actingAs($this->tenantUserA)->post(route('portal.documents.store'), [
            'title' => 'Dokumen Raksasa',
            'type' => 'other',
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
    }

    public function test_tenant_cannot_tamper_with_ownership_or_verification_status_on_upload(): void
    {
        $file = UploadedFile::fake()->create('ktp.pdf', 300, 'application/pdf');

        $this->actingAs($this->tenantUserA)->post(route('portal.documents.store'), [
            'tenant_id' => $this->tenantB->id,
            'uploaded_by' => $this->owner->id,
            'status' => 'verified',
            'title' => 'KTP Palsu',
            'type' => 'ktp',
            'file' => $file,
        ]);

        $this->assertDatabaseHas('tenant_documents', [
            'title' => 'KTP Palsu',
            'tenant_id' => $this->tenantA->id,
            'uploaded_by' => $this->tenantUserA->id,
            'status' => 'pending',
        ]);
    }

    public function test_tenant_can_delete_own_pending_document(): void
    {
        $file = UploadedFile::fake()->create('ktp_temp.pdf', 200, 'application/pdf');
        $service = app(TenantDocumentService::class);
        $doc = $service->createDocument($this->tenantA, $this->tenantUserA, [
            'title' => 'KTP Temp',
            'type' => DocumentType::Ktp,
        ], $file);

        $this->assertTrue(Storage::disk('local')->exists($doc->file_path));

        $response = $this->actingAs($this->tenantUserA)->delete(route('portal.documents.destroy', $doc));

        $response->assertRedirect(route('portal.documents.index'));
        $this->assertDatabaseMissing('tenant_documents', ['id' => $doc->id]);
        $this->assertFalse(Storage::disk('local')->exists($doc->file_path));
    }

    public function test_tenant_cannot_delete_verified_document(): void
    {
        $doc = TenantDocument::factory()->create([
            'tenant_id' => $this->tenantA->id,
            'uploaded_by' => $this->tenantUserA->id,
            'status' => DocumentStatus::Verified,
        ]);

        $response = $this->actingAs($this->tenantUserA)->delete(route('portal.documents.destroy', $doc));

        $response->assertForbidden();
        $this->assertDatabaseHas('tenant_documents', ['id' => $doc->id]);
    }

    public function test_tenant_cannot_verify_or_reject_own_document(): void
    {
        $doc = TenantDocument::factory()->create([
            'tenant_id' => $this->tenantA->id,
            'uploaded_by' => $this->tenantUserA->id,
            'status' => DocumentStatus::Pending,
        ]);

        $response = $this->actingAs($this->tenantUserA)->post(route('documents.review', $doc), [
            'action' => 'verify',
        ]);

        $response->assertForbidden();
    }

    // ─── Staff Access & Review ───────────────────────────────────────────────

    public function test_admin_and_owner_can_view_all_tenant_documents(): void
    {
        $docA = TenantDocument::factory()->create(['tenant_id' => $this->tenantA->id, 'title' => 'Doc Tenant A']);
        $docB = TenantDocument::factory()->create(['tenant_id' => $this->tenantB->id, 'title' => 'Doc Tenant B']);

        // Admin
        $resAdmin = $this->actingAs($this->admin)->get(route('documents.index'));
        $resAdmin->assertOk()->assertSee('Doc Tenant A')->assertSee('Doc Tenant B');

        // Owner
        $resOwner = $this->actingAs($this->owner)->get(route('documents.index'));
        $resOwner->assertOk()->assertSee('Doc Tenant A')->assertSee('Doc Tenant B');
    }

    public function test_staff_can_verify_tenant_document(): void
    {
        $doc = TenantDocument::factory()->create([
            'tenant_id' => $this->tenantA->id,
            'uploaded_by' => $this->tenantUserA->id,
            'status' => DocumentStatus::Pending,
        ]);

        $response = $this->actingAs($this->admin)->post(route('documents.review', $doc), [
            'action' => 'verify',
        ]);

        $response->assertRedirect(route('documents.show', $doc));
        $doc->refresh();
        $this->assertSame(DocumentStatus::Verified, $doc->status);
        $this->assertSame($this->admin->id, $doc->verified_by);
        $this->assertNotNull($doc->verified_at);
        $this->assertNull($doc->rejection_reason);
    }

    public function test_staff_can_reject_tenant_document_with_reason(): void
    {
        $doc = TenantDocument::factory()->create([
            'tenant_id' => $this->tenantA->id,
            'uploaded_by' => $this->tenantUserA->id,
            'status' => DocumentStatus::Pending,
        ]);

        $response = $this->actingAs($this->owner)->post(route('documents.review', $doc), [
            'action' => 'reject',
            'rejection_reason' => 'Foto KTP buram dan NIK terpotong.',
        ]);

        $response->assertRedirect(route('documents.show', $doc));
        $doc->refresh();
        $this->assertSame(DocumentStatus::Rejected, $doc->status);
        $this->assertSame('Foto KTP buram dan NIK terpotong.', $doc->rejection_reason);
        $this->assertSame($this->owner->id, $doc->verified_by);
        $this->assertNotNull($doc->verified_at);
    }

    public function test_staff_reject_without_reason_is_rejected(): void
    {
        $doc = TenantDocument::factory()->create([
            'tenant_id' => $this->tenantA->id,
            'status' => DocumentStatus::Pending,
        ]);

        $response = $this->actingAs($this->admin)->post(route('documents.review', $doc), [
            'action' => 'reject',
            'rejection_reason' => null,
        ]);

        $response->assertSessionHasErrors('rejection_reason');
    }

    public function test_staff_can_download_and_delete_document(): void
    {
        $file = UploadedFile::fake()->create('doc_staff.pdf', 300, 'application/pdf');
        $service = app(TenantDocumentService::class);
        $doc = $service->createDocument($this->tenantA, $this->tenantUserA, [
            'title' => 'Doc Staff Test',
            'type' => DocumentType::Other,
        ], $file);

        // Download by admin
        $resDownload = $this->actingAs($this->admin)->get(route('documents.download', $doc));
        $resDownload->assertOk();

        // Delete by owner
        $resDelete = $this->actingAs($this->owner)->delete(route('documents.destroy', $doc));
        $resDelete->assertRedirect(route('documents.index'));
        $this->assertDatabaseMissing('tenant_documents', ['id' => $doc->id]);
        $this->assertFalse(Storage::disk('local')->exists($doc->file_path));
    }
}
