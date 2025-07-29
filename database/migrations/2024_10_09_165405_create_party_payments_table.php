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
        Schema::create('party_payments', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            
            $table->unsignedBigInteger('payment_type_id');
            $table->foreign('payment_type_id')->references('id')->on('payment_types');

            $table->enum('payment_direction', ['receive', 'pay']);

            $table->unsignedBigInteger('party_id');
            $table->foreign('party_id')->references('id')->on('parties');

            //Each Qty Price: with or without tax
            $table->decimal('amount', 20, 4)->default(0);
            $table->string('reference_no')->nullable();
            $table->text('note')->nullable();

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
        Schema::dropIfExists('party_payments');
    }
};
