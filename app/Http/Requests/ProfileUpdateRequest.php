<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            "nama" => ["required", "string", "max:100"],
            "email" => [
                "required",
                "string",
                "lowercase",
                "email",
                "max:100",
                Rule::unique(User::class)->ignore(
                    $this->user()->id_user,
                    "id_user",
                ),
            ],
        ];

        if ($this->user()->role === "klien") {
            $rules["no_telepon"] = ["nullable", "string", "max:20"];
            $rules["alamat"] = ["nullable", "string"];
            $rules["jenis_kelamin"] = [
                "nullable",
                "string",
                "max:20",
                Rule::in(["laki-laki", "perempuan"]),
            ];
            $rules["pekerjaan"] = ["nullable", "string", "max:100"];
            $rules["no_identitas"] = ["nullable", "string", "max:50"];
        }

        return $rules;
    }
}
