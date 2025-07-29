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
        Schema::table('orders', function (Blueprint $table) {
             // Drop the old foreign key
            //$table->dropColumn(['customer_id']);

            $table->unsignedBigInteger('party_id')->after('order_code'); // Add column after 'order_code'
            $table->foreign('party_id')->references('id')->on('parties'); // Define the foreign key

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['party_id']);
        });
    }
};
