<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Database\Seeders\PermissionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

         Role::firstOrCreate(['name' => 'Admin' , 'guard_name' => 'web']);
         Role::firstOrCreate(['name' => 'Sales', 'guard_name' => 'web']);
        $admin = User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@uminventory.com',
            'password' => Hash::make('Password123!'),
        ]);

        $admin->assignRole('Admin');
        $this->call(PermissionSeeder::class);
    }
}
