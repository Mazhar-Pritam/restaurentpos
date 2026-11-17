<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('customer')?->id;

        return [
            'name'      => ['required', 'string', 'max:255'],
            'phone'     => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{5,30}$/',
                            Rule::unique('customers', 'phone')->ignore($id)->whereNull('deleted_at')],
            'email'     => ['nullable', 'email', 'max:255'],
            'address'   => ['nullable', 'string', 'max:255'],
            'is_active' => ['required', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'phone.unique' => 'A customer with this phone number already exists.',
            'phone.regex'  => 'Phone number may contain digits, spaces, + and - only.',
        ];
    }
}
