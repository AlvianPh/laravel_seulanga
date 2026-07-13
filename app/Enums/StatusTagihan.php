<?php

namespace App\Enums;

/** Enum status tagihan bulanan. */
enum StatusTagihan: string
{
    case Pending   = 'pending';
    case Paid      = 'paid';
    case Overdue   = 'overdue';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending   => 'Menunggu',
            self::Paid      => 'Lunas',
            self::Overdue   => 'Jatuh Tempo',
            self::Cancelled => 'Dibatalkan',
        };
    }
}
