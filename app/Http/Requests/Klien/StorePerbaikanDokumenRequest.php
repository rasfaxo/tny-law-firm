<?php

namespace App\Http\Requests\Klien;

use Illuminate\Foundation\Http\FormRequest;

class StorePerbaikanDokumenRequest extends FormRequest
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
