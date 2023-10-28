<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaneReservationListByDateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'plane_registration' => 'required|exists:planes,registration',
            'date' => 'required|date|format:Y-m-d',
        ];
    }
}
