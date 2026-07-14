<?php

namespace App\Models;

use App\Enums\StatusKontrak;
use Database\Factories\ContractFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Contract — perjanjian sewa antara penghuni dan kamar.
 *
 * @property int            $id
 * @property int            $tenant_id
 * @property int            $room_id
 * @property string         $start_date
 * @property string         $end_date
 * @property float          $rent_price   Snapshot harga sewa saat kontrak dibuat
 * @property float          $deposit_amount
 * @property StatusKontrak  $status
 * @property string|null    $notes
 * @property int            $created_by
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
    ];

    protected function casts(): array
    {
        return [
            'status'         => StatusKontrak::class,
            'start_date'     => 'date',
            'end_date'       => 'date',
            'rent_price'     => 'decimal:2',
            'deposit_amount' => 'decimal:2',
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

    /** Semua tagihan yang dihasilkan kontrak ini. */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    /** Cek apakah kontrak masih aktif. */
    public function isActive(): bool
    {
        return $this->status === StatusKontrak::Active;
    }
}
