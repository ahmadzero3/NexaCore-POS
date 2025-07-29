<?php

namespace Database\Seeders\Updates;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PermissionGroup;
use Spatie\Permission\Models\Permission;

class Version134Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        echo "Version134Seeder Running...";
        $this->updatePermissions();
        $this->addNewPermissions();

        echo "\Version134Seeder Completed!!\n";
    }

    public function updatePermissions()
    {
       //
    }

    public function addNewPermissions()
    {


        $reportPermissionsArray = [
            [
                'name'          =>'sale.invoice.can.view.other.users.sale.invoices',//
                'display_name'  =>'Allow User to View All Sale Invoices Created By Other Users',
                'permission_group_id'  => PermissionGroup::firstOrCreate(['name' => 'Sale Bill'])->id,
            ],
            [
                'name'          =>'sale.order.can.view.other.users.sale.orders',//
                'display_name'  =>'Allow User to View All Sale Orders Created By Other Users',
                'permission_group_id'  => PermissionGroup::firstOrCreate(['name' => 'Sale Order'])->id,
            ],
            [
                'name'          =>'sale.return.can.view.other.users.sale.returns',//
                'display_name'  =>'Allow User to View All Sale Returns Created By Other Users',
                'permission_group_id'  => PermissionGroup::firstOrCreate(['name' => 'Sale Return'])->id,
            ],
            [
                'name'          =>'purchase.bill.can.view.other.users.purchase.bills',//
                'display_name'  =>'Allow User to View All Purchase Bills Created By Other Users',
                'permission_group_id'  => PermissionGroup::firstOrCreate(['name' => 'Purchase Bill'])->id,
            ],
            [
                'name'          =>'purchase.order.can.view.other.users.purchase.orders',//
                'display_name'  =>'Allow User to View All Purchase Orders Created By Other Users',
                'permission_group_id'  => PermissionGroup::firstOrCreate(['name' => 'Purchase Order'])->id,
            ],
            [
                'name'          =>'purchase.return.can.view.other.users.purchase.returns',//
                'display_name'  =>'Allow User to View All Purchase Returns Created By Other Users',
                'permission_group_id'  => PermissionGroup::firstOrCreate(['name' => 'Purchase Return'])->id,
            ],
            [
                'name'          =>'expense.can.view.other.users.expenses',
                'display_name'  =>'Allow User to View All Expenses Created By Other Users',
                'permission_group_id'  => PermissionGroup::firstOrCreate(['name' => 'Expense'])->id,
            ],
            [
                'name'          =>'stock_transfer.can.view.other.users.stock.transfers',
                'display_name'  =>'Allow User to View All Stock Transfer Created By Other Users',
                'permission_group_id'  => PermissionGroup::firstOrCreate(['name' => 'Stock Transfer'])->id,
            ],
            [
                'name'          =>'dashboard.can.view.widget.cards',
                'display_name'  =>'Allow User to View Dashboard Widget Cards',
                'permission_group_id'  => PermissionGroup::firstOrCreate(['name' => 'Dashboard'])->id,
            ],

            [
                'name'          =>'dashboard.can.view.sale.vs.purchase.bar.chart',
                'display_name'  =>'Allow User to View Sale Vs. Purchase Bar Chart on Dashboard',
                'permission_group_id'  => PermissionGroup::firstOrCreate(['name' => 'Dashboard'])->id,
            ],

            [
                'name'          =>'dashboard.can.view.trending.items.pie.chart',
                'display_name'  =>'Allow User to View Trending Items Pie Chart on Dashboard',
                'permission_group_id'  => PermissionGroup::firstOrCreate(['name' => 'Dashboard'])->id,
            ],

            [
                'name'          =>'dashboard.can.view.recent.invoices.table',
                'display_name'  =>'Allow User to View Recent Invoices Table on Dashboard',
                'permission_group_id'  => PermissionGroup::firstOrCreate(['name' => 'Dashboard'])->id,
            ],

            [
                'name'          =>'dashboard.can.view.self.dashboard.details.only',
                'display_name'  =>'Allow User to View Only Their Own Dashboard Details',
                'permission_group_id'  => PermissionGroup::firstOrCreate(['name' => 'Dashboard'])->id,
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
