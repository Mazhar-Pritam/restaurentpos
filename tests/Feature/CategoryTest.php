<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_a_category_with_a_slug(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('categories.store'), [
                'name' => 'Burgers',
                'is_active' => '1',
            ])
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', [
            'name' => 'Burgers',
            'slug' => 'burgers',
            'is_active' => true,
        ]);
    }

    public function test_update_allows_the_current_category_name(): void
    {
        $category = Category::create([
            'name' => 'Drinks',
            'is_active' => true,
        ]);

        $this->actingAs(User::factory()->create())
            ->put(route('categories.update', $category), [
                'name' => 'Drinks',
                'is_active' => '0',
            ])
            ->assertRedirect(route('categories.index'));

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Drinks',
            'is_active' => false,
        ]);
    }

    public function test_category_with_menu_items_cannot_be_deleted(): void
    {
        Schema::create('menu_items', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('category_id');
        });

        $category = Category::create([
            'name' => 'Sides',
            'is_active' => true,
        ]);
        DB::table('menu_items')->insert(['category_id' => $category->id]);

        $this->actingAs(User::factory()->create())
            ->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }
}
