<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use App\Models\User;
use App\Models\PersonProfile;
use App\Models\CompanyProfile;
use App\Models\Property;

class AutoUserAndPropertySeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Create 10 individual users with profiles
        for ($i = 0; $i < 10; $i++) {
            $user = User::create([
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password'),
                'phone_number' => '0912345678',
                'location_region' => $faker->randomElement(['Addis Ababa', 'Amhara', 'Oromia', 'Tigray']),
            'location_city' => $faker->randomElement(['Addis Ababa', 'Adama', 'Diredawa','Arba Minch', 'Bishoftu', 'Jimma', 'Gambela', 'Mekelle', 'Dessie', 'Gonder', 'Goba', 'Hawassa', 'Gulele', 'Moyale']),
            'location_subcity' => $faker->randomElement(['Bole', 'Yeka', 'Kirkos', 'Arada', 'Lideta']),
            'location_specific_area' => $faker->streetName(),
                'type' => 'person',

                'preference' => $faker->randomElement(['tenant', 'buyer', 'seller', 'lessor']),
            ]);

            PersonProfile::create([
                'user_id' => $user->id,
                'gender' => $faker->randomElement(['male', 'female']),
            ]);
        }

        // Create 5 company users with profiles
        for ($i = 0; $i < 5; $i++) {
            $user = User::create([
                'name' => $faker->company,
                'email' => $faker->unique()->companyEmail,
                'password' => Hash::make('password'),
                'phone_number' => '0912345678',
                'location_region' => $faker->state(),
                'location_city' => $faker->city(),
                'location_subcity' => $faker->city(),
                'location_specific_area' => $faker->streetName(),
                'type' => 'company',
                'preference' => $faker->randomElement(['tenant', 'buyer', 'seller', 'lessor']),
            ]);

            CompanyProfile::create([
                'user_id' => $user->id,
                'google_map_link' => $faker->url(),
                'business_license_path' => 'licenses/' . $faker->uuid() . '.pdf',
            ]);

            // Each company lists 3 properties
            for ($j = 0; $j < 3; $j++) {
                Property::create([
                    'lister_id' => $user->id,
                    'title' => $faker->sentence(4),
                    'description' => $faker->paragraph(3),
                    'listing_type' => $faker->randomElement(['sale', 'rent']),
                    'property_type' => $faker->randomElement([
                        'apartment', 'house', 'office', 'land', 'villa',
                        'shop', 'condo', 'studio', 'building', 'warehouse','guesthouse', 'other'
                    ]),
                    'status' => $faker->randomElement(['active', 'inactive', 'pending', 'booked']),
                    'price' => $faker->numberBetween(5000, 50000000),
                    'currency' => $faker->randomElement(['ETB', 'USD', 'GBP']),
                    'area' => $faker->numberBetween(50, 500),
                    'bedrooms' => $faker->numberBetween(1, 5),
                    'bathrooms' => $faker->numberBetween(1, 3),
                    'is_furnished' => $faker->boolean,
                    'amenities' => json_encode($faker->randomElements([
                        'parking', 'elevator', 'security', 'balcony', 'gym'
                    ], 2)),
                    'address_region' => $faker->state(),
                    'address_city' => $faker->city(),
                    'address_subcity' => $faker->city(),
                    'address_specific_area' => $faker->streetName(),
                    'latitude' => $faker->latitude(),
                    'longitude' => $faker->longitude(),
                    'is_featured' => $faker->boolean(30), // 30% chance
                ]);
            }
        }
    }
}