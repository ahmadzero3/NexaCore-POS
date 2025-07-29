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
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('prefix_code')->nullable();
            $table->string('count_id');
            $table->string('item_code');
            $table->string('name');
            $table->text('description')->nullable();
            $table->text('hsn')->nullable();
            $table->text('sku')->nullable();
            //Is Service item or not
            $table->boolean('is_service')->default(0);

            $table->unsignedBigInteger('item_category_id')->nullable();
            $table->foreign('item_category_id')->references('id')->on('item_categories');

            //Unit Details
            $table->unsignedBigInteger('base_unit_id')->nullable();
            $table->foreign('base_unit_id')->references('id')->on('units');
            $table->unsignedBigInteger('secondary_unit_id')->nullable();
            $table->foreign('secondary_unit_id')->references('id')->on('units');
            $table->decimal('conversion_rate', 20, 4)->default(0);

            //Pricing Details
            $table->decimal('sale_price', 20, 4)->default(0);
            $table->boolean('is_sale_price_with_tax');
            $table->decimal('sale_price_discount', 20, 4)->default(0);
            $table->string('sale_price_discount_type');
            $table->decimal('purchase_price', 20, 4)->default(0);
            $table->boolean('is_purchase_price_with_tax');
            $table->decimal('mrp', 20, 4)->default(0);

            //Tax
            $table->unsignedBigInteger('tax_id')->nullable();
            $table->foreign('tax_id')->references('id')->on('taxes');

            //Tracking
            $table->string('tracking_type');
            $table->string('item_location')->nullable();

            //Stock
            $table->decimal('min_stock', 20, 4)->default(0);
            $table->decimal('current_stock', 20, 4)->default(0);

            //Image
            $table->string('image_path')->nullable();

            //Status
            $table->boolean('status')->default(1);
            
            //entry details
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
        Schema::dropIfExists('items');
    }
};
