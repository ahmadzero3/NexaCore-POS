<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCloseCashTable extends Migration
{
    public function up()
    {
        Schema::create('close_cash', function (Blueprint $table) {
            $table->id();
            $table->decimal('opening_balance', 15, 2);
            $table->decimal('today_income', 15, 2);
            $table->decimal('total_income', 15, 2);
            $table->decimal('today_expenses', 15, 2);
            $table->decimal('balance', 15, 2);
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('close_cash');
    }
}
