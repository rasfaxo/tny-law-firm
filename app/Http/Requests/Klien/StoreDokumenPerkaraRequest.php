<?php

namespace App\Http\Requests\Klien;

use Illuminate\Foundation\Http\FormRequest;

class StoreDokumenPerkaraRequest extends FormRequest
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
            "nama_dokumen" => ["required", "string", "max:150"],
            "jenis_dokumen" => ["required", "string", "max:100"],
            "file" => [
                "required",
                "file",
                "mimes:pdf,jpg,jpeg,png",
                "mimetypes:application/pdf,image/jpeg,image/png",
                "max:5120",
            ],
        ];
    }
}
