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
        Schema::create('mpesa_payments', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique(); // TK4B99917A
            $table->string('customer_name'); // REGINA, ASHLEY, etc.
            $table->string('phone_number'); // 2547****570
            $table->decimal('amount', 10, 2); // 60.00
            $table->timestamp('transaction_time'); // 2025-11-04 10:57:38
            $table->enum('status', ['pending', 'linked', 'refunded'])->default('pending');
            $table->string('paybill_account')->nullable(); // Account number if used
            $table->text('raw_mpesa_data')->nullable(); // Store original M-Pesa callback data
            
            // Link to sale when payment is used
            $table->foreignId('sale_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamp('linked_at')->nullable();
            $table->foreignId('linked_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamps();
            
            $table->index(['status', 'transaction_time']);
            $table->index(['phone_number', 'amount']);
            $table->index('transaction_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mpesa_payments');
    }
};