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
            $table->id();

            // Booking information
            $table->string('booking_reference')->unique();
            $table->date('booking_date');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled'])->default('pending');

            // Customer information
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Transport details
            $table->foreignId('vehicle_id')->constrained()->onDelete('cascade');
            $table->foreignId('route_id')->constrained()->onDelete('cascade');

            // Trip details
            $table->string('pickup_location');
            $table->string('dropoff_location');
            $table->dateTime('pickup_time');
            $table->dateTime('estimated_dropoff_time');
            $table->integer('passenger_count')->default(1);
            $table->text('special_requests')->nullable();

            // Pricing
            $table->decimal('total_amount', 10, 2);
            $table->string('currency')->default('NG');

            // Payment information
            $table->enum('payment_status', ['pending', 'paid', 'failed', 'refunded'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('transaction_id')->nullable();

            // Driver assignment (if applicable)
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');

            $table->timestamps();
            $table->softDeletes();
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
