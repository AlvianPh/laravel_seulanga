<?php

namespace App\Models;

use App\Enums\JenisKelamin;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Tenant — data diri penghuni kost.
 *
 * @property int $id
 * @property string $name
 * @property string $nik
 * @property string $phone
 * @property string|null $email
 * @property JenisKelamin $gender
 * @property string|null $birth_date
 * @property string|null $address
 * @property string|null $ktp_photo_path
 * @property string|null $tenant_photo_path
 * @property string|null $emergency_contact_name
 * @property string|null $emergency_contact_phone
 */
class Tenant extends Model
{
    use HasFactory, \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'nik',
        'phone',
        'email',
        'gender',
        'birth_date',
        'address',
        'ktp_photo_path',
        'tenant_photo_path',
        'emergency_contact_name',
        'emergency_contact_phone',
    ];

    protected function casts(): array
    {
        return [
            'gender' => JenisKelamin::class,
            'birth_date' => 'date',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /** Akun login penghuni (User) jika sudah dibuat. */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Semua kontrak penghuni ini (termasuk riwayat). */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /** Semua tagihan penghuni ini. */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /** Semua pembayaran penghuni ini. */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    /** Ambil kontrak yang sedang aktif (jika ada). */
    public function activeContract(): ?Contract
    {
        return $this->contracts()->where('status', 'active')->latest()->first();
    }
}
