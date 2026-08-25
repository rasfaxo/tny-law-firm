<?php

namespace App\Http\Requests\Admin;

use App\Models\BookingKonsultasi;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConfirmBookingKonsultasiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $booking = $this->route("bookingKonsultasi");
        $metode =
            $booking instanceof BookingKonsultasi
                ? $booking->metode_konsultasi
                : null;

        return [
            "link_konsultasi" => [
                Rule::requiredIf($metode === "online"),
                "nullable",
                "url",
                "max:255",
            ],
            "lokasi_konsultasi" => [
                Rule::requiredIf($metode === "offline"),
                "nullable",
                "string",
                "max:255",
            ],
            "catatan_konsultasi" => ["nullable", "string", "max:2000"],
        ];
    }
}
