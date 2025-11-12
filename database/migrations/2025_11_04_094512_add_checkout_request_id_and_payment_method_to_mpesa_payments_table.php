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
        Schema::table('mpesa_payments', function (Blueprint $table) {
            // Add checkout_request_id for STK Push and Lipa na M-Pesa tracking
            $table->string('checkout_request_id')->nullable()->after('raw_mpesa_data');
            
            // Add payment_method to distinguish between different payment types
            $table->enum('payment_method', ['manual', 'stk_push', 'lipa_na_mpesa', 'c2b'])->default('manual')->after('checkout_request_id');
            
            // Make some existing fields nullable for Lipa na M-Pesa
            $table->string('transaction_code')->nullable()->change();
            $table->string('customer_name')->nullable()->change();
            $table->string('phone_number')->nullable()->change();
            $table->timestamp('transaction_time')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mpesa_payments', function (Blueprint $table) {
            $table->dropColumn(['checkout_request_id', 'payment_method']);
            
            // Revert nullable changes (Note: data may be lost if nulls exist)
            $table->string('transaction_code')->nullable(false)->change();
            $table->string('customer_name')->nullable(false)->change();
            $table->string('phone_number')->nullable(false)->change();
            $table->timestamp('transaction_time')->nullable(false)->change();
        });
    }
};
