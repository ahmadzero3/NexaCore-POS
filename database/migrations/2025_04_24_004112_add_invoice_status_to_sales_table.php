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
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'invoice_status')) {
                $table->string('invoice_status')->default('pending');
            }
        });
    }

    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'invoice_status')) {
                $table->dropColumn('invoice_status');
            }
        });
    }
};
