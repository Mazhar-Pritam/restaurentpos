<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'menu_item_id', 'item_name', 'quantity', 'unit_price', 'unit_cost', 'line_total', 'consumption'];

    protected $casts = [
        'quantity'    => 'integer',
        'unit_price'  => 'decimal:2',
        'unit_cost'   => 'decimal:4',
        'line_total'  => 'decimal:2',
        'consumption' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class)->withTrashed();
    }

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class)->withTrashed();
    }
}
