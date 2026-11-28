<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    protected $fillable = ['purchase_return_id', 'ingredient_id', 'quantity', 'unit_cost', 'line_total'];

    protected $casts = [
        'quantity'   => 'decimal:3',
        'unit_cost'  => 'decimal:4',
        'line_total' => 'decimal:2',
    ];

    // soft-deleted return বাদ রাখতে ইচ্ছাকৃতভাবে withTrashed নেই
    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class)->withTrashed();
    }
}
