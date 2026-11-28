<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalesReturn extends Model
{
    use HasFactory, SoftDeletes;

    public const REASONS = [
        'wrong_item'    => 'Wrong item served',
        'quality'       => 'Quality problem',
        'long_wait'     => 'Took too long',
        'changed_mind'  => 'Customer changed mind',
        'other'         => 'Other',
    ];

    protected $fillable = [
        'return_no', 'order_id', 'subtotal', 'discount_amount', 'vat_amount', 'refund_amount',
        'refund_method', 'reason', 'restock', 'note', 'user_id',
    ];

    protected $casts = [
        'subtotal'        => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'vat_amount'      => 'decimal:2',
        'refund_amount'   => 'decimal:2',
        'restock'         => 'boolean',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class)->withTrashed();
    }

    public function items()
    {
        return $this->hasMany(SalesReturnItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
