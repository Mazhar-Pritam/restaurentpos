<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reservation extends Model
{
    use SoftDeletes;

    public const STATUSES = [
        'booked' => 'Booked',
        'seated' => 'Seated',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ];

    protected $fillable = [
        'table_id', 'customer_id', 'guest_name', 'phone', 'party_size',
        'reservation_at', 'duration_minutes', 'status', 'note', 'user_id',
    ];

    protected $casts = [
        'reservation_at' => 'datetime',
        'party_size' => 'integer',
        'duration_minutes' => 'integer',
    ];

    public function table()
    {
        return $this->belongsTo(RestaurantTable::class, 'table_id')->withTrashed();
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
