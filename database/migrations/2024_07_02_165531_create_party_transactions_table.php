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
        Schema::create('party_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            
            $table->unsignedBigInteger('party_id');
            $table->foreign('party_id')->references('id')->on('parties')->onDelete('cascade');

            $table->decimal('to_pay', 20, 4)->default(0);
            $table->decimal('to_receive', 20, 4)->default(0);

            //auto creates transaction_id & transaction_type
            $table->morphs('transaction');

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
        Schema::dropIfExists('party_transactions');
    }
};
