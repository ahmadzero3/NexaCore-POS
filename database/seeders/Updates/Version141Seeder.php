<?php

namespace Database\Seeders\Updates;

use App\Models\Items\ItemTransaction;
use App\Models\Party\Party;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PermissionGroup;
use Spatie\Permission\Models\Permission;

class Version141Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        echo "\Version141Seeder Running...";

        $this->updateParties();

        echo "\Version141Seeder Completed!!\n";
    }

    public function updateParties()
    {
        $itemTransactions = ItemTransaction::where('transaction_type', 'Item Opening')->get();

        if($itemTransactions->count() > 0){
            foreach ($itemTransactions as $transaction) {
                $transaction->total = $transaction->quantity * $transaction->unit_price;
                $transaction->save();

            }//foreach

        }//if

    }//updateParties

}
