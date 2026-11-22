<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'supplier_id'           => ['required', Rule::exists('suppliers', 'id')->whereNull('deleted_at')],
            'purchase_date'         => ['required', 'date'],
            'paid_amount'           => ['required', 'numeric', 'min:0', 'max:999999999'],
            'note'                  => ['nullable', 'string', 'max:255'],
            'items'                 => ['required', 'array', 'min:1'],
            'items.*.ingredient_id' => ['required', 'distinct', Rule::exists('ingredients', 'id')->whereNull('deleted_at')],
            'items.*.quantity'      => ['required', 'numeric', 'gt:0', 'max:999999'],
            'items.*.unit_cost'     => ['required', 'numeric', 'min:0', 'max:9999999'],
        ];
    }

    public function messages(): array
    {
        return [
            'supplier_id.required'              => 'Please choose a supplier.',
            'items.required'                    => 'Add at least one ingredient.',
            'items.min'                         => 'Add at least one ingredient.',
            'items.*.ingredient_id.required'    => 'Choose an ingredient.',
            'items.*.ingredient_id.distinct'    => 'This ingredient is added more than once.',
            'items.*.quantity.required'         => 'Enter a quantity.',
            'items.*.quantity.gt'               => 'Quantity must be greater than zero.',
            'items.*.unit_cost.required'        => 'Enter the unit cost.',
        ];
    }
}
