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
        Schema::create('booking_requests', function (Blueprint $table) {
            $table->id();

            // Who sent the request (must be a registered user).
            $table->foreignId('seeker_id')->constrained('users')->onDelete('cascade');

            // Which property is the request for.
            $table->foreignId('property_id')->constrained()->onDelete('cascade');

            $table->text('message');
            $table->enum('status', ['pending', 'approved', 'rejected', 'cancelled'])->default('pending');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_requests');
    }
};
