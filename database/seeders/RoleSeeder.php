<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::create([
                                    'id' => 1,
                                    'name' => 'Admin',
                                    'status' => 1,
                                ]);

        $user = User::find(1);
        $user->role_id = 1;
        $user->save();

        // Create a new user record using Eloquent and save it
        $user = User::find(1);

        //This will add entry in model_has_roles entry
        $role = Role::find(1);
        
        $user->assignRole($role);

        $permissions = $role->permissions;

        $user->givePermissionTo($permissions);//Table: model_has_permissions


    }
}
