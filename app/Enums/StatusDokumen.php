<?php

namespace App\Enums;

enum StatusDokumen: string
{
    case Terkirim = 'terkirim';
    case Valid = 'valid';
    case PerluPerbaikan = 'perlu_perbaikan';
    case Diganti = 'diganti';

    public function label(): string
    {
        return match ($this) {
            self::Terkirim => 'Terkirim',
            self::Valid => 'Valid',
            self::PerluPerbaikan => 'Perlu Perbaikan',
            self::Diganti => 'Diganti',
        };
    }

    public static function activeStatuses(): array
    {
        return [
            self::Terkirim->value,
            self::Valid->value,
            self::PerluPerbaikan->value,
        ];
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
