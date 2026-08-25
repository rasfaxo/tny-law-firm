<?php

namespace App\Enums;

enum StatusKonfirmasi: string
{
    case MenungguKonfirmasi = 'menunggu_konfirmasi';
    case Terkonfirmasi = 'terkonfirmasi';

    public function label(): string
    {
        return match ($this) {
            self::MenungguKonfirmasi => 'Menunggu Konfirmasi',
            self::Terkonfirmasi => 'Terkonfirmasi',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
