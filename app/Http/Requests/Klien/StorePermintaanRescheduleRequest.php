<?php

namespace App\Http\Requests\Klien;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePermintaanRescheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            "alasan_reschedule" => ["required", "string", "max:2000"],
            "preferensi_jadwal" => ["nullable", "string", "max:2000"],
            "preferensi_metode" => ["nullable", Rule::in(["online", "offline"])],
        ];
    }
}
