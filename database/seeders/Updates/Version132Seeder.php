<?php

namespace Database\Seeders\Updates;

use App\Models\Party\Party;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\PermissionGroup;
use Spatie\Permission\Models\Permission;

class Version132Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        echo "\nVersion132Seeder Running...";

        $this->updateParties();

        echo "\nVersion132Seeder Completed!!\n";
    }

    public function updateParties()
    {
        $parties = Party::all();

        if($parties->count() > 0){
            foreach ($parties as $party) {
                if($party->credit_limit == 0){
                    $party->is_set_credit_limit = 0;//No credit limit

                }else{
                    $party->is_set_credit_limit = 1;//Has credit limit

                }
                $party->save();

            }//foreach

        }//if

    }//updateParties

}
