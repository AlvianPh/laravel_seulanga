<?php

namespace App\Enums;

/**
 * Enum jenis/tipe permohonan izin penghuni kost.
 */
enum PermissionType: string
{
    case GuestStay = 'guest_stay';
    case LateReturn = 'late_return';
    case ElectronicDevice = 'electronic_device';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::GuestStay => 'Izin Menginapkan Tamu',
            self::LateReturn => 'Izin Pulang Larut Malam',
            self::ElectronicDevice => 'Izin Perangkat Elektronik',
            self::Other => 'Izin Lainnya',
        };
    }

    public function requiresDateRange(): bool
    {
        return $this === self::GuestStay;
    }

    public function requiresStartDate(): bool
    {
        return in_array($this, [self::GuestStay, self::LateReturn], true);
    }
}
