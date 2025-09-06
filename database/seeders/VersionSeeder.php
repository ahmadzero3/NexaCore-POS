<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Database\Seeders\Updates\Version12Seeder;
use Database\Seeders\Updates\Version131Seeder;
use Database\Seeders\Updates\Version132Seeder;
use Database\Seeders\Updates\Version133Seeder;
use Database\Seeders\Updates\Version134Seeder;
use Database\Seeders\Updates\Version141Seeder;
use Database\Seeders\Updates\Version142Seeder;
use Database\Seeders\Updates\Version143Seeder;
use Database\Seeders\Updates\Version144Seeder;
use Database\Seeders\Updates\Version145Seeder;
use Database\Seeders\Updates\Version146Seeder;
use Database\Seeders\Updates\Version147Seeder;
use Database\Seeders\Updates\Version148Seeder;
use Database\Seeders\Updates\Version149Seeder;
use Database\Seeders\Updates\Version21Seeder;
use Database\Seeders\Updates\Version22Seeder;
use Database\Seeders\Updates\Version23Seeder;

class VersionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

       $newVersionArray = [
            '1.0',  //1.0, Date: 24-10-2024
            '1.1',  //1.0, Date: 28-11-2024
            '1.1.1',  //1.0, Date: 30-11-2024
            '1.2',  //1.2, Date: 07-12-2024
            '1.3',  //1.3, Date: 17-12-2024
            '1.3.1',  //1.3.1, Date: 22-12-2024
            '1.3.2',  //1.3.2, Date: 24-12-2024
            '1.3.3',  //1.3.3, Date: 28-12-2024
            '1.3.4',  //1.3.4, Date: 31-12-2024
            '1.4',  //1.4, Date: 01-01-2025
            '1.4.1',  //1.4.1, Date: 09-01-2025
            '1.4.2',  //1.4.2, Date: 13-01-2025
            '1.4.3',  //1.4.3, Date: 14-01-2025
            '1.4.4',  //1.4.4, Date: 18-01-2025
            '1.4.5',  //1.4.5, Date: 18-01-2025
            '1.4.6',  //1.4.6, Date: 31-01-2025
            '1.4.7',  //1.4.7, Date: 31-01-2025
            '1.4.8',  //1.4.8, Date: 03-02-2025
            '1.4.9',  //1.4.9, Date: 12-02-2025
            '1.5',  //1.5, Date: 14-02-2025
            '2.0',  //2.0, Date: 21-02-2025
            '2.1',  //2.1, Date: 25-02-2025
            '2.2',  //2.2, Date: 06-03-2025
            env('APP_VERSION'),  //2.3, Date: 08-03-2025
        ];

        $existingVersions = DB::table('versions')->pluck('version')->toArray();

        foreach ($newVersionArray as $version) {
            //validate is the version exist in it?
            if(!in_array($version, $existingVersions)){
                DB::table('versions')->insert([
                    'version' => $version,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                /**
                 * Version wise any seeder updates
                 * */
                $this->updateDatabaseTransaction($version);
            }
        }
    }

    public function updateDatabaseTransaction($version)
    {
        if($version == '1.2'){
            $adminSeeder = new Version12Seeder();
            $adminSeeder->run();
        }
        if($version == '1.3.1'){
            $adminSeeder = new Version131Seeder();
            $adminSeeder->run();
        }
        if($version == '1.3.2'){
            $adminSeeder = new Version132Seeder();
            $adminSeeder->run();
        }
        if($version == '1.3.3'){
            $adminSeeder = new Version133Seeder();
            $adminSeeder->run();
        }
        if($version == '1.3.4'){
            $adminSeeder = new Version134Seeder();
            $adminSeeder->run();
        }
        if($version == '1.4.1'){
            $adminSeeder = new Version141Seeder();
            $adminSeeder->run();
        }
        if($version == '1.4.2'){
            $adminSeeder = new Version142Seeder();
            $adminSeeder->run();
        }
        if($version == '1.4.3'){
            $adminSeeder = new Version143Seeder();
            $adminSeeder->run();
        }
        if($version == '1.4.4'){
            $adminSeeder = new Version144Seeder();
            $adminSeeder->run();
        }
        if($version == '1.4.5'){
            $adminSeeder = new Version145Seeder();
            $adminSeeder->run();
        }
        if($version == '1.4.6'){
            $adminSeeder = new Version146Seeder();
            $adminSeeder->run();
        }
        if($version == '1.4.7'){
            $adminSeeder = new Version147Seeder();
            $adminSeeder->run();
        }
        if($version == '1.4.8'){
            $adminSeeder = new Version148Seeder();
            $adminSeeder->run();
        }
        if($version == '1.4.9'){
            $adminSeeder = new Version149Seeder();
            $adminSeeder->run();
        }
        if($version == '2.1'){
            $adminSeeder = new Version21Seeder();
            $adminSeeder->run();
        }
        if($version == '2.2'){
            $adminSeeder = new Version22Seeder();
            $adminSeeder->run();
        }
        if($version == '2.3'){
            $adminSeeder = new Version23Seeder();
            $adminSeeder->run();
        }
    }

}
