<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->foreignId('trip_id')->constrained(); // Instead of individual fields
            $table->integer('seat_count'); // How many seats booked
            $table->dropColumn(['pickup_location', 'dropoff_location', 'pickup_time']); // Now comes from trip
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
