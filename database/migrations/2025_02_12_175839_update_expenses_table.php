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
        Schema::table('expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('expense_subcategory_id')->nullable()->after('expense_category_id');
            $table->foreign('expense_subcategory_id')->references('id')->on('expense_subcategories');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['expense_subcategory_id']);
            $table->dropColumn(['expense_subcategory_id']);
        });
    }

};
