<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->route('category')->user_id === auth()->id();
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:120',
                // Ignoramos el registro actual en el unique
                // para que el usuario pueda guardar sin cambiar
                // el nombre (de lo contrario daría error unique).
                Rule::unique('categories')
                    ->where(fn($query) => $query->where('user_id', auth()->id()))
                    ->ignore($this->route('category')->id),
            ],
            'display_name' => ['nullable', 'string', 'max:120'],
            'description'  => ['nullable', 'string', 'max:500'],
            'type'         => ['required', 'in:income,expense'],
            'parent_id'    => [
                'nullable',
                Rule::exists('categories', 'id')->where(
                    fn($query) => $query->where('user_id', auth()->id())
                ),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'    => __('app.val_category_name_required'),
            'name.max'         => __('app.val_category_name_max'),
            'name.unique'      => __('app.val_category_name_unique'),
            'type.required'    => __('app.val_category_type_required'),
            'type.in'          => __('app.val_category_type_in'),
            'parent_id.exists' => __('app.val_category_parent_exists'),
        ];
    }
}
