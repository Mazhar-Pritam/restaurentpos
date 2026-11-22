<?php

namespace Database\Seeders;

use App\Models\Ingredient;
use App\Models\MenuItem;
use App\Models\Recipe;
use Illuminate\Database\Seeder;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        // menu item নাম => [ingredient নাম => এক প্লেটের পরিমাণ (ingredient-এর নিজের unit এ)]
        $recipes = [
            'Mutton Kacchi' => [
                'Basmati Rice' => 0.450,  // kg
                'Olive Oil'    => 0.100,  // l
                // বাকি ingredient এখানে যোগ করুন
            ],
        ];

        foreach ($recipes as $menuName => $items) {
            $menuItem = MenuItem::where('name', $menuName)->firstOrFail();

            foreach ($items as $ingredientName => $qty) {
                $ingredient = Ingredient::where('name', $ingredientName)->firstOrFail();

                Recipe::updateOrCreate(
                    [
                        'menu_item_id'  => $menuItem->id,
                        'ingredient_id' => $ingredient->id,
                    ],
                    ['quantity' => $qty]
                );
            }
        }
    }
}