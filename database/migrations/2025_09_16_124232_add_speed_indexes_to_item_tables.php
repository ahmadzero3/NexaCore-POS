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
        // Item transactions
        Schema::table('item_transactions', function (Blueprint $table) {
            $table->index(['item_id', 'warehouse_id'], 'idx_item_transactions_item_warehouse');
            $table->index('transaction_date', 'idx_item_transactions_date');
            $table->index('unique_code', 'idx_item_transactions_code');
        });

        // Batch transactions
        Schema::table('item_batch_transactions', function (Blueprint $table) {
            $table->index(['item_id', 'warehouse_id'], 'idx_batch_item_warehouse');
            $table->index('item_batch_master_id', 'idx_batch_master');
        });

        // Serial transactions
        Schema::table('item_serial_transactions', function (Blueprint $table) {
            $table->index(['item_id', 'warehouse_id'], 'idx_serial_item_warehouse');
            $table->index('item_serial_master_id', 'idx_serial_master');
        });

        // General quantities
        Schema::table('item_general_quantities', function (Blueprint $table) {
            $table->index(['item_id', 'warehouse_id'], 'idx_general_item_warehouse');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_item_transactions_item_warehouse');
            $table->dropIndex('idx_item_transactions_date');
            $table->dropIndex('idx_item_transactions_code');
        });

        Schema::table('item_batch_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_batch_item_warehouse');
            $table->dropIndex('idx_batch_master');
        });

        Schema::table('item_serial_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_serial_item_warehouse');
            $table->dropIndex('idx_serial_master');
        });

        Schema::table('item_general_quantities', function (Blueprint $table) {
            $table->dropIndex('idx_general_item_warehouse');
        });
    }
};