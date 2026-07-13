<?php

namespace App\Enums;

/** Enum status pembayaran. */
enum StatusPembayaran: string
{
    case Pending  = 'pending';
    case Verified = 'verified';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending  => 'Menunggu Verifikasi',
            self::Verified => 'Terverifikasi',
            self::Rejected => 'Ditolak',
        };
    }
}
