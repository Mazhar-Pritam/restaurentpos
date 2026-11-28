<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Purchase extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'invoice_no', 'supplier_id', 'purchase_date', 'total_amount', 'paid_amount', 'note', 'user_id',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'total_amount'  => 'decimal:2',
        'paid_amount'   => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class)->withTrashed();
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function returns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    // মোট ফেরত দেওয়া মালের দাম
    public function getReturnedAmountAttribute(): float
    {
        return round((float) $this->returns->sum('total_amount'), 2);
    }

    // ফেরতের বদলে সাপ্লায়ারের কাছ থেকে নগদ ফেরত পাওয়া টাকা
    public function getRefundedAmountAttribute(): float
    {
        return round((float) $this->returns->sum('refund_amount'), 2);
    }

    // বাকি টাকা = মোট − ফেরত − (পরিশোধ − নগদ ফেরত পাওয়া)
    public function getDueAttribute(): float
    {
        $netPaid = (float) $this->paid_amount - $this->refunded_amount;

        return max(0, round((float) $this->total_amount - $this->returned_amount - $netPaid, 2));
    }

    // Paid / Partial / Unpaid
    public function getPaymentStatusAttribute(): string
    {
        if ($this->due <= 0.005) {
            return 'Paid';
        }

        return (float) $this->paid_amount > 0.005 ? 'Partial' : 'Unpaid';
    }
}
