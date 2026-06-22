<?php

namespace App\Http\Requests\Klien;

use Illuminate\Foundation\Http\FormRequest;

class StorePraPendaftaranPerkaraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isKlien() ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            "id_kategori" => ["required", "exists:kategori_perkara,id_kategori"],
            "judul_perkara" => ["required", "string", "max:150"],
            "kronologi" => ["required", "string"],
        ];
    }
}
