<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Model RoomType — master data tipe kamar kost.
 *
 * @property int         $id
 * @property string      $name
 * @property string|null $description
 * @property float|null  $default_price
 */
class RoomType extends Model
{
    protected $fillable = [
        'name',
        'description',
        'default_price',
    ];

    protected function casts(): array
    {
        return [
            'default_price' => 'decimal:2',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /** Semua kamar yang menggunakan tipe ini. */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    /** Cek apakah tipe ini sedang digunakan oleh kamar manapun. */
    public function isUsed(): bool
    {
        return $this->rooms()->exists();
    }
}
