<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RolePermissionSeeder::class);
    }

    public function test_authenticated_user_can_create_a_category_with_a_slug(): void
    {
        $this->actingAs($this->userWithRole('admin'))
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

        $this->actingAs($this->userWithRole('admin'))
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

        $this->actingAs($this->userWithRole('admin'))
            ->delete(route('categories.destroy', $category))
            ->assertRedirect(route('categories.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_staff_can_view_categories_but_cannot_manage_them(): void
    {
        $category = Category::create([
            'name' => 'Sides',
            'is_active' => true,
        ]);
        $staff = $this->userWithRole('staff');

        $this->actingAs($staff)
            ->get(route('categories.index'))
            ->assertOk()
            ->assertDontSee('Add category')
            ->assertDontSee('Delete');

        $this->get(route('categories.create'))->assertForbidden();
        $this->post(route('categories.store'), [
            'name' => 'New category',
            'is_active' => '1',
        ])->assertForbidden();
        $this->get(route('categories.edit', $category))->assertForbidden();
        $this->put(route('categories.update', $category), [
            'name' => 'Changed sides',
            'is_active' => '1',
        ])->assertForbidden();
        $this->delete(route('categories.destroy', $category))->assertForbidden();

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_first_registration_is_super_admin_and_later_users_are_staff(): void
    {
        $this->post('/register', [
            'name' => 'First User',
            'email' => 'first@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $firstUser = User::where('email', 'first@example.com')->firstOrFail();
        $this->assertTrue($firstUser->hasRole('super-admin'));

        $this->post(route('logout'));

        $this->post('/register', [
            'name' => 'Later User',
            'email' => 'later@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $laterUser = User::where('email', 'later@example.com')->firstOrFail();
        $this->assertTrue($laterUser->hasRole('staff'));
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }
}
