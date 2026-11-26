<?php

namespace App\Http\Requests;

use App\Models\Reservation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $reservation = $this->route('reservation');

        return [
            'table_id' => ['nullable', 'integer', Rule::exists('restaurant_tables', 'id')->where(fn ($q) => $q->where('is_active', true)->whereNull('deleted_at'))],
            'customer_id' => ['nullable', 'integer', Rule::exists('customers', 'id')->whereNull('deleted_at')],
            'guest_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\-\s()]{5,30}$/'],
            'party_size' => ['required', 'integer', 'min:1', 'max:100'],
            'reservation_at' => ['required', 'date'],
            'duration_minutes' => ['required', 'integer', 'min:15', 'max:1440'],
            'status' => ['required', Rule::in(array_keys(Reservation::STATUSES))],
            'note' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
