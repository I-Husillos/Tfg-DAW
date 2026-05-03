<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Tipo: solo income o expense, transfer eliminado
            // porque eliminamos accounts del proyecto.
            'type'        => ['required', 'in:income,expense'],

            // Importe: positivo, máximo 13 dígitos enteros
            // y 2 decimales (decimal 15,2 en la migración).
            'amount'      => ['required', 'numeric', 'min:0.01', 'max:9999999999999.99'],

            // Moneda: código ISO 4217 de 3 letras (EUR, USD…)
            'currency'    => ['required', 'string', 'size:3'],

            // Fecha: formato estándar, no puede ser futura
            // más de 1 día para evitar errores de zona horaria.
            'date'        => ['required', 'date', 'before_or_equal:tomorrow'],

            // Categoría opcional: si se envía debe existir
            // y pertenecer al usuario autenticado.
            'category_id' => [
                'nullable',
                Rule::exists('categories', 'id')->where(
                    fn($query) => $query->where('user_id', auth()->id())
                ),
            ],

            'name'        => ['nullable', 'string', 'max:150'],
            'merchant'    => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required'        => __('app.val_type_required'),
            'type.in'              => __('app.val_type_in'),
            'amount.required'      => __('app.val_amount_required'),
            'amount.numeric'       => __('app.val_amount_numeric'),
            'amount.min'           => __('app.val_amount_min'),
            'currency.required'    => __('app.val_currency_required'),
            'currency.size'        => __('app.val_currency_size'),
            'date.required'        => __('app.val_date_required'),
            'date.date'            => __('app.val_date_date'),
            'date.before_or_equal' => __('app.val_date_future'),
            'category_id.exists'   => __('app.val_category_exists'),
        ];
    }
}