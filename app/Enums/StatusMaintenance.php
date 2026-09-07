<?php

namespace App\Enums;

/** Enum status laporan perbaikan/maintenance. */
enum StatusMaintenance: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Resolved = 'resolved';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::InProgress => 'Diproses',
            self::Resolved => 'Selesai',
            self::Rejected => 'Ditolak',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::InProgress => 'info',
            self::Resolved => 'success',
            self::Rejected => 'danger',
        };
    }
}
