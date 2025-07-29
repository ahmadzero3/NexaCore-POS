<?php

namespace Database\Seeders\Updates;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PermissionGroup;
use Spatie\Permission\Models\Permission;

class Version143Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        echo "Version131Seeder Running...";
        $this->updatePermissions();
        $this->addNewPermissions();

        echo "\Version131Seeder Completed!!\n";
    }

    public function updatePermissions()
    {
       //
    }

    public function addNewPermissions()
    {
        $group = PermissionGroup::firstOrCreate(['name' => 'Items']);
        $groupId = $group->id; // Extract the ID

        $reportPermissionsArray = [
            [
                'name'          =>'item.brand.create',
                'display_name'  =>'Brand Create',
            ],
            [
                'name'          =>'item.brand.edit',
                'display_name'  =>'Brand Edit',
            ],
            [
                'name'          =>'item.brand.view',
                'display_name'  =>'Brand View',
            ],
            [
                'name'          =>'item.brand.delete',
                'display_name'  =>'Brand Delete',
            ],
        ];

        foreach ($reportPermissionsArray as $permission) {
            //Validate is the permission exist
            $isPermssionExist = Permission::where('name', $permission['name'])->count();
            $isPermssionExist = $isPermssionExist>0 ? true : false;

            if(!$isPermssionExist){
                Permission::firstOrCreate([
                                        'name' => $permission['name'],
                                        'display_name' => $permission['display_name'],
                                        'permission_group_id' => $groupId,
                                        'status' => 1,
                                    ]);
            }

        }
    }
}
