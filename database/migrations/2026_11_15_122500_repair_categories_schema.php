<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $addName = ! Schema::hasColumn('categories', 'name');
        $addSlug = ! Schema::hasColumn('categories', 'slug');
        $addActive = ! Schema::hasColumn('categories', 'is_active');
        $addDeletedAt = ! Schema::hasColumn('categories', 'deleted_at');

        Schema::table('categories', function (Blueprint $table) use ($addName, $addSlug, $addActive, $addDeletedAt): void {
            if ($addName) {
                $table->string('name')->nullable();
            }

            if ($addSlug) {
                $table->string('slug')->nullable();
            }

            if ($addActive) {
                $table->boolean('is_active')->default(true);
            }

            if ($addDeletedAt) {
                $table->softDeletes();
            }
        });

        if ($addName || $addSlug) {
            foreach (DB::table('categories')->orderBy('id')->get() as $category) {
                $name = $addName ? 'Category '.$category->id : $category->name;
                $values = [];

                if ($addName) {
                    $values['name'] = $name;
                }

                if ($addSlug) {
                    $baseSlug = Str::slug($name) ?: 'category-'.$category->id;
                    $slug = $baseSlug;
                    $number = 1;

                    while (DB::table('categories')->where('slug', $slug)->exists()) {
                        $slug = $baseSlug.'-'.$number;
                        $number++;
                    }

                    $values['slug'] = $slug;
                }

                DB::table('categories')->where('id', $category->id)->update($values);
            }

            Schema::table('categories', function (Blueprint $table) use ($addName, $addSlug): void {
                if ($addName) {
                    $table->string('name')->nullable(false)->change();
                    $table->unique('name');
                }

                if ($addSlug) {
                    $table->string('slug')->nullable(false)->change();
                    $table->unique('slug');
                }
            });
        }
    }

    public function down(): void
    {
        // Keep repaired columns in place to avoid removing category data on rollback.
    }
};
