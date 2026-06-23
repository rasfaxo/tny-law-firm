<?php

namespace App\Http\Requests\StafLegal;

use App\Models\PraPendaftaranPerkara;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class StoreVerifikasiBerkasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isStafLegal() ?? false;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            "status_verifikasi" => [
                "required",
                Rule::in(["berkas_lengkap", "berkas_tidak_lengkap"]),
            ],
            "catatan_umum" => ["nullable", "string"],
            "dokumen" => ["nullable", "array"],
            "dokumen.*.status_dokumen" => [
                "required",
                Rule::in(["valid", "perlu_perbaikan"]),
            ],
            "dokumen.*.catatan" => ["nullable", "string"],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator): void {
                $pengajuan = $this->route("praPendaftaranPerkara");

                if (!$pengajuan instanceof PraPendaftaranPerkara) {
                    return;
                }

                $dokumen = $this->input("dokumen", []);

                if (!is_array($dokumen)) {
                    return;
                }

                $allowedDocumentIds = $pengajuan
                    ->dokumenAktif()
                    ->pluck("id_dokumen")
                    ->map(fn($id) => (string) $id)
                    ->all();

                $submittedDocumentIds = array_keys($dokumen);

                foreach ($submittedDocumentIds as $documentId) {
                    if (!ctype_digit((string) $documentId)) {
                        $validator
                            ->errors()
                            ->add("dokumen", "ID dokumen tidak valid.");

                        return;
                    }
                }

                $invalidDocumentIds = array_diff(
                    array_map("strval", $submittedDocumentIds),
                    $allowedDocumentIds,
                );

                if ($invalidDocumentIds !== []) {
                    $validator
                        ->errors()
                        ->add(
                            "dokumen",
                            "Dokumen yang diverifikasi harus berasal dari pengajuan ini.",
                        );

                    return;
                }

                $missingDocumentIds = array_diff(
                    $allowedDocumentIds,
                    array_map("strval", $submittedDocumentIds),
                );

                if ($allowedDocumentIds !== [] && $missingDocumentIds !== []) {
                    $validator
                        ->errors()
                        ->add(
                            "dokumen",
                            "Semua dokumen aktif pada pengajuan harus diberi status verifikasi.",
                        );
                }

                if ($this->input("status_verifikasi") === "berkas_lengkap") {
                    foreach ($dokumen as $documentId => $document) {
                        if (($document["status_dokumen"] ?? null) !== "valid") {
                            $validator
                                ->errors()
                                ->add(
                                    "dokumen.{$documentId}.status_dokumen",
                                    "Jika berkas lengkap, semua dokumen harus berstatus valid.",
                                );
                        }
                    }

                    return;
                }

                if (
                    $this->input("status_verifikasi") !== "berkas_tidak_lengkap"
                ) {
                    return;
                }

                $hasPerluPerbaikan = false;

                foreach ($dokumen as $documentId => $document) {
                    if (
                        ($document["status_dokumen"] ?? null) !==
                        "perlu_perbaikan"
                    ) {
                        continue;
                    }

                    $hasPerluPerbaikan = true;

                    if (blank($document["catatan"] ?? null)) {
                        $validator
                            ->errors()
                            ->add(
                                "dokumen.{$documentId}.catatan",
                                "Catatan perbaikan wajib diisi untuk dokumen yang perlu perbaikan.",
                            );
                    }
                }

                if (!$hasPerluPerbaikan) {
                    $validator
                        ->errors()
                        ->add(
                            "dokumen",
                            "Minimal satu dokumen harus diberi status perlu perbaikan.",
                        );
                }
            },
        ];
    }
}
