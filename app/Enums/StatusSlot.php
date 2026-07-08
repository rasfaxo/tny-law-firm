<?php

namespace App\Enums;

enum StatusSlot: string
{
    case Tersedia = 'tersedia';
    case Terisi = 'terisi';
    case TidakAktif = 'tidak_aktif';

    public function label(): string
    {
        return match ($this) {
            self::Tersedia => 'Tersedia',
            self::Terisi => 'Terisi',
            self::TidakAktif => 'Tidak Aktif',
        };
    }

    public static function bookableStatuses(): array
    {
        return [self::Tersedia->value];
    }

    public static function overlapCheckStatuses(): array
    {
        return [
            self::Tersedia->value,
            self::Terisi->value,
        ];
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
