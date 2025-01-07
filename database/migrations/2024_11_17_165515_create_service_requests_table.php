<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('household_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null'); // Linked household user

            $table->foreignId('company_id')
                ->nullable()
                ->constrained('companies')
                ->onDelete('set null'); 
                $table->foreignId('accepted_company_id')
                ->nullable()
                ->constrained('companies')
                ->onDelete('set null'); 
            $table->foreignId('company_user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null'); // Assigned company user

            $table->string('client_number')->nullable();

            $table->string('status')->default('pending'); // Request status
            $table->string('waste_type')->nullable(); // Type of waste
            $table->string('quantity')->nullable(); // Qty of waste

            $table->timestamp('preferred_date')->nullable(); // Preferred service date
            $table->time('preferred_time')->nullable(); // Preferred service time
            
            $table->string('address')->nullable(); // Service address
            $table->timestamp('scheduled_date')->nullable(); // Scheduled service date
            $table->time('scheduled_time')->nullable(); // Scheduled service time

            $table->decimal('estimated_cost', 12, 2)->nullable(); // Estimated cost
            $table->text('description')->nullable(); // Additional notes or description

            $table->decimal('payment_amount', 12, 2)->nullable(); // Payment amount
            $table->string('payment_status')->nullable(); // Status of payment

            $table->integer('admin_commission_percentage')->nullable(); // Admin commission percentage
            $table->decimal('admin_commission_amount', 12, 2)->default(0.00); // Admin commission amount
            $table->decimal('company_payout_amount', 12, 2)->default(0.00); // Payout to the company
            $table->decimal('final_amount', 12, 2)->default(0.00); // Final amount payable

            $table->timestamp('completed_at')->nullable(); // Completion timestamp
            $table->text('completion_notes')->nullable(); // Notes upon completion
            $table->string('completion_photos')->nullable(); // URLs or paths of completion photos

            $table->timestamp('payment_received_at')->nullable(); // Payment received timestamp
            $table->timestamp('commission_paid_at')->nullable(); // Commission paid timestamp

            $table->timestamps(); // Created and updated timestamps
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
