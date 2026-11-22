<?php

namespace App\Http\Requests;

use App\Models\RestaurantTable;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RestaurantTableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('table')?->id;

        return [
            'name'      => ['required', 'string', 'max:50',
                            Rule::unique('restaurant_tables', 'name')->ignore($id)->whereNull('deleted_at')],
            'capacity'  => ['required', 'integer', 'min:1', 'max:100'],
            'status'    => ['required', Rule::in(array_keys(RestaurantTable::STATUSES))],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'A table with this name already exists.',
        ];
    }
}
