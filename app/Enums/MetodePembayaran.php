<?php

namespace App\Enums;

/** Enum metode pembayaran. */
enum MetodePembayaran: string
{
    case Cash     = 'cash';
    case Transfer = 'transfer';
    case Qris     = 'qris';
    case Other    = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Cash     => 'Tunai',
            self::Transfer => 'Transfer Bank',
            self::Qris     => 'QRIS',
            self::Other    => 'Lainnya',
        };
    }
}
