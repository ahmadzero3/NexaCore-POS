<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Payment Transactions
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->index(['payment_type_id', 'transaction_date'], 'idx_payment_type_date');
            $table->index(['transaction_type', 'transaction_id'], 'idx_payment_transaction_type_id');
        });

        // Account Transactions
        Schema::table('account_transactions', function (Blueprint $table) {
            $table->index(['account_id', 'transaction_date'], 'idx_account_date');
        });
    }

    public function down(): void
    {
        Schema::table('payment_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_payment_type_date');
            $table->dropIndex('idx_payment_transaction_type_id');
        });

        Schema::table('account_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_account_date');
        });
    }
};