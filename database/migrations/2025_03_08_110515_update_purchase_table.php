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
        Schema::table('purchases', function (Blueprint $table) {
            $table->unsignedBigInteger('carrier_id')->nullable()->after('state_id');
            $table->foreign('carrier_id')->references('id')->on('carriers');

            $table->decimal('shipping_charge', 20, 4)->default(0)->after('note');

            $table->boolean('is_shipping_charge_distributed')->default(0)->after('shipping_charge');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            $table->dropForeign(['carrier_id']);
            $table->dropColumn(['carrier_id', 'is_shipping_charge_distributed', 'shipping_charge']);
        });
    }
};
