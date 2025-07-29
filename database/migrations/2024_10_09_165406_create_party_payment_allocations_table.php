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
        Schema::create('party_payment_allocations', function (Blueprint $table) {
            $table->id();
            
            //Record full payment id
            $table->unsignedBigInteger('party_payment_id');
            $table->foreign('party_payment_id')->references('id')->on('party_payments')->onDelete('cascade');

            //Record adjusted invoice or bills payment id
            $table->unsignedBigInteger('payment_transaction_id');
            $table->foreign('payment_transaction_id')->references('id')->on('payment_transactions')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('party_payment_allocations');
    }
};
