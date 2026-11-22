<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

   $this->call([
    IngredientSeeder::class,
    MenuItemSeeder::class,   // menu item আগে লাগবে
    RecipeSeeder::class,
]);
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);
    }
}
