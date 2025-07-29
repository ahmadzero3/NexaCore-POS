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
        Schema::create('prefix', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id');
            $table->foreign('company_id')->references('id')->on('company');
            $table->string('order')->nullable();
            $table->string('service')->nullable();
            $table->string('job_code')->nullable();/*Used in OrderedProduct Model*/
            $table->string('service_master')->nullable();
            $table->string('customer')->nullable();
            $table->string('expense')->nullable();
            $table->string('purchase_order')->nullable();
            $table->string('purchase_bill')->nullable();
            $table->string('purchase_return')->nullable();
            $table->string('sale_order')->nullable();
            $table->string('sale')->nullable();
            $table->string('sale_return')->nullable();
            $table->string('stock_transfer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prefix');
    }
};
