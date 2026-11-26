<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    public const TYPES = ['dine_in' => 'Dine-in', 'takeaway' => 'Takeaway'];

    public const PAYMENT_METHODS = [
        'cash'   => 'Cash',
        'card'   => 'Card',
        'bkash'  => 'bKash',
        'nagad'  => 'Nagad',
        'rocket' => 'Rocket',
    ];

    public const STATUSES = ['cooking' => 'Cooking', 'ready' => 'Ready', 'completed' => 'Completed'];

    protected $fillable = [
        'order_no', 'type', 'table_id', 'customer_id', 'subtotal', 'discount_type', 'discount_value',
        'discount_amount', 'vat_rate', 'vat_amount', 'total', 'payment_method', 'status', 'note', 'user_id',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'discount_value'  => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'vat_rate'        => 'decimal:2',
        'vat_amount'      => 'decimal:2',
        'total'           => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

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
