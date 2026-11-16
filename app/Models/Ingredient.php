<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ingredient extends Model
{
    use HasFactory, SoftDeletes;

    public const UNITS = ['pcs', 'kg', 'g', 'l', 'ml', 'dozen'];

    protected $fillable = ['name', 'unit', 'stock_quantity', 'reorder_level', 'average_cost', 'is_active'];

    protected $casts = [
        'stock_quantity' => 'decimal:3',
        'reorder_level'  => 'decimal:3',
        'average_cost'   => 'decimal:4',
        'is_active'      => 'boolean',
    ];

    // স্টক রিঅর্ডার লেভেলের সমান বা কম হলে true
    public function getIsLowAttribute(): bool
    {
        return (float) $this->stock_quantity <= (float) $this->reorder_level;
    }

    // 12.500 কে 12.5 দেখানোর জন্য
    public function formatQty($value): string
    {
        return rtrim(rtrim(number_format((float) $value, 3, '.', ''), '0'), '.');
    }
}
