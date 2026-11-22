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
            ];
        

        foreach ($categoryPermissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $admin = Role::findOrCreate('admin', 'web');
        $admin->syncPermissions($categoryPermissions);
        $superAdmin = Role::findOrCreate('super-admin', 'web');
        $superAdmin->syncPermissions($categoryPermissions);
        $superAdmin->syncPermissions($supplierPermissions);
        $superAdmin->syncPermissions($customerPermissions);
        $superAdmin->syncPermissions($recipePermissions);
        $superAdmin->syncPermissions($purchasePermissions);
        $superAdmin->syncPermissions($tablePermissions);
        $superAdmin->syncPermissions($expensePermissions);

        $staff = Role::findOrCreate('staff', 'web');
        $staff->syncPermissions(['categories.view']);
        $staff->syncPermissions(['categories.view', 'menu_items.view']);
        $staff->syncPermissions(['categories.view', 'menu_items.view', 'ingredients.view']);
        $staff->syncPermissions(['suppliers.view',]);
        $staff->syncPermissions(['customers.view',]);
        $staff->syncPermissions(['recipes.view',]);
        $staff->syncPermissions(['purchases.view',]);
        $staff->syncPermissions(['tables.view',]);
        $staff->syncPermissions(['expenses.view',]);

        User::query()->orderBy('id')->get()->each(function (User $user, int $index): void {
            $user->syncRoles($index === 0 ? 'super-admin' : 'staff');
        });
    }
}
