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
        Schema::create('purchase_return', function (Blueprint $table) {
            $table->id();
            $table->date('return_date');
            $table->string('prefix_code')->nullable();
            $table->string('count_id')->nullable();
            $table->string('return_code')->nullable();
            $table->string('reference_no')->nullable();

            //converted Purchases
            // $table->unsignedBigInteger('purchase_id')->nullable();
            // $table->foreign('purchase_id')->references('id')->on('purchases');

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
        Schema::dropIfExists('purchase_return');
    }
};
