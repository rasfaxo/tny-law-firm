<?php

namespace App\Enums;

enum StatusPengajuan: string
{
    case MenungguVerifikasi = 'menunggu_verifikasi';
    case BerkasTidakLengkap = 'berkas_tidak_lengkap';
    case MenungguVerifikasiUlang = 'menunggu_verifikasi_ulang';
    case BerkasLengkap = 'berkas_lengkap';
    case JadwalDipilih = 'jadwal_dipilih';
    case Selesai = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::MenungguVerifikasi => 'Menunggu Verifikasi',
            self::BerkasTidakLengkap => 'Berkas Tidak Lengkap',
            self::MenungguVerifikasiUlang => 'Menunggu Verifikasi Ulang',
            self::BerkasLengkap => 'Berkas Lengkap',
            self::JadwalDipilih => 'Jadwal Dipilih',
            self::Selesai => 'Selesai',
        };
    }

    public static function verifiableStatuses(): array
    {
        return [
            self::MenungguVerifikasi->value,
            self::MenungguVerifikasiUlang->value,
        ];
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
