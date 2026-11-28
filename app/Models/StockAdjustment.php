<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/** স্টক লেজার: একবার তৈরি হলে বদলানো/মোছা যায় না। ভুল হলে উল্টো একটা adjustment করতে হয়। */
class StockAdjustment extends Model
{
    public const REASONS = [
        'out' => [
            'wastage'          => 'Wastage (spilled / over-cooked)',
            'expired'          => 'Expired',
            'spoiled'          => 'Spoiled / rotten',
            'damaged'          => 'Damaged',
            'staff_meal'       => 'Staff meal',
            'count_correction' => 'Stock count correction',
            'other'            => 'Other',
        ],
        'in' => [
            'count_correction' => 'Stock count correction',
            'found'            => 'Found / returned to stock',
            'other'            => 'Other',
        ],
    ];

    protected $fillable = [
        'ingredient_id', 'direction', 'quantity', 'reason', 'unit_cost', 'stock_before', 'stock_after', 'note', 'user_id',
    ];

    protected $casts = [
        'quantity'     => 'decimal:3',
        'unit_cost'    => 'decimal:4',
        'stock_before' => 'decimal:3',
        'stock_after'  => 'decimal:3',
    ];

    public static function reasonLabel(string $direction, string $reason): string
    {
        return self::REASONS[$direction][$reason] ?? $reason;
    }

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class)->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
