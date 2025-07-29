<?php

namespace Database\Seeders\Updates;

use Illuminate\Database\Seeder;
use App\Models\Sale\SaleOrder;
use App\Models\StatusHistory;
use Illuminate\Support\Facades\DB;

class Version144Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        echo "Version144Seeder Running...";
        $this->updateRecords();
        $this->addNewRecords();

        echo "\Version144Seeder Completed!!\n";
    }

    public function updateRecords()
    {
        if (SaleOrder::query()->exists()) {
            SaleOrder::query()->update(['order_status' => 'No Status']);
        }

        $SaleOrders = SaleOrder::all();


        if($SaleOrders->isNotEmpty()){
            foreach($SaleOrders as $SaleOrder){

                DB::table('status_histories')->insert([
                    'status' => 'No Status',
                        'status_date' => $SaleOrder->order_date,

                        'statusable_type' => 'Sale Order',
                        'statusable_id' => $SaleOrder->id,

                        'created_by' => $SaleOrder->created_by,
                        'updated_by' => $SaleOrder->updated_by,

                        'created_at' => $SaleOrder->created_at,
                        'updated_at' => $SaleOrder->updated_at,
                ]);


            }
        }
    }

    public function addNewRecords()
    {
        //
    }
}
