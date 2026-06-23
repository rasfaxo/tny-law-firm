<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreJadwalKonsultasiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            "tanggal" => ["required", "date", "after_or_equal:today"],
            "waktu_mulai" => ["required", "date_format:H:i"],
            "waktu_selesai" => [
                "required",
                "date_format:H:i",
                "after:waktu_mulai",
            ],
        ];
    }
}
