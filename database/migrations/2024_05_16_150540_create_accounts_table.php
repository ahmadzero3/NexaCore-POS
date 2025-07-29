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
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('group_id');
            $table->foreign('group_id')->references('id')->on('account_groups');

            $table->string('number')->nullable();
            $table->string('name');
            $table->string('description')->nullable();
            
            $table->decimal('debit_amt', 20, 4)->default(0);
            $table->decimal('credit_amt', 20, 4)->default(0);
            //$table->decimal('balance', 20, 4)->default(0);
            $table->string('unique_code')->nullable();
            
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users'); 
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('updated_by')->references('id')->on('users');
            
            $table->timestamps();
            
            $table->boolean('is_deletable')->default(1);

            $table->unsignedBigInteger('payment_type_bank_id')->nullable();
            $table->foreign('payment_type_bank_id')->references('id')->on('payment_types')->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts');
    }
};
