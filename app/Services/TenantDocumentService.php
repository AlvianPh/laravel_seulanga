<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Models\Tenant;
use App\Models\TenantDocument;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * TenantDocumentService — Mengelola penyimpanan berkas dokumen privat penghuni dan verifikasi.
 */
class TenantDocumentService
{
    /** Disk penyimpanan dokumen privat penghuni */
    protected string $disk = 'local';

    /**
     * Mengunggah dan mencatat dokumen penghuni baru ke penyimpanan privat yang aman.
     *
     * @param  array{title: string, type: DocumentType|string, description?: string|null}  $data
     */
    public function createDocument(Tenant $tenant, User $uploader, array $data, UploadedFile $file): TenantDocument
    {
        return DB::transaction(function () use ($tenant, $uploader, $data, $file) {
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension() ?: 'bin';
            $mimeType = $file->getClientMimeType() ?: 'application/octet-stream';
            $fileSize = $file->getSize();

            // Simpan file dengan nama acak aman di direktori privat
            $storedName = Str::uuid().'.'.$extension;
            $directory = "documents/{$tenant->id}";
            $path = $file->storeAs($directory, $storedName, $this->disk);

            $type = $data['type'] instanceof DocumentType
                ? $data['type']
                : DocumentType::from($data['type']);

            return TenantDocument::create([
                'tenant_id' => $tenant->id,
                'uploaded_by' => $uploader->id,
                'type' => $type,
                'status' => DocumentStatus::Pending,
                'title' => $data['title'],
                'file_path' => $path,
                'file_name' => $originalName,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'description' => $data['description'] ?? null,
                'rejection_reason' => null,
                'verified_by' => null,
                'verified_at' => null,
            ]);
        });
    }

    /**
     * Memverifikasi dokumen penghuni oleh staff/admin/owner.
     */
    public function verifyDocument(TenantDocument $document, User $staff): TenantDocument
    {
        $document->update([
            'status' => DocumentStatus::Verified,
            'verified_by' => $staff->id,
            'verified_at' => now(),
            'rejection_reason' => null,
        ]);

        return $document->fresh();
    }

    /**
     * Menolak dokumen penghuni dengan alasan yang jelas.
     */
    public function rejectDocument(TenantDocument $document, User $staff, string $reason): TenantDocument
    {
        $document->update([
            'status' => DocumentStatus::Rejected,
            'verified_by' => $staff->id,
            'verified_at' => now(),
            'rejection_reason' => $reason,
        ]);

        return $document->fresh();
    }

    /**
     * Menghapus dokumen penghuni dan membersihkan berkas fisiknya dari storage privat.
     */
    public function deleteDocument(TenantDocument $document): bool
    {
        return DB::transaction(function () use ($document) {
            if ($document->file_path && Storage::disk($this->disk)->exists($document->file_path)) {
                Storage::disk($this->disk)->delete($document->file_path);
            }

            return $document->delete();
        });
    }

    /**
     * Mengunduh berkas dokumen dengan nama berkas asli.
     */
    public function download(TenantDocument $document): StreamedResponse
    {
        if (! Storage::disk($this->disk)->exists($document->file_path)) {
            abort(404, 'Berkas dokumen tidak ditemukan di server.');
        }

        return Storage::disk($this->disk)->download($document->file_path, $document->file_name);
    }

    /**
     * Menampilkan/stream berkas dokumen di browser (inline preview untuk PDF/gambar).
     */
    public function stream(TenantDocument $document): BinaryFileResponse
    {
        if (! Storage::disk($this->disk)->exists($document->file_path)) {
            abort(404, 'Berkas dokumen tidak ditemukan di server.');
        }

        $fullPath = Storage::disk($this->disk)->path($document->file_path);

        return response()->file($fullPath, [
            'Content-Type' => $document->mime_type,
            'Content-Disposition' => 'inline; filename="'.addslashes($document->file_name).'"',
        ]);
    }
}
