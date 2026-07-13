<?php

namespace App\Enums;

/** Enum role pengguna sistem kost. */
enum RoleUser: string
{
    case Owner = 'owner';
    case Admin = 'admin';

    /** Label tampilan untuk UI. */
    public function label(): string
    {
        return match ($this) {
            self::Owner => 'Owner',
            self::Admin => 'Admin',
        };
    }
}
