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
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('route_id')->constrained();
            $table->foreignId('vehicle_id')->constrained();
            $table->foreignId('driver_id')->constrained();
            $table->dateTime('departure_time');
            $table->dateTime('estimated_arrival_time');
            $table->dateTime('actual_arrival_time')->nullable();
            $table->integer('available_seats');
            $table->decimal('price', 8, 2);
            $table->enum('status', ['scheduled', 'boarding', 'departed', 'completed', 'cancelled']);
            $table->text('notes')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trips');
    }
};
