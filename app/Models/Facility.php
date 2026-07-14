<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Model Facility — master data fasilitas kamar kost.
 *
 * @property int         $id
 * @property string      $name
 * @property string|null $icon
 */
class Facility extends Model
{
    protected $fillable = [
        'name',
        'icon',
    ];

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /** Semua kamar yang memiliki fasilitas ini. */
    public function rooms(): BelongsToMany
    {
        return $this->belongsToMany(Room::class, 'room_facilities');
    }

    // ─── Helper ──────────────────────────────────────────────────────────────

    /** Cek apakah fasilitas ini sedang digunakan oleh kamar manapun. */
    public function isUsed(): bool
    {
        return $this->rooms()->exists();
    }
}
