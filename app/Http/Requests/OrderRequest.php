<?php

namespace App\Http\Requests;

use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type'                 => ['required', Rule::in(array_keys(Order::TYPES))],
            'table_id'             => [
                Rule::requiredIf(fn () => $this->input('type') === 'dine_in'),
                'nullable',
                Rule::exists('restaurant_tables', 'id')->where(fn ($q) => $q->whereNull('deleted_at')->where('is_active', 1)),
            ],
            'customer_id'          => ['nullable', Rule::exists('customers', 'id')->whereNull('deleted_at')],
            'discount_type'        => ['nullable', Rule::in(['percent', 'flat'])],
            'discount_value'       => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'payment_method'       => ['required', Rule::in(array_keys(Order::PAYMENT_METHODS))],
            'note'                 => ['nullable', 'string', 'max:255'],
            'items'                => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['required', 'distinct',
                                       Rule::exists('menu_items', 'id')->where(fn ($q) => $q->whereNull('deleted_at')->where('is_active', 1))],
            'items.*.quantity'     => ['required', 'integer', 'min:1', 'max:999'],
        ];
    }

    public function messages(): array
    {
        return [
            'table_id.required'    => 'Please choose a table for a dine-in order.',
            'items.required'       => 'Add at least one item to the order.',
            'items.min'            => 'Add at least one item to the order.',
            'items.*.menu_item_id.exists' => 'One of the items is no longer available.',
        ];
    }
}
