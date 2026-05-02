<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Valida los datos del perfil personal.
    // Estos campos viven en la tabla profiles,
    // no en users. Son todos opcionales porque
    // el usuario puede querer dejar algunos vacíos.
    public function rules(): array
    {
        return [
            'name'        => ['nullable', 'string', 'max:100'],
            'surname'     => ['nullable', 'string', 'max:100'],
            'phone'       => ['nullable', 'string', 'max:30'],
            'address'     => ['nullable', 'string', 'max:255'],
            'city'        => ['nullable', 'string', 'max:100'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'country'     => ['nullable', 'string', 'max:100'],
            'company'     => ['nullable', 'string', 'max:150'],
            'currency'    => ['required', 'string', 'size:3'],
            'language'    => ['required', 'string', 'max:5'],
            'timezone'    => ['required', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'currency.required' => __('app.val_profile_currency_required'),
            'currency.size'     => __('app.val_profile_currency_size'),
            'language.required' => __('app.val_profile_language_required'),
            'timezone.required' => __('app.val_profile_timezone_required'),
        ];
    }
}