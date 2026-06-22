<?php

namespace App\Http\Requests\Admin;

use App\Models\KategoriPerkara;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKategoriPerkaraRequest extends FormRequest
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
        $kategoriPerkara = $this->route("kategoriPerkara");
        $kategoriId = $kategoriPerkara instanceof KategoriPerkara
            ? $kategoriPerkara->getKey()
            : null;

        return [
            "nama_kategori" => [
                "required",
                "string",
                "max:100",
                Rule::unique("kategori_perkara", "nama_kategori")->ignore(
                    $kategoriId,
                    "id_kategori",
                ),
            ],
            "deskripsi" => ["nullable", "string"],
        ];
    }
}
