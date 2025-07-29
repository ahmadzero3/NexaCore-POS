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
        Schema::table('ordered_products', function (Blueprint $table) {
            $table->string('staff_status')->nullable();
            $table->text('staff_status_note')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ordered_products', function (Blueprint $table) {
            $table->dropColumn(['staff_status','staff_status_note']);
        });
    }
};
