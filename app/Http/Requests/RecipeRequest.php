<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RecipeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'items'                  => ['required', 'array', 'min:1'],
            'items.*.ingredient_id'  => ['required', 'distinct',
                                         Rule::exists('ingredients', 'id')->whereNull('deleted_at')],
            'items.*.quantity'       => ['required', 'numeric', 'gt:0', 'max:999999'],
        ];

        // নতুন রেসিপি বানানোর সময় মেনু আইটেমও লাগে; edit এ মেনু আইটেম URL থেকে আসে
        if ($this->isMethod('post')) {
            $rules['menu_item_id'] = [
                'required',
                Rule::exists('menu_items', 'id')->whereNull('deleted_at'),
                Rule::unique('recipes', 'menu_item_id'),
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'menu_item_id.required'              => 'Please choose a menu item.',
            'menu_item_id.unique'                => 'This menu item already has a recipe. Edit it instead.',
            'items.required'                     => 'Add at least one ingredient.',
            'items.min'                          => 'Add at least one ingredient.',
            'items.*.ingredient_id.required'     => 'Choose an ingredient.',
            'items.*.ingredient_id.distinct'     => 'This ingredient is added more than once.',
            'items.*.quantity.required'          => 'Enter a quantity.',
            'items.*.quantity.gt'                => 'Quantity must be greater than zero.',
        ];
    }
}
