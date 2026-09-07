<?php

namespace App\Models;

use App\Enums\StatusMoveOut;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Model MoveOutRequest — permohonan keluar kost dan penyelesaian kontrak sewa.
 */
class MoveOutRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'contract_id',
        'room_id',
        'requested_move_out_date',
        'reason',
        'notes',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_note',
        'inspected_by',
        'inspected_at',
        'room_condition',
        'damage_notes',
        'damage_cost',
        'requires_room_maintenance',
        'inspection_notes',
        'settled_by',
        'settled_at',
        'deposit_amount',
        'outstanding_invoices_amount',
        'damage_deduction_amount',
        'deposit_returned_amount',
        'remaining_tenant_liability',
        'settlement_notes',
        'completed_by',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => StatusMoveOut::class,
            'requested_move_out_date' => 'date',
            'reviewed_at' => 'datetime',
            'inspected_at' => 'datetime',
            'settled_at' => 'datetime',
            'completed_at' => 'datetime',
            'damage_cost' => 'decimal:2',
            'requires_room_maintenance' => 'boolean',
            'deposit_amount' => 'decimal:2',
            'outstanding_invoices_amount' => 'decimal:2',
            'damage_deduction_amount' => 'decimal:2',
            'deposit_returned_amount' => 'decimal:2',
            'remaining_tenant_liability' => 'decimal:2',
        ];
    }

    // ─── Relasi ──────────────────────────────────────────────────────────────

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class)->withTrashed();
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class)->withTrashed();
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function inspector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'inspected_by');
    }

    public function settler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'settled_by');
    }

    public function completer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    // ─── Helper State ────────────────────────────────────────────────────────

    public function isPending(): bool
    {
        return $this->status === StatusMoveOut::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === StatusMoveOut::Approved;
    }

    public function isInspection(): bool
    {
        return $this->status === StatusMoveOut::Inspection;
    }

    public function isSettlement(): bool
    {
        return $this->status === StatusMoveOut::Settlement;
    }

    public function isCompleted(): bool
    {
        return $this->status === StatusMoveOut::Completed;
    }

    public function isRejected(): bool
    {
        return $this->status === StatusMoveOut::Rejected;
    }

    public function isCancelled(): bool
    {
        return $this->status === StatusMoveOut::Cancelled;
    }

    public function canBeCancelled(): bool
    {
        return $this->isPending();
    }

    public function canBeReviewed(): bool
    {
        return $this->isPending();
    }

    public function canBeInspected(): bool
    {
        return in_array($this->status, [StatusMoveOut::Approved, StatusMoveOut::Inspection], true);
    }

    public function canBeFinalized(): bool
    {
        return in_array($this->status, [StatusMoveOut::Approved, StatusMoveOut::Inspection, StatusMoveOut::Settlement], true);
    }
}
