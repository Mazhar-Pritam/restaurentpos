<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', fn () => redirect('/dashboard'));

// আপনার ড্যাশবোর্ড ভিউ ফাইলের নাম অনুযায়ী বদলান
// (resources/views/dashboard.blade.php হলে 'dashboard')
Route::view('/dashboard', 'dashboard');

// যে পেজগুলো এখনো বানাননি, সেগুলোর জন্য আপাতত "coming soon" পেজ
$pages = [
    'pos', 'orders', 'orders/returns',
    'tables', 'kitchen', 'tables/reservations',
    'menu', 'menu/categories', 'menu/recipes',
    'inventory', 'inventory/low-stock', 'inventory/adjust', 'inventory/waste',
    'purchases', 'purchases/returns', 'suppliers',
    'customers', 'users',
    'expenses', 'expenses/cash-register',
    'reports/sales', 'reports/purchases', 'reports/inventory', 'reports/profit-loss',
    'settings',
];

foreach ($pages as $page) {
    Route::view($page, 'coming-soon', ['title' => Str::headline($page)]);
}