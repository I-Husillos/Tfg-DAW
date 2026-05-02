<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Verificamos que la transacción que se quiere
        // editar pertenece al usuario autenticado.
        // $this->route('transaction') devuelve el modelo
        // Transaction resuelto por route model binding.
        return $this->route('transaction')->user_id === auth()->id();
    }

    public function rules(): array
    {
        // Las reglas son idénticas al store.
        // Se separan en dos clases por SRP: cada Request
        // tiene su propio contexto y puede evolucionar
        // independientemente.
        return [
            'type'        => ['required', 'in:income,expense'],
            'amount'      => ['required', 'numeric', 'min:0.01', 'max:9999999999999.99'],
            'currency'    => ['required', 'string', 'size:3'],
            'date'        => ['required', 'date', 'before_or_equal:tomorrow'],
            'category_id' => ['nullable', 'exists:categories,id'],
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