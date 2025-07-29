<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Get the records from the users table in the main database
        $groupData = DB::connection('mysql')->table('permission_groups')->get();

        // Insert the records into the users table in the tenant database
        foreach ($groupData as $group) {
            DB::table('permission_groups')->insert([
            'id' => $group->id,
            'name' => $group->name,
            'status' => $group->status,
            'created_at' => $group->created_at,
            'updated_at' => $group->updated_at,
            ]);
        }


        // Get the records from the users table in the main database
        $permissions = DB::connection('mysql')->table('permissions')->get();

        // Insert the records into the users table in the tenant database
        foreach ($permissions as $permission) {
            DB::table('permissions')->insert([
            'id' => $permission->id,
            'name' => $permission->name,
            'guard_name' => $permission->guard_name,
            'permission_group_id' => $permission->permission_group_id,
            'status' => $permission->status,
            'display_name' => $permission->display_name,
            'created_at' => $group->created_at,
            'updated_at' => $group->updated_at,
            ]);
        }

    }//up

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permission_groups', function (Blueprint $table) {
            //
        });
        
        Schema::table('permissions', function (Blueprint $table) {
            //
        });
    }
};
