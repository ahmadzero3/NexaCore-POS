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
        Schema::table('item_transactions', function (Blueprint $table) {
            $table->decimal('charge_tax_amount', 20, 4)->default(0)->after('charge_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_transactions', function (Blueprint $table) {
            $table->dropColumn(['charge_tax_amount']);
        });
    }
};
