<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class StoreStafLegalRequest extends FormRequest
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
            "nama" => ["required", "string", "max:100"],
            "email" => ["required", "email", "max:100", "unique:users,email"],
            "no_telepon" => ["nullable", "string", "max:20"],
            "password" => ["required", "confirmed", Password::defaults()],
            "status_akun" => ["required", Rule::in(["aktif", "nonaktif"])],
        ];
    }
}
