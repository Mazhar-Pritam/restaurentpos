<?php

namespace App\Http\Requests;

use App\Models\StockAdjustment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StockAdjustmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ingredient_id' => ['required', Rule::exists('ingredients', 'id')->where(fn ($q) => $q->whereNull('deleted_at')->where('is_active', 1))],
            'direction'     => ['required', Rule::in(['in', 'out'])],
            'quantity'      => ['required', 'numeric', 'gt:0', 'max:999999'],
            'reason'        => ['required', 'string'],
            'note'          => ['nullable', 'string', 'max:255'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v): void {
            if ($v->errors()->hasAny(['direction', 'reason'])) {
                return;
            }

            $dir    = $this->input('direction');
            $reason = $this->input('reason');

            if (! array_key_exists($reason, StockAdjustment::REASONS[$dir])) {
                $v->errors()->add('reason', 'This reason is not valid for the chosen type.');

                return;
            }

            if ($reason === 'other' && trim((string) $this->input('note')) === '') {
                $v->errors()->add('note', 'Please write a note when the reason is "Other".');
            }
        });
    }
}
