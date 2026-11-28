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
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\RestaurantTableController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\LowStockController;

// লগইন, রেজিস্টার, পাসওয়ার্ড রিসেট, লগআউট: laravel/ui এর রুট
// (লগআউট রুটও এর ভেতরেই আছে, আলাদা করে লাগবে না)
Auth::routes();

    Route::get('/', fn () => redirect('/dashboard'));
    Route::pattern('purchase', '[0-9]+');
    Route::pattern('order', '[0-9]+');

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

         Route::resource('purchases', PurchaseController::class)
            ->middlewareFor(['index', 'show'], 'can:purchases.view')
            ->middlewareFor(['create', 'store'], 'can:purchases.create')
            ->middlewareFor(['edit', 'update'], 'can:purchases.edit')
            ->middlewareFor('destroy', 'can:purchases.delete');   

         Route::resource('tables', RestaurantTableController::class)
            ->except('show')
            ->parameters(['tables' => 'table'])
            ->middlewareFor('index', 'can:tables.view')
            ->middlewareFor('create', 'can:tables.create')
            ->middlewareFor('store', 'can:tables.create')
            ->middlewareFor('edit', 'can:tables.edit')
            ->middlewareFor('update', 'can:tables.edit')
            ->middlewareFor('destroy', 'can:tables.delete');

         Route::get('pos', [OrderController::class, 'create'])->name('pos')->middleware('can:orders.create');
          Route::get('orders', [OrderController::class, 'index'])->name('orders.index')->middleware('can:orders.view');
          Route::post('orders', [OrderController::class, 'store'])->name('orders.store')->middleware('can:orders.create');
          Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show')->middleware('can:orders.view');
          Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status')->middleware('can:orders.update_status');
          Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy')->middleware('can:orders.delete');
          Route::get('kitchen', [OrderController::class, 'kitchen'])->name('kitchen')->middleware('can:orders.view');

          Route::resource('reservations', ReservationController::class)
            ->except('show')
            ->middlewareFor('index', 'can:reservations.view')
            ->middlewareFor('create', 'can:reservations.create')
            ->middlewareFor('store', 'can:reservations.create')
            ->middlewareFor('edit', 'can:reservations.edit')
            ->middlewareFor('update', 'can:reservations.edit')
            ->middlewareFor('destroy', 'can:reservations.delete');

          Route::resource('reservations', ReservationController::class)
            ->except(['show'])
            ->middlewareFor(['index'], 'can:reservations.view')
            ->middlewareFor(['create', 'store'], 'can:reservations.create')
            ->middlewareFor(['edit', 'update'], 'can:reservations.edit')
            ->middlewareFor(['destroy'], 'can:reservations.delete');


        Route::patch('reservations/{reservation}/status', [ReservationController::class, 'updateStatus'])
            ->name('reservations.status')
            ->middleware('can:reservations.update_status');

            
        Route::get('stock-adjustments', [StockAdjustmentController::class, 'index'])
            ->name('stock-adjustments.index')->middleware('can:stock_adjustments.view');
        Route::get('stock-adjustments/create', [StockAdjustmentController::class, 'create'])
            ->name('stock-adjustments.create')->middleware('can:stock_adjustments.create');
        Route::post('stock-adjustments', [StockAdjustmentController::class, 'store'])
            ->name('stock-adjustments.store')->middleware('can:stock_adjustments.create');

       Route::get('inventory/low-stock', [LowStockController::class, 'index'])
                ->name('low-stock')
                ->middleware('can:ingredients.view');


          Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit')->middleware('can:settings.view');
          Route::put('settings', [SettingController::class, 'update'])->name('settings.update')->middleware('can:settings.edit');
            
    // যে পেজগুলো এখনো বানাননি: আপাতত "coming soon" পেজ
    // ('menu' ও 'menu/categories' এখানে নেই, কারণ ওগুলো উপরে বানানো হয়ে গেছে)
        $pages = [
            'orders/returns',
            'purchases/returns', 'users',
            'expenses', 'expenses/cash-register',
            'reports/sales', 'reports/purchases', 'reports/inventory', 'reports/profit-loss',
        ];

    foreach ($pages as $page) {
        Route::view($page, 'coming-soon', ['title' => Str::headline($page)]);
    }
});