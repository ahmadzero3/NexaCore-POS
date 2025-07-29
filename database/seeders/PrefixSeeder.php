<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Prefix;

class PrefixSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Prefix::create([
            'company_id'            => 1,
            'order'                 => 'ORD/',
            'job_code'              => 'JOB/',
            'expense'               => 'EXP/',
            'purchase_order'        => 'PO/',
            'purchase_bill'         => 'PB/',
            'purchase_return'       => 'PR/',
            'sale_order'            => 'SO/',
            'sale'                  => 'SL/',
            'sale_return'           => 'SR/',
            'stock_transfer'        => 'ST/',
            'quotation'             => 'QT/',
        ]);
    }
}
