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
        Schema::table('items', function (Blueprint $table) {
            $table->decimal('wholesale_price', 20, 4)->default(0)->after('sale_price_discount_type');
            $table->boolean('is_wholesale_price_with_tax')->after('wholesale_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['wholesale_price', 'is_wholesale_price_with_tax']);
        });
    }
};
