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
            $table->boolean('is_wholesale_price_with_tax')
                  ->default(0)
                  ->change(); // Use change() to modify the column.
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->boolean('is_wholesale_price_with_tax')
                  ->default(null) // Revert to no default value if necessary.
                  ->change();
        });
    }
};
