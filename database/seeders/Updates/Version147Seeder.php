<?php

namespace Database\Seeders\Updates;

use App\Models\Currency;
use App\Models\Party\Party;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PermissionGroup;
use App\Models\Purchase\Purchase;
use App\Models\Purchase\PurchaseOrder;
use App\Models\Purchase\PurchaseReturn;
use App\Models\Sale\Quotation;
use App\Models\Sale\Sale;
use App\Models\Sale\SaleOrder;
use App\Models\Sale\SaleReturn;
use Spatie\Permission\Models\Permission;

class Version147Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        echo "Version147Seeder Running...";
        $this->updatePermissions();
        $this->addNewPermissions();

        echo "\Version147Seeder Completed!!\n";
    }

    public function updatePermissions()
    {
       //
    }

    public function addNewPermissions()
    {

        $permissionGroupId = PermissionGroup::firstOrCreate(['name' => 'Currency'])->id;

        $reportPermissionsArray = [
            [
                'name'          =>'currency.create',
                'display_name'  =>'Create',
                'permission_group_id'  => $permissionGroupId,
            ],
            [
                'name'          =>'currency.edit',
                'display_name'  =>'Edit',
                'permission_group_id'  => $permissionGroupId,
            ],
            [
                'name'          =>'currency.view',
                'display_name'  =>'View',
                'permission_group_id'  => $permissionGroupId,
            ],
            [
                'name'          =>'currency.delete',
                'display_name'  =>'Delete',
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

        //update other is_company_currency 0
        //Currency::query()->update(['is_company_currency' => 0]);

        //Insert Default Company Currency in the Curreny Model
        Currency::firstOrCreate(
            ['code' => 'USD'],
            [
            'name' => 'US Dollar',
            'symbol' => '$',
            'exchange_rate' => 1,
            'is_company_currency' => 1,
            'created_at' => now(),
            'updated_at' => now(),
            ]
        );

        $companyCurrency = Currency::where('is_company_currency', 1)->first();

        DB::table('parties')->update(['currency_id' => $companyCurrency->id]);

        //update Sale modal currency_id and exchange rate
        //use eloquent
        Sale::query()->update(['currency_id' => $companyCurrency->id, 'exchange_rate' => $companyCurrency->exchange_rate]);
        SaleOrder::query()->update(['currency_id' => $companyCurrency->id, 'exchange_rate' => $companyCurrency->exchange_rate]);
        SaleReturn::query()->update(['currency_id' => $companyCurrency->id, 'exchange_rate' => $companyCurrency->exchange_rate]);
        Quotation::query()->update(['currency_id' => $companyCurrency->id, 'exchange_rate' => $companyCurrency->exchange_rate]);
        Purchase::query()->update(['currency_id' => $companyCurrency->id, 'exchange_rate' => $companyCurrency->exchange_rate]);
        PurchaseOrder::query()->update(['currency_id' => $companyCurrency->id, 'exchange_rate' => $companyCurrency->exchange_rate]);
        PurchaseReturn::query()->update(['currency_id' => $companyCurrency->id, 'exchange_rate' => $companyCurrency->exchange_rate]);
        Party::query()->update(['currency_id' => $companyCurrency->id, 'exchange_rate' => $companyCurrency->exchange_rate]);

    }
}
