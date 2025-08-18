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
        Schema::create('favorites', function (Blueprint $table) {
            // Foreign key to the users table. If the user is deleted, this record is also deleted.
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Foreign key to the properties table. If the property is deleted, this record is also deleted.
            $table->foreignId('property_id')->constrained()->onDelete('cascade');

            // This is the primary key. It's a combination of both foreign keys.
            // This guarantees that a user can only favorite a specific property ONCE.
            $table->primary(['user_id', 'property_id']);
            
            // This automatically adds a `created_at` timestamp so we know when it was favorited.
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
