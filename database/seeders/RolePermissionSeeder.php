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
            ];
        

        foreach ($categoryPermissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        $admin = Role::findOrCreate('admin', 'web');
        $admin->syncPermissions($categoryPermissions);
        $superAdmin = Role::findOrCreate('super-admin', 'web');
        $superAdmin->syncPermissions($categoryPermissions);

        $staff = Role::findOrCreate('staff', 'web');
        $staff->syncPermissions(['categories.view']);
        $staff->syncPermissions(['categories.view', 'menu_items.view']);
        $staff->syncPermissions(['categories.view', 'menu_items.view', 'ingredients.view']);

        User::query()->orderBy('id')->get()->each(function (User $user, int $index): void {
            $user->syncRoles($index === 0 ? 'super-admin' : 'staff');
        });
    }
}
