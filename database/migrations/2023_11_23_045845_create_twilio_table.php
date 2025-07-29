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
        Schema::create('twilio', function (Blueprint $table) {
            $table->id();
            $table->text('sid')->nullable();
            $table->text('auth_token')->nullable();
            $table->text('twilio_number')->nullable();
            $table->integer('tenant_id')->nullable();
            $table->boolean('status')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('twilio');
    }
};
