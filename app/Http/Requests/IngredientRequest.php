<?php

namespace App\Http\Requests;

use App\Models\Ingredient;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IngredientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('ingredient')?->id;

        return [
            'name'           => ['required', 'string', 'max:255',
                                 Rule::unique('ingredients', 'name')->ignore($id)->whereNull('deleted_at')],
            'unit'           => ['required', Rule::in(Ingredient::UNITS)],
            'reorder_level'  => ['required', 'numeric', 'min:0', 'max:999999999'],
            'is_active'      => ['required', 'boolean'],
            // শুধু নতুন ইনগ্রেডিয়েন্ট বানানোর সময় (opening stock)। edit এ কন্ট্রোলার এগুলো বাদ দেয়।
            'stock_quantity' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'average_cost'   => ['nullable', 'numeric', 'min:0', 'max:999999'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'An ingredient with this name already exists.',
            'unit.in'     => 'Please choose a valid unit.',
        ];
    }
}
