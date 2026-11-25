<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $categoryPermissions = [
            'categories.view',
            'categories.create',
            'categories.edit',
            'categories.delete',
            'menu_items.view',
            'menu_items.create',
            'menu_items.edit',
            'menu_items.delete',
            'ingredients.view',
            'ingredients.create',
            'ingredients.edit',
            'ingredients.delete',
            'suppliers.view',
            'suppliers.create',
            'suppliers.edit',
            'suppliers.delete',
            'customers.view',
            'customers.create',
            'customers.edit',
            'customers.delete',
            'recipes.view',
            'recipes.create',
            'recipes.edit',
            'recipes.delete',
            'purchases.view',
            'purchases.create',
            'purchases.edit',
            'purchases.delete',
            'tables.view',
            'tables.create',
            'tables.edit',
            'tables.delete',
            'expenses.view',
            'expenses.create',
            'expenses.edit',
            'expenses.delete',
            'orders.view',
            'orders.create',
            'orders.edit',
            'orders.update_status',
            'orders.delete',
            'reservations.view',
            'reservations.create',
            'reservations.edit',
            'reservations.delete',
            'settings.view',
            'settings.edit',
            ];
        

        foreach ($categoryPermissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $admin = Role::findOrCreate('admin', 'web');
        $admin->syncPermissions($categoryPermissions);
        $superAdmin = Role::findOrCreate('super-admin', 'web');
        $superAdmin->syncPermissions($categoryPermissions);
    
        $staff = Role::findOrCreate('staff', 'web');
        $staff->syncPermissions([
            'categories.view',
            'menu_items.view',
            'ingredients.view',
            'suppliers.view',
            'customers.view',
            'recipes.view',
            'purchases.view',
            'tables.view',
            'expenses.view',
            'orders.view',
            'orders.create',
            'orders.update_status',
            'reservations.view',
            'reservations.create',
            'reservations.edit',
        ]);

        User::query()->orderBy('id')->get()->each(function (User $user, int $index): void {
            $user->syncRoles($index === 0 ? 'super-admin' : 'staff');
        });
    }
}
