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
        Schema::create('item_transactions', function (Blueprint $table) {
            $table->id();

            //auto creates transaction_id & transaction_type
            $table->morphs('transaction');

            $table->string('unique_code');

            $table->date('transaction_date');
            $table->unsignedBigInteger('warehouse_id');
            $table->foreign('warehouse_id')->references('id')->on('warehouses');
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items');
            $table->text('description')->nullable();

            //general, batch, serial
            $table->string('tracking_type');
            //$table->string('item_location')->nullable();

            $table->unsignedBigInteger('unit_id');
            $table->foreign('unit_id')->references('id')->on('units');

            $table->decimal('mrp', 20, 4)->default(0);

            $table->decimal('quantity', 20, 4)->default(0);

            //Each Qty Price: with or without tax
            $table->decimal('unit_price', 20, 4)->default(0);
            //$table->boolean('is_price_with_tax');//with or without tax

            //Discount
            $table->decimal('discount', 20, 4)->default(0);
            $table->decimal('discount_amount', 20, 4)->default(0);
            $table->string('discount_type')->default('percentage');//percentage or fixed

            //Tax
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->foreign('tax_id')->references('id')->on('taxes');
            $table->string('tax_type')->default('inclusive');
            $table->decimal('tax_amount', 20, 4)->default(0);

            $table->decimal('total', 20, 4)->default(0)->comment('Including (Discount) - (with or without Tax) ');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users'); 
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_transactions');
    }
};
