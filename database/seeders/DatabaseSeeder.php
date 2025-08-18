<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        $this->call([
            AutoUserAndPropertySeeder::class,
        ]);

        $this->call([
            PermissionSeeder::class,
        ]);
    
            $user = User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'phone_number' => '0912345678',
                'location_region' => 'Addis Ababa',
                'location_city' => 'Addis Ababa',
                'location_subcity' => 'Bole',
                'location_specific_area' => 'Airport',
                'type' => 'person',
            ])->assignRole('lister');
    }
}
