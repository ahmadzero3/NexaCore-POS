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
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique(); // Currency name (e.g., "US Dollar")
            $table->string('symbol'); // Currency symbol (e.g., "$")
            $table->string('code', 3)->unique(); // ISO 4217 currency code (e.g., "USD")
            $table->decimal('exchange_rate', 15, 2)->default(1.00); // Exchange rate
            $table->boolean('is_company_currency')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
