<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => [
                'required',
                // La categoría debe existir y pertenecer al usuario.
                Rule::exists('categories', 'id')->where(
                    fn($query) => $query->where('user_id', auth()->id())
                ),
            ],
            'period_year' => [
                'required',
                'integer',
                'min:2000',
                'max:2100',
            ],
            'period_month' => [
                'required',
                'integer',
                'min:1',
                'max:12',
            ],
            'limit_amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],
            'alert_threshold' => [
                'nullable',
                'numeric',
                'min:0.01',
                'max:1',
            ],
            // Unique compuesto: no puede haber dos presupuestos
            // para la misma categoría en el mismo mes.
            // Se valida aquí además de en la migración para
            // dar un mensaje de error claro al usuario.
            '_unique_budget' => [
                Rule::unique('budgets')
                    ->where(
                        fn($query) => $query
                            ->where('user_id', auth()->id())
                            ->where('category_id', $this->category_id)
                            ->where('period_year', $this->period_year)
                            ->where('period_month', $this->period_month)
                    ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required'    => __('app.val_budget_category_required'),
            'category_id.exists'      => __('app.val_category_exists'),
            'period_year.required'    => __('app.val_budget_year_required'),
            'period_year.min'         => __('app.val_budget_year_min'),
            'period_month.required'   => __('app.val_budget_month_required'),
            'period_month.min'        => __('app.val_budget_month_range'),
            'period_month.max'        => __('app.val_budget_month_range'),
            'limit_amount.required'   => __('app.val_budget_limit_required'),
            'limit_amount.min'        => __('app.val_budget_limit_min'),
            'alert_threshold.min'     => __('app.val_budget_threshold_min'),
            'alert_threshold.max'     => __('app.val_budget_threshold_max'),
            '_unique_budget'          => __('app.val_budget_unique'),
        ];
    }
}
