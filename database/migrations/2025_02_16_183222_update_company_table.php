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
        Schema::table('company', function (Blueprint $table) {
            //USed While making sale invoice
            $table->boolean('restrict_to_sell_above_mrp')->default(0)->after('show_mrp');
            $table->boolean('restrict_to_sell_below_msp')->default(0)->after('restrict_to_sell_above_mrp');
            $table->boolean('auto_update_sale_price')->default(0)->after('restrict_to_sell_below_msp');

            //While making purchase entry/bill
            $table->boolean('auto_update_purchase_price')->default(0)->after('auto_update_sale_price');
            $table->boolean('auto_update_average_purchase_price')->default(0)->after('auto_update_purchase_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company', function (Blueprint $table) {
            $table->dropColumn(['restrict_to_sell_below_msp', 'restrict_to_sell_above_mrp', 'auto_update_sale_price', 'auto_update_purchase_price', 'auto_update_average_purchase_price']);
        });
    }
};
