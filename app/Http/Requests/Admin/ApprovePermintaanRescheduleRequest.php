<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ApprovePermintaanRescheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "id_jadwal_baru" => [
                "required",
                "exists:jadwal_konsultasi,id_jadwal",
            ],
            "catatan_admin" => ["nullable", "string", "max:2000"],
        ];
    }
}
