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
        Schema::create('item_stock_transfers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('stock_transfer_id');
            $table->foreign('stock_transfer_id')->references('id')->on('stock_transfers')->onDelete('cascade');

            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');

            $table->unsignedBigInteger('from_warehouse_id');
            $table->foreign('from_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');

            $table->unsignedBigInteger('to_warehouse_id');
            $table->foreign('to_warehouse_id')->references('id')->on('warehouses')->onDelete('cascade');
            
            $table->unsignedBigInteger('from_item_transaction_id');
            $table->foreign('from_item_transaction_id')->references('id')->on('item_transactions')->onDelete('cascade');

            $table->unsignedBigInteger('to_item_transaction_id');
            $table->foreign('to_item_transaction_id')->references('id')->on('item_transactions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_stock_transfers');
    }
};
