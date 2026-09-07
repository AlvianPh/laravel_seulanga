<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * Model TenantDocument — dokumen identitas/syarat/persetujuan milik penghuni.
 *
 * @property int $id
 * @property int $tenant_id
 * @property int $uploaded_by
 * @property DocumentType $type
 * @property DocumentStatus $status
 * @property string $title
 * @property string $file_path
 * @property string $file_name
 * @property int $file_size
 * @property string $mime_type
 * @property string|null $description
 * @property string|null $rejection_reason
 * @property int|null $verified_by
 * @property Carbon|null $verified_at
 */
class TenantDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'uploaded_by',
        'type',
        'status',
        'title',
        'file_path',
        'file_name',
        'file_size',
        'mime_type',
        'description',
        'rejection_reason',
        'verified_by',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'type' => DocumentType::class,
            'status' => DocumentStatus::class,
            'file_size' => 'integer',
            'verified_at' => 'datetime',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /** Penghuni pemilik dokumen. */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class)->withTrashed();
    }

    /** User yang mengunggah dokumen. */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /** Staff yang memverifikasi/menolak dokumen. */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    /** Format ukuran file agar mudah dibaca manusia (KB / MB). */
    public function formattedSize(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1).' KB';
        }

        return $bytes.' B';
    }

    /** Cek apakah dokumen berformat gambar. */
    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }

    /** Cek apakah dokumen berformat PDF. */
    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }
}
