<?php

namespace App\Models;

use App\Enums\StatusPembayaran;
use App\Enums\StatusTagihan;
use Database\Factories\InvoiceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Model Invoice — tagihan bulanan penghuni.
 *
 * @property int $id
 * @property int $contract_id
 * @property int $tenant_id
 * @property int $room_id
 * @property int $year
 * @property int $month
 * @property float $rent_amount
 * @property float|null $electricity_fee
 * @property float|null $water_fee
 * @property float|null $internet_fee
 * @property float|null $penalty_fee
 * @property float|null $other_fee
 * @property float $total_amount
 * @property Carbon $due_date
 * @property StatusTagihan $status
 */
class Invoice extends Model
{
    /** @use HasFactory<InvoiceFactory> */
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'tenant_id',
        'room_id',
        'year',
        'month',
        'rent_amount',
        'electricity_fee',
        'water_fee',
        'internet_fee',
        'penalty_fee',
        'other_fee',
        'total_amount',
        'due_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusTagihan::class,
            'due_date' => 'date',
            'rent_amount' => 'decimal:2',
            'electricity_fee' => 'decimal:2',
            'water_fee' => 'decimal:2',
            'internet_fee' => 'decimal:2',
            'penalty_fee' => 'decimal:2',
            'other_fee' => 'decimal:2',
            'total_amount' => 'decimal:2',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /** Kontrak yang menghasilkan tagihan ini. */
    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    /** Penghuni pemilik tagihan ini. */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /** Kamar terkait tagihan ini. */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /** Semua pembayaran untuk tagihan ini. */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    /** Hitung ulang total dari semua komponen biaya. */
    public function calculateTotal(): float
    {
        return (float) $this->rent_amount
            + (float) ($this->electricity_fee ?? 0)
            + (float) ($this->water_fee ?? 0)
            + (float) ($this->internet_fee ?? 0)
            + (float) ($this->penalty_fee ?? 0)
            + (float) ($this->other_fee ?? 0);
    }

    /** Hitung total seluruh pembayaran yang sudah diverifikasi (sah). */
    public function paidAmount(): float
    {
        return (float) $this->payments()
            ->where('status', StatusPembayaran::Verified)
            ->sum('amount');
    }

    /** Hitung sisa tagihan yang belum dibayar. */
    public function remainingBalance(): float
    {
        return max(0.0, (float) $this->total_amount - $this->paidAmount());
    }

    /** Cek apakah tagihan sudah lunas sepenuhnya. */
    public function isPaid(): bool
    {
        return $this->status === StatusTagihan::Paid;
    }

    /** Cek apakah tagihan dibayar sebagian. */
    public function isPartiallyPaid(): bool
    {
        return $this->status === StatusTagihan::PartiallyPaid;
    }

    /** Cek apakah tagihan telah melewati jatuh tempo. */
    public function isOverdue(): bool
    {
        return $this->status === StatusTagihan::Overdue ||
            (! $this->isPaid() && $this->due_date < now()->startOfDay());
    }

    /** Hitung dan sinkronkan status tagihan berdasarkan total pembayaran verified. */
    public function recalculateStatus(): void
    {
        $totalVerified = $this->paidAmount();

        if ($totalVerified >= (float) $this->total_amount) {
            $this->update(['status' => StatusTagihan::Paid]);
        } elseif ($totalVerified > 0) {
            $this->update(['status' => StatusTagihan::PartiallyPaid]);
        } else {
            if ($this->due_date < now()->startOfDay() && $this->status !== StatusTagihan::Cancelled) {
                $this->update(['status' => StatusTagihan::Overdue]);
            } elseif ($this->status !== StatusTagihan::Cancelled) {
                $this->update(['status' => StatusTagihan::Pending]);
            }
        }
    }
}
