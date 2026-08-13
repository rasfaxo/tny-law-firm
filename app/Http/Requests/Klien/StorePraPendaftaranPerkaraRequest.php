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
            "dokumen" => ["required", "array", "min:1", "max:5"],
            "dokumen.*.nama_dokumen" => ["required", "string", "max:100"],
            "dokumen.*.jenis_dokumen" => ["required", "string", "max:50"],
            "dokumen.*.file_dokumen" => ["required", "file", "mimes:pdf,jpg,jpeg,png", "max:5120"],
        ];
    }

    public function messages(): array
    {
        return [
            "dokumen.required" => "Anda wajib mengunggah minimal 1 dokumen pendukung.",
            "dokumen.min" => "Anda wajib mengunggah minimal 1 dokumen pendukung.",
            "dokumen.max" => "Anda hanya dapat mengunggah maksimal 5 dokumen.",
            "dokumen.*.nama_dokumen.required" => "Nama dokumen wajib diisi.",
            "dokumen.*.jenis_dokumen.required" => "Jenis dokumen wajib dipilih.",
            "dokumen.*.file_dokumen.required" => "File dokumen wajib diunggah.",
            "dokumen.*.file_dokumen.mimes" => "File dokumen harus berupa PDF, JPG, JPEG, atau PNG.",
            "dokumen.*.file_dokumen.max" => "Ukuran file dokumen tidak boleh lebih dari 5MB.",
        ];
    }
}
