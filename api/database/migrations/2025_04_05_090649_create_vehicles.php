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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();

            // Vehicle identification
            $table->string('registration_number')->unique();
            $table->string('make');
            $table->string('model');
            $table->integer('year');
            $table->string('color');

            // Vehicle specifications
            $table->enum('type', ['sedan', 'suv', 'van', 'bus', 'luxury', 'eco']);
            $table->integer('seat_capacity');
            $table->decimal('luggage_capacity', 5, 2)->comment('In cubic feet');

            // Operational details
            //$table->foreignId('current_driver_id')->nullable()->constrained('drivers')->onDelete('set null');

            $table->enum('status', ['available', 'in_maintenance', 'unavailable']);
            $table->string('current_location')->nullable();

            // Insurance and compliance
            $table->string('insurance_provider');
            $table->date('insurance_expiry');
            $table->date('next_service_date');

            // Pricing
            $table->decimal('base_fare', 10, 2);
            $table->decimal('per_km_rate', 10, 2);
            $table->decimal('per_minute_rate', 10, 2)->nullable();

            // Amenities/Features
            $table->boolean('has_ac')->default(true);
            $table->boolean('has_wifi')->default(false);
            $table->boolean('is_wheelchair_accessible')->default(false);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
