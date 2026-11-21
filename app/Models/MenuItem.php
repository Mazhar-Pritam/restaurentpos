<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['category_id', 'name', 'price', 'description', 'image', 'is_active'];

    protected $casts = [
        'price'     => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category()
    {
        // soft-delete হওয়া ক্যাটাগরির নামও যাতে দেখা যায়
        return $this->belongsTo(Category::class)->withTrashed();
    }
    public function recipes()
     {
            return $this->hasMany(Recipe::class);
      } 

    // $item->image_url : ছবি থাকলে পাবলিক URL, না থাকলে null
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}
