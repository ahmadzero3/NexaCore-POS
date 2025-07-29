<?php

namespace Database\Seeders\Updates;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PermissionGroup;
use Spatie\Permission\Models\Permission;

class Version22Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        echo "Version22Seeder Running...";
        $this->updatePermissions();
        $this->addNewPermissions();

        echo "\Version22Seeder Completed!!\n";
    }

    public function updatePermissions()
    {
       //
    }

    public function addNewPermissions()
    {
        $group = PermissionGroup::firstOrCreate(['name' => 'Carrier']);
        $groupId = $group->id; // Extract the ID

        $reportPermissionsArray = [
            [
                'name'          =>'carrier.create',
                'display_name'  =>'Carrier Create',
            ],
            [
                'name'          =>'carrier.edit',
                'display_name'  =>'Carrier Edit',
            ],
            [
                'name'          =>'carrier.view',
                'display_name'  =>'Carrier View',
            ],
            [
                'name'          =>'carrier.delete',
                'display_name'  =>'Carrier Delete',
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
