<?php

namespace App\Enums;

enum StatusBooking: string
{
    case Aktif = 'aktif';
    case Dibatalkan = 'dibatalkan';
    case Selesai = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::Aktif => 'Aktif',
            self::Dibatalkan => 'Dibatalkan',
            self::Selesai => 'Selesai',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
