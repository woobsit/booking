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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Payment reference
            $table->string('payment_reference')->unique();
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Payment details
            $table->decimal('amount', 12, 2);

            $table->decimal('service_fee', 12, 2)->default(0);
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('total_amount', 12, 2);
            $table->string('currency', 3)->default('NG');

            // Payment method
            $table->enum('payment_method', ['credit_card', 'debit_card', 'bank_transfer', 'mobile_money', 'cash', 'wallet']);
            $table->string('payment_gateway')->nullable();
            $table->string('transaction_id')->nullable();
            $table->json('gateway_response')->nullable()->comment('Raw response from payment gateway');

            // Status and timing
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded', 'partially_refunded']);
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('refunded_at')->nullable();

            // Metadata
            $table->text('failure_reason')->nullable();
            $table->text('notes')->nullable();

            // Billing information
            $table->string('billing_name')->nullable();
            $table->string('billing_email')->nullable();
            $table->string('billing_phone')->nullable();
            $table->text('billing_address')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
