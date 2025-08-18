<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        
        Schema::create('properties', function (Blueprint $table) {
            $table->id();

            // Foreign key to users table
            $table->foreignId('lister_id')->constrained('users')->onDelete('cascade');

            $table->string('title');
            $table->text('description');

            $table->enum('listing_type', ['sale', 'rent']);
            $table->enum('property_type', ['apartment', 'house', 'office', 'land', 'villa', 'shop', 'condo', 'studio', 'building', 'warehouse','guesthouse', 'other']);
            $table->enum('status', ['active', 'inactive', 'pending', 'booked']);

            $table->unsignedBigInteger('price'); // stored in cents

            $table->enum('currency', ['ETB', 'USD' , 'GBP'])->default('ETB'); // add more currencies if needed

            $table->unsignedInteger('area'); // in square meters

            $table->unsignedInteger('bedrooms')->nullable();
            $table->unsignedInteger('bathrooms')->nullable();

            $table->boolean('is_furnished');

            $table->json('amenities')->nullable();

            $table->string('address_region');
            $table->string('address_city');
            $table->string('address_subcity');
            $table->string('address_specific_area');

            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->boolean('is_featured')->default(false);

            $table->timestamps();
        });
    
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
