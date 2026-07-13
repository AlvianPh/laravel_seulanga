<?php

namespace App\Enums;

/** Enum jenis kelamin penghuni. */
enum JenisKelamin: string
{
    case Male   = 'male';
    case Female = 'female';

    public function label(): string
    {
        return match ($this) {
            self::Male   => 'Laki-laki',
            self::Female => 'Perempuan',
        };
    }
}
