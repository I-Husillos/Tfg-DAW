<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:120',
                // Unique por usuario: mismo nombre no puede
                // repetirse para el mismo user_id.
                Rule::unique('categories')->where(
                    fn($query) => $query->where('user_id', auth()->id())
                ),
            ],
            'display_name' => ['nullable', 'string', 'max:120'],
            'description'  => ['nullable', 'string', 'max:500'],
            'type'         => ['required', 'in:income,expense'],
            'parent_id'    => [
                'nullable',
                // Si se envía parent_id debe existir y
                // pertenecer al mismo usuario.
                Rule::exists('categories', 'id')->where(
                    fn($query) => $query->where('user_id', auth()->id())
                ),
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (!$value) {
                        return;
                    }

                    $parent = Category::where('user_id', auth()->id())->find($value);
                    if (!$parent) {
                        return;
                    }

                    if (!is_null($parent->parent_id)) {
                        $fail(__('app.val_category_parent_exists'));
                        return;
                    }

                    if ($this->input('type') && $parent->type !== $this->input('type')) {
                        $fail(__('app.val_category_type_in'));
                    }
                },
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => __('app.val_category_name_required'),
            'name.max'      => __('app.val_category_name_max'),
            'name.unique'   => __('app.val_category_name_unique'),
            'type.required' => __('app.val_category_type_required'),
            'type.in'       => __('app.val_category_type_in'),
            'parent_id.exists' => __('app.val_category_parent_exists'),
        ];
    }
}
