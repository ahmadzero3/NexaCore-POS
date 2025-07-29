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
        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->dropForeign(['permission_id']);
            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions'); // Recreate the foreign key without cascade

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('role_has_permissions', function (Blueprint $table) {
            $table->dropForeign(['permission_id']);
            $table->foreign('permission_id')
                ->references('id')
                ->on('permissions')
                ->onDelete('cascade'); // Recreate the foreign key with cascade
            
        });
    }
};
