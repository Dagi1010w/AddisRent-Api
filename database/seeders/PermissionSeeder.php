<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Clear cache to avoid stale permission errors
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        Permission::create(['name' => 'edit properties']);
        Permission::create(['name' => 'delete properties']);
        Permission::create(['name' => 'create properties']);
        Permission::create(['name' => 'update properties']);
        Permission::create(['name' => 'view properties']);
        Permission::create(['name' => 'view users']);
        Permission::create(['name' => 'manage users']);

        // Create roles and assign existing permissions
        $adminRole = Role::create(['name' => 'admin']);
        $listerRole = Role::create(['name' => 'lister']);
        $seekerRole = Role::create(['name' => 'seeker']);

        $adminRole->givePermissionTo(Permission::all());
        $listerRole->givePermissionTo(['create properties', 'edit properties', 'delete properties', 'view properties']);
        $seekerRole->givePermissionTo(['view properties']);

        // Optionally: create a demo admin user and assign the admin role
        $user = User::where('email', 'admin@example.com')->first();
        if (!$user) {
            $user = User::factory()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
                'phone_number' => '1234567890',
                'location_region' => 'Addis Ababa',
                'location_city' => 'Addis Ababa',
                'location_subcity' => 'Bole',
                'location_specific_area' => 'Teklehaymanot',
                'preference' => null,
                'type' => null,
            ]);
        }
        $user->assignRole('admin');
    }
}
