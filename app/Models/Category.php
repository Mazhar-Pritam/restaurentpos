<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Category extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Category $category): void {
            $baseSlug = Str::slug($category->name) ?: 'category';
            $slug = $baseSlug;
            $number = 1;

            while (static::withTrashed()
                ->where('slug', $slug)
                ->when($category->exists, fn ($query) => $query->where(
                    $category->getKeyName(),
                    '!=',
                    $category->getKey()
                ))
                ->exists()) {
                $slug = $baseSlug.'-'.$number;
                $number++;
            }

            $category->slug = $slug;
        });
    }
}
