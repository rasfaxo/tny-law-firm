<?php

namespace App\Http\Requests\Klien;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBookingKonsultasiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isKlien() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "id_jadwal" => ["required", "exists:jadwal_konsultasi,id_jadwal"],
            "metode_konsultasi" => [
                "required",
                Rule::in(["online", "offline"]),
            ],
            "catatan_preferensi_klien" => ["nullable", "string", "max:1000"],
        ];
    }
}
