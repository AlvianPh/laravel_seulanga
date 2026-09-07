<?php

namespace App\Enums;

/** Enum status permohonan move-out / keluar kost. */
enum StatusMoveOut: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Inspection = 'inspection';
    case Settlement = 'settlement';
    case Completed = 'completed';
    case Rejected = 'rejected';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu Review',
            self::Approved => 'Disetujui (Menunggu Inspeksi)',
            self::Inspection => 'Inspeksi Selesai',
            self::Settlement => 'Perhitungan Selesai',
            self::Completed => 'Selesai (Kontrak Berakhir)',
            self::Rejected => 'Ditolak',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Pending => 'warning',
            self::Approved => 'info',
            self::Inspection => 'info',
            self::Settlement => 'warning',
            self::Completed => 'success',
            self::Rejected => 'danger',
            self::Cancelled => 'neutral',
        };
    }
}
