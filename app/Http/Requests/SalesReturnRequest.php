<?php

namespace App\Http\Requests;

use App\Models\Order;
use App\Models\SalesReturn;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SalesReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id'      => ['required', Rule::exists('orders', 'id')->whereNull('deleted_at')],
            'refund_method' => ['required', Rule::in(array_keys(Order::PAYMENT_METHODS))],
            'reason'        => ['required', Rule::in(array_keys(SalesReturn::REASONS))],
            'restock'       => ['nullable', 'boolean'],
            'note'          => ['nullable', 'string', 'max:255'],
            'items'         => ['required', 'array'],
            'items.*'       => ['nullable', 'integer', 'min:0', 'max:100000'],
        ];
    }
}
