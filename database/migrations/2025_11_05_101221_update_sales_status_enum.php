<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // First, expand the enum to include all old and new values
            DB::statement("ALTER TABLE sales MODIFY COLUMN status ENUM('pending', 'paid', 'cancelled', 'draft', 'awaiting_payment', 'completed') DEFAULT 'draft'");
            
            // Then update existing records to use new statuses
            DB::statement("UPDATE sales SET status = 'draft' WHERE status = 'pending' AND total = 0");
            DB::statement("UPDATE sales SET status = 'awaiting_payment' WHERE status = 'pending' AND total > 0");
            DB::statement("UPDATE sales SET status = 'completed' WHERE status = 'paid'");
            
            // Finally, remove the old values from enum
            DB::statement("ALTER TABLE sales MODIFY COLUMN status ENUM('draft', 'awaiting_payment', 'completed', 'cancelled') DEFAULT 'draft'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            // First, expand the enum to include all old and new values
            DB::statement("ALTER TABLE sales MODIFY COLUMN status ENUM('pending', 'paid', 'cancelled', 'draft', 'awaiting_payment', 'completed') DEFAULT 'pending'");
            
            // Then revert to original statuses
            DB::statement("UPDATE sales SET status = 'pending' WHERE status IN ('draft', 'awaiting_payment')");
            DB::statement("UPDATE sales SET status = 'paid' WHERE status = 'completed'");
            
            // Finally, remove the new values from enum
            DB::statement("ALTER TABLE sales MODIFY COLUMN status ENUM('pending', 'paid', 'cancelled') DEFAULT 'pending'");
        });
    }
};
