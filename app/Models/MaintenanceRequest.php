<?php

namespace App\Models;

use App\Enums\KategoriMaintenance;
use App\Enums\PrioritasMaintenance;
use App\Enums\StatusMaintenance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaintenanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'tenant_id',
        'room_id',
        'category',
        'priority',
        'status',
        'location',
        'description',
        'photo_path',
        'reported_at',
        'resolved_at',
        'notes',
        'rejection_reason',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'category' => KategoriMaintenance::class,
        'priority' => PrioritasMaintenance::class,
        'status' => StatusMaintenance::class,
        'reported_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(Expense::class);
    }

    public function getTotalCostAttribute(): float
    {
        return (float) $this->expenses()->sum('amount');
    }

    public function isPending(): bool
    {
        return $this->status === StatusMaintenance::Pending;
    }

    public function isInProgress(): bool
    {
        return $this->status === StatusMaintenance::InProgress;
    }

    public function isResolved(): bool
    {
        return $this->status === StatusMaintenance::Resolved;
    }

    public function isRejected(): bool
    {
        return $this->status === StatusMaintenance::Rejected;
    }
}
