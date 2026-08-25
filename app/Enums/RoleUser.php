<?php

namespace App\Enums;

enum RoleUser: string
{
    case Klien = 'klien';
    case Admin = 'admin';
    case StafLegal = 'staf_legal';

    public function label(): string
    {
        return match ($this) {
            self::Klien => 'Klien',
            self::Admin => 'Admin',
            self::StafLegal => 'Staf Legal',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
