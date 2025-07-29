<?php

namespace Database\Seeders\Updates;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PermissionGroup;
use Spatie\Permission\Models\Permission;

class Version12Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        echo "Version12Seeder Running...";
        $this->updatePermissions();
        $this->addNewPermissions();

        echo "\Version12Seeder Completed!!\n";
    }

    public function updatePermissions()
    {
        DB::table('permissions')
            ->where('name', 'expense.group.create')
            ->update(['name' => 'expense.category.create']);

        DB::table('permissions')
            ->where('name', 'expense.group.edit')
            ->update(['name' => 'expense.category.edit']);

        DB::table('permissions')
            ->where('name', 'expense.group.view')
            ->update(['name' => 'expense.category.view']);

        DB::table('permissions')
            ->where('name', 'expense.group.delete')
            ->update(['name' => 'expense.category.delete']);
    }

    public function addNewPermissions()
    {
        $group = PermissionGroup::firstOrCreate(['name' => 'Reports']);
        $groupId = $group->id; // Extract the ID

        $reportPermissionsArray = [
            [
                'name'          =>'report.customer.due.payment',
                'display_name'  =>'Customer Payments Due Report',
            ],
            [
                'name'          =>'report.supplier.due.payment',
                'display_name'  =>'Supplier Payments Due Report',
            ],
            [
                'name'          =>'report.stock_report.item.batch',
                'display_name'  =>'Batch Wise Item Stock Report',
            ],
            [
                'name'          =>'report.stock_report.item.serial',
                'display_name'  =>'Serial Wise Item Stock Report',
            ],
            [
                'name'          =>'report.stock_report.item.general',
                'display_name'  =>'General Item Stock Report',
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
