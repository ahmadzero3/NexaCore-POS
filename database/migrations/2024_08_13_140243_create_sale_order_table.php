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
        Schema::create('sale_orders', function (Blueprint $table) {
            $table->id();
            $table->date('order_date');
            $table->date('due_date')->nullable();
            $table->string('prefix_code')->nullable();
            $table->string('count_id')->nullable();
            $table->string('order_code')->nullable();

            // $table->unsignedBigInteger('warehouse_id');
            // $table->foreign('warehouse_id')->references('id')->on('warehouses'); 

            $table->unsignedBigInteger('party_id');
            $table->foreign('party_id')->references('id')->on('parties'); 

            /**
             * State of supply
             * Only if GST enabled
             * */
            $table->unsignedBigInteger('state_id')->nullable();
            $table->foreign('state_id')->references('id')->on('states');

            $table->text('note')->nullable();

            $table->decimal('round_off', 20, 4)->default(0);
            $table->decimal('grand_total', 20, 4)->default(0);
            $table->decimal('paid_amount', 20, 4)->default(0);

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
        Schema::dropIfExists('sale_order');
    }
};
