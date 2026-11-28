<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PurchaseReturnRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'purchase_id'   => ['required', Rule::exists('purchases', 'id')->whereNull('deleted_at')],
            'return_date'   => ['required', 'date', 'before_or_equal:today'],
            'refund_amount' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'note'          => ['nullable', 'string', 'max:255'],
            'items'         => ['required', 'array'],
            'items.*'       => ['nullable', 'numeric', 'min:0', 'max:999999'],
        ];
    }
}
