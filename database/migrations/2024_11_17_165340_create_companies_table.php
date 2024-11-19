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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('cascade'); // Reference to users table
            $table->string('company_name')->nullable(); // Name of the company
            $table->string('ceo_name')->nullable(); // Name of the CEO
            $table->string('ceo_email')->nullable(); // Email of the CEO

            $table->string('contact_info')->nullable(); // Contact information
            $table->string('description')->nullable(); // Description of the company
            $table->string('image')->nullable(); 
            $table->string('registration_number')->nullable(); // Registration number
            $table->string('tax_number')->nullable(); // Tax number
            $table->enum('availability_status', ['open', 'closed'])->default('closed'); // Open/closed status
            $table->enum('verification_status', ['pending', 'verified', 'rejected'])->default('pending'); // Verification status
            $table->text('service_radius')->nullable(); // Service radius in kilometers
            $table->decimal('commission_rate', 5, 2)->default(0.00); // Commission percentage
            $table->text('bank_details')->nullable(); // Bank account details
            $table->text('rejection_note')->nullable(); 
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
