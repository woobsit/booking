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
        Schema::create('routes', function (Blueprint $table) {
            $table->id();

            // Route identification
            $table->string('route_code')->unique();
            $table->string('name');
            $table->text('description')->nullable();

            // Location details
            $table->string('origin');
            $table->string('destination');
            $table->json('waypoints')->nullable()->comment('Intermediate stops in JSON format');

            // Route specifications
            $table->decimal('distance', 8, 2)->comment('Distance in kilometers');
            $table->decimal('estimated_duration', 8, 2)->comment('Duration in minutes');
            $table->enum('type', ['city', 'intercity', 'airport', 'special']);

            // Pricing
            $table->decimal('base_price', 10, 2);
            $table->decimal('price_per_km', 10, 2);
            $table->decimal('peak_time_surcharge', 5, 2)->default(0);
            $table->decimal('weekend_surcharge', 5, 2)->default(0);

            // Operational details
            $table->time('first_departure_time');
            $table->time('last_departure_time');
            $table->integer('frequency_minutes')->nullable()->comment('For regular routes');

            // Status
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
