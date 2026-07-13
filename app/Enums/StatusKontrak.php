<?php

namespace App\Enums;

/** Enum status kontrak sewa. */
enum StatusKontrak: string
{
    case Active     = 'active';
    case Ended      = 'ended';
    case Terminated = 'terminated';

    public function label(): string
    {
        return match ($this) {
            self::Active     => 'Aktif',
            self::Ended      => 'Selesai',
            self::Terminated => 'Dibatalkan',
        };
    }
}
