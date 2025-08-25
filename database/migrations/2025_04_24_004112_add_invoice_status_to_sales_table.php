<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (Schema::hasTable('sales') && !Schema::hasColumn('sales', 'invoice_status')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->string('invoice_status')->default('pending');
            });
        }
    }

    public function down()
    {
        if (Schema::hasTable('sales')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn('invoice_status');
            });
        }
    }
};
