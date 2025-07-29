<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $records = [
            [
                'name'              =>  'None',
                'short_code'        =>  'None',
                'is_deletable'      =>  0,
            ],
            [
                'name'              =>  'Box',
                'short_code'        =>  'Box',
                'is_deletable'      =>  1,
            ],
            [
                'name'              =>  'Pieces',
                'short_code'        =>  'Pcs',
                'is_deletable'      =>  1,
            ],
            [
                'name'              =>  'Bag',
                'short_code'        =>  'Bgs',
                'is_deletable'      =>  1,
            ],
            [
                'name'              =>  'Bottles',
                'short_code'        =>  'Btl',
                'is_deletable'      =>  1,
            ],
            [
                'name'              =>  'Kilogram',
                'short_code'        =>  'Kgs',
                'is_deletable'      =>  1,
            ],
            [
                'name'              =>  'Grams',
                'short_code'        =>  'Gms',
                'is_deletable'      =>  1,
            ],
        ];
       
       foreach ($records as $record) {
            Unit::create([
                'name'               => $record['name'],
                'short_code'         => $record['short_code'],
                'is_deletable'       => $record['is_deletable'],
            ]);   
       }
        
    }
}
