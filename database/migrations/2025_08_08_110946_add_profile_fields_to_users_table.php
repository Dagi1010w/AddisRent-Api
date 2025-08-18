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
       Schema::table('users', function (Blueprint $table) {
        $table->string('phone_number')->nullable()->after('password');
        $table->string('location_region');
        $table->string('location_city');
        $table->string('location_subcity');
        $table->string('location_specific_area')->nullable();
        $table->enum('type', ['person', 'company']);
        $table->enum('preference', ['tenant', 'buyer', 'seller', 'lessor']);
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
