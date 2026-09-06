<?php

namespace App\Http\Requests\Admin;

use App\Enums\StatusBooking;
use App\Enums\StatusKonfirmasi;
use App\Enums\StatusPengajuan;
use App\Enums\StatusReschedule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReportFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() === true;
    }

    public function rules(): array
    {
        return [
            'tanggal_mulai' => ['nullable', 'date_format:Y-m-d'],
            'tanggal_selesai' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:tanggal_mulai'],
            'status_pengajuan' => ['nullable', Rule::in(StatusPengajuan::values())],
            'status_verifikasi' => ['nullable', Rule::in([
                StatusPengajuan::BerkasLengkap->value,
                StatusPengajuan::BerkasTidakLengkap->value,
            ])],
            'id_kategori' => ['nullable', 'integer', 'exists:kategori_perkara,id_kategori'],
            'id_staf_legal' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id_user')->where('role', 'staf_legal'),
            ],
            'status_booking' => ['nullable', Rule::in(StatusBooking::values())],
            'metode_konsultasi' => ['nullable', Rule::in(['online', 'offline'])],
            'status_konfirmasi_konsultasi' => ['nullable', Rule::in(StatusKonfirmasi::values())],
            'status_reschedule' => ['nullable', Rule::in(StatusReschedule::values())],
            'preferensi_metode' => ['nullable', Rule::in(['online', 'offline'])],
        ];
    }
}
