<?php

namespace App\Http\Requests;

use App\Enums\StatusBooking;
use App\Enums\StatusPengajuan;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexFilterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:100'],
            'status' => ['nullable', Rule::in(StatusPengajuan::values())],
            'status_booking' => ['nullable', Rule::in(StatusBooking::values())],
            'kategori' => ['nullable', 'integer', 'exists:kategori_perkara,id_kategori'],
            'tanggal' => ['nullable', 'date_format:Y-m-d'],
            'tanggal_mulai' => ['nullable', 'date_format:Y-m-d'],
            'tanggal_selesai' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:tanggal_mulai'],
        ];
    }
}
