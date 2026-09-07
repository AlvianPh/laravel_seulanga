<?php

namespace App\Models;

use App\Enums\StatusKontrak;
use App\Enums\StatusPembayaran;
use App\Enums\StatusTagihan;
use Database\Factories\ContractFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Model Contract — perjanjian sewa antara penghuni dan kamar.
 *
 * @property int $id
 * @property int $tenant_id
 * @property int $room_id
 * @property string $start_date
 * @property string $end_date
 * @property float $rent_price Snapshot harga sewa saat kontrak dibuat
 * @property float $deposit_amount
 * @property StatusKontrak $status
 * @property string|null $notes
 * @property int $created_by
 * @property int|null $application_id
 * @property Carbon|null $agreement_accepted_at
 * @property int|null $agreement_accepted_by
 * @property string|null $agreement_version
 */
class Contract extends Model
{
    /** @use HasFactory<ContractFactory> */
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'room_id',
        'start_date',
        'end_date',
        'rent_price',
        'deposit_amount',
        'status',
        'notes',
        'created_by',
        'application_id',
        'agreement_accepted_at',
        'agreement_accepted_by',
        'agreement_version',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusKontrak::class,
            'start_date' => 'date',
            'end_date' => 'date',
            'rent_price' => 'decimal:2',
            'deposit_amount' => 'decimal:2',
            'agreement_accepted_at' => 'datetime',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /** Penghuni yang menyewa. */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class)->withTrashed();
    }

    /** Kamar yang disewa. */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class)->withTrashed();
    }

    /** User yang membuat kontrak ini. */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** Tenant Application yang mendasari pembuatan kontrak ini jika ada. */
    public function application(): BelongsTo
    {
        return $this->belongsTo(TenantApplication::class, 'application_id');
    }

    /** User / Tenant yang menyetujui tata tertib / agreement. */
    public function agreementAcceptedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agreement_accepted_by');
    }

    /** Semua tagihan yang dihasilkan kontrak ini. */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    /** Cek apakah kontrak masih berstatus draft. */
    public function isDraft(): bool
    {
        return $this->status === StatusKontrak::Draft;
    }

    /** Cek apakah kontrak masih aktif. */
    public function isActive(): bool
    {
        return $this->status === StatusKontrak::Active;
    }

    /** Cek apakah tata tertib / agreement telah disetujui. */
    public function isAgreementAccepted(): bool
    {
        return $this->agreement_accepted_at !== null;
    }

    /** Mengambil tagihan awal / invoice pertama terkait kontrak ini. */
    public function initialInvoice(): ?Invoice
    {
        return $this->invoices()->oldest()->first();
    }

    /** Cek apakah pembayaran awal sudah lunas atau diverifikasi. */
    public function hasVerifiedInitialPayment(): bool
    {
        $invoice = $this->initialInvoice();
        if (! $invoice) {
            return true;
        }

        if ($invoice->status === StatusTagihan::Paid) {
            return true;
        }

        $verifiedSum = $invoice->payments()
            ->where('status', StatusPembayaran::Verified)
            ->sum('amount');

        return $verifiedSum >= $invoice->total_amount;
    }
}
