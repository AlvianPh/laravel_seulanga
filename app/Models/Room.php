<?php

namespace App\Models;

use App\Enums\StatusKamar;
use App\Enums\TipeKamar;
use Database\Factories\RoomFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model Room — data kamar kost.
 *
 * @property int         $id
 * @property string      $room_number
 * @property int         $floor
 * @property TipeKamar   $type
 * @property float|null  $size_m2
 * @property float       $monthly_price
 * @property float       $deposit_price
 * @property StatusKamar $status
 * @property array|null  $facilities
 */
class Room extends Model
{
    /** @use HasFactory<RoomFactory> */
    use HasFactory;

    protected $fillable = [
        'room_number',
        'floor',
        'type',
        'size_m2',
        'monthly_price',
        'deposit_price',
        'status',
        'facilities',
    ];

    protected function casts(): array
    {
        return [
            'type'          => TipeKamar::class,
            'status'        => StatusKamar::class,
            'facilities'    => 'array',
            'monthly_price' => 'decimal:2',
            'deposit_price' => 'decimal:2',
            'size_m2'       => 'decimal:2',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /** Foto-foto kamar ini. */
    public function photos(): HasMany
    {
        return $this->hasMany(RoomPhoto::class);
    }

    /** Semua kontrak (termasuk riwayat) kamar ini. */
    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    /** Semua tagihan yang pernah dikeluarkan untuk kamar ini. */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    /** Cek apakah kamar sedang tersedia. */
    public function isAvailable(): bool
    {
        return $this->status === StatusKamar::Available;
    }

    /** Ambil kontrak yang sedang aktif (jika ada). */
    public function activeContract(): ?Contract
    {
        return $this->contracts()->where('status', 'active')->latest()->first();
    }
}
