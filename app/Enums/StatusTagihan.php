<?php

namespace App\Enums;

/** Enum status tagihan bulanan. */
enum StatusTagihan: string
{
    case Pending = 'pending';
    case PartiallyPaid = 'partially_paid';
    case Paid = 'paid';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Menunggu',
            self::PartiallyPaid => 'Sebagian Dibayar',
            self::Paid => 'Lunas',
            self::Overdue => 'Jatuh Tempo',
            self::Cancelled => 'Dibatalkan',
        };
    }

    public function badgeVariant(): string
    {
        return match ($this) {
            self::Paid => 'success',
            self::PartiallyPaid => 'info',
            self::Pending => 'warning',
            self::Overdue, self::Cancelled => 'danger',
        };
    }
}
