<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MenuItemController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\RecipeController;
// লগইন, রেজিস্টার, পাসওয়ার্ড রিসেট, লগআউট: laravel/ui এর রুট
// (লগআউট রুটও এর ভেতরেই আছে, আলাদা করে লাগবে না)
Auth::routes();

Route::get('/', fn () => redirect('/dashboard'));

// এই গ্রুপের সব পেজ লগইন ছাড়া খুলবে না
Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [HomeController::class, 'index'])->name('dashboard');

    // Categories
    Route::resource('menu/categories', CategoryController::class)
        ->except('show')
        ->names('categories')
        ->middlewareFor('index', 'can:categories.view')
        ->middlewareFor('create', 'can:categories.create')
        ->middlewareFor('store', 'can:categories.create')
        ->middlewareFor('edit', 'can:categories.edit')
        ->middlewareFor('update', 'can:categories.edit')
        ->middlewareFor('destroy', 'can:categories.delete');

    // Menu items
    Route::resource('menu', MenuItemController::class)
        ->except('show')
        ->parameters(['menu' => 'menu_item'])
        ->names('menu-items')
        ->middlewareFor('index', 'can:menu_items.view')
        ->middlewareFor('create', 'can:menu_items.create')
        ->middlewareFor('store', 'can:menu_items.create')
        ->middlewareFor('edit', 'can:menu_items.edit')
        ->middlewareFor('update', 'can:menu_items.edit')
        ->middlewareFor('destroy', 'can:menu_items.delete');
        
        Route::resource('inventory', IngredientController::class)
        ->except('show')
        ->parameters(['inventory' => 'ingredient'])
        ->names('ingredients')
        ->middlewareFor('index', 'can:ingredients.view')
        ->middlewareFor('create', 'can:ingredients.create')
        ->middlewareFor('store', 'can:ingredients.create')
        ->middlewareFor('edit', 'can:ingredients.edit')
        ->middlewareFor('update', 'can:ingredients.edit')
        ->middlewareFor('destroy', 'can:ingredients.delete');
      
         Route::resource('suppliers', SupplierController::class)
        ->except('show')
        ->middlewareFor('index', 'can:suppliers.view')
        ->middlewareFor('create', 'can:suppliers.create')
        ->middlewareFor('store', 'can:suppliers.create')
        ->middlewareFor('edit', 'can:suppliers.edit')
        ->middlewareFor('update', 'can:suppliers.edit')
        ->middlewareFor('destroy', 'can:suppliers.delete');

        Route::resource('customers', CustomerController::class)
            ->except('show')
            ->middlewareFor('index', 'can:customers.view')
            ->middlewareFor('create', 'can:customers.create')
            ->middlewareFor('store', 'can:customers.create')
            ->middlewareFor('edit', 'can:customers.edit')
            ->middlewareFor('update', 'can:customers.edit')
            ->middlewareFor('destroy', 'can:customers.delete');

      Route::resource('menu/recipes', RecipeController::class)
            ->except('show')
            ->parameters(['recipes' => 'menu_item'])
            ->names('recipes')
            ->middlewareFor('index', 'can:recipes.view')
            ->middlewareFor('create', 'can:recipes.create')
            ->middlewareFor('store', 'can:recipes.create')
            ->middlewareFor('edit', 'can:recipes.edit')
            ->middlewareFor('update', 'can:recipes.edit')
            ->middlewareFor('destroy', 'can:recipes.delete');
            
    // যে পেজগুলো এখনো বানাননি: আপাতত "coming soon" পেজ
    // ('menu' ও 'menu/categories' এখানে নেই, কারণ ওগুলো উপরে বানানো হয়ে গেছে)
    $pages = [
        'pos', 'orders', 'orders/returns',
        'tables', 'kitchen', 'tables/reservations',
        'inventory/low-stock', 'inventory/adjust', 'inventory/waste',
        'purchases', 'purchases/returns','users',
        'expenses', 'expenses/cash-register',
        'reports/sales', 'reports/purchases', 'reports/inventory', 'reports/profit-loss',
        'settings',
    ];

    foreach ($pages as $page) {
        Route::view($page, 'coming-soon', ['title' => Str::headline($page)]);
    }
});