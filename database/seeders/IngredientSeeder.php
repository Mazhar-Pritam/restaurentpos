<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IngredientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ingredients = [
            // ---------- Patty, Meat & Protein ----------
            ['name' => 'Beef Patty', 'unit' => 'pcs', 'stock_quantity' => 120.00, 'reorder_level' => 30, 'average_cost' => 123.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chicken Patty', 'unit' => 'pcs', 'stock_quantity' => 120.00, 'reorder_level' => 30, 'average_cost' => 85.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chicken Fillet (Crispy)', 'unit' => 'pcs', 'stock_quantity' => 100.00, 'reorder_level' => 30, 'average_cost' => 70.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chicken Breast Boneless', 'unit' => 'kg', 'stock_quantity' => 15.00, 'reorder_level' => 5, 'average_cost' => 320.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chicken Wings', 'unit' => 'kg', 'stock_quantity' => 10.00, 'reorder_level' => 3, 'average_cost' => 220.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chicken Nuggets', 'unit' => 'pcs', 'stock_quantity' => 300.00, 'reorder_level' => 80, 'average_cost' => 12.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chicken Sausage', 'unit' => 'pcs', 'stock_quantity' => 100.00, 'reorder_level' => 30, 'average_cost' => 25.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Beef Bacon', 'unit' => 'kg', 'stock_quantity' => 5.00, 'reorder_level' => 1.5, 'average_cost' => 1100.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Beef Shawarma Meat', 'unit' => 'kg', 'stock_quantity' => 10.00, 'reorder_level' => 3, 'average_cost' => 850.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chicken Shawarma Meat', 'unit' => 'kg', 'stock_quantity' => 10.00, 'reorder_level' => 3, 'average_cost' => 380.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Egg (Chicken)', 'unit' => 'pcs', 'stock_quantity' => 180.00, 'reorder_level' => 60, 'average_cost' => 13.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],

            // ---------- Buns, Bread & Dough ----------
            ['name' => 'Burger Bun (Plain)', 'unit' => 'pcs', 'stock_quantity' => 200.00, 'reorder_level' => 50, 'average_cost' => 15.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Burger Bun (Sesame)', 'unit' => 'pcs', 'stock_quantity' => 200.00, 'reorder_level' => 50, 'average_cost' => 18.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Hot Dog Bun', 'unit' => 'pcs', 'stock_quantity' => 100.00, 'reorder_level' => 30, 'average_cost' => 14.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sandwich Bread', 'unit' => 'pcs', 'stock_quantity' => 20.00, 'reorder_level' => 6, 'average_cost' => 60.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Shawarma Bread (Pita)', 'unit' => 'pcs', 'stock_quantity' => 150.00, 'reorder_level' => 40, 'average_cost' => 12.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tortilla Wrap', 'unit' => 'pcs', 'stock_quantity' => 100.00, 'reorder_level' => 30, 'average_cost' => 14.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pizza Dough Ball', 'unit' => 'pcs', 'stock_quantity' => 60.00, 'reorder_level' => 20, 'average_cost' => 35.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Maida (Refined Flour)', 'unit' => 'kg', 'stock_quantity' => 40.00, 'reorder_level' => 10, 'average_cost' => 70.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Corn Flour', 'unit' => 'kg', 'stock_quantity' => 10.00, 'reorder_level' => 3, 'average_cost' => 120.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bread Crumbs', 'unit' => 'kg', 'stock_quantity' => 15.00, 'reorder_level' => 4, 'average_cost' => 220.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Spaghetti Pasta', 'unit' => 'kg', 'stock_quantity' => 10.00, 'reorder_level' => 3, 'average_cost' => 250.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Penne Pasta', 'unit' => 'kg', 'stock_quantity' => 10.00, 'reorder_level' => 3, 'average_cost' => 260.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],

            // ---------- Cheese & Dairy ----------
            ['name' => 'Cheddar Cheese Slice', 'unit' => 'pcs', 'stock_quantity' => 300.00, 'reorder_level' => 80, 'average_cost' => 14.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mozzarella Cheese', 'unit' => 'kg', 'stock_quantity' => 10.00, 'reorder_level' => 3, 'average_cost' => 900.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cheese Sauce', 'unit' => 'kg', 'stock_quantity' => 6.00, 'reorder_level' => 2, 'average_cost' => 650.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Parmesan Cheese', 'unit' => 'kg', 'stock_quantity' => 2.00, 'reorder_level' => 0.5, 'average_cost' => 1800.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Butter', 'unit' => 'kg', 'stock_quantity' => 8.00, 'reorder_level' => 2, 'average_cost' => 750.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Milk', 'unit' => 'l', 'stock_quantity' => 30.00, 'reorder_level' => 8, 'average_cost' => 95.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Whipping Cream', 'unit' => 'l', 'stock_quantity' => 6.00, 'reorder_level' => 2, 'average_cost' => 450.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ice Cream (Vanilla)', 'unit' => 'l', 'stock_quantity' => 10.00, 'reorder_level' => 3, 'average_cost' => 380.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],

            // ---------- Vegetables & Fresh ----------
            ['name' => 'Lettuce (Iceberg)', 'unit' => 'kg', 'stock_quantity' => 6.00, 'reorder_level' => 2, 'average_cost' => 200.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tomato', 'unit' => 'kg', 'stock_quantity' => 15.00, 'reorder_level' => 5, 'average_cost' => 80.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Onion', 'unit' => 'kg', 'stock_quantity' => 30.00, 'reorder_level' => 10, 'average_cost' => 80.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cucumber', 'unit' => 'kg', 'stock_quantity' => 8.00, 'reorder_level' => 3, 'average_cost' => 60.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Capsicum', 'unit' => 'kg', 'stock_quantity' => 5.00, 'reorder_level' => 2, 'average_cost' => 180.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cabbage', 'unit' => 'kg', 'stock_quantity' => 10.00, 'reorder_level' => 3, 'average_cost' => 40.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Carrot', 'unit' => 'kg', 'stock_quantity' => 8.00, 'reorder_level' => 3, 'average_cost' => 70.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mushroom', 'unit' => 'kg', 'stock_quantity' => 3.00, 'reorder_level' => 1, 'average_cost' => 450.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jalapeno (Sliced)', 'unit' => 'kg', 'stock_quantity' => 3.00, 'reorder_level' => 1, 'average_cost' => 600.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pickled Cucumber (Gherkin)', 'unit' => 'kg', 'stock_quantity' => 5.00, 'reorder_level' => 1.5, 'average_cost' => 380.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Black Olive (Sliced)', 'unit' => 'kg', 'stock_quantity' => 2.00, 'reorder_level' => 0.5, 'average_cost' => 700.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Garlic', 'unit' => 'kg', 'stock_quantity' => 8.00, 'reorder_level' => 2, 'average_cost' => 220.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Green Chili', 'unit' => 'kg', 'stock_quantity' => 3.00, 'reorder_level' => 1, 'average_cost' => 120.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lemon', 'unit' => 'pcs', 'stock_quantity' => 100.00, 'reorder_level' => 30, 'average_cost' => 8.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Coriander Leaves', 'unit' => 'kg', 'stock_quantity' => 2.00, 'reorder_level' => 0.5, 'average_cost' => 150.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],

            // ---------- Potato & Fried Items ----------
            ['name' => 'Frozen French Fries', 'unit' => 'kg', 'stock_quantity' => 50.00, 'reorder_level' => 15, 'average_cost' => 190.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Potato Wedges (Frozen)', 'unit' => 'kg', 'stock_quantity' => 20.00, 'reorder_level' => 6, 'average_cost' => 210.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Onion Rings (Frozen)', 'unit' => 'kg', 'stock_quantity' => 10.00, 'reorder_level' => 3, 'average_cost' => 320.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Potato (Fresh)', 'unit' => 'kg', 'stock_quantity' => 30.00, 'reorder_level' => 10, 'average_cost' => 40.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Coleslaw Mix', 'unit' => 'kg', 'stock_quantity' => 5.00, 'reorder_level' => 2, 'average_cost' => 180.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],

            // ---------- Sauces & Condiments ----------
            ['name' => 'Mayonnaise', 'unit' => 'kg', 'stock_quantity' => 15.00, 'reorder_level' => 4, 'average_cost' => 380.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tomato Ketchup', 'unit' => 'l', 'stock_quantity' => 20.00, 'reorder_level' => 6, 'average_cost' => 220.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mustard Sauce', 'unit' => 'l', 'stock_quantity' => 6.00, 'reorder_level' => 2, 'average_cost' => 400.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'BBQ Sauce', 'unit' => 'l', 'stock_quantity' => 8.00, 'reorder_level' => 2, 'average_cost' => 450.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chili Sauce', 'unit' => 'l', 'stock_quantity' => 8.00, 'reorder_level' => 2, 'average_cost' => 250.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Garlic Sauce', 'unit' => 'kg', 'stock_quantity' => 8.00, 'reorder_level' => 2, 'average_cost' => 400.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Burger Special Sauce', 'unit' => 'kg', 'stock_quantity' => 8.00, 'reorder_level' => 2, 'average_cost' => 420.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sriracha Sauce', 'unit' => 'l', 'stock_quantity' => 4.00, 'reorder_level' => 1, 'average_cost' => 550.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pizza Sauce', 'unit' => 'kg', 'stock_quantity' => 8.00, 'reorder_level' => 2, 'average_cost' => 300.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tomato Paste', 'unit' => 'kg', 'stock_quantity' => 6.00, 'reorder_level' => 2, 'average_cost' => 300.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Soy Sauce', 'unit' => 'l', 'stock_quantity' => 5.00, 'reorder_level' => 1.5, 'average_cost' => 220.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chocolate Syrup', 'unit' => 'l', 'stock_quantity' => 4.00, 'reorder_level' => 1, 'average_cost' => 500.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],

            // ---------- Oil, Spices & Seasoning ----------
            ['name' => 'Soybean Oil', 'unit' => 'l', 'stock_quantity' => 60.00, 'reorder_level' => 20, 'average_cost' => 175.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Olive Oil', 'unit' => 'l', 'stock_quantity' => 3.00, 'reorder_level' => 1, 'average_cost' => 1200.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Salt', 'unit' => 'kg', 'stock_quantity' => 15.00, 'reorder_level' => 5, 'average_cost' => 40.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sugar', 'unit' => 'kg', 'stock_quantity' => 15.00, 'reorder_level' => 5, 'average_cost' => 130.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Black Pepper Powder', 'unit' => 'kg', 'stock_quantity' => 2.00, 'reorder_level' => 0.5, 'average_cost' => 1500.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chili Flakes', 'unit' => 'kg', 'stock_quantity' => 2.00, 'reorder_level' => 0.5, 'average_cost' => 700.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Oregano', 'unit' => 'kg', 'stock_quantity' => 1.00, 'reorder_level' => 0.3, 'average_cost' => 1200.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Paprika Powder', 'unit' => 'kg', 'stock_quantity' => 1.50, 'reorder_level' => 0.5, 'average_cost' => 1000.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Garlic Powder', 'unit' => 'kg', 'stock_quantity' => 1.50, 'reorder_level' => 0.5, 'average_cost' => 900.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fried Chicken Coating Mix', 'unit' => 'kg', 'stock_quantity' => 20.00, 'reorder_level' => 6, 'average_cost' => 350.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ajinomoto (MSG)', 'unit' => 'kg', 'stock_quantity' => 2.00, 'reorder_level' => 0.5, 'average_cost' => 350.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],

            // ---------- Beverages ----------
            ['name' => 'Soft Drink (250ml)', 'unit' => 'pcs', 'stock_quantity' => 120.00, 'reorder_level' => 48, 'average_cost' => 22.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Soft Drink (500ml)', 'unit' => 'pcs', 'stock_quantity' => 120.00, 'reorder_level' => 48, 'average_cost' => 30.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mineral Water (500ml)', 'unit' => 'pcs', 'stock_quantity' => 240.00, 'reorder_level' => 72, 'average_cost' => 12.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Coffee Powder', 'unit' => 'kg', 'stock_quantity' => 3.00, 'reorder_level' => 1, 'average_cost' => 1800.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tea Leaves', 'unit' => 'kg', 'stock_quantity' => 4.00, 'reorder_level' => 1, 'average_cost' => 550.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Milkshake Powder', 'unit' => 'kg', 'stock_quantity' => 5.00, 'reorder_level' => 1.5, 'average_cost' => 600.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ice Cubes', 'unit' => 'kg', 'stock_quantity' => 30.00, 'reorder_level' => 10, 'average_cost' => 10.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],

            // ---------- Packaging & Consumables ----------
            ['name' => 'Burger Box', 'unit' => 'pcs', 'stock_quantity' => 500.00, 'reorder_level' => 150, 'average_cost' => 7.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fries Box / Pouch', 'unit' => 'pcs', 'stock_quantity' => 500.00, 'reorder_level' => 150, 'average_cost' => 4.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Pizza Box (8 inch)', 'unit' => 'pcs', 'stock_quantity' => 200.00, 'reorder_level' => 60, 'average_cost' => 25.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Wrap / Shawarma Paper', 'unit' => 'pcs', 'stock_quantity' => 1000.00, 'reorder_level' => 300, 'average_cost' => 1.50, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Paper Carry Bag', 'unit' => 'pcs', 'stock_quantity' => 500.00, 'reorder_level' => 150, 'average_cost' => 6.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Paper Cup (12oz)', 'unit' => 'pcs', 'stock_quantity' => 500.00, 'reorder_level' => 150, 'average_cost' => 5.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cup Lid', 'unit' => 'pcs', 'stock_quantity' => 500.00, 'reorder_level' => 150, 'average_cost' => 1.50, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Straw', 'unit' => 'pcs', 'stock_quantity' => 1000.00, 'reorder_level' => 300, 'average_cost' => 0.50, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sauce Sachet / Dip Cup', 'unit' => 'pcs', 'stock_quantity' => 1000.00, 'reorder_level' => 300, 'average_cost' => 1.50, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tissue Paper', 'unit' => 'pcs', 'stock_quantity' => 50.00, 'reorder_level' => 15, 'average_cost' => 40.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Aluminium Foil', 'unit' => 'pcs', 'stock_quantity' => 8.00, 'reorder_level' => 3, 'average_cost' => 350.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Disposable Gloves', 'unit' => 'pcs', 'stock_quantity' => 10.00, 'reorder_level' => 3, 'average_cost' => 250.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gas Cylinder (LPG)', 'unit' => 'pcs', 'stock_quantity' => 4.00, 'reorder_level' => 2, 'average_cost' => 1450.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
       
            ['name' => 'Basmati Rice', 'unit' => 'kg', 'stock_quantity' => 50.00, 'reorder_level' => 15, 'average_cost' => 160.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chinigura (Kalijira) Rice', 'unit' => 'kg', 'stock_quantity' => 60.00, 'reorder_level' => 15, 'average_cost' => 130.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Polao Rice', 'unit' => 'kg', 'stock_quantity' => 40.00, 'reorder_level' => 10, 'average_cost' => 140.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],

            // ---------- Meat & Eggs ----------
            ['name' => 'Mutton (Biriyani Cut)', 'unit' => 'kg', 'stock_quantity' => 25.00, 'reorder_level' => 8, 'average_cost' => 1150.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Beef (Biriyani Cut)', 'unit' => 'kg', 'stock_quantity' => 30.00, 'reorder_level' => 10, 'average_cost' => 780.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Broiler Chicken (Biriyani Cut)', 'unit' => 'kg', 'stock_quantity' => 30.00, 'reorder_level' => 10, 'average_cost' => 200.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Deshi Chicken', 'unit' => 'kg', 'stock_quantity' => 15.00, 'reorder_level' => 5, 'average_cost' => 550.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Egg (Chicken)', 'unit' => 'pcs', 'stock_quantity' => 180.00, 'reorder_level' => 60, 'average_cost' => 13.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],

            // ---------- Vegetables & Fresh Herbs ----------
            ['name' => 'Potato', 'unit' => 'kg', 'stock_quantity' => 40.00, 'reorder_level' => 12, 'average_cost' => 40.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Onion', 'unit' => 'kg', 'stock_quantity' => 50.00, 'reorder_level' => 15, 'average_cost' => 80.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Fried Onion (Beresta)', 'unit' => 'kg', 'stock_quantity' => 8.00, 'reorder_level' => 2, 'average_cost' => 450.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Garlic', 'unit' => 'kg', 'stock_quantity' => 10.00, 'reorder_level' => 3, 'average_cost' => 220.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ginger', 'unit' => 'kg', 'stock_quantity' => 10.00, 'reorder_level' => 3, 'average_cost' => 200.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Green Chili', 'unit' => 'kg', 'stock_quantity' => 5.00, 'reorder_level' => 1.5, 'average_cost' => 120.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Coriander Leaves', 'unit' => 'kg', 'stock_quantity' => 3.00, 'reorder_level' => 1, 'average_cost' => 150.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mint Leaves', 'unit' => 'kg', 'stock_quantity' => 3.00, 'reorder_level' => 1, 'average_cost' => 200.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lemon', 'unit' => 'pcs', 'stock_quantity' => 100.00, 'reorder_level' => 30, 'average_cost' => 8.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cucumber', 'unit' => 'kg', 'stock_quantity' => 8.00, 'reorder_level' => 3, 'average_cost' => 60.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],

            // ---------- Dairy & Fats ----------
            ['name' => 'Yogurt (Doi)', 'unit' => 'kg', 'stock_quantity' => 20.00, 'reorder_level' => 6, 'average_cost' => 160.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Ghee', 'unit' => 'kg', 'stock_quantity' => 10.00, 'reorder_level' => 3, 'average_cost' => 950.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Milk', 'unit' => 'l', 'stock_quantity' => 15.00, 'reorder_level' => 5, 'average_cost' => 95.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Soybean Oil', 'unit' => 'l', 'stock_quantity' => 60.00, 'reorder_level' => 20, 'average_cost' => 175.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],

            // ---------- Whole Spices ----------
            ['name' => 'Cardamom (Elach)', 'unit' => 'kg', 'stock_quantity' => 1.00, 'reorder_level' => 0.3, 'average_cost' => 4500.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cinnamon (Daruchini)', 'unit' => 'kg', 'stock_quantity' => 1.50, 'reorder_level' => 0.5, 'average_cost' => 700.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cloves (Lobongo)', 'unit' => 'kg', 'stock_quantity' => 1.00, 'reorder_level' => 0.3, 'average_cost' => 1800.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Bay Leaf (Tejpata)', 'unit' => 'kg', 'stock_quantity' => 1.00, 'reorder_level' => 0.3, 'average_cost' => 300.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Black Pepper Whole', 'unit' => 'kg', 'stock_quantity' => 2.00, 'reorder_level' => 0.5, 'average_cost' => 1400.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Nutmeg (Joyfol)', 'unit' => 'kg', 'stock_quantity' => 0.50, 'reorder_level' => 0.1, 'average_cost' => 3500.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Mace (Joitri)', 'unit' => 'kg', 'stock_quantity' => 0.30, 'reorder_level' => 0.1, 'average_cost' => 5500.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Star Anise', 'unit' => 'kg', 'stock_quantity' => 0.50, 'reorder_level' => 0.1, 'average_cost' => 1600.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cumin Seeds (Jira)', 'unit' => 'kg', 'stock_quantity' => 3.00, 'reorder_level' => 1, 'average_cost' => 750.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Shahi Jeera (Caraway)', 'unit' => 'kg', 'stock_quantity' => 0.50, 'reorder_level' => 0.1, 'average_cost' => 1500.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dry Red Chili', 'unit' => 'kg', 'stock_quantity' => 3.00, 'reorder_level' => 1, 'average_cost' => 450.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],

            // ---------- Ground Spices & Masala ----------
            ['name' => 'Turmeric Powder', 'unit' => 'kg', 'stock_quantity' => 4.00, 'reorder_level' => 1.5, 'average_cost' => 300.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Chili Powder', 'unit' => 'kg', 'stock_quantity' => 5.00, 'reorder_level' => 1.5, 'average_cost' => 500.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Coriander Powder', 'unit' => 'kg', 'stock_quantity' => 4.00, 'reorder_level' => 1.5, 'average_cost' => 300.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cumin Powder', 'unit' => 'kg', 'stock_quantity' => 3.00, 'reorder_level' => 1, 'average_cost' => 800.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Garam Masala Powder', 'unit' => 'kg', 'stock_quantity' => 3.00, 'reorder_level' => 1, 'average_cost' => 1200.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Biryani Masala', 'unit' => 'kg', 'stock_quantity' => 3.00, 'reorder_level' => 1, 'average_cost' => 1000.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kacchi Masala', 'unit' => 'kg', 'stock_quantity' => 3.00, 'reorder_level' => 1, 'average_cost' => 1000.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Black Pepper Powder', 'unit' => 'kg', 'stock_quantity' => 2.00, 'reorder_level' => 0.5, 'average_cost' => 1500.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Jafran (Biryani Color)', 'unit' => 'g', 'stock_quantity' => 200.00, 'reorder_level' => 50, 'average_cost' => 2.50, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Saffron (Zafran)', 'unit' => 'g', 'stock_quantity' => 20.00, 'reorder_level' => 5, 'average_cost' => 450.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Kewra Water', 'unit' => 'ml', 'stock_quantity' => 1000.00, 'reorder_level' => 250, 'average_cost' => 0.30, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Rose Water', 'unit' => 'ml', 'stock_quantity' => 1000.00, 'reorder_level' => 250, 'average_cost' => 0.35, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Salt', 'unit' => 'kg', 'stock_quantity' => 20.00, 'reorder_level' => 6, 'average_cost' => 40.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sugar', 'unit' => 'kg', 'stock_quantity' => 10.00, 'reorder_level' => 3, 'average_cost' => 130.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],

            // ---------- Dry Fruits & Add-ons ----------
            ['name' => 'Raisin (Kishmish)', 'unit' => 'kg', 'stock_quantity' => 3.00, 'reorder_level' => 1, 'average_cost' => 700.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Cashew Nut', 'unit' => 'kg', 'stock_quantity' => 3.00, 'reorder_level' => 1, 'average_cost' => 1400.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Almond', 'unit' => 'kg', 'stock_quantity' => 2.00, 'reorder_level' => 0.5, 'average_cost' => 1500.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dried Plum (Alubukhara)', 'unit' => 'kg', 'stock_quantity' => 2.00, 'reorder_level' => 0.5, 'average_cost' => 900.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],

            // ---------- Side Dishes ----------
            ['name' => 'Borhani Mix', 'unit' => 'kg', 'stock_quantity' => 3.00, 'reorder_level' => 1, 'average_cost' => 600.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Roasted Chicken / Jali Kabab Meat', 'unit' => 'kg', 'stock_quantity' => 6.00, 'reorder_level' => 2, 'average_cost' => 420.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Firni / Zarda Mix', 'unit' => 'kg', 'stock_quantity' => 4.00, 'reorder_level' => 1, 'average_cost' => 450.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],

            // ---------- Packaging & Fuel ----------
            ['name' => 'Biryani Box (Container)', 'unit' => 'pcs', 'stock_quantity' => 300.00, 'reorder_level' => 100, 'average_cost' => 18.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Borhani Cup with Lid', 'unit' => 'pcs', 'stock_quantity' => 300.00, 'reorder_level' => 100, 'average_cost' => 6.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Salad Cup', 'unit' => 'pcs', 'stock_quantity' => 300.00, 'reorder_level' => 100, 'average_cost' => 3.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Aluminium Foil', 'unit' => 'pcs', 'stock_quantity' => 8.00, 'reorder_level' => 3, 'average_cost' => 350.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Disposable Spoon', 'unit' => 'pcs', 'stock_quantity' => 1000.00, 'reorder_level' => 300, 'average_cost' => 1.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Tissue Paper', 'unit' => 'pcs', 'stock_quantity' => 50.00, 'reorder_level' => 15, 'average_cost' => 40.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Gas Cylinder (LPG)', 'unit' => 'pcs', 'stock_quantity' => 6.00, 'reorder_level' => 2, 'average_cost' => 1450.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Firewood / Charcoal (Dum)', 'unit' => 'kg', 'stock_quantity' => 30.00, 'reorder_level' => 10, 'average_cost' => 45.00, 'is_active' => 1, 'deleted_at' => null, 'created_at' => now(), 'updated_at' => now()],
        ];
        

               foreach ($ingredients as $ingredient) {
            $exists = DB::table('ingredients')
                ->where('name', $ingredient['name'])
                ->exists();

            if (! $exists) {
                DB::table('ingredients')->insert($ingredient);
            }
        }
    }
}
