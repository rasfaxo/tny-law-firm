<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateJadwalKonsultasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            "tanggal" => ["required", "date"],
            "waktu_mulai" => ["required", "date_format:H:i"],
            "waktu_selesai" => [
                "required",
                "date_format:H:i",
                "after:waktu_mulai",
            ],
            "status_slot" => [
                "required",
                Rule::in(["tersedia", "tidak_aktif"]),
            ],
        ];
    }
}
