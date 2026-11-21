<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// recipes টেবিলের প্রতিটি সারি = একটি মেনু আইটেমের একটি ইনগ্রেডিয়েন্ট
class Recipe extends Model
{
    protected $fillable = ['menu_item_id', 'ingredient_id', 'quantity'];

    protected $casts = [
        'quantity' => 'decimal:3',
    ];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class)->withTrashed();
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class)->withTrashed();
    }
}
