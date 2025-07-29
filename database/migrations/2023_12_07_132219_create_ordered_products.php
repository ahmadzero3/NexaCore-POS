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
        Schema::create('ordered_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');

            $table->unsignedBigInteger('service_id');
            $table->foreign('service_id')->references('id')->on('services');

            $table->text('description')->nullable();

            $table->date('start_date')->nullable()->comment('Event start date');
            $table->time('start_time')->nullable()->comment('Event start time');
            $table->date('end_date')->nullable()->comment('Event End date');
            $table->time('end_time')->nullable()->comment('Event End time');

            $table->decimal('unit_price', 10, 2)->default(0)->comment('original price(without tax)');
            $table->decimal('quantity', 10, 0)->default(0);
            $table->decimal('total_price', 10, 2)->default(0)->comment('(original price * quantity)');;

            /*Tax*/
            $table->unsignedBigInteger('tax_id');
            $table->foreign('tax_id')->references('id')->on('taxes');
            $table->string('tax_type')->default('inclusive');
            $table->decimal('tax_amount', 10, 2)->default(0);

            /*Discount*/
            $table->decimal('discount', 10, 2)->default(0);
            $table->string('discount_type')->nullable()->comment('fixed or percentage');
            $table->decimal('discount_amount', 10, 2)->default(0);

            $table->decimal('total_price_after_discount', 10, 2)->default(0);

            $table->decimal('total_price_with_tax', 10, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ordered_products');
    }
};
