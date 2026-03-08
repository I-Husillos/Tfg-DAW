<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email'    => ['required', 'string', 'email', 'max:150'],
            'password' => ['required', 'string', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'El campo de correo electrónico es obligatorio.',
            'email.email'    => 'El campo de correo electrónico debe ser una dirección de correo válida.',
            'email.max'      => 'El campo de correo electrónico no puede tener más de 150 caracteres.',
            'password.required' => 'El campo de contraseña es obligatorio.',
            'password.min'      => 'El campo de contraseña debe tener al menos 8 caracteres.',
        ];
    }
}
