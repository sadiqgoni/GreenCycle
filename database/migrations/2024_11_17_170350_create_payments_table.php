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
        Schema::create('payments', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->foreignId('service_request_id')->constrained('service_requests')->onDelete('cascade'); // Associated waste request
            $table->decimal('amount', 12, 2); // Total payment amount
            $table->enum('status', ['pending', 'confirmed', 'failed'])->default('pending'); // Payment status
            $table->enum('payment_method', ['credit_card', 'bank_transfer', ])->nullable(); // Payment method
            $table->decimal('commission_amount', 12, 2)->default(0.00); // Admin commission
            $table->decimal('company_amount', 12, 2)->default(0.00); // Amount payable to the company
            $table->timestamp('paid_at')->nullable(); // Payment completion timestamp
            $table->timestamps(); // created_at & updated_at
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
