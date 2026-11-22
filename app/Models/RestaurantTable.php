<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RestaurantTable extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUSES = ['available' => 'Available', 'occupied' => 'Occupied', 'reserved' => 'Reserved'];

    protected $fillable = ['name', 'capacity', 'status', 'is_active'];

    protected $casts = [
        'capacity'  => 'integer',
        'is_active' => 'boolean',
    ];
}
