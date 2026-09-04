<?php

namespace App\Enums;

/** Enum status kontrak sewa. */
enum StatusKontrak: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Ended = 'ended';
    case Terminated = 'terminated';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Active => 'Aktif',
            self::Ended => 'Selesai',
            self::Terminated => 'Dibatalkan',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Draft => 'warning',
            self::Active => 'success',
            self::Ended => 'neutral',
            self::Terminated => 'danger',
        };
    }
}
