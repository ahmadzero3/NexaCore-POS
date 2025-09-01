<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::create([
            'id'                    => 1,
            'name'                  => 'Synkode Shop.',
            'mobile'                => '71793152',
            'email'                 => 'synkode@info.com',
            'address'               => 'Ap: Lebanon, Saida',
            'language_code'         => null,
            'language_name'         => null,
            'timezone'              => 'Asia/Beirut',
            'date_format'           => 'Y-m-d',
            'time_format'           => '24',
        ]);
    }
}
