<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('supplier')?->id;

        return [
            'name'      => ['required', 'string', 'max:255',
                            Rule::unique('suppliers', 'name')->ignore($id)->whereNull('deleted_at')],
            'phone'     => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{5,30}$/'],
            'email'     => ['nullable', 'email', 'max:255'],
            'address'   => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.unique' => 'A supplier with this name already exists.',
            'phone.regex' => 'Phone number may contain digits, spaces, + and - only.',
        ];
    }
}
