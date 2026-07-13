<?php

namespace App\Enums;

/** Enum kategori pengeluaran operasional. */
enum KategoriPengeluaran: string
{
    case Electricity = 'electricity';
    case Water       = 'water';
    case Internet    = 'internet';
    case Repair      = 'repair';
    case Cleaning    = 'cleaning';
    case Salary      = 'salary';
    case Other       = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Electricity => 'Listrik',
            self::Water       => 'Air',
            self::Internet    => 'Internet',
            self::Repair      => 'Perbaikan',
            self::Cleaning    => 'Kebersihan',
            self::Salary      => 'Gaji',
            self::Other       => 'Lainnya',
        };
    }
}
