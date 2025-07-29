<?php

namespace Database\Seeders\Updates;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PermissionGroup;
use Spatie\Permission\Models\Permission;

class Version131Seeder extends Seeder
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
        $group = PermissionGroup::firstOrCreate(['name' => 'General']);
        $groupId = $group->id; // Extract the ID

        $reportPermissionsArray = [
            [
                'name'          =>'general.allow.to.view.item.purchase.price',
                'display_name'  =>'Allow User to View Item Purchase Price in Item Search(Invoice/Bill)',
            ],
            [
                'name'          =>'general.permission.to.apply.discount.to.sale',
                'display_name'  =>'Permission to Apply Discounts on Invoices',
            ],
            [
                'name'          =>'general.permission.to.apply.discount.to.purchase',
                'display_name'  =>'Permission to Apply Discounts on Purchases',
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
