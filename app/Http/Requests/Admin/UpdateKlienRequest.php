<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKlienRequest extends FormRequest
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
        $user = $this->route("user");
        $userId = $user instanceof User ? $user->getKey() : null;

        return [
            "nama" => ["required", "string", "max:100"],
            "email" => [
                "required",
                "email",
                "max:100",
                Rule::unique("users", "email")->ignore($userId, "id_user"),
            ],
            "no_telepon" => ["nullable", "string", "max:20"],
            "status_akun" => ["required", Rule::in(["aktif", "nonaktif"])],
        ];
    }
}
