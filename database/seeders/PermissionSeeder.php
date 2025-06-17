<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Define permissions for each model
        $models = ['sale', 'product', 'user', 'ProductVariant', 'color', 'size', 'saleItem', 'Role', 'Permission'];
         //   $models  = [ 'SaleItem'];
        $actions = ['view', 'create', 'edit', 'delete'];

        foreach ($models as $model) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "{$action} {$model}"]);
            }
        }

        // Assign all permissions to Admin role
            $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions(Permission::all());

        // Example: Assign only sale permissions to Sales role

    }
}