<?php

namespace App\Models;

use App\Enums\StatusApplication;
use Database\Factories\TenantApplicationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model TenantApplication — pengajuan sewa kamar oleh calon penghuni.
 *
 * @property int $id
 * @property int $tenant_id
 * @property int $room_id
 * @property StatusApplication $status
 * @property string|null $application_notes
 * @property string|null $rejection_reason
 * @property int|null $reviewed_by
 * @property string|null $reviewed_at
 */
class TenantApplication extends Model
{
    /** @use HasFactory<TenantApplicationFactory> */
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'room_id',
        'status',
        'application_notes',
        'rejection_reason',
        'reviewed_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusApplication::class,
            'reviewed_at' => 'datetime',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────────────────────

    /** Calon penghuni yang mengajukan. */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class)->withTrashed();
    }

    /** Kamar yang diajukan. */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class)->withTrashed();
    }

    /** Staf (Admin/Owner) yang meninjau pengajuan ini. */
    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', StatusApplication::Pending);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', StatusApplication::Approved);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', StatusApplication::Rejected);
    }
}
