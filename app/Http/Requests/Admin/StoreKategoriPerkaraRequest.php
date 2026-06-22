<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreKategoriPerkaraRequest extends FormRequest
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
            "nama_kategori" => [
                "required",
                "string",
                "max:100",
                Rule::unique("kategori_perkara", "nama_kategori"),
            ],
            "deskripsi" => ["nullable", "string"],
        ];
    }
}
