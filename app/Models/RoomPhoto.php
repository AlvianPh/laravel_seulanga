<?php

namespace App\Models;

use Database\Factories\RoomPhotoFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model RoomPhoto — foto kamar kost.
 *
 * @property int    $id
 * @property int    $room_id
 * @property string $file_path
 * @property bool   $is_primary
 */
class RoomPhoto extends Model
{
    /** @use HasFactory<RoomPhotoFactory> */
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'room_id',
        'file_path',
        'is_primary',
    ];

    protected function casts(): array
    {
        return [
            'is_primary' => 'boolean',
            'created_at' => 'datetime',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /** Kamar pemilik foto ini. */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }
}
