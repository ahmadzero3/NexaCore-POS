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
        Schema::create('expense_items', function (Blueprint $table) {
             $table->id();
            $table->unsignedBigInteger('expense_id');
            $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('cascade');

            $table->unsignedBigInteger('expense_item_master_id');
            $table->foreign('expense_item_master_id')->references('id')->on('expense_item_master')->onDelete('cascade');

            $table->text('description')->nullable();

            $table->decimal('unit_price', 20, 4)->default(0)->comment('original price(without tax)');
            $table->decimal('quantity', 20, 4)->default(0);
            //$table->decimal('total_price', 20, 4)->default(0)->comment('(original price * quantity)');;

            /*Tax*/
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->foreign('tax_id')->references('id')->on('taxes');
            $table->string('tax_type')->default('inclusive');
            $table->decimal('tax_amount', 20, 4)->default(0);

            /*Discount*/
            $table->decimal('discount', 20, 4)->default(0);
            $table->string('discount_type')->nullable()->comment('fixed or percentage');
            $table->decimal('discount_amount', 20, 4)->default(0);

            //$table->decimal('total_price_after_discount', 20, 4)->default(0);

            $table->decimal('total', 20, 4)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_items');
    }
};
