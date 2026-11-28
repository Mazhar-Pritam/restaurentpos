<?php

namespace App\Http\Requests;

use App\Models\Expense;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ExpenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'expense_date'   => ['required', 'date', 'before_or_equal:today'],
            'category'       => ['required', Rule::in(array_keys(Expense::CATEGORIES))],
            'amount'         => ['required', 'numeric', 'gt:0', 'max:99999999'],
            'payment_method' => ['required', Rule::in(array_keys(Expense::PAYMENT_METHODS))],
            'paid_to'        => ['nullable', 'string', 'max:255'],
            'note'           => ['nullable', 'string', 'max:255'],
        ];
    }
}
