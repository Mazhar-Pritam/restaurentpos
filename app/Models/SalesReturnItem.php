<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturnItem extends Model
{
    protected $fillable = ['sales_return_id', 'order_item_id', 'item_name', 'quantity', 'unit_price', 'unit_cost', 'line_total', 'restocked'];

    protected $casts = [
        'quantity'   => 'integer',
        'unit_price' => 'decimal:2',
        'unit_cost'  => 'decimal:4',
        'line_total' => 'decimal:2',
        'restocked'  => 'array',
    ];

    // soft-deleted return বাদ রাখতে ইচ্ছাকৃতভাবে withTrashed নেই
    public function salesReturn()
    {
        return $this->belongsTo(SalesReturn::class);
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }
}
