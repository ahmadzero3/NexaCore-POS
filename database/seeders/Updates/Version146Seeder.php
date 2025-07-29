<?php

namespace Database\Seeders\Updates;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PermissionGroup;
use Spatie\Permission\Models\Permission;

class Version146Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        echo "Version146Seeder Running...";
        $this->updatePermissions();
        $this->addNewPermissions();

        echo "\Version146Seeder Completed!!\n";
    }

    public function updatePermissions()
    {
       //
    }

    public function addNewPermissions()
    {

        $permissionGroupId = PermissionGroup::firstOrCreate(['name' => 'Quotation'])->id;

        $reportPermissionsArray = [
            [
                'name'          =>'sale.quotation.create',
                'display_name'  =>'Create',
                'permission_group_id'  => $permissionGroupId,
            ],
            [
                'name'          =>'sale.quotation.edit',
                'display_name'  =>'Edit',
                'permission_group_id'  => $permissionGroupId,
            ],
            [
                'name'          =>'sale.quotation.view',
                'display_name'  =>'View',
                'permission_group_id'  => $permissionGroupId,
            ],
            [
                'name'          =>'sale.quotation.delete',
                'display_name'  =>'Delete',
                'permission_group_id'  => $permissionGroupId,
            ],
            [
                'name'          =>'sale.quotation.can.view.other.users.sale.quotations',
                'display_name'  =>'Allow User to View All Quotations Created By Other Users',
                'permission_group_id'  => $permissionGroupId,
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
                                        'permission_group_id' => $permission['permission_group_id'],
                                        'status' => 1,
                                    ]);
            }//if

        }//foreach
    }
}
