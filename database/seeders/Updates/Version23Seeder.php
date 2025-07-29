<?php

namespace Database\Seeders\Updates;

use App\Models\Items\Item;
use App\Models\Items\ItemTransaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PermissionGroup;
use Spatie\Permission\Models\Permission;

class Version23Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        echo "\nVersion23Seeder Running...";

        $this->updateItemTransactions();

        echo "\nVersion23Seeder Completed!!\n";
    }

    public function updateItemTransactions()
    {
        
        //get ItemTransaction table has null tax_id, just need to update the tax id as same as Item model tax_id
        
        $itemTransactions = ItemTransaction::where('tax_id', null)->where('unique_code', 'ITEM_OPENING')->get();

        if($itemTransactions->count() > 0){
            foreach ($itemTransactions as $transaction) {
                $transaction->tax_id = Item::find($transaction->item_id)->tax_id;
                $transaction->save();
            }//foreach

        }//if

    }//updateParties

}
