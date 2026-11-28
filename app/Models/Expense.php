<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory, SoftDeletes;

    public const CATEGORIES = [
        'rent'        => 'Rent',
        'utilities'   => 'Utilities (electricity, gas, water)',
        'salary'      => 'Salary / wages',
        'transport'   => 'Transport',
        'maintenance' => 'Repair & maintenance',
        'marketing'   => 'Marketing',
        'cleaning'    => 'Cleaning & supplies',
        'license'     => 'License / tax / fees',
        'other'       => 'Other',
    ];

    public const PAYMENT_METHODS = [
        'cash'   => 'Cash',
        'bank'   => 'Bank',
        'bkash'  => 'bKash',
        'nagad'  => 'Nagad',
        'rocket' => 'Rocket',
    ];

    protected $fillable = ['expense_date', 'category', 'amount', 'payment_method', 'paid_to', 'note', 'user_id'];

    protected $casts = [
        'expense_date' => 'date',
        'amount'       => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
