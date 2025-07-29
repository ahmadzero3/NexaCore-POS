<?php

namespace Database\Seeders\Updates;

use Illuminate\Database\Seeder;
use App\Models\Purchase\PurchaseOrder;
use App\Models\StatusHistory;
use Illuminate\Support\Facades\DB;

class Version148Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        echo "Version148Seeder Running...";
        $this->updateRecords();
        $this->addNewRecords();

        echo "\Version148Seeder Completed!!\n";
    }

    public function updateRecords()
    {
        if (PurchaseOrder::query()->exists()) {
            PurchaseOrder::query()->update(['order_status' => 'No Status']);

            $PurchaseOrders = PurchaseOrder::all();

            if($PurchaseOrders->isNotEmpty()){
                foreach($PurchaseOrders as $PurchaseOrder){

                    DB::table('status_histories')->insert([
                        'status' => 'No Status',
                            'status_date' => $PurchaseOrder->order_date,

                            'statusable_type' => 'Purchase Order',
                            'statusable_id' => $PurchaseOrder->id,

                            'created_by' => $PurchaseOrder->created_by,
                            'updated_by' => $PurchaseOrder->updated_by,

                            'created_at' => $PurchaseOrder->created_at,
                            'updated_at' => $PurchaseOrder->updated_at,
                    ]);


                }
            }
        }
    }

    public function addNewRecords()
    {
        //
    }
}
