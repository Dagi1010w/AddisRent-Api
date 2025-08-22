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
        $table->string('location_region')->nullable()->change();
        $table->string('location_city')->nullable()->change();
        $table->string('location_subcity')->nullable()->change();
        $table->string('location_specific_area')->nullable()->change();
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('location_region')->nullable(false)->change();
        $table->string('location_city')->nullable(false)->change();
        $table->string('location_subcity')->nullable(false)->change();
        $table->string('location_specific_area')->nullable(false)->change();
    });
}

};
