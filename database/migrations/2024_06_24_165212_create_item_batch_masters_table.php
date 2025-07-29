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
        Schema::create('item_batch_masters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
            $table->string('batch_no')->nullable();
            $table->date('mfg_date')->nullable();
            $table->date('exp_date')->nullable();
            $table->string('model_no')->nullable();
            $table->decimal('mrp', 20, 4)->default(0);
            $table->string('color')->nullable();
            $table->string('size')->nullable();
            //$table->decimal('stock', 20, 4)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_batch_masters');
    }
};
