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
        Schema::create('item_serial_transactions', function (Blueprint $table) {
            $table->id();

            $table->string('unique_code');
            
            $table->unsignedBigInteger('item_transaction_id');
            $table->foreign('item_transaction_id')->references('id')->on('item_transactions')->onDelete('cascade');

            $table->unsignedBigInteger('item_serial_master_id')->nullable();
            $table->foreign('item_serial_master_id')->references('id')->on('item_serial_masters');
            
            $table->unsignedBigInteger('warehouse_id');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');

            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_serial_transactions');
    }
};
