<?php

namespace App\Enums;

/** Enum tipe kamar kost. */
enum TipeKamar: string
{
    case Standard = 'standard';
    case Deluxe   = 'deluxe';
    case Suite    = 'suite';

    public function label(): string
    {
        return match ($this) {
            self::Standard => 'Standard',
            self::Deluxe   => 'Deluxe',
            self::Suite    => 'Suite',
        };
    }
}
