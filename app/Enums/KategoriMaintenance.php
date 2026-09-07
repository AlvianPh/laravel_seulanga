<?php

namespace App\Enums;

/** Enum kategori kerusakan fasilitas kost. */
enum KategoriMaintenance: string
{
    case Plumbing = 'plumbing';
    case Electrical = 'electrical';
    case Furniture = 'furniture';
    case Cleanliness = 'cleanliness';
    case Internet = 'internet';
    case AirConditioning = 'air_conditioning';
    case Bathroom = 'bathroom';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Plumbing => 'Pipa & Sanitasi',
            self::Electrical => 'Listrik & Lampu',
            self::Furniture => 'Perabot & Inventaris',
            self::Cleanliness => 'Kebersihan',
            self::Internet => 'Jaringan / Wi-Fi',
            self::AirConditioning => 'AC & Pendingin',
            self::Bathroom => 'Kamar Mandi',
            self::Other => 'Lain-lain',
        };
    }
}
