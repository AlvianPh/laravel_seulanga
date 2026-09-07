<?php

namespace App\Models;

use App\Enums\PermissionStatus;
use App\Enums\PermissionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantPermission extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'contract_id',
        'type',
        'title',
        'description',
        'start_at',
        'end_at',
        'status',
        'reviewed_by',
        'reviewed_at',
        'review_note',
    ];

    protected $casts = [
        'type' => PermissionType::class,
        'status' => PermissionStatus::class,
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isPending(): bool
    {
        return $this->status === PermissionStatus::Pending;
    }

    public function isApproved(): bool
    {
        return $this->status === PermissionStatus::Approved;
    }

    public function isRejected(): bool
    {
        return $this->status === PermissionStatus::Rejected;
    }

    public function isCancelled(): bool
    {
        return $this->status === PermissionStatus::Cancelled;
    }
}
