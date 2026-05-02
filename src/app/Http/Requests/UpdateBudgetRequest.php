<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('budget')->user_id === auth()->id();
    }

    public function rules(): array
    {
        $budget = $this->route('budget');

        return [
            'category_id' => [
                'required',
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
            // Al actualizar ignoramos el propio registro
            // para que no falle si no cambia el período.
            '_unique_budget' => [
                Rule::unique('budgets')
                    ->where(
                        fn($query) => $query
                            ->where('user_id', auth()->id())
                            ->where('category_id', $this->category_id)
                            ->where('period_year', $this->period_year)
                            ->where('period_month', $this->period_month)
                    )
                    ->ignore($budget->id),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required'  => __('app.val_budget_category_required'),
            'category_id.exists'    => __('app.val_category_exists'),
            'period_year.required'  => __('app.val_budget_year_required'),
            'period_month.required' => __('app.val_budget_month_required'),
            'period_month.min'      => __('app.val_budget_month_range'),
            'period_month.max'      => __('app.val_budget_month_range'),
            'limit_amount.required' => __('app.val_budget_limit_required'),
            'limit_amount.min'      => __('app.val_budget_limit_min'),
            '_unique_budget'        => __('app.val_budget_unique'),
        ];
    }
}
