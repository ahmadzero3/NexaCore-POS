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
            $table->string('charge_type')->nullable()->after('tax_amount');
            $table->decimal('charge_amount', 20, 4)->default(0)->after('charge_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('item_transactions', function (Blueprint $table) {
            $table->dropColumn(['charge_amount', 'charge_type']);
        });
    }
};
