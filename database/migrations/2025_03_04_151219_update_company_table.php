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
            $table->boolean('is_enable_carrier')->default(1)->after('is_enable_crm');
            $table->boolean('is_enable_carrier_charge')->default(1)->after('is_enable_carrier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('company', function (Blueprint $table) {
            $table->dropColumn(['is_enable_carrier', 'is_enable_carrier_charge']);
        });
    }
};
