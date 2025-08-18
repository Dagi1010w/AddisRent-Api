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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();

            // A message from a guest (public contact form) will have a NULL sender_id.
            $table->foreignId('sender_id')->nullable()->constrained('users')->onDelete('cascade');
            
            $table->foreignId('recipient_id')->constrained('users')->onDelete('cascade');

            $table->text('body');
            
            // This optional field links a message thread back to a specific booking.
            $table->foreignId('related_to_booking_id')->nullable()->constrained('booking_requests')->onDelete('set null');

            // To track if a message has been seen by the recipient.
            $table->timestamp('read_at')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
