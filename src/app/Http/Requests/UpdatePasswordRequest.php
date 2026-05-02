<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdatePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Valida el cambio de contraseña.
    // Separado de UpdateProfileRequest por SRP:
    // cambiar la contraseña es una operación de
    // seguridad diferente a editar datos personales.
    // current_password se verifica contra el hash
    // almacenado usando la regla 'current_password'
    // nativa de Laravel, sin necesidad de lógica
    // adicional en el controlador.
    public function rules(): array
    {
        return [
            'current_password' => ['required', 'current_password'],
            'password'         => [
                'required',
                'confirmed',
                Password::min(8),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'current_password.required'      => __('app.val_current_password_required'),
            'current_password.current_password' => __('app.val_current_password_incorrect'),
            'password.required'              => __('app.val_new_password_required'),
            'password.confirmed'             => __('app.val_password_confirmed'),
        ];
    }
}