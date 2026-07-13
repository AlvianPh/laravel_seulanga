<?php

namespace App\Enums;

/** Enum status kamar kost. */
enum StatusKamar: string
{
    case Available    = 'available';
    case Occupied     = 'occupied';
    case Maintenance  = 'maintenance';

    public function label(): string
    {
        return match ($this) {
            self::Available   => 'Tersedia',
            self::Occupied    => 'Terisi',
            self::Maintenance => 'Perbaikan',
        };
    }
}
